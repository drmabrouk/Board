<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Variables available from board.php: $program (object from custom table)
 */
$user = wp_get_current_user();
$is_logged_in = is_user_logged_in();

get_header();
?>

<div class="board-container" style="padding: 60px 20px;">
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 50px;">

        <!-- Left: Program Info -->
        <div>
            <div style="margin-bottom: 40px;">
                <span class="status-badge" style="margin-bottom: 15px;"><?php echo esc_html($program->code); ?></span>
                <h1 style="font-size: 42px; line-height: 1.1; margin-bottom: 20px;"><?php echo esc_html($program->title); ?></h1>
                <p style="font-size: 20px; color: #555;"><?php echo esc_html($program->type); ?> | <?php echo esc_html($program->category); ?></p>
            </div>

            <div class="board-program-card" style="margin-bottom: 40px; padding: 40px; border-radius: 15px;">
                <h3 style="border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px;"><?php _e('Program Overview', 'board'); ?></h3>
                <div style="font-size: 16px; line-height: 1.8; color: #333;">
                    <?php echo nl2br(esc_html($program->description)); ?>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 40px;">
                <div class="board-program-card" style="padding: 25px; border-left: 5px solid #000;">
                    <h4 style="font-size: 14px; text-transform: uppercase; color: #888;"><?php _e('Lead Instructor', 'board'); ?></h4>
                    <p style="font-size: 18px; font-weight: 700;"><?php echo esc_html($program->instructor ?: 'N/A'); ?></p>
                </div>
                <div class="board-program-card" style="padding: 25px; border-left: 5px solid #000;">
                    <h4 style="font-size: 14px; text-transform: uppercase; color: #888;"><?php _e('Academic Credits', 'board'); ?></h4>
                    <p style="font-size: 18px; font-weight: 700;"><?php echo intval($program->credits); ?> Units</p>
                </div>
            </div>

            <div class="board-program-card" style="padding: 40px; background: #fafafa; border: 1px dashed #ccc;">
                <h3><?php _e('Regulations & Conditions', 'board'); ?></h3>
                <ul style="padding-left: 20px; margin-top: 20px;">
                    <li><?php _e('Full attendance is required for all modules.', 'board'); ?></li>
                    <li><?php _e('Minimum grade of 75% in final assessment for certification.', 'board'); ?></li>
                    <li><?php _e('Valid medical or health professional registration required.', 'board'); ?></li>
                </ul>
            </div>
        </div>

        <!-- Right: Application Workflow -->
        <aside>
            <div class="board-program-card" style="position: sticky; top: 120px; padding: 40px; border-width: 2px; border-color: #000;">
                <h3 style="text-align: center; margin-bottom: 30px;"><?php _e('Application Center', 'board'); ?></h3>

                <?php if (!$is_logged_in) : ?>
                    <div style="text-align: center;">
                        <p style="margin-bottom: 20px;"><?php _e('Please log in or register to apply for this program.', 'board'); ?></p>
                        <a href="<?php echo home_url('/registration'); ?>" class="board-btn-black"><?php _e('Go to Login', 'board'); ?></a>
                    </div>
                <?php else : ?>
                    <form id="board-program-application-form">
                        <input type="hidden" name="program_id" value="<?php echo $program->id; ?>">

                        <div id="app-step-1" class="app-step">
                            <h4 style="margin-bottom: 15px; font-size: 16px;"><?php _e('Step 1: Confirm Details', 'board'); ?></h4>
                            <p style="font-size: 13px; color: #666; margin-bottom: 20px;"><?php _e('Please verify your profile information before proceeding.', 'board'); ?></p>
                            <div class="board-form-field">
                                <label><?php _e('Full Name', 'board'); ?></label>
                                <input type="text" value="<?php echo esc_attr($user->display_name); ?>" readonly>
                            </div>
                            <button type="button" class="board-btn-black app-next" data-next="2"><?php _e('Next: Requirements', 'board'); ?></button>
                        </div>

                        <div id="app-step-2" class="app-step" style="display: none;">
                            <h4 style="margin-bottom: 15px; font-size: 16px;"><?php _e('Step 2: Experience', 'board'); ?></h4>
                            <div class="board-form-field">
                                <label><?php _e('Years of Experience', 'board'); ?></label>
                                <input type="number" name="exp_years" required>
                            </div>
                            <div class="board-form-field">
                                <label><?php _e('Professional Statement', 'board'); ?></label>
                                <textarea name="statement" rows="4" required placeholder="Why do you wish to join?"></textarea>
                            </div>
                            <div style="display: flex; gap: 10px;">
                                <button type="button" class="board-btn-black board-btn-outline app-prev" data-prev="1" style="flex:1;">Back</button>
                                <button type="submit" class="board-btn-black" style="flex:2;"><?php _e('Submit Application', 'board'); ?></button>
                            </div>
                        </div>
                    </form>

                    <div id="app-success" style="display: none; text-align: center;">
                        <span class="dashicons dashicons-yes-alt" style="font-size: 50px; width: 50px; height: 50px; color: green; margin-bottom: 15px;"></span>
                        <h4><?php _e('Application Submitted', 'board'); ?></h4>
                        <p style="font-size: 14px; margin-top: 10px;"><?php _e('Your request is being processed. You can track status in your account.', 'board'); ?></p>
                        <a href="<?php echo home_url('/mb'); ?>" class="board-btn-black board-btn-outline" style="margin-top: 20px;"><?php _e('Go to Dashboard', 'board'); ?></a>
                    </div>
                <?php endif; ?>
            </div>
        </aside>

    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('.app-next').on('click', function() {
        var next = $(this).data('next');
        $('.app-step').hide();
        $('#app-step-' + next).fadeIn();
    });
    $('.app-prev').on('click', function() {
        var prev = $(this).data('prev');
        $('.app-step').hide();
        $('#app-step-' + prev).fadeIn();
    });

    $('#board-program-application-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var btn = form.find('button[type="submit"]');
        btn.prop('disabled', true).text('Processing...');

        $.post(board_ajax.ajax_url, {
            action: 'board_submit_program_application',
            nonce: board_ajax.nonce,
            program_id: form.find('input[name="program_id"]').val(),
            data: form.serialize()
        }, function(response) {
            if (response.success) {
                form.hide();
                $('#app-success').fadeIn();
                boardNotify(response.data.message);
            } else {
                boardNotify(response.data.message, 'error');
                btn.prop('disabled', false).text('Submit Application');
            }
        });
    });
});
</script>

<?php get_footer(); ?>
