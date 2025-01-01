<?php
if (!defined('ABSPATH')) {
    exit;
}
function simple_register_resume_shortcode() {
    if (!is_user_logged_in()) {
        return '<p>برای ثبت یا ویرایش رزومه باید وارد شوید.</p>';
    }

    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;

    // Find existing resume
    $existing_resume = new WP_Query([
        'post_type'      => 'resume',
        'author'         => $user_id,
        'posts_per_page' => 1,
    ]);

    $resume_id = null;
    $resume_data = [
        'title'               => '',
        'full_name'           => '',
        'gender'              => '',
        'birth_date'          => '',
        'about_me'            => '',
        'work_experience'     => '',
        'education_experience' => '',
        'skills'              => [],
        'user_avatar'         => '',
        'portfolio'           => [],
    ];

    if ($existing_resume->have_posts()) {
        $existing_resume->the_post();
        $resume_id = get_the_ID();
        $resume_data = [
            'title'               => get_the_title(),
            'full_name'           => get_post_meta($resume_id, 'full_name', true),
            'gender'              => get_post_meta($resume_id, 'gender', true),
            'birth_date'          => get_post_meta($resume_id, 'birth_date', true),
            'about_me'            => get_post_meta($resume_id, 'about_me', true),
            'work_experience'     => get_post_meta($resume_id, 'work_experience', true),
            'education_experience' => get_post_meta($resume_id, 'education_experience', true),
            'skills'              => wp_get_object_terms($resume_id, 'skills', ['fields' => 'slugs']),
            'user_avatar'         => get_post_meta($resume_id, 'user_avatar', true),
            'portfolio'           => get_post_meta($resume_id, 'portfolio', true) ?: [],
        ];
        wp_reset_postdata();
    }

    ob_start();
    ?>
    <form method="post" action="" class="resume-form" enctype="multipart/form-data">
        <label for="resume_title">عنوان رزومه:</label>
        <input type="text" id="resume_title" name="resume_title" value="<?php echo esc_attr($resume_data['title']); ?>" required>

        <label for="user_avatar">تصویر آواتار:</label>
        <?php if ($resume_data['user_avatar']) : ?>
            <img src="<?php echo wp_get_attachment_url($resume_data['user_avatar']); ?>" alt="آواتار" style="max-width: 100px; display: block;">
        <?php endif; ?>
        <input type="file" id="user_avatar" name="user_avatar" accept="image/*">

        <label for="full_name">نام و نام خانوادگی:</label>
        <input type="text" id="full_name" name="full_name" value="<?php echo esc_attr($resume_data['full_name']); ?>" required>

        <label for="gender">جنسیت:</label>
        <select id="gender" name="gender">
            <option value="male" <?php selected($resume_data['gender'], 'male'); ?>>مرد</option>
            <option value="female" <?php selected($resume_data['gender'], 'female'); ?>>زن</option>
        </select>

        <label for="birth_date">تاریخ تولد:</label>
        <input type="date" id="birth_date" name="birth_date" value="<?php echo esc_attr($resume_data['birth_date']); ?>">

        <label for="about_me">درباره من:</label>
        <?php wp_editor($resume_data['about_me'], 'about_me', ['textarea_name' => 'about_me']); ?>

        <label for="work_experience">سابقه کاری:</label>
        <textarea id="work_experience" name="work_experience"><?php echo esc_textarea($resume_data['work_experience']); ?></textarea>

        <label for="education_experience">سابقه تحصیلی:</label>
        <textarea id="education_experience" name="education_experience"><?php echo esc_textarea($resume_data['education_experience']); ?></textarea>

        <label>مهارت‌ها:</label>
        <div>
            <?php
            $skills_terms = get_terms(['taxonomy' => 'skills', 'hide_empty' => false]);
            foreach ($skills_terms as $term) {
                $checked = in_array($term->slug, $resume_data['skills']) ? 'checked' : '';
                echo '<label><input type="checkbox" name="skills[]" value="' . esc_attr($term->slug) . '" ' . $checked . '> ' . esc_html($term->name) . '</label>';
            }
            ?>
        </div>

        <label for="portfolio">نمونه کارها:</label>
        <?php if (!empty($resume_data['portfolio'])) : ?>
            <div>
                <?php foreach ($resume_data['portfolio'] as $portfolio_id) : ?>
                    <img src="<?php echo wp_get_attachment_url($portfolio_id); ?>" alt="نمونه کار" style="max-width: 100px; margin: 5px;">
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <input type="file" id="portfolio" name="portfolio[]" multiple accept="image/*">

        <button type="submit" name="submit_resume"><?php echo $resume_id ? 'ویرایش رزومه' : 'ثبت رزومه'; ?></button>
    </form>
    <?php

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_resume'])) {
        $title = sanitize_text_field($_POST['resume_title']);
        $full_name = sanitize_text_field($_POST['full_name']);
        $gender = sanitize_text_field($_POST['gender']);
        $birth_date = sanitize_text_field($_POST['birth_date']);
        $about_me = wp_kses_post($_POST['about_me']);
        $work_experience = sanitize_textarea_field($_POST['work_experience']);
        $education_experience = sanitize_textarea_field($_POST['education_experience']);
        $skills = isset($_POST['skills']) ? array_map('sanitize_text_field', $_POST['skills']) : [];

        $post_args = [
            'ID'           => $resume_id,
            'post_title'   => $title,
            'post_type'    => 'resume',
            'post_status'  => 'publish',
        ];

        if (!$resume_id) {
            $post_args['post_author'] = $user_id;
        }

        $post_id = wp_insert_post($post_args);

        if ($post_id) {
            // Save metadata
            update_post_meta($post_id, 'full_name', $full_name);
            update_post_meta($post_id, 'gender', $gender);
            update_post_meta($post_id, 'birth_date', $birth_date);
            update_post_meta($post_id, 'about_me', $about_me);
            update_post_meta($post_id, 'work_experience', $work_experience);
            update_post_meta($post_id, 'education_experience', $education_experience);

            // Save taxonomy terms
            wp_set_object_terms($post_id, $skills, 'skills');
        }

        echo '<p>رزومه با موفقیت ذخیره شد.</p>';
    }

    return ob_get_clean();
}

add_shortcode('register_resume', 'simple_register_resume_shortcode');
