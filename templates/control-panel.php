<?php
if (!defined('ABSPATH')) {
    exit;
}

$user = wp_get_current_user();
?>

<div class="board-cp-header">
    <div class="board-cp-logo">
        <strong>GSHB</strong> <?php _e('Control Panel', 'board'); ?>
    </div>
    <div class="board-cp-user">
        <?php printf(__('Welcome, %s', 'board'), $user->display_name); ?> |
        <a href="<?php echo wp_logout_url(home_url('/registration')); ?>" style="color: white;"><?php _e('Logout', 'board'); ?></a>
    </div>
</div>

<div class="board-cp-layout">
    <aside class="board-cp-sidebar">
        <ul>
            <li><a href="?cp_tab=dashboard"><?php _e('Dashboard', 'board'); ?></a></li>

            <?php if (current_user_can('manage_options') || Board_Roles::is_board_admin()) : ?>
                <li><a href="?cp_tab=users"><?php _e('Manage Users', 'board'); ?></a></li>
            <?php endif; ?>

            <?php if (current_user_can('manage_options') || Board_Roles::is_board_admin() || Board_Roles::is_programs_manager()) : ?>
                <li><a href="?cp_tab=programs"><?php _e('Programs & Exams', 'board'); ?></a></li>
            <?php endif; ?>

            <?php if (current_user_can('manage_options') || Board_Roles::is_board_admin() || Board_Roles::is_certs_manager()) : ?>
                <li><a href="?cp_tab=requests"><?php _e('Certifications & Memberships', 'board'); ?></a></li>
            <?php endif; ?>

            <?php if (current_user_can('manage_options') || Board_Roles::is_board_admin() || Board_Roles::is_academic_supervisor()) : ?>
                <li><a href="?cp_tab=academic"><?php _e('Academic Supervision', 'board'); ?></a></li>
            <?php endif; ?>

            <li><a href="?cp_tab=verification"><?php _e('Verification System', 'board'); ?></a></li>
            <li><a href="?cp_tab=settings"><?php _e('Settings', 'board'); ?></a></li>
        </ul>
    </aside>

    <main class="board-cp-main" style="padding: 30px;">
        <?php
        $tab = isset($_GET['cp_tab']) ? $_GET['cp_tab'] : 'dashboard';
        $pending_requests = Board_Admin::get_pending_requests();
        $total_pending = count($pending_requests);
        ?>

        <?php if ($tab == 'dashboard') : ?>
            <h2><?php _e('Overview', 'board'); ?></h2>
            <div class="board-cp-cards" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                <div class="board-program-card">
                    <h3><?php _e('Pending Requests', 'board'); ?></h3>
                    <p style="font-size: 24px; font-weight: bold;"><?php echo $total_pending; ?></p>
                </div>
                <div class="board-program-card">
                    <h3><?php _e('Active Members', 'board'); ?></h3>
                    <p style="font-size: 24px; font-weight: bold;">-</p>
                </div>
                <div class="board-program-card">
                    <h3><?php _e('Upcoming Exams', 'board'); ?></h3>
                    <p style="font-size: 24px; font-weight: bold;">-</p>
                </div>
            </div>

            <div style="margin-top: 40px;">
                <h3><?php _e('Quick Actions', 'board'); ?></h3>
                <ul>
                    <li><a href="?cp_tab=requests"><?php _e('Review Pending Applications', 'board'); ?></a></li>
                    <li><a href="?cp_tab=programs"><?php _e('Create New Program', 'board'); ?></a></li>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($tab == 'requests') : ?>
            <h3><?php _e('Pending Membership Requests', 'board'); ?></h3>
            <table class="board-table">
                <thead>
                    <tr>
                        <th><?php _e('Date', 'board'); ?></th>
                        <th><?php _e('Applicant', 'board'); ?></th>
                        <th><?php _e('Country', 'board'); ?></th>
                        <th><?php _e('Action', 'board'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($pending_requests)) : ?>
                        <?php foreach ($pending_requests as $request) : ?>
                            <tr>
                                <td><?php echo get_the_date('', $request->ID); ?></td>
                                <td><?php echo get_post_meta($request->ID, 'full_name', true); ?></td>
                                <td><?php echo get_post_meta($request->ID, 'country', true); ?></td>
                                <td>
                                    <button class="board-btn-black approve-request" data-id="<?php echo $request->ID; ?>" style="width: auto; padding: 5px 15px; font-size: 12px;"><?php _e('Approve', 'board'); ?></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="4" style="text-align: center;"><?php _e('No pending requests.', 'board'); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <?php if ($tab == 'programs') : ?>
            <h3><?php _e('Manage Programs', 'board'); ?></h3>
            <form id="board-save-program-form" style="margin-bottom: 30px;">
                <div class="board-form-field"><input type="text" name="title" placeholder="Program Title" required></div>
                <div class="board-form-field"><input type="text" name="code" placeholder="Program Code" required></div>
                <div class="board-form-field"><textarea name="desc" placeholder="Program Description"></textarea></div>
                <button type="submit" class="board-btn-black" style="width: auto;"><?php _e('Save Program', 'board'); ?></button>
            </form>

            <hr>
            <h3><?php _e('Assign Exams', 'board'); ?></h3>
            <form id="board-assign-exam-form">
                <div class="board-form-field">
                    <select name="user_id" required>
                        <option value=""><?php _e('Select User', 'board'); ?></option>
                        <?php
                        $users = get_users();
                        foreach($users as $u) echo "<option value='{$u->ID}'>{$u->display_name}</option>";
                        ?>
                    </select>
                </div>
                <div class="board-form-field">
                    <select name="exam_id" required>
                        <option value=""><?php _e('Select Exam', 'board'); ?></option>
                        <?php
                        $exams = get_posts(array('post_type' => 'board_exam'));
                        foreach($exams as $e) echo "<option value='{$e->ID}'>{$e->post_title}</option>";
                        ?>
                    </select>
                </div>
                <button type="submit" class="board-btn-black" style="width: auto;"><?php _e('Assign Exam', 'board'); ?></button>
            </form>
        <?php endif; ?>
    </main>
</div>

<script>
jQuery(document).ready(function($) {
    $('.approve-request').on('click', function() {
        var btn = $(this);
        var requestId = btn.data('id');

        if (!confirm('<?php _e('Are you sure you want to approve this membership application?', 'board'); ?>')) {
            return;
        }

        btn.prop('disabled', true).text('Processing...');

        $.post(board_ajax.ajax_url, {
            action: 'board_approve_request',
            nonce: board_ajax.nonce,
            request_id: requestId
        }, function(response) {
            if (response.success) {
                alert(response.data.message);
                location.reload();
            } else {
                alert(response.data.message);
                btn.prop('disabled', false).text('Approve');
            }
        });
    });

    $('#board-save-program-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        $.post(board_ajax.ajax_url, form.serialize() + '&action=board_save_program&nonce=' + board_ajax.nonce, function(response) {
            alert(response.data.message);
            if(response.success) form[0].reset();
        });
    });

    $('#board-assign-exam-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        $.post(board_ajax.ajax_url, form.serialize() + '&action=board_assign_exam&nonce=' + board_ajax.nonce, function(response) {
            alert(response.data.message);
            if(response.success) form[0].reset();
        });
    });
});
</script>
