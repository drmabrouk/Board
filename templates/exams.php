<?php
if (!defined('ABSPATH')) {
    exit;
}

$user = wp_get_current_user();
$program_code = isset($_GET['p']) ? sanitize_text_field($_GET['p']) : 'General';

/**
 * Real implementation: Query board_exam CPT and check for user assignments
 */
$assigned_exam_ids = get_user_meta($user->ID, 'assigned_exams', true);
if (!is_array($assigned_exam_ids)) {
    $assigned_exam_ids = array();
}

$assigned_exams = array();
if (!empty($assigned_exam_ids)) {
    $exams_query = new WP_Query(array(
        'post_type' => 'board_exam',
        'post__in' => $assigned_exam_ids,
        'posts_per_page' => -1
    ));

    if ($exams_query->have_posts()) {
        while ($exams_query->have_posts()) {
            $exams_query->the_post();
            $assigned_exams[] = array(
                'id'    => get_the_ID(),
                'title' => get_the_title(),
                'code'  => get_post_meta(get_the_ID(), 'exam_code', true) ?: 'N/A',
                'due'   => get_post_meta(get_the_ID(), 'exam_due', true) ?: 'N/A'
            );
        }
        wp_reset_postdata();
    }
}

?>

<script>
jQuery(document).ready(function($) {
    $('.start-exam').on('click', function() {
        var btn = $(this);
        var examId = btn.data('id');
        if (confirm('<?php _e('Do you want to submit this exam with a random score for demo?', 'board'); ?>')) {
            var score = Math.floor(Math.random() * 40) + 60; // 60-100
            btn.prop('disabled', true).text('Submitting...');
            $.post(board_ajax.ajax_url, {
                action: 'board_submit_exam',
                nonce: board_ajax.nonce,
                exam_id: examId,
                score: score
            }, function(response) {
                alert(response.data.message);
                if (response.success) window.location.href = '<?php echo home_url('/mb'); ?>';
            });
        }
    });
});
</script>

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
                            <p style="margin: 5px 0;"><strong><?php _e('Code:', 'board'); ?></strong> <?php echo esc_html($exam['code']); ?> | <strong><?php _e('Due:', 'board'); ?></strong> <?php echo esc_html($exam['due']); ?></p>
                        </div>
                        <button class="board-btn-black start-exam" data-id="<?php echo $exam['id']; ?>" style="width: auto; padding: 10px 30px;"><?php _e('Start Exam', 'board'); ?></button>
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
