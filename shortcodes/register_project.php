<?php
if (!defined('ABSPATH')) {
    exit;
}

function advanced_register_project_shortcode() {
    ob_start();

    // دریافت تمامی ترم‌های مربوط به taxonomy "skills"
    $skills_terms = get_terms([
        'taxonomy' => 'skills', // نام صحیح taxonomy
        'hide_empty' => false,
    ]);
    ?>
    <form method="post" action="" class="advanced-project-form" enctype="multipart/form-data">
        <label for="project_title">عنوان پروژه:</label>
        <input type="text" id="project_title" name="project_title" required>

        <label for="project_description">توضیحات پروژه:</label>
        <textarea id="project_description" name="project_description" required></textarea>

        <label for="required_skills">مهارت‌های مورد نیاز:</label>
        <div class="skills-checkbox">
            <?php
            if (!empty($skills_terms) && !is_wp_error($skills_terms)) {
                foreach ($skills_terms as $term) {
                    echo '<label><input type="checkbox" name="required_skills[]" value="' . esc_attr($term->term_id) . '"> ' . esc_html($term->name) . '</label>';
                }
            } else {
                echo '<p>مهارتی یافت نشد.</p>';
            }
            ?>
        </div>

        <label for="project_budget">هزینه پیشنهادی:</label>
        <input type="number" id="project_budget" name="project_budget" step="0.01" min="0" placeholder="مثال: 5000" required>

        <label for="project_attachments">فایل‌های ضمیمه:</label>
        <input type="file" id="project_attachments" name="project_attachments[]" multiple accept=".jpg,.jpeg,.png,.pdf,.docx">

        <button type="submit" name="submit_project">ثبت پروژه</button>
    </form>
    <?php
    if (isset($_POST['submit_project'])) {
        $title = sanitize_text_field($_POST['project_title']);
        $description = sanitize_textarea_field($_POST['project_description']);
        $budget = sanitize_text_field($_POST['project_budget']);
        $skills = isset($_POST['required_skills']) ? array_map('intval', $_POST['required_skills']) : [];

        // آپلود فایل‌های ضمیمه
        $attachments = [];
        if (!empty($_FILES['project_attachments']['name'][0])) {
            foreach ($_FILES['project_attachments']['name'] as $key => $value) {
                if ($_FILES['project_attachments']['error'][$key] === UPLOAD_ERR_OK) {
                    $file = wp_handle_upload([
                        'name' => $_FILES['project_attachments']['name'][$key],
                        'type' => $_FILES['project_attachments']['type'][$key],
                        'tmp_name' => $_FILES['project_attachments']['tmp_name'][$key],
                        'error' => $_FILES['project_attachments']['error'][$key],
                        'size' => $_FILES['project_attachments']['size'][$key],
                    ], ['test_form' => false]);

                    if (isset($file['file'])) {
                        $attachments[] = $file['url'];
                    }
                }
            }
        }

        $post_id = wp_insert_post([
            'post_title' => $title,
            'post_content' => $description,
            'post_type' => 'project',
            'post_status' => 'publish',
        ]);

        if ($post_id) {
            // ذخیره هزینه پیشنهادی
            update_post_meta($post_id, 'project_budget', $budget);

            // تخصیص مهارت‌ها به پروژه
            if (!empty($skills)) {
                wp_set_object_terms($post_id, $skills, 'skills'); // استفاده از taxonomy صحیح
            }

            // ذخیره فایل‌های ضمیمه
            if (!empty($attachments)) {
                update_post_meta($post_id, 'project_attachments', $attachments);
            }

            echo '<p>پروژه با موفقیت ثبت شد.</p>';
        }
    }
    return ob_get_clean();
}

add_shortcode('register_project', 'advanced_register_project_shortcode');
?>
