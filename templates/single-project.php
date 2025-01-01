<?php
// جلوگیری از دسترسی مستقیم
if (!defined('ABSPATH')) {
    exit;
}

get_header();

// شروع حلقه
if (have_posts()) :
    while (have_posts()) : the_post();

        $project_id = get_the_ID();
        $project_budget = get_post_meta($project_id, 'project_budget', true);
        $project_attachments = get_post_meta($project_id, 'project_attachments', true);
        $skills = wp_get_object_terms($project_id, 'skills', ['fields' => 'names']);
        ?>

        <div class="project-container">
            <div class="project-header">
                <h1><?php the_title(); ?></h1>
            </div>

            <div class="project-content">
                <h2>توضیحات پروژه</h2>
                <p><?php the_content(); ?></p>

                <?php if ($project_budget) : ?>
                    <p><strong>هزینه پیشنهادی:</strong> <?php echo esc_html(number_format($project_budget, 0)) . ' تومان'; ?></p>
                <?php endif; ?>

                <?php if (!empty($skills)) : ?>
                    <h2>مهارت‌های مورد نیاز</h2>
                    <ul>
                        <?php foreach ($skills as $skill) : ?>
                            <li><?php echo esc_html($skill); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <?php if (!empty($project_attachments)) : ?>
                    <h2>فایل‌های ضمیمه</h2>
                    <ul>
                        <?php foreach ($project_attachments as $attachment) : ?>
                            <li><a href="<?php echo esc_url($attachment); ?>" target="_blank">دانلود فایل</a></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    <?php
    endwhile;
else :
    ?>
    <div class="project-container">
        <p>پروژه‌ای یافت نشد.</p>
    </div>
<?php
endif;

get_footer();
