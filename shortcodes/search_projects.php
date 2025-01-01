<?php
if (!defined('ABSPATH')) {
    exit;
}

function advanced_search_projects_shortcode()
{
    ob_start();

    // دریافت شماره صفحه فعلی
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

    // تنظیم آرگومان‌های پیش‌فرض
    $args = [
        'post_type' => 'project',
        'posts_per_page' => 12,
        'paged' => $paged,
    ];

    // اگر جستجویی انجام شده باشد
    if (!empty($_GET['search_keyword'])) {
        $keyword = sanitize_text_field($_GET['search_keyword']);
        $args['s'] = $keyword;
    }

    $query = new WP_Query($args);
    ?>
    <form method="get" action="">
        <label for="search_keyword">کلمه کلیدی:</label>
        <input type="text" id="search_keyword" name="search_keyword"
               value="<?php echo isset($_GET['search_keyword']) ? esc_attr($_GET['search_keyword']) : ''; ?>">
        <button type="submit">جستجو</button>
    </form>
    <?php
    if ($query->have_posts()) {

        echo '<ul class="project-list">';
        while ($query->have_posts()) {
            $query->the_post();

            // دریافت متا‌فیلدها و مهارت‌ها
            $budget = get_post_meta(get_the_ID(), 'project_budget', true);
            //  $skills = wp_get_post_terms(get_the_ID(), 'skills');
            $skills = wp_get_post_terms(get_the_ID(), 'skills', ['fields' => 'names']);
            $excerpt = get_the_excerpt();

            echo '<li>';
            echo '<h3><a href="' . get_permalink() . '">' . get_the_title() . '</a></h3>';

            // نمایش خلاصه پروژه
            if (!empty($excerpt)) {
                echo '<p>' . esc_html($excerpt) . '</p>';
            }

            // نمایش قیمت
            if (!empty($budget)) {
                echo '<p><strong>هزینه:</strong> ' . esc_html(number_format($budget)) . ' تومان</p>';
            }

            // نمایش مهارت‌ها
            if (!empty($skills)) {

                echo '<p><strong>مهارت‌ها:</strong> ' . esc_html(implode(', ', $skills)) . '</p>';
            } else {
                echo '<p><strong>مهارت‌ها:</strong> مشخص نشده است.</p>';
            }

            echo '</li>';
        }
        echo '</ul>';

        // نمایش صفحه‌بندی
        echo '<div class="pagination">';
        echo paginate_links([
            'total' => $query->max_num_pages,
            'current' => $paged,
        ]);
        echo '</div>';

    } else {
        echo '<p>هیچ پروژه‌ای پیدا نشد.</p>';
    }

    wp_reset_postdata();
    return ob_get_clean();
}

add_shortcode('search_projects', 'advanced_search_projects_shortcode');
