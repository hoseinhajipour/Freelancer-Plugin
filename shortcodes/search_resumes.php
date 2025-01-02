<?php
if (!defined('ABSPATH')) {
    exit;
}

// شورت‌کد برای جستجو و فیلتر رزومه‌ها
function search_resumes_shortcode($atts) {
    // تنظیمات صفحه‌بندی
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

    // گرفتن مقادیر فیلترها
    $search_query = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
    $province_filter = isset($_GET['province']) ? intval($_GET['province']) : '';
    $city_filter = isset($_GET['city']) ? intval($_GET['city']) : '';
    $skills_filter = isset($_GET['skills']) ? array_map('intval', $_GET['skills']) : [];

    // تنظیم آرگومان‌های WP_Query
    $args = [
        'post_type' => 'resume',
        'post_status' => 'publish',
        'paged' => $paged,
        's' => $search_query,
        'tax_query' => [
            'relation' => 'AND',
        ],
    ];

    if ($province_filter) {
        $args['tax_query'][] = [
            'taxonomy' => 'province',
            'field' => 'term_id',
            'terms' => $province_filter,
        ];
    }

    if ($city_filter) {
        $args['tax_query'][] = [
            'taxonomy' => 'city',
            'field' => 'term_id',
            'terms' => $city_filter,
        ];
    }

    if (!empty($skills_filter)) {
        $args['tax_query'][] = [
            'taxonomy' => 'skills',
            'field' => 'term_id',
            'terms' => $skills_filter,
            'operator' => 'AND',
        ];
    }

    $query = new WP_Query($args);

    ob_start();
    ?>
    <form method="get" action="" class="resume-search-form">
        <label for="search">جستجو:</label>
        <input type="text" id="search" name="s" value="<?php echo esc_attr($search_query); ?>" placeholder="عنوان یا توضیحات رزومه">

        <label for="province">استان:</label>
        <select id="province" name="province" onchange="updateCities(this.value)">
            <option value="">همه استان‌ها</option>
            <?php
            $provinces = get_terms(['taxonomy' => 'province', 'hide_empty' => false]);
            foreach ($provinces as $province) {
                $selected = ($province_filter == $province->term_id) ? 'selected' : '';
                echo '<option value="' . esc_attr($province->term_id) . '" ' . $selected . '>' . esc_html($province->name) . '</option>';
            }
            ?>
        </select>

        <label for="city">شهر:</label>
        <select id="city" name="city">
            <option value="">همه شهرها</option>
            <?php
            if ($province_filter) {
                $cities = get_terms([
                    'taxonomy' => 'city',
                    'hide_empty' => false,
                    'meta_query' => [
                        [
                            'key' => 'province_id',
                            'value' => $province_filter,
                            'compare' => '=',
                        ],
                    ],
                ]);
                foreach ($cities as $city) {
                    $selected = ($city_filter == $city->term_id) ? 'selected' : '';
                    echo '<option value="' . esc_attr($city->term_id) . '" ' . $selected . '>' . esc_html($city->name) . '</option>';
                }
            }
            ?>
        </select>

        <label>مهارت‌ها:</label>
        <div class="skills-checkbox">
            <?php
            $skills = get_terms(['taxonomy' => 'skills', 'hide_empty' => false]);
            foreach ($skills as $skill) {
                $checked = in_array($skill->term_id, $skills_filter) ? 'checked' : '';
                echo '<label><input type="checkbox" name="skills[]" value="' . esc_attr($skill->term_id) . '" ' . $checked . '> ' . esc_html($skill->name) . '</label>';
            }
            ?>
        </div>

        <button type="submit">جستجو</button>
    </form>

    <?php if ($query->have_posts()) : ?>
        <ul class="resume-list">
            <?php while ($query->have_posts()) : $query->the_post(); ?>
                <li>
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    <p><?php echo wp_trim_words(get_the_content(), 15); ?></p>
                    <?php
                    $province_terms = wp_get_post_terms(get_the_ID(), 'province');
                    $city_terms = wp_get_post_terms(get_the_ID(), 'city');
                    $skills_terms = wp_get_post_terms(get_the_ID(), 'skills');
                    ?>
                    <p>
                        <strong>استان:</strong> <?php echo $province_terms ? esc_html($province_terms[0]->name) : 'نامشخص'; ?>
                        <strong>شهر:</strong> <?php echo $city_terms ? esc_html($city_terms[0]->name) : 'نامشخص'; ?>
                        <strong>مهارت‌ها:</strong>
                        <?php
                        if (!empty($skills_terms)) {
                            foreach ($skills_terms as $skill) {
                                echo esc_html($skill->name) . ' ';
                            }
                        } else {
                            echo 'نامشخص';
                        }
                        ?>
                    </p>
                </li>
            <?php endwhile; ?>
        </ul>

        <div class="pagination">
            <?php
            echo paginate_links([
                'total' => $query->max_num_pages,
                'current' => $paged,
                'format' => '?paged=%#%',
                'prev_text' => '«',
                'next_text' => '»',
            ]);
            ?>
        </div>
    <?php else : ?>
        <p>هیچ رزومه‌ای یافت نشد.</p>
    <?php endif;

    wp_reset_postdata();

    return ob_get_clean();
}
add_shortcode('search_resumes', 'search_resumes_shortcode');
