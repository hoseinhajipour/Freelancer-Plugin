<?php

// ثبت پست تایپ پروژه‌ها
function register_project_post_type() {
    $labels = [
        'name' => 'پروژه‌ها',
        'singular_name' => 'پروژه',
        'add_new' => 'افزودن پروژه جدید',
        'add_new_item' => 'افزودن پروژه جدید',
        'edit_item' => 'ویرایش پروژه',
        'new_item' => 'پروژه جدید',
        'view_item' => 'مشاهده پروژه',
        'search_items' => 'جستجوی پروژه‌ها',
        'not_found' => 'هیچ پروژه‌ای پیدا نشد',
        'not_found_in_trash' => 'هیچ پروژه‌ای در زباله‌دان پیدا نشد',
        'all_items' => 'تمام پروژه‌ها',
        'menu_name' => 'پروژه‌ها',
        'name_admin_bar' => 'پروژه',
    ];

    $args = [
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'supports' => ['title', 'editor', 'custom-fields'],
        'menu_icon' => 'dashicons-portfolio',
    ];

    register_post_type('project', $args);
}
add_action('init', 'register_project_post_type');

// ثبت پست تایپ رزومه‌ها
function register_resume_post_type() {
    $labels = [
        'name' => 'رزومه‌ها',
        'singular_name' => 'رزومه',
        'add_new' => 'افزودن رزومه جدید',
        'add_new_item' => 'افزودن رزومه جدید',
        'edit_item' => 'ویرایش رزومه',
        'new_item' => 'رزومه جدید',
        'view_item' => 'مشاهده رزومه',
        'search_items' => 'جستجوی رزومه‌ها',
        'not_found' => 'هیچ رزومه‌ای پیدا نشد',
        'not_found_in_trash' => 'هیچ رزومه‌ای در زباله‌دان پیدا نشد',
        'all_items' => 'تمام رزومه‌ها',
        'menu_name' => 'رزومه‌ها',
        'name_admin_bar' => 'رزومه',
    ];

    $args = [
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'supports' => ['title', 'editor', 'custom-fields'],
        'menu_icon' => 'dashicons-id',
    ];

    register_post_type('resume', $args);
}
add_action('init', 'register_resume_post_type');

// ثبت taxonomy مهارت‌ها
function register_skills_taxonomy() {
    $labels = [
        'name'              => 'مهارت‌ها',
        'singular_name'     => 'مهارت',
        'search_items'      => 'جستجوی مهارت‌ها',
        'all_items'         => 'تمام مهارت‌ها',
        'edit_item'         => 'ویرایش مهارت',
        'update_item'       => 'بروزرسانی مهارت',
        'add_new_item'      => 'افزودن مهارت جدید',
        'new_item_name'     => 'نام مهارت جدید',
        'menu_name'         => 'مهارت‌ها',
    ];

    $args = [
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => ['slug' => 'skills'],
    ];

    register_taxonomy('skills', ['resume', 'project'], $args);
}
add_action('init', 'register_skills_taxonomy');

// افزودن منوها و زیرمنوها
function register_project_management_menu() {
    add_menu_page(
        'مدیریت پروژه‌ها',
        'مدیریت پروژه‌ها',
        'manage_options',
        'project-management',
        'project_management_dashboard',
        'dashicons-portfolio',
        6
    );

    add_submenu_page(
        'project-management',
        'پروژه‌ها',
        'پروژه‌ها',
        'manage_options',
        'edit.php?post_type=project'
    );

    add_submenu_page(
        'project-management',
        'رزومه‌ها',
        'رزومه‌ها',
        'manage_options',
        'edit.php?post_type=resume'
    );

    add_submenu_page(
        'project-management',
        'مهارت‌ها',
        'مهارت‌ها',
        'manage_options',
        'edit-tags.php?taxonomy=skills'
    );

    add_submenu_page(
        'project-management',
        'تنظیمات',
        'تنظیمات',
        'manage_options',
        'project-settings',
        'project_settings_page'
    );
}
add_action('admin_menu', 'register_project_management_menu');

// صفحه داشبورد مدیریت پروژه‌ها
function project_management_dashboard() {
    $project_count = wp_count_posts('project')->publish;
    $resume_count = wp_count_posts('resume')->publish;
    ?>
    <div class="wrap">
        <h1>مدیریت پروژه‌ها</h1>
        <ul>
            <li><strong>تعداد پروژه‌ها:</strong> <?php echo esc_html($project_count); ?></li>
            <li><strong>تعداد رزومه‌ها:</strong> <?php echo esc_html($resume_count); ?></li>
        </ul>
    </div>
    <?php
}

// صفحه تنظیمات
function project_settings_page() {
    ?>
    <div class="wrap">
        <h1>تنظیمات</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('project_settings_group');
            do_settings_sections('project-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// ثبت تنظیمات
function register_project_settings() {
    register_setting('project_settings_group', 'project_option');
    add_settings_section('project_settings_section', 'تنظیمات عمومی', null, 'project-settings');
    add_settings_field('project_option', 'گزینه تنظیمات', 'project_option_callback', 'project-settings', 'project_settings_section');
}
add_action('admin_init', 'register_project_settings');

// ورودی تنظیمات
function project_option_callback() {
    $option = get_option('project_option');
    echo '<input type="text" name="project_option" value="' . esc_attr($option) . '" />';

    if (isset($_POST['add_provinces_and_cities'])) {
        init_default_provinces_and_cities();
        echo '<div class="updated"><p>استان‌ها و شهرها با موفقیت اضافه شدند.</p></div>';
    }
    ?>
    <div class="wrap">
        <h1>مدیریت استان‌ها و شهرها</h1>
        <form method="post" action="">
            <p>با کلیک روی دکمه زیر، لیست پیش‌فرض استان‌ها و شهرهای ایران اضافه خواهد شد.</p>
            <button type="submit" name="add_provinces_and_cities" class="button button-primary">افزودن استان‌ها و شهرها</button>
        </form>
    </div>
    <?php
}


// ثبت taxonomy برای استان
function register_province_taxonomy() {
    $labels = [
        'name'              => 'استان‌ها',
        'singular_name'     => 'استان',
        'search_items'      => 'جستجوی استان‌ها',
        'all_items'         => 'تمام استان‌ها',
        'edit_item'         => 'ویرایش استان',
        'update_item'       => 'به‌روزرسانی استان',
        'add_new_item'      => 'افزودن استان جدید',
        'new_item_name'     => 'نام استان جدید',
        'menu_name'         => 'استان‌ها',
    ];

    $args = [
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => ['slug' => 'province'],
    ];

    register_taxonomy('province', 'resume', $args);
}
add_action('init', 'register_province_taxonomy');

// ثبت taxonomy برای شهر
function register_city_taxonomy() {
    $labels = [
        'name'              => 'شهرها',
        'singular_name'     => 'شهر',
        'search_items'      => 'جستجوی شهرها',
        'all_items'         => 'تمام شهرها',
        'edit_item'         => 'ویرایش شهر',
        'update_item'       => 'به‌روزرسانی شهر',
        'add_new_item'      => 'افزودن شهر جدید',
        'new_item_name'     => 'نام شهر جدید',
        'menu_name'         => 'شهرها',
    ];

    $args = [
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => ['slug' => 'city'],
    ];

    register_taxonomy('city', 'resume', $args);
}
add_action('init', 'register_city_taxonomy');
