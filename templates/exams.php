<?php
if (!defined('ABSPATH')) {
    exit;
}

$user = wp_get_current_user();
$program_code = isset($_GET['p']) ? sanitize_text_field($_GET['p']) : 'General';

/**
 * Custom Table Implementation
 */
$assigned_exam_ids = get_user_meta($user->ID, 'assigned_exams', true);
if (!is_array($assigned_exam_ids)) {
    $assigned_exam_ids = array();
}

$assigned_exams = array();
if (!empty($assigned_exam_ids)) {
    global $wpdb;
    $table = $wpdb->prefix . 'board_exams';
    $ids = implode(',', array_map('intval', $assigned_exam_ids));
    $db_exams = $wpdb->get_results("SELECT * FROM $table WHERE id IN ($ids)");

    if (!empty($db_exams)) {
        foreach ($db_exams as $e) {
            $assigned_exams[] = array(
                'id'    => $e->id,
                'title' => $e->title,
                'code'  => $e->code ?: 'N/A',
                'due'   => $e->due_date ?: 'N/A'
            );
        }
    }
}

?>


<div class="board-container">
    <div style="text-align: center; margin-bottom: 40px;">
        <h2><?php printf(__('Exams for %s', 'board'), $program_code); ?></h2>
        <p><?php _e('Manage and take your assigned professional examinations.', 'board'); ?></p>
    </div>

    <div style="max-width: 800px; margin: 0 auto;">
        <div class="board-cp-header" style="margin-bottom: 30px; border: 1px solid var(--board-black);">
            <div class="board-cp-logo" style="display: flex; align-items: center; gap: 10px;">
                <span class="dashicons dashicons-clipboard"></span>
                <strong><?php _e('Assigned Exams', 'board'); ?></strong>
            </div>
            <div class="board-cp-user"><?php echo $user->display_name; ?></div>
        </div>

        <?php if (!empty($assigned_exams)) : ?>
            <?php foreach ($assigned_exams as $exam) : ?>
                <div class="board-program-card" style="margin-bottom: 20px; border-left: 8px solid var(--board-black);">
                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
                        <div>
                            <h3 style="margin: 0;"><?php echo esc_html($exam['title']); ?></h3>
                            <p style="margin: 8px 0; font-size: 13px;">
                                <strong style="text-transform: uppercase; font-size: 11px;"><?php _e('Exam Code:', 'board'); ?></strong> <code><?php echo esc_html($exam['code']); ?></code>
                                <span style="margin: 0 10px; color: #ccc;">|</span>
                                <strong style="text-transform: uppercase; font-size: 11px;"><?php _e('Due Date:', 'board'); ?></strong> <?php echo esc_html($exam['due']); ?>
                            </p>
                        </div>
                        <button class="board-btn-black start-exam" data-id="<?php echo $exam['id']; ?>" style="width: auto; padding: 12px 40px;"><?php _e('Start Exam', 'board'); ?></button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="board-program-card" style="text-align: center;">
                <p><?php _e('No exams have been assigned to you at this time.', 'board'); ?></p>
            </div>
        <?php endif; ?>

        <div style="margin-top: 20px; text-align: center;">
            <a href="<?php echo home_url('/programs'); ?>" style="color: var(--board-black); text-decoration: underline;"><?php _e('Back to Programs', 'board'); ?></a>
        </div>
    </div>
</div>
