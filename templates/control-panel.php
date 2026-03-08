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
                <div style="display: flex; gap: 10px;">
                    <form action="<?php echo admin_url('admin-post.php'); ?>" method="post" enctype="multipart/form-data" style="display: flex; gap: 5px; align-items: center;">
                        <input type="hidden" name="action" value="board_import_users">
                        <input type="file" name="import_file" style="font-size: 11px;" required>
                        <button type="submit" class="board-btn-black" style="width: auto; padding: 5px 10px; font-size: 11px;"><?php _e('Import CSV', 'board'); ?></button>
                    </form>
                    <a href="<?php echo admin_url('admin-post.php?action=board_export_users'); ?>" class="board-btn-black" style="width: auto; text-decoration: none; padding: 5px 15px; font-size: 12px;"><?php _e('Export CSV', 'board'); ?></a>
                    <button class="board-btn-black" id="open-add-user" style="width: auto; padding: 5px 15px; font-size: 12px;"><?php _e('Add New User', 'board'); ?></button>
                </div>
            </div>

            <!-- Add User Form (Hidden by default) -->
            <div id="add-user-section" style="display: none; background: #f9f9f9; padding: 20px; border: 1px solid var(--board-black); margin-bottom: 20px;">
                <h4><?php _e('Create New User', 'board'); ?></h4>
                <form id="board-add-user-form">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <div class="board-form-field"><input type="text" name="username" placeholder="Username" required></div>
                        <div class="board-form-field"><input type="email" name="email" placeholder="Email" required></div>
                        <div class="board-form-field"><input type="password" name="password" placeholder="Password" required></div>
                        <div class="board-form-field">
                            <select name="role" required>
                                <option value="board_member"><?php _e('Member', 'board'); ?></option>
                                <option value="certified_member"><?php _e('Certified Member', 'board'); ?></option>
                                <option value="academic_supervisor"><?php _e('Academic Supervisor', 'board'); ?></option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="board-btn-black" style="width: auto;"><?php _e('Create User', 'board'); ?></button>
                    <button type="button" id="close-add-user" class="board-btn-black" style="width: auto; background: grey;"><?php _e('Cancel', 'board'); ?></button>
                </form>
            </div>

            <div style="margin-bottom: 20px; display: flex; gap: 10px;">
                <input type="text" id="user-search" placeholder="<?php _e('Search users...', 'board'); ?>" style="flex-grow: 1; padding: 10px; border: 1px solid var(--board-black);">
                <select id="role-filter" style="padding: 10px; border: 1px solid var(--board-black);">
                    <option value=""><?php _e('All Roles', 'board'); ?></option>
                    <option value="board_member"><?php _e('Member', 'board'); ?></option>
                    <option value="certified_member"><?php _e('Certified', 'board'); ?></option>
                </select>
            </div>

            <table class="board-table" id="users-table">
                <thead>
                    <tr>
                        <th><?php _e('Name / ID', 'board'); ?></th>
                        <th><?php _e('Email', 'board'); ?></th>
                        <th><?php _e('Role', 'board'); ?></th>
                        <th><?php _e('Status', 'board'); ?></th>
                        <th><?php _e('Certs / Exams', 'board'); ?></th>
                        <th><?php _e('Action', 'board'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $users_list = get_users();
                    foreach ($users_list as $u) :
                        $id_code = get_user_meta($u->ID, 'verification_code', true) ?: 'N/A';
                        $status = get_user_meta($u->ID, 'membership_status', true) ?: 'active';
                        $certs_count = count(get_posts(array('post_type' => 'board_certificate', 'meta_key' => 'user_id', 'meta_value' => $u->ID)));
                        $exams_count = count(get_user_meta($u->ID, 'assigned_exams', true) ?: array());
                        ?>
                        <tr data-role="<?php echo implode(' ', $u->roles); ?>">
                            <td><strong><?php echo esc_html($u->display_name); ?></strong><br><small><?php echo $id_code; ?></small></td>
                            <td><?php echo esc_html($u->user_email); ?></td>
                            <td>
                                <select class="quick-role-change" data-id="<?php echo $u->ID; ?>" style="padding: 2px; font-size: 11px;">
                                    <option value="board_member" <?php selected(in_array('board_member', $u->roles)); ?>><?php _e('Member', 'board'); ?></option>
                                    <option value="certified_member" <?php selected(in_array('certified_member', $u->roles)); ?>><?php _e('Certified', 'board'); ?></option>
                                    <option value="academic_supervisor" <?php selected(in_array('academic_supervisor', $u->roles)); ?>><?php _e('Supervisor', 'board'); ?></option>
                                </select>
                            </td>
                            <td>
                                <select class="quick-status-change" data-id="<?php echo $u->ID; ?>" style="padding: 2px; font-size: 11px;">
                                    <option value="active" <?php selected($status, 'active'); ?>><?php _e('Active', 'board'); ?></option>
                                    <option value="inactive" <?php selected($status, 'inactive'); ?>><?php _e('Inactive', 'board'); ?></option>
                                    <option value="suspended" <?php selected($status, 'suspended'); ?>><?php _e('Suspended', 'board'); ?></option>
                                </select>
                            </td>
                            <td><?php echo $certs_count; ?> / <?php echo $exams_count; ?></td>
                            <td>
                                <div style="display: flex; gap: 5px;">
                                    <button class="board-btn-black delete-user" data-id="<?php echo $u->ID; ?>" style="width: auto; padding: 3px 8px; font-size: 10px; background: red;"><?php _e('Delete', 'board'); ?></button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <?php if ($tab == 'programs') : ?>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3><?php _e('Manage Programs', 'board'); ?></h3>
                <div style="display: flex; gap: 10px;">
                    <a href="<?php echo admin_url('admin-post.php?action=board_export_programs'); ?>" class="board-btn-black" style="width: auto; text-decoration: none; padding: 5px 15px; font-size: 12px;"><?php _e('Export Programs', 'board'); ?></a>
                    <button class="board-btn-black" id="open-add-program" style="width: auto; padding: 5px 15px; font-size: 12px;"><?php _e('Add New Program', 'board'); ?></button>
                </div>
            </div>

            <!-- Add Program Form -->
            <div id="add-program-section" style="display: none; background: #f9f9f9; padding: 20px; border: 1px solid var(--board-black); margin-bottom: 30px;">
                <h4><?php _e('Create Program', 'board'); ?></h4>
                <form id="board-save-program-form">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <div class="board-form-field"><input type="text" name="title" placeholder="Program Title" required></div>
                        <div class="board-form-field"><input type="text" name="code" placeholder="Program Code" required></div>
                        <div class="board-form-field">
                            <select name="type" required>
                                <option value="Course"><?php _e('Course', 'board'); ?></option>
                                <option value="Diploma"><?php _e('Diploma', 'board'); ?></option>
                                <option value="Board Membership"><?php _e('Board Membership', 'board'); ?></option>
                            </select>
                        </div>
                        <div class="board-form-field"><input type="text" name="duration" placeholder="Duration (e.g., 6 Months)"></div>
                    </div>
                    <div class="board-form-field"><textarea name="desc" placeholder="Program Description"></textarea></div>
                    <button type="submit" class="board-btn-black" style="width: auto;"><?php _e('Save Program', 'board'); ?></button>
                    <button type="button" id="close-add-program" class="board-btn-black" style="width: auto; background: grey;"><?php _e('Cancel', 'board'); ?></button>
                </form>
            </div>

            <div style="margin-bottom: 20px;">
                <input type="text" id="program-search" placeholder="<?php _e('Search programs...', 'board'); ?>" style="width: 100%; padding: 10px; border: 1px solid var(--board-black);">
            </div>

            <div class="board-programs-grid" id="admin-programs-grid" style="padding: 0;">
                <?php
                $progs = get_posts(array('post_type' => 'board_program', 'posts_per_page' => -1));
                if (!empty($progs)) :
                    foreach ($progs as $p) :
                        $code = get_post_meta($p->ID, 'program_code', true);
                        $type = get_post_meta($p->ID, 'program_type', true) ?: 'Course';
                        $duration = get_post_meta($p->ID, 'program_duration', true) ?: 'N/A';
                        $status = get_post_status($p->ID);
                        ?>
                        <div class="board-program-card" data-title="<?php echo strtolower($p->post_title); ?>">
                            <h4><?php echo $p->post_title; ?></h4>
                            <p style="font-size: 12px; margin-bottom: 10px;">
                                <strong><?php _e('Type:', 'board'); ?></strong> <?php echo $type; ?> |
                                <strong><?php _e('Code:', 'board'); ?></strong> <?php echo $code; ?>
                            </p>
                            <p style="font-size: 13px;"><?php echo wp_trim_words($p->post_content, 15); ?></p>
                            <div style="margin-top: 15px; display: flex; gap: 5px;">
                                <button class="board-btn-black" style="width: auto; padding: 5px 10px; font-size: 10px;"><?php _e('Edit', 'board'); ?></button>
                                <button class="board-btn-black delete-program" data-id="<?php echo $p->ID; ?>" style="width: auto; padding: 5px 10px; font-size: 10px; background: red;"><?php _e('Delete', 'board'); ?></button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p><?php _e('No programs found.', 'board'); ?></p>
                <?php endif; ?>
            </div>
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
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3><?php _e('Certificates Management', 'board'); ?></h3>
                <div style="display: flex; gap: 10px;">
                    <a href="<?php echo admin_url('admin-post.php?action=board_export_certificates'); ?>" class="board-btn-black" style="width: auto; text-decoration: none; padding: 5px 15px; font-size: 12px;"><?php _e('Export Certificates', 'board'); ?></a>
                    <button class="board-btn-black" id="open-generate-cert" style="width: auto; padding: 5px 15px; font-size: 12px;"><?php _e('New Certificate', 'board'); ?></button>
                </div>
            </div>

            <div id="generate-cert-section" style="display: none; background: #f9f9f9; padding: 20px; border: 1px solid var(--board-black); margin-bottom: 30px;">
                <h4><?php _e('Generate & Link', 'board'); ?></h4>
                <form id="board-generate-cert-form">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
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
                                <option value="Membership"><?php _e('Membership', 'board'); ?></option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="board-btn-black" style="width: auto;"><?php _e('Generate Code & Link', 'board'); ?></button>
                    <button type="button" id="close-generate-cert" class="board-btn-black" style="width: auto; background: grey;"><?php _e('Cancel', 'board'); ?></button>
                </form>
            </div>

            <div style="margin-bottom: 20px;">
                <input type="text" id="cert-search" placeholder="<?php _e('Search certificates...', 'board'); ?>" style="width: 100%; padding: 10px; border: 1px solid var(--board-black);">
            </div>

            <div class="board-programs-grid" id="admin-certs-grid" style="padding: 0;">
                <?php
                $certs = get_posts(array('post_type' => 'board_certificate', 'posts_per_page' => -1));
                if (!empty($certs)) :
                    foreach ($certs as $c) :
                        $serial = get_post_meta($c->ID, 'serial_number', true);
                        $type = get_post_meta($c->ID, 'cert_type', true);
                        $status = get_post_meta($c->ID, 'cert_status', true) ?: 'active';
                        $issue_date = get_post_meta($c->ID, 'issue_date', true);
                        ?>
                        <div class="board-program-card" data-title="<?php echo strtolower($c->post_title . ' ' . $serial); ?>">
                            <h4><?php echo esc_html($c->post_title); ?></h4>
                            <p style="font-size: 12px; margin-bottom: 10px;">
                                <strong><?php _e('Type:', 'board'); ?></strong> <?php echo $type; ?> |
                                <strong><?php _e('Status:', 'board'); ?></strong> <span style="text-transform: capitalize;"><?php echo $status; ?></span>
                            </p>
                            <p style="font-size: 13px;"><code><?php echo $serial; ?></code></p>
                            <p style="font-size: 11px; margin-top: 5px; color: grey;"><?php _e('Issued:', 'board'); ?> <?php echo $issue_date; ?></p>
                            <div style="margin-top: 15px; display: flex; gap: 5px;">
                                <button class="board-btn-black revoke-cert" data-id="<?php echo $c->ID; ?>" style="width: auto; padding: 5px 10px; font-size: 10px; background: orange;"><?php _e('Revoke', 'board'); ?></button>
                                <button class="board-btn-black delete-cert" data-id="<?php echo $c->ID; ?>" style="width: auto; padding: 5px 10px; font-size: 10px; background: red;"><?php _e('Delete', 'board'); ?></button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p><?php _e('No certificates issued yet.', 'board'); ?></p>
                <?php endif; ?>
            </div>
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
    $('#user-search').on('keyup', function() {
        var val = $(this).val().toLowerCase();
        $('#users-table tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(val) > -1);
        });
    });

    $('#role-filter').on('change', function() {
        var val = $(this).val();
        $('#users-table tbody tr').filter(function() {
            if (!val) { $(this).show(); return; }
            $(this).toggle($(this).data('role').indexOf(val) > -1);
        });
    });

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

    $('#open-add-program').on('click', function() { $('#add-program-section').slideDown(); });
    $('#close-add-program').on('click', function() { $('#add-program-section').slideUp(); });

    $('#program-search').on('keyup', function() {
        var val = $(this).val().toLowerCase();
        $('#admin-programs-grid .board-program-card').filter(function() {
            $(this).toggle($(this).data('title').indexOf(val) > -1);
        });
    });

    $('#board-save-program-form').on('submit', function(e) {
        e.preventDefault();
        $.post(board_ajax.ajax_url, $(this).serialize() + '&action=board_save_program&nonce=' + board_ajax.nonce, function(response) {
            alert(response.data.message);
            if(response.success) location.reload();
        });
    });

    $('.delete-program').on('click', function() {
        var id = $(this).data('id');
        if (!confirm('<?php _e('Delete program?', 'board'); ?>')) return;
        $.post(board_ajax.ajax_url, { action: 'board_delete_program', nonce: board_ajax.nonce, program_id: id }, function(response) {
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

    $('#open-generate-cert').on('click', function() { $('#generate-cert-section').slideDown(); });
    $('#close-generate-cert').on('click', function() { $('#generate-cert-section').slideUp(); });

    $('#cert-search').on('keyup', function() {
        var val = $(this).val().toLowerCase();
        $('#admin-certs-grid .board-program-card').filter(function() {
            $(this).toggle($(this).data('title').indexOf(val) > -1);
        });
    });

    $('#board-generate-cert-form').on('submit', function(e) {
        e.preventDefault();
        $.post(board_ajax.ajax_url, $(this).serialize() + '&action=board_generate_certificate&nonce=' + board_ajax.nonce, function(response) {
            alert(response.data.message);
            if(response.success) location.reload();
        });
    });

    $('.revoke-cert').on('click', function() {
        var id = $(this).data('id');
        if (!confirm('<?php _e('Revoke this certificate?', 'board'); ?>')) return;
        $.post(board_ajax.ajax_url, { action: 'board_revoke_certificate', nonce: board_ajax.nonce, cert_id: id }, function(response) {
            alert(response.data.message);
            location.reload();
        });
    });

    $('.delete-cert').on('click', function() {
        var id = $(this).data('id');
        if (!confirm('<?php _e('Delete record?', 'board'); ?>')) return;
        $.post(board_ajax.ajax_url, { action: 'board_delete_certificate', nonce: board_ajax.nonce, cert_id: id }, function(response) {
            alert(response.data.message);
            location.reload();
        });
    });

    $('.quick-role-change').on('change', function() {
        var id = $(this).data('id');
        var role = $(this).val();
        $.post(board_ajax.ajax_url, { action: 'board_update_user_role', nonce: board_ajax.nonce, user_id: id, role: role }, function(response) {
            alert(response.data.message);
        });
    });

    $('.quick-status-change').on('change', function() {
        var id = $(this).data('id');
        var status = $(this).val();
        $.post(board_ajax.ajax_url, { action: 'board_update_user_status', nonce: board_ajax.nonce, user_id: id, status: status }, function(response) {
            alert(response.data.message);
        });
    });

    $('#open-add-user').on('click', function() { $('#add-user-section').slideDown(); });
    $('#close-add-user').on('click', function() { $('#add-user-section').slideUp(); });

    $('#board-add-user-form').on('submit', function(e) {
        e.preventDefault();
        $.post(board_ajax.ajax_url, $(this).serialize() + '&action=board_add_user&nonce=' + board_ajax.nonce, function(response) {
            alert(response.data.message);
            if(response.success) location.reload();
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
