<?php
if (!defined('ABSPATH')) {
    exit;
}

$user = wp_get_current_user();
$program_code = isset($_GET['p']) ? sanitize_text_field($_GET['p']) : 'General';

/**
 * For a real implementation, you would check if the current user
 * has been assigned an exam for this program code.
 */
$assigned_exams = array(
    array(
        'title' => 'Initial Assessment - Level 1',
        'code' => 'EX-101',
        'due' => date('Y-m-d', strtotime('+7 days'))
    ),
);

?>

<div class="board-container">
    <div style="text-align: center; margin-bottom: 40px;">
        <h2><?php printf(__('Exams for %s', 'board'), $program_code); ?></h2>
        <p><?php _e('Manage and take your assigned professional examinations.', 'board'); ?></p>
    </div>

    <div style="max-width: 800px; margin: 0 auto;">
        <div class="board-cp-header" style="margin-bottom: 20px;">
            <div class="board-cp-logo"><?php _e('Assigned Exams', 'board'); ?></div>
            <div class="board-cp-user"><?php echo $user->display_name; ?></div>
        </div>

        <?php if (!empty($assigned_exams)) : ?>
            <?php foreach ($assigned_exams as $exam) : ?>
                <div class="board-program-card" style="margin-bottom: 20px; border-left: 5px solid var(--board-black);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h3 style="margin: 0;"><?php echo esc_html($exam['title']); ?></h3>
                            <p style="margin: 5px 0;"><strong>Code:</strong> <?php echo esc_html($exam['code']); ?> | <strong>Due:</strong> <?php echo esc_html($exam['due']); ?></p>
                        </div>
                        <a href="#" class="board-btn-black" style="width: auto; padding: 10px 30px; text-decoration: none;"><?php _e('Start Exam', 'board'); ?></a>
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
