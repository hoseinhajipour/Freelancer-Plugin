<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Start the Loop
if (have_posts()) :
    while (have_posts()) : the_post();

        // Fetch resume meta data
        $resume_id = get_the_ID();
        $full_name = get_post_meta($resume_id, 'full_name', true);
        $gender = get_post_meta($resume_id, 'gender', true);
        $birth_date = get_post_meta($resume_id, 'birth_date', true);
        $about_me = get_post_meta($resume_id, 'about_me', true);
        $work_experience = get_post_meta($resume_id, 'work_experience', true);
        $education_experience = get_post_meta($resume_id, 'education_experience', true);
        $skills = wp_get_object_terms($resume_id, 'skills', ['fields' => 'names']);
        $user_avatar = get_post_meta($resume_id, 'user_avatar', true);
        ?>

        <div class="resume-container">
            <div class="resume-header">
                <h1><?php echo esc_html($full_name ? $full_name : get_the_title()); ?></h1>
                <?php if ($user_avatar) : ?>
                    <img src="<?php echo esc_url(wp_get_attachment_url($user_avatar)); ?>" alt="<?php echo esc_attr($full_name); ?>" class="resume-avatar">
                <?php endif; ?>
            </div>

            <div class="resume-content">
                <p><strong>جنسیت:</strong> <?php echo esc_html($gender === 'male' ? 'مرد' : 'زن'); ?></p>
                <p><strong>تاریخ تولد:</strong> <?php echo esc_html($birth_date); ?></p>

                <?php if ($about_me) : ?>
                    <h2>درباره من</h2>
                    <p><?php echo wp_kses_post($about_me); ?></p>
                <?php endif; ?>

                <?php if ($work_experience) : ?>
                    <h2>سابقه کاری</h2>
                    <p><?php echo wp_kses_post($work_experience); ?></p>
                <?php endif; ?>

                <?php if ($education_experience) : ?>
                    <h2>سابقه تحصیلی</h2>
                    <p><?php echo wp_kses_post($education_experience); ?></p>
                <?php endif; ?>

                <?php if (!empty($skills)) : ?>
                    <h2>مهارت‌ها</h2>
                    <ul>
                        <?php foreach ($skills as $skill) : ?>
                            <li><?php echo esc_html($skill); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

    <?php
    endwhile;
else :
    ?>
    <div class="resume-container">
        <p>رزومه‌ای یافت نشد.</p>
    </div>
<?php
endif;

get_footer();
