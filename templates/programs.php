<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Real implementation: Query board_program CPT
 */
$programs = array();
$programs_query = new WP_Query(array(
    'post_type' => 'board_program',
    'posts_per_page' => -1
));

if ($programs_query->have_posts()) {
    while ($programs_query->have_posts()) {
        $programs_query->the_post();
        $programs[] = array(
            'title' => get_the_title(),
            'code' => get_post_meta(get_the_ID(), 'program_code', true),
            'desc' => get_the_content()
        );
    }
    wp_reset_postdata();
}
?>

<div class="board-container">
    <div style="text-align: center; margin-bottom: 40px;">
        <h2><?php _e('Programs & Exams', 'board'); ?></h2>
        <p><?php _e('Explore available professional programs and certifications.', 'board'); ?></p>
    </div>

    <div class="board-programs-grid">
        <?php foreach ($programs as $program) : ?>
            <div class="board-program-card">
                <h3><?php echo esc_html($program['title']); ?></h3>
                <p><strong>Code:</strong> <?php echo esc_html($program['code']); ?></p>
                <p><?php echo esc_html($program['desc']); ?></p>
                <a href="<?php echo home_url('/qb?p=' . $program['code']); ?>" class="board-btn-black" style="display: block; text-decoration: none; margin-top: 15px; text-align: center;"><?php _e('View Exams', 'board'); ?></a>
            </div>
        <?php endforeach; ?>
    </div>
</div>
