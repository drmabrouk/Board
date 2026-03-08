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
            <li><a href="?cp_tab=users"><?php _e('Users Management', 'board'); ?></a></li>
            <li><a href="?cp_tab=programs"><?php _e('Programs', 'board'); ?></a></li>
            <li><a href="?cp_tab=exams"><?php _e('Exams', 'board'); ?></a></li>
            <li><a href="?cp_tab=requests"><?php _e('Membership Requests', 'board'); ?></a></li>
            <li><a href="?cp_tab=certificates"><?php _e('Certificates', 'board'); ?></a></li>
            <li><a href="?cp_tab=verification"><?php _e('Verification', 'board'); ?></a></li>
            <li><a href="?cp_tab=reports"><?php _e('Reports', 'board'); ?></a></li>
            <li><a href="?cp_tab=logs"><?php _e('Activity Logs', 'board'); ?></a></li>
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
            <h2><?php _e('Dashboard Overview', 'board'); ?></h2>
            <div class="board-cp-cards" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
                <div class="board-program-card"><h3><?php _e('Users', 'board'); ?></h3><p style="font-size: 24px; font-weight: bold;"><?php $uc = count_users(); echo $uc['total_users']; ?></p></div>
                <div class="board-program-card"><h3><?php _e('Programs', 'board'); ?></h3><p style="font-size: 24px; font-weight: bold;"><?php echo wp_count_posts('board_program')->publish; ?></p></div>
                <div class="board-program-card"><h3><?php _e('Pending Requests', 'board'); ?></h3><p style="font-size: 24px; font-weight: bold;"><?php echo $total_pending; ?></p></div>
                <div class="board-program-card"><h3><?php _e('Certificates', 'board'); ?></h3><p style="font-size: 24px; font-weight: bold;"><?php echo wp_count_posts('board_certificate')->publish; ?></p></div>
            </div>
            <div style="margin-top: 40px; background: var(--board-grey); height: 200px; border: 1px solid var(--board-black); display: flex; align-items: center; justify-content: center;">
                <p><?php _e('[ Daily Activity Chart Placeholder ]', 'board'); ?></p>
            </div>
        <?php endif; ?>

        <?php if ($tab == 'users') : ?>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3><?php _e('Users Management', 'board'); ?></h3>
                <div>
                    <a href="<?php echo admin_url('admin-post.php?action=board_export_users'); ?>" class="board-btn-black" style="width: auto; text-decoration: none; padding: 5px 15px; font-size: 12px; margin-right: 10px;"><?php _e('Export CSV', 'board'); ?></a>
                </div>
            </div>
            <table class="board-table">
                <thead>
                    <tr>
                        <th><?php _e('Name', 'board'); ?></th>
                        <th><?php _e('Email', 'board'); ?></th>
                        <th><?php _e('Role', 'board'); ?></th>
                        <th><?php _e('Action', 'board'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $users_list = get_users();
                    foreach ($users_list as $u) : ?>
                        <tr>
                            <td><?php echo esc_html($u->display_name); ?></td>
                            <td><?php echo esc_html($u->user_email); ?></td>
                            <td><?php echo implode(', ', $u->roles); ?></td>
                            <td>
                                <button class="board-btn-black delete-user" data-id="<?php echo $u->ID; ?>" style="width: auto; padding: 5px 10px; font-size: 11px; background: red;"><?php _e('Delete', 'board'); ?></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
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
            <table class="board-table">
                <thead><tr><th><?php _e('Title', 'board'); ?></th><th><?php _e('Code', 'board'); ?></th><th><?php _e('Action', 'board'); ?></th></tr></thead>
                <tbody>
                    <?php
                    $progs = get_posts(array('post_type' => 'board_program'));
                    foreach ($progs as $p) : ?>
                        <tr>
                            <td><?php echo $p->post_title; ?></td>
                            <td><?php echo get_post_meta($p->ID, 'program_code', true); ?></td>
                            <td><a href="#" style="color: red;"><?php _e('Delete', 'board'); ?></a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <?php if ($tab == 'exams') : ?>
            <h3><?php _e('Manage Exams', 'board'); ?></h3>
            <form id="board-assign-exam-form" style="margin-bottom: 30px;">
                <div class="board-form-field">
                    <select name="user_id" required>
                        <option value=""><?php _e('Select User', 'board'); ?></option>
                        <?php foreach($users_list as $u) echo "<option value='{$u->ID}'>{$u->display_name}</option>"; ?>
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

        <?php if ($tab == 'requests') : ?>
            <h3><?php _e('Pending Membership Requests', 'board'); ?></h3>
            <table class="board-table">
                <thead><tr><th><?php _e('Date', 'board'); ?></th><th><?php _e('Applicant', 'board'); ?></th><th><?php _e('Action', 'board'); ?></th></tr></thead>
                <tbody>
                    <?php if (!empty($pending_requests)) : ?>
                        <?php foreach ($pending_requests as $request) : ?>
                            <tr>
                                <td><?php echo get_the_date('', $request->ID); ?></td>
                                <td><?php echo get_post_meta($request->ID, 'full_name', true); ?></td>
                                <td><button class="board-btn-black approve-request" data-id="<?php echo $request->ID; ?>" style="width: auto; padding: 5px 15px; font-size: 12px;"><?php _e('Approve', 'board'); ?></button></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="3" style="text-align: center;"><?php _e('No pending requests.', 'board'); ?></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <?php if ($tab == 'certificates') : ?>
            <h3><?php _e('Generate Certificate', 'board'); ?></h3>
            <form id="board-generate-cert-form" style="margin-bottom: 40px;">
                <div class="board-form-field">
                    <select name="user_id" required>
                        <option value=""><?php _e('Select User', 'board'); ?></option>
                        <?php foreach($users_list as $u) echo "<option value='{$u->ID}'>{$u->display_name}</option>"; ?>
                    </select>
                </div>
                <div class="board-form-field">
                    <select name="cert_type" required>
                        <option value="Course"><?php _e('Course', 'board'); ?></option>
                        <option value="Diploma"><?php _e('Diploma', 'board'); ?></option>
                        <option value="Board Membership"><?php _e('Board Membership', 'board'); ?></option>
                        <option value="Exam Certificate"><?php _e('Exam Certificate', 'board'); ?></option>
                    </select>
                </div>
                <button type="submit" class="board-btn-black" style="width: auto;"><?php _e('Generate & Link', 'board'); ?></button>
            </form>

            <h3><?php _e('Active Certificates', 'board'); ?></h3>
            <table class="board-table">
                <thead><tr><th><?php _e('User', 'board'); ?></th><th><?php _e('Type', 'board'); ?></th><th><?php _e('Serial Number', 'board'); ?></th></tr></thead>
                <tbody>
                    <?php
                    $certs = get_posts(array('post_type' => 'board_certificate', 'posts_per_page' => -1));
                    if (!empty($certs)) :
                        foreach ($certs as $c) : ?>
                            <tr>
                                <td><?php echo get_the_title($c->ID); ?></td>
                                <td><?php echo get_post_meta($c->ID, 'cert_type', true); ?></td>
                                <td><code><?php echo get_post_meta($c->ID, 'serial_number', true); ?></code></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="3" style="text-align: center;"><?php _e('No certificates issued yet.', 'board'); ?></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <?php if ($tab == 'verification') : ?>
            <h3><?php _e('Verification System', 'board'); ?></h3>
            <p><?php _e('Manage codes and search permissions here.', 'board'); ?></p>
        <?php endif; ?>

        <?php if ($tab == 'reports') : ?>
            <h3><?php _e('System Reports', 'board'); ?></h3>
            <button class="board-btn-black" style="width: auto;"><?php _e('Export All Data (CSV)', 'board'); ?></button>
        <?php endif; ?>

        <?php if ($tab == 'logs') : ?>
            <h3><?php _e('Activity Logs', 'board'); ?></h3>
            <table class="board-table">
                <thead><tr><th><?php _e('Date', 'board'); ?></th><th><?php _e('Action', 'board'); ?></th><th><?php _e('Details', 'board'); ?></th></tr></thead>
                <tbody>
                    <?php
                    $logs = get_posts(array('post_type' => 'board_log', 'posts_per_page' => 20));
                    foreach ($logs as $log) : ?>
                        <tr><td><?php echo get_the_date('Y-m-d H:i', $log->ID); ?></td><td><?php echo $log->post_title; ?></td><td><?php echo $log->post_content; ?></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <?php if ($tab == 'settings') : ?>
            <h3><?php _e('Plugin Settings', 'board'); ?></h3>
            <div class="board-form-field">
                <label><?php _e('Visual Identity Primary Color', 'board'); ?></label>
                <input type="text" value="#000000" disabled>
            </div>
            <button class="board-btn-black" style="width: auto;"><?php _e('Save Settings', 'board'); ?></button>
        <?php endif; ?>

    </main>
</div>

<script>
jQuery(document).ready(function($) {
    $('.approve-request').on('click', function() {
        var btn = $(this);
        var requestId = btn.data('id');
        if (!confirm('<?php _e('Are you sure?', 'board'); ?>')) return;
        btn.prop('disabled', true).text('...');
        $.post(board_ajax.ajax_url, { action: 'board_approve_request', nonce: board_ajax.nonce, request_id: requestId }, function(response) {
            alert(response.data.message);
            location.reload();
        });
    });

    $('#board-save-program-form').on('submit', function(e) {
        e.preventDefault();
        $.post(board_ajax.ajax_url, $(this).serialize() + '&action=board_save_program&nonce=' + board_ajax.nonce, function(response) {
            alert(response.data.message);
            location.reload();
        });
    });

    $('#board-assign-exam-form').on('submit', function(e) {
        e.preventDefault();
        $.post(board_ajax.ajax_url, $(this).serialize() + '&action=board_assign_exam&nonce=' + board_ajax.nonce, function(response) {
            alert(response.data.message);
        });
    });

    $('#board-generate-cert-form').on('submit', function(e) {
        e.preventDefault();
        $.post(board_ajax.ajax_url, $(this).serialize() + '&action=board_generate_certificate&nonce=' + board_ajax.nonce, function(response) {
            alert(response.data.message);
            location.reload();
        });
    });

    $('.delete-user').on('click', function() {
        var id = $(this).data('id');
        if (!confirm('<?php _e('Delete this user? This action cannot be undone.', 'board'); ?>')) return;
        $.post(board_ajax.ajax_url, { action: 'board_delete_user', nonce: board_ajax.nonce, user_id: id }, function(response) {
            alert(response.data.message);
            location.reload();
        });
    });
});
</script>
