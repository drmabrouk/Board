<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Custom Table Implementation
 */
$programs = array();
$db_programs = \GSHB\Board\Database\Manager::get_programs();

if (!empty($db_programs)) {
    foreach ($db_programs as $p) {
        $programs[] = array(
            'title' => $p->title,
            'code'  => $p->code ?: 'N/A',
            'type'  => $p->type ?: 'Course',
            'dur'   => $p->duration ?: 'N/A',
            'desc'  => $p->description
        );
    }
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
            <div class="board-program-card" data-tooltip="<?php echo esc_attr(wp_trim_words($program['desc'], 20)); ?>">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="margin: 0;"><?php echo esc_html($program['title']); ?></h3>
                    <span class="dashicons dashicons-arrow-down-alt2 board-expand-toggle"></span>
                </div>
                <div class="board-program-card-content">
                    <p style="font-size: 13px; margin: 15px 0 10px;">
                        <strong><?php _e('Type:', 'board'); ?></strong> <?php echo esc_html($program['type']); ?> |
                        <strong><?php _e('Duration:', 'board'); ?></strong> <?php echo esc_html($program['dur']); ?>
                    </p>
                    <p><strong><?php _e('Code:', 'board'); ?></strong> <code><?php echo esc_html($program['code']); ?></code></p>
                    <p style="margin-top: 10px; flex-grow: 1; font-size: 13px; color: var(--board-grey-dark);"><?php echo esc_html($program['desc']); ?></p>
                    <a href="<?php echo home_url('/qb?p=' . urlencode($program['code'])); ?>" class="board-btn-black" style="display: block; text-decoration: none; margin-top: 25px; text-align: center;"><?php _e('View Exams', 'board'); ?></a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
