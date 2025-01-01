<?php
if (!defined('ABSPATH')) {
    exit;
}

function my_projects_shortcode() {
    // بررسی اینکه آیا کاربر وارد شده است
    if (!is_user_logged_in()) {
        return '<p>لطفاً ابتدا وارد حساب کاربری خود شوید.</p>';
    }

    // دریافت شناسه کاربر فعلی
    $current_user_id = get_current_user_id();

    // آرگومان‌های کوئری برای دریافت پروژه‌های کاربر فعلی
    $args = [
        'post_type' => 'project',
        'author' => $current_user_id,
        'posts_per_page' => -1,
    ];

    $query = new WP_Query($args);

    ob_start();

    echo '<h3>پروژه‌های من</h3>';

    if ($query->have_posts()) {
        echo '<ul class="my-projects-list">';
        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            $title = get_the_title();
            $edit_link = get_edit_post_link($post_id);

            echo '<li>';
            echo '<h4>' . esc_html($title) . '</h4>';
            echo '<p><a href="' . get_permalink($post_id) . '">مشاهده پروژه</a></p>';
            if ($edit_link) {
                echo '<p><a href="' . esc_url($edit_link) . '">ویرایش پروژه</a></p>';
            }
            echo '<form method="post" action="">
                    <input type="hidden" name="delete_project_id" value="' . esc_attr($post_id) . '">
                    <button type="submit" name="delete_project" onclick="return confirm(\'آیا از حذف پروژه اطمینان دارید؟\');">حذف پروژه</button>
                  </form>';
            echo '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>شما هیچ پروژه‌ای ثبت نکرده‌اید.</p>';
    }

    // بررسی درخواست حذف پروژه
    if (isset($_POST['delete_project']) && isset($_POST['delete_project_id'])) {
        $delete_project_id = intval($_POST['delete_project_id']);

        // اطمینان از اینکه پروژه متعلق به کاربر فعلی است
        $project_author = get_post_field('post_author', $delete_project_id);
        if ($project_author == $current_user_id) {
            wp_delete_post($delete_project_id, true);
            echo '<p>پروژه با موفقیت حذف شد.</p>';
        } else {
            echo '<p>شما مجاز به حذف این پروژه نیستید.</p>';
        }
    }

    wp_reset_postdata();

    return ob_get_clean();
}

add_shortcode('my_projects', 'my_projects_shortcode');
