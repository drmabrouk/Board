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
    <div style="text-align: center; margin-bottom: 60px;">
        <h1 style="font-size: 42px; margin-bottom: 15px;"><?php printf(__('Assessments for %s', 'board'), $program_code); ?></h1>
        <p style="font-size: 18px; color: #666; max-width: 700px; margin: 0 auto;"><?php _e('Review and complete your assigned professional examinations to achieve official certification.', 'board'); ?></p>
    </div>

    <div style="max-width: 1000px; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; padding: 20px 30px; background: #000; color: #fff; border-radius: 12px;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <span class="dashicons dashicons-clipboard" style="font-size: 24px; width: 24px; height: 24px;"></span>
                <strong style="text-transform: uppercase; letter-spacing: 1px; font-size: 14px;"><?php _e('Active Assignments', 'board'); ?></strong>
            </div>
            <div style="font-size: 14px; font-weight: 700; opacity: 0.8;"><?php echo $user->display_name; ?></div>
        </div>

        <?php if (!empty($assigned_exams)) : ?>
            <div style="display: grid; grid-template-columns: 1fr; gap: 20px; margin-bottom: 50px;">
            <?php foreach ($assigned_exams as $exam) : ?>
                <div class="board-program-card" style="border-left: 10px solid #000;">
                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 30px;">
                        <div style="flex: 1; min-width: 300px;">
                            <h3 style="margin: 0; font-size: 22px;"><?php echo esc_html($exam['title']); ?></h3>
                            <div style="margin-top: 15px; display: flex; gap: 25px; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; color: #666;">
                                <span><strong><?php _e('ID:', 'board'); ?></strong> <code><?php echo esc_html($exam['code']); ?></code></span>
                                <span><strong><?php _e('Deadline:', 'board'); ?></strong> <?php echo esc_html($exam['due']); ?></span>
                                <span><strong><?php _e('Status:', 'board'); ?></strong> <span style="color: #000; font-weight: 800;"><?php _e('Awaiting Completion', 'board'); ?></span></span>
                            </div>
                        </div>
                        <a href="<?php echo home_url('/board-session?exam_id=' . $exam['id']); ?>" class="board-btn-black" style="padding: 18px 50px; font-size: 14px; text-decoration: none; text-align: center;"><?php _e('Launch Assessment', 'board'); ?></a>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
        <?php else : ?>
            <div class="board-program-card" style="text-align: center; margin-bottom: 50px;">
                <p><?php _e('No exams have been assigned to you at this time.', 'board'); ?></p>
            </div>
        <?php endif; ?>

        <div style="background: #f9f9f9; padding: 40px; border-radius: 12px; border: 1px solid #000;">
            <h3 style="margin-top: 0;"><?php _e('Apply for New Examination', 'board'); ?></h3>
            <p style="font-size: 14px; color: #666; margin-bottom: 25px;"><?php _e('If you wish to participate in a specific professional assessment, please select the exam below to submit a participation request.', 'board'); ?></p>
            <form id="board-exam-request-form" style="display: flex; gap: 15px; align-items: flex-end;">
                <div class="board-form-field" style="flex-grow: 1; margin-bottom: 0;">
                    <label><?php _e('Available Assessments', 'board'); ?></label>
                    <select name="exam_id" required>
                        <option value=""><?php _e('Select an exam...', 'board'); ?></option>
                        <?php
                        $all_exams = \GSHB\Board\Database\Manager::get_exams();
                        foreach ($all_exams as $ae) {
                            if (!in_array($ae->id, $assigned_exam_ids)) {
                                echo "<option value='{$ae->id}'>{$ae->title} ({$ae->code})</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" class="board-btn-black" style="width: auto; padding: 15px 40px;"><?php _e('Submit Request', 'board'); ?></button>
            </form>
        </div>

        <div style="margin-top: 20px; text-align: center;">
            <a href="<?php echo home_url('/programs'); ?>" style="color: var(--board-black); text-decoration: underline;"><?php _e('Back to Programs', 'board'); ?></a>
        </div>
    </div>
</div>
