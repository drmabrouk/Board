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
            'code'  => get_post_meta(get_the_ID(), 'program_code', true) ?: 'N/A',
            'type'  => get_post_meta(get_the_ID(), 'program_type', true) ?: 'Course',
            'dur'   => get_post_meta(get_the_ID(), 'program_duration', true) ?: 'N/A',
            'desc'  => get_the_content()
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

    <div style="margin-bottom: 30px; display: flex; gap: 15px; align-items: flex-end; max-width: 800px; margin-left: auto; margin-right: auto;">
        <div style="flex-grow: 1;">
            <label style="display: block; font-size: 11px; font-weight: bold; margin-bottom: 5px; text-transform: uppercase;"><?php _e('Search Programs', 'board'); ?></label>
            <input type="text" id="program-search" placeholder="<?php _e('Search by title or code...', 'board'); ?>" style="width: 100%; padding: 12px; border: 1px solid var(--board-black);">
        </div>
        <div>
            <label style="display: block; font-size: 11px; font-weight: bold; margin-bottom: 5px; text-transform: uppercase;"><?php _e('Filter by Type', 'board'); ?></label>
            <select id="program-type-filter" style="padding: 12px; border: 1px solid var(--board-black); min-width: 150px;">
                <option value=""><?php _e('All Types', 'board'); ?></option>
                <option value="Course"><?php _e('Course', 'board'); ?></option>
                <option value="Diploma"><?php _e('Diploma', 'board'); ?></option>
                <option value="Board Membership"><?php _e('Board Membership', 'board'); ?></option>
            </select>
        </div>
    </div>

    <div class="board-programs-grid">
        <?php foreach ($programs as $program) : ?>
            <div class="board-program-card">
                <h3><?php echo esc_html($program['title']); ?></h3>
                <p style="font-size: 13px; margin-bottom: 10px;">
                    <strong><?php _e('Type:', 'board'); ?></strong> <?php echo esc_html($program['type']); ?> |
                    <strong><?php _e('Duration:', 'board'); ?></strong> <?php echo esc_html($program['dur']); ?>
                </p>
                <p><strong><?php _e('Code:', 'board'); ?></strong> <?php echo esc_html($program['code']); ?></p>
                <p style="margin-top: 10px; flex-grow: 1;"><?php echo esc_html($program['desc']); ?></p>
                <a href="<?php echo home_url('/qb?p=' . urlencode($program['code'])); ?>" class="board-btn-black" style="display: block; text-decoration: none; margin-top: 25px; text-align: center;"><?php _e('View Exams', 'board'); ?></a>
            </div>
        <?php endforeach; ?>
    </div>
</div>
