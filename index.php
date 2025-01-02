<?php
/**
 * Plugin Name: Simple Freelancer Plugin
 * Description: A simple plugin for freelancers and employers.
 * Version: 1.0
 * Author: حسین حاجی پور
 */

// جلوگیری از دسترسی مستقیم
if (!defined('ABSPATH')) {
    exit;
}


function simple_freelancer_enqueue_styles() {
    wp_enqueue_style('simple-freelancer-styles', plugin_dir_url(__FILE__) . 'asset/css/freelancer.css?v=1.2');
}
add_action('wp_enqueue_scripts', 'simple_freelancer_enqueue_styles');

include_once plugin_dir_path(__FILE__) . 'admin/post_types.php';
include_once plugin_dir_path(__FILE__) . 'admin/init_provinces_and_cities.php';
// تعریف فایل‌های شورت‌کد
include_once plugin_dir_path(__FILE__) . 'shortcodes/register_project.php';
include_once plugin_dir_path(__FILE__) . 'shortcodes/search_projects.php';
include_once plugin_dir_path(__FILE__) . 'shortcodes/register_resume.php';
include_once plugin_dir_path(__FILE__) . 'shortcodes/search_resumes.php';
include_once plugin_dir_path(__FILE__) . 'shortcodes/my_projects.php';




function load_custom_resume_template($template) {
    if (is_singular('resume')) {
        $custom_template = plugin_dir_path(__FILE__) . 'templates/single-resume.php';
        if (file_exists($custom_template)) {
            return $custom_template;
        }
    }
    return $template;
}
add_filter('single_template', 'load_custom_resume_template');
function load_custom_project_template($template) {
    if (is_singular('project')) {
        $custom_template = plugin_dir_path(__FILE__) . 'templates/single-project.php';
        if (file_exists($custom_template)) {
            return $custom_template;
        }
    }
    return $template;
}
add_filter('single_template', 'load_custom_project_template');


