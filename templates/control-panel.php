<?php
if (!defined('ABSPATH')) {
    exit;
}

$user = wp_get_current_user();
?>

<div class="board-cp-header">
    <div class="board-cp-logo" style="display: flex; align-items: center; gap: 10px; flex: 0 0 auto;">
        <?php if ($logo_url = get_option('board_logo_url')) : ?>
            <img src="<?php echo esc_url($logo_url); ?>" style="max-height: 30px;">
        <?php endif; ?>
        <strong><?php echo esc_html(get_option('board_org_name', 'GSHB')); ?></strong> <?php _e('CP', 'board'); ?>
    </div>

    <div class="board-header-search">
        <span class="dashicons dashicons-search"></span>
        <input type="text" id="global-header-search" placeholder="<?php _e('Search sections...', 'board'); ?>" autocomplete="off">
        <div id="global-search-results" class="board-search-suggestions"></div>
    </div>

    <div class="board-cp-user" style="flex: 0 0 auto; display: flex; align-items: center; gap: 15px;">
        <div style="text-align: right;">
            <div style="font-weight: 700;"><?php printf(__('Welcome, %s', 'board'), $user->display_name); ?></div>
            <div style="font-size: 10px; text-transform: uppercase; opacity: 0.7; letter-spacing: 1px;"><?php echo date_i18n('l, j F Y'); ?></div>
        </div>
        <div style="width: 1px; height: 25px; background: rgba(255,255,255,0.2);"></div>
        <a href="<?php echo wp_logout_url(home_url('/registration')); ?>" style="color: white; font-size: 12px; font-weight: 700; text-transform: uppercase;"><?php _e('Logout', 'board'); ?></a>
    </div>
</div>

<div class="board-cp-layout">
    <aside class="board-cp-sidebar">
        <ul>
            <li class="<?php echo (!isset($_GET['cp_tab']) || $_GET['cp_tab'] == 'dashboard') ? 'active' : ''; ?>"><a href="?cp_tab=dashboard" data-tooltip="<?php _e('System Overview', 'board'); ?>"><span class="dashicons dashicons-dashboard"></span> <?php _e('Dashboard', 'board'); ?></a></li>
            <li class="<?php echo (isset($_GET['cp_tab']) && $_GET['cp_tab'] == 'users') ? 'active' : ''; ?>"><a href="?cp_tab=users" data-tooltip="<?php _e('Manage Members', 'board'); ?>"><span class="dashicons dashicons-users"></span> <?php _e('Users Management', 'board'); ?></a></li>
            <li class="<?php echo (isset($_GET['cp_tab']) && $_GET['cp_tab'] == 'programs') ? 'active' : ''; ?>"><a href="?cp_tab=programs" data-tooltip="<?php _e('Course Catalog', 'board'); ?>"><span class="dashicons dashicons-welcome-learn-more"></span> <?php _e('Programs', 'board'); ?></a></li>
            <li class="<?php echo (isset($_GET['cp_tab']) && $_GET['cp_tab'] == 'exams') ? 'active' : ''; ?>"><a href="?cp_tab=exams" data-tooltip="<?php _e('Assessment Center', 'board'); ?>"><span class="dashicons dashicons-clipboard"></span> <?php _e('Exams', 'board'); ?></a></li>
            <li class="<?php echo (isset($_GET['cp_tab']) && $_GET['cp_tab'] == 'requests') ? 'active' : ''; ?>"><a href="?cp_tab=requests" data-tooltip="<?php _e('Approve Upgrades', 'board'); ?>"><span class="dashicons dashicons-email-alt"></span> <?php _e('Membership Requests', 'board'); ?></a></li>
            <li class="<?php echo (isset($_GET['cp_tab']) && $_GET['cp_tab'] == 'applications') ? 'active' : ''; ?>"><a href="?cp_tab=applications" data-tooltip="<?php _e('Program Enrollments', 'board'); ?>"><span class="dashicons dashicons-clipboard"></span> <?php _e('Applications', 'board'); ?></a></li>
            <li class="<?php echo (isset($_GET['cp_tab']) && $_GET['cp_tab'] == 'certificates') ? 'active' : ''; ?>"><a href="?cp_tab=certificates" data-tooltip="<?php _e('Credentialing', 'board'); ?>"><span class="dashicons dashicons-awards"></span> <?php _e('Certificates & Accreditations', 'board'); ?></a></li>
            <li class="<?php echo (isset($_GET['cp_tab']) && $_GET['cp_tab'] == 'verification') ? 'active' : ''; ?>"><a href="?cp_tab=verification" data-tooltip="<?php _e('Verify Integrity', 'board'); ?>"><span class="dashicons dashicons-shield-alt"></span> <?php _e('Verification', 'board'); ?></a></li>
            <li class="<?php echo (isset($_GET['cp_tab']) && $_GET['cp_tab'] == 'reports') ? 'active' : ''; ?>"><a href="?cp_tab=reports" data-tooltip="<?php _e('View Analytics', 'board'); ?>"><span class="dashicons dashicons-chart-bar"></span> <?php _e('Reports', 'board'); ?></a></li>
            <li class="<?php echo (isset($_GET['cp_tab']) && $_GET['cp_tab'] == 'settings') ? 'active' : ''; ?>"><a href="?cp_tab=settings" data-tooltip="<?php _e('Global Config', 'board'); ?>"><span class="dashicons dashicons-admin-settings"></span> <?php _e('Settings', 'board'); ?></a></li>
        </ul>
    </aside>

    <main class="board-cp-main">
        <?php
        $tab = isset($_GET['cp_tab']) ? $_GET['cp_tab'] : 'dashboard';
        ?>
        <div class="board-breadcrumb" style="margin-bottom: 30px; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: grey;">
            <a href="?cp_tab=dashboard"><?php _e('Home', 'board'); ?></a>
            <?php if ($tab != 'dashboard') : ?>
                <span style="margin: 0 10px;">/</span>
                <span style="color: black; font-weight: bold;"><?php echo ucfirst($tab); ?></span>
            <?php endif; ?>
        </div>
        <?php
        use GSHB\Board\Database\Manager as DB;
        $pending_requests = \GSHB\Board\Admin\Manager::get_pending_requests();
        $total_pending = count($pending_requests);
        ?>

        <?php if ($tab == 'dashboard') : ?>
            <h2><?php _e('Dashboard Overview', 'board'); ?></h2>
            <div class="board-cp-cards" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                <div class="board-stat-card"><h3><?php _e('Users', 'board'); ?></h3><p class="board-stat-number"><?php $uc = count_users(); echo $uc['total_users']; ?></p></div>
                <div class="board-stat-card"><h3><?php _e('Programs', 'board'); ?></h3><p class="board-stat-number"><?php echo count(DB::get_programs()); ?></p></div>
                <div class="board-stat-card"><h3><?php _e('Pending Requests', 'board'); ?></h3><p class="board-stat-number"><?php echo $total_pending; ?></p></div>
                <div class="board-stat-card"><h3><?php _e('Certificates', 'board'); ?></h3><p class="board-stat-number"><?php echo count(DB::get_certificates()); ?></p></div>
            </div>

            <?php if (current_user_can('manage_options') || \GSHB\Board\Core\Roles::is_board_admin()) : ?>
            <div style="margin-top: 40px; display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                <div class="board-program-card">
                    <h4><?php _e('Action Center', 'board'); ?></h4>
                    <p style="font-size: 13px; color: grey; margin-bottom: 20px;"><?php _e('Critical items requiring immediate attention.', 'board'); ?></p>
                    <ul style="list-style: none; padding: 0;">
                        <li style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee;">
                            <span><?php _e('Pending Member Upgrades', 'board'); ?></span>
                            <strong><?php echo $total_pending; ?></strong>
                        </li>
                        <li style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee;">
                            <span><?php _e('System Health', 'board'); ?></span>
                            <span style="font-weight: bold; border-bottom: 2px solid black;"><?php _e('Optimal', 'board'); ?></span>
                        </li>
                    </ul>
                    <a href="?cp_tab=requests" class="board-btn-black board-btn-small" style="margin-top: 20px;"><?php _e('Review Requests', 'board'); ?></a>
                </div>
                <div class="board-program-card">
                    <h4><?php _e('Quick Statistics', 'board'); ?></h4>
                    <div style="margin-top: 15px;">
                        <div style="margin-bottom: 15px;">
                            <div style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 5px;">
                                <span><?php _e('Program Utilization', 'board'); ?></span>
                                <span>85%</span>
                            </div>
                            <div style="height: 6px; background: #eee; border: 1px solid #000;">
                                <div style="height: 100%; width: 85%; background: #000;"></div>
                            </div>
                        </div>
                        <div>
                            <div style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 5px;">
                                <span><?php _e('Certificate Issuance Rate', 'board'); ?></span>
                                <span>62%</span>
                            </div>
                            <div style="height: 6px; background: #eee; border: 1px solid #000;">
                                <div style="height: 100%; width: 62%; background: #000;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div style="margin-top: 40px;">
                <h4><?php _e('Weekly Enrollment Activity', 'board'); ?></h4>
                <div style="display: flex; align-items: flex-end; gap: 10px; height: 150px; background: #f9f9f9; padding: 20px; border: 1px solid var(--board-black);">
                    <div style="flex: 1; background: var(--board-black); height: 40%; opacity: 0.1;"></div>
                    <div style="flex: 1; background: var(--board-black); height: 70%; opacity: 0.3;"></div>
                    <div style="flex: 1; background: var(--board-black); height: 55%; opacity: 0.5;"></div>
                    <div style="flex: 1; background: var(--board-black); height: 90%; opacity: 0.7;"></div>
                    <div style="flex: 1; background: var(--board-black); height: 30%; opacity: 0.4;"></div>
                    <div style="flex: 1; background: var(--board-black); height: 65%; opacity: 0.6;"></div>
                    <div style="flex: 1; background: var(--board-black); height: 80%; opacity: 0.9;"></div>
                </div>
                <p style="font-size: 11px; text-align: center; margin-top: 10px;"><?php _e('Visual representation of program engagement and new member registrations.', 'board'); ?></p>
            </div>
        <?php endif; ?>

        <?php if ($tab == 'users') : ?>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3><?php _e('Users Management', 'board'); ?></h3>
                <div style="display: flex; gap: 10px;">
                    <form action="<?php echo admin_url('admin-post.php'); ?>" method="post" enctype="multipart/form-data" style="display: flex; gap: 5px; align-items: center;">
                        <?php wp_nonce_field('board_import_nonce'); ?>
                        <input type="hidden" name="action" value="board_import_users">
                        <input type="file" name="import_file" style="font-size: 11px;" required>
                        <button type="submit" class="board-btn-black" style="width: auto; padding: 5px 10px; font-size: 11px;"><?php _e('Import CSV', 'board'); ?></button>
                    </form>
                    <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=board_export_users'), 'board_export_users'); ?>" class="board-btn-black" style="width: auto; text-decoration: none; padding: 5px 15px; font-size: 12px; margin-right: 10px;"><?php _e('Export CSV', 'board'); ?></a>
                    <button class="board-btn-black" id="open-add-user" style="width: auto; padding: 5px 15px; font-size: 12px;"><?php _e('Add New User', 'board'); ?></button>
                </div>
            </div>

            <!-- Add User Form (Hidden by default) -->
            <div id="add-user-section" style="display: none; background: #f9f9f9; padding: 20px; border: 1px solid var(--board-black); margin-bottom: 20px;">
                <h4><?php _e('Create New User', 'board'); ?></h4>
                <form id="board-add-user-form">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="board-form-field">
                            <label><?php _e('Username', 'board'); ?></label>
                            <input type="text" name="username" placeholder="johndoe" required>
                        </div>
                        <div class="board-form-field">
                            <label><?php _e('Email Address', 'board'); ?></label>
                            <input type="email" name="email" placeholder="john@example.com" required>
                        </div>
                        <div class="board-form-field">
                            <label><?php _e('Password', 'board'); ?></label>
                            <input type="password" name="password" placeholder="••••••••" required>
                        </div>
                        <div class="board-form-field">
                            <label><?php _e('Assigned Role', 'board'); ?></label>
                            <select name="role" required>
                                <option value="board_member"><?php _e('Member', 'board'); ?></option>
                                <option value="certified_member"><?php _e('Certified Member', 'board'); ?></option>
                                <option value="programs_manager"><?php _e('Programs Manager', 'board'); ?></option>
                                <option value="certs_manager"><?php _e('Certifications Manager', 'board'); ?></option>
                                <option value="academic_supervisor"><?php _e('Academic Supervisor', 'board'); ?></option>
                                <option value="board_admin"><?php _e('Board Administrator', 'board'); ?></option>
                            </select>
                        </div>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="board-btn-black" style="width: auto;"><?php _e('Create User', 'board'); ?></button>
                        <button type="button" id="close-add-user" class="board-btn-black board-btn-outline" style="width: auto;"><?php _e('Cancel', 'board'); ?></button>
                    </div>
                </form>
            </div>

            <div style="margin-bottom: 30px; display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 15px; align-items: flex-end;">
                <div style="position: relative;">
                    <label style="display: block; font-size: 11px; font-weight: bold; margin-bottom: 5px; text-transform: uppercase;"><?php _e('Search Users', 'board'); ?></label>
                    <input type="text" id="user-search" placeholder="<?php _e('Search by name, ID or email...', 'board'); ?>" style="width: 100%; padding: 12px; border: 1px solid var(--board-black);" autocomplete="off">
                    <div id="user-search-suggestions" class="board-search-suggestions"></div>
                </div>
                <div>
                    <label style="display: block; font-size: 11px; font-weight: bold; margin-bottom: 5px; text-transform: uppercase;"><?php _e('Role Filter', 'board'); ?></label>
                    <select id="role-filter" style="width: 100%; padding: 12px; border: 1px solid var(--board-black);">
                        <option value=""><?php _e('All Roles', 'board'); ?></option>
                        <option value="board_member"><?php _e('Member', 'board'); ?></option>
                        <option value="certified_member"><?php _e('Certified', 'board'); ?></option>
                        <option value="programs_manager"><?php _e('Programs Manager', 'board'); ?></option>
                        <option value="certs_manager"><?php _e('Certs Manager', 'board'); ?></option>
                        <option value="academic_supervisor"><?php _e('Supervisor', 'board'); ?></option>
                        <option value="board_admin"><?php _e('Board Admin', 'board'); ?></option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 11px; font-weight: bold; margin-bottom: 5px; text-transform: uppercase;"><?php _e('Status Filter', 'board'); ?></label>
                    <select id="status-filter" style="width: 100%; padding: 12px; border: 1px solid var(--board-black);">
                        <option value=""><?php _e('All Statuses', 'board'); ?></option>
                        <option value="active"><?php _e('Active', 'board'); ?></option>
                        <option value="inactive"><?php _e('Inactive', 'board'); ?></option>
                        <option value="suspended"><?php _e('Suspended', 'board'); ?></option>
                    </select>
                </div>
            </div>

            <table class="board-table" id="users-table">
                <thead>
                    <tr>
                        <th><?php _e('Name / ID', 'board'); ?></th>
                        <th><?php _e('Email / Date', 'board'); ?></th>
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
                        $certs_count = count(DB::get_certificates($u->ID));
                        $exams_count = count(get_user_meta($u->ID, 'assigned_exams', true) ?: array());
                        ?>
                        <tr data-role="<?php echo implode(' ', $u->roles); ?>" data-status="<?php echo $status; ?>">
                            <td><strong><?php echo esc_html($u->display_name); ?></strong><br><small><?php echo $id_code; ?></small></td>
                            <td><?php echo esc_html($u->user_email); ?><br><small><?php echo date('Y-m-d', strtotime($u->user_registered)); ?></small></td>
                            <td>
                                <select class="quick-role-change" data-id="<?php echo $u->ID; ?>" style="padding: 2px; font-size: 11px;">
                                    <option value="board_member" <?php selected(in_array('board_member', $u->roles)); ?>><?php _e('Member', 'board'); ?></option>
                                    <option value="certified_member" <?php selected(in_array('certified_member', $u->roles)); ?>><?php _e('Certified', 'board'); ?></option>
                                    <option value="programs_manager" <?php selected(in_array('programs_manager', $u->roles)); ?>><?php _e('Programs', 'board'); ?></option>
                                    <option value="certs_manager" <?php selected(in_array('certs_manager', $u->roles)); ?>><?php _e('Certs', 'board'); ?></option>
                                    <option value="academic_supervisor" <?php selected(in_array('academic_supervisor', $u->roles)); ?>><?php _e('Supervisor', 'board'); ?></option>
                                    <option value="board_admin" <?php selected(in_array('board_admin', $u->roles)); ?>><?php _e('Admin', 'board'); ?></option>
                                </select>
                            </td>
                            <td>
                                <select class="quick-status-change" data-id="<?php echo $u->ID; ?>" style="padding: 2px; font-size: 11px;">
                                    <option value="active" <?php selected($status, 'active'); ?>><?php _e('Active', 'board'); ?></option>
                                    <option value="inactive" <?php selected($status, 'inactive'); ?>><?php _e('Inactive', 'board'); ?></option>
                                    <option value="suspended" <?php selected($status, 'suspended'); ?>><?php _e('Suspended', 'board'); ?></option>
                                </select>
                            </td>
                            <td><strong><?php echo $certs_count; ?></strong> / <?php echo $exams_count; ?></td>
                            <td>
                                <div style="display: flex; gap: 5px;">
                                    <button class="board-btn-black board-btn-small board-btn-destructive delete-user" data-id="<?php echo $u->ID; ?>" data-tooltip="<?php _e('Permanently remove this user', 'board'); ?>"><?php _e('Delete', 'board'); ?></button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <?php if ($tab == 'programs') : ?>
            <?php $prog_sub = isset($_GET['prog_sub']) ? $_GET['prog_sub'] : 'list'; ?>
            <div style="display: flex; border-bottom: 1px solid #ddd; margin-bottom: 30px; gap: 30px;">
                <a href="?cp_tab=programs&prog_sub=list" style="padding: 10px 0; text-decoration: none; color: <?php echo $prog_sub == 'list' ? 'black' : 'grey'; ?>; font-weight: 700; border-bottom: 2px solid <?php echo $prog_sub == 'list' ? 'black' : 'transparent'; ?>;"><?php _e('Program List', 'board'); ?></a>
                <a href="?cp_tab=programs&prog_sub=apps" style="padding: 10px 0; text-decoration: none; color: <?php echo $prog_sub == 'apps' ? 'black' : 'grey'; ?>; font-weight: 700; border-bottom: 2px solid <?php echo $prog_sub == 'apps' ? 'black' : 'transparent'; ?>;"><?php _e('Applications Workflow', 'board'); ?></a>
            </div>

            <?php if ($prog_sub == 'list') : ?>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3><?php _e('Manage Programs', 'board'); ?></h3>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <form action="<?php echo admin_url('admin-post.php'); ?>" method="post" enctype="multipart/form-data" style="display: flex; gap: 5px; align-items: center;">
                        <?php wp_nonce_field('board_import_nonce'); ?>
                        <input type="hidden" name="action" value="board_import_programs">
                        <input type="file" name="import_file" style="font-size: 11px;" required>
                        <button type="submit" class="board-btn-black" style="width: auto; padding: 5px 10px; font-size: 11px;"><?php _e('Import CSV', 'board'); ?></button>
                    </form>
                    <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=board_export_programs'), 'board_export_programs'); ?>" class="board-btn-black" style="width: auto; text-decoration: none; padding: 5px 15px; font-size: 12px;"><?php _e('Export Programs', 'board'); ?></a>
                    <button class="board-btn-black" id="open-add-program" style="width: auto; padding: 5px 15px; font-size: 12px;"><?php _e('Add New Program', 'board'); ?></button>
                </div>
            </div>

            <!-- Add Program Form -->
            <div id="add-program-section" style="display: none; background: #f9f9f9; padding: 30px; border: 1px solid var(--board-black); margin-bottom: 30px; border-radius: 8px;">
                <h4><?php _e('Create Professional Program', 'board'); ?></h4>
                <form id="board-save-program-form">
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                        <div class="board-form-field" style="grid-column: span 2;">
                            <label><?php _e('Program Title', 'board'); ?></label>
                            <input type="text" name="title" placeholder="e.g., Advanced Sports Nutrition" required>
                        </div>
                        <div class="board-form-field">
                            <label><?php _e('Program Type', 'board'); ?></label>
                            <select name="type" required>
                                <option value="Course"><?php _e('Course', 'board'); ?></option>
                                <option value="Diploma"><?php _e('Diploma', 'board'); ?></option>
                                <option value="Board Membership"><?php _e('Board Membership', 'board'); ?></option>
                                <option value="Accreditation"><?php _e('Accreditation', 'board'); ?></option>
                            </select>
                        </div>
                        <div class="board-form-field">
                            <label><?php _e('Category / Field', 'board'); ?></label>
                            <input type="text" name="category" placeholder="e.g., Medicine">
                        </div>
                        <div class="board-form-field">
                            <label><?php _e('Lead Instructor', 'board'); ?></label>
                            <input type="text" name="instructor" placeholder="e.g., Dr. Smith">
                        </div>
                        <div class="board-form-field">
                            <label><?php _e('Academic Credits', 'board'); ?></label>
                            <input type="number" name="credits" value="0">
                        </div>
                        <div class="board-form-field">
                            <label><?php _e('Program Duration', 'board'); ?></label>
                            <input type="text" name="duration" placeholder="e.g., 6 Months">
                        </div>
                        <div class="board-form-field">
                            <label><?php _e('Auto-Code (GSHB-PROG-XXXX)', 'board'); ?></label>
                            <input type="text" name="code" placeholder="Leave empty for auto-gen" readonly style="background: #eee;">
                        </div>
                    </div>
                    <div class="board-form-field">
                        <label><?php _e('Professional Description', 'board'); ?></label>
                        <textarea name="desc" placeholder="Detailed program overview..." rows="5"></textarea>
                    </div>
                    <div style="display: flex; gap: 15px;">
                        <button type="submit" class="board-btn-black"><?php _e('Publish Program', 'board'); ?></button>
                        <button type="button" id="close-add-program" class="board-btn-black board-btn-outline"><?php _e('Cancel', 'board'); ?></button>
                    </div>
                </form>
            </div>

            <div style="margin-bottom: 30px; display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 15px; align-items: flex-end;">
                <div style="position: relative;">
                    <label style="display: block; font-size: 11px; font-weight: bold; margin-bottom: 5px; text-transform: uppercase;"><?php _e('Search Programs', 'board'); ?></label>
                    <input type="text" id="program-search" placeholder="<?php _e('Search by title, code, instructor...', 'board'); ?>" style="width: 100%; padding: 12px; border: 1px solid var(--board-black);" autocomplete="off">
                    <div id="program-search-suggestions" class="board-search-suggestions"></div>
                </div>
                <div>
                    <label style="display: block; font-size: 11px; font-weight: bold; margin-bottom: 5px; text-transform: uppercase;"><?php _e('Type Filter', 'board'); ?></label>
                    <select id="program-type-filter" style="width: 100%; padding: 12px; border: 1px solid var(--board-black);">
                        <option value=""><?php _e('All Types', 'board'); ?></option>
                        <option value="course"><?php _e('Course', 'board'); ?></option>
                        <option value="diploma"><?php _e('Diploma', 'board'); ?></option>
                        <option value="board membership"><?php _e('Board Membership', 'board'); ?></option>
                        <option value="accreditation"><?php _e('Accreditation', 'board'); ?></option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 11px; font-weight: bold; margin-bottom: 5px; text-transform: uppercase;"><?php _e('Category Filter', 'board'); ?></label>
                    <select id="program-category-filter" style="width: 100%; padding: 12px; border: 1px solid var(--board-black);">
                        <option value=""><?php _e('All Categories', 'board'); ?></option>
                        <?php
                        $cats = $wpdb->get_col("SELECT DISTINCT category FROM {$wpdb->prefix}board_programs WHERE category != ''");
                        foreach($cats as $cat) echo '<option value="'.strtolower($cat).'">'.esc_html($cat).'</option>';
                        ?>
                    </select>
                </div>
            </div>

            <div class="board-programs-grid" id="admin-programs-grid" style="padding: 0;">
                <?php
                $progs = DB::get_programs();
                if (!empty($progs)) :
                    foreach ($progs as $p) :
                        ?>
                        <div class="board-program-card" data-title="<?php echo strtolower($p->title); ?>" data-type="<?php echo strtolower($p->type); ?>" data-category="<?php echo strtolower($p->category); ?>">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                                <h4 style="margin: 0;"><?php echo esc_html($p->title); ?></h4>
                                <span class="status-badge" style="font-size: 9px; background: #f0f0f0;"><?php echo esc_html($p->code); ?></span>
                            </div>
                            <p style="font-size: 12px; margin-bottom: 10px; color: #666;">
                                <strong><?php _e('Type:', 'board'); ?></strong> <?php echo esc_html($p->type); ?> |
                                <strong><?php _e('Category:', 'board'); ?></strong> <?php echo esc_html($p->category ?: 'General'); ?>
                            </p>
                            <p style="font-size: 13px; flex-grow: 1;"><?php echo wp_trim_words($p->description, 20); ?></p>
                            <p style="font-size: 11px; margin-top: 10px; border-top: 1px solid #eee; pt-10;">
                                <strong><?php _e('Instructor:', 'board'); ?></strong> <?php echo esc_html($p->instructor ?: 'N/A'); ?> |
                                <strong><?php _e('Credits:', 'board'); ?></strong> <?php echo intval($p->credits); ?>
                            </p>
                            <div style="margin-top: 15px; display: flex; gap: 10px;">
                                <button class="board-btn-black board-btn-small" data-tooltip="<?php _e('Modify program details', 'board'); ?>"><?php _e('Edit', 'board'); ?></button>
                                    <button class="board-btn-black board-btn-small board-btn-destructive delete-program" data-id="<?php echo $p->id; ?>" data-tooltip="<?php _e('Remove this program', 'board'); ?>"><?php _e('Delete', 'board'); ?></button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p><?php _e('No programs found.', 'board'); ?></p>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if ($prog_sub == 'apps') : ?>
                <div style="background: #f9f9f9; padding: 30px; border-radius: 8px; margin-bottom: 30px;">
                    <h3><?php _e('Enrollment Workflow Manager', 'board'); ?></h3>
                    <p style="font-size: 14px; color: #666;"><?php _e('Review and process structured program applications using the intelligent approval system.', 'board'); ?></p>
                </div>
                <table class="board-table">
                    <thead><tr><th><?php _e('Date', 'board'); ?></th><th><?php _e('Applicant', 'board'); ?></th><th><?php _e('Program', 'board'); ?></th><th><?php _e('Details', 'board'); ?></th><th><?php _e('Status', 'board'); ?></th><th><?php _e('Workflow Action', 'board'); ?></th></tr></thead>
                    <tbody>
                        <?php
                        $apps = DB::get_applications();
                        if (!empty($apps)) :
                            foreach ($apps as $app) :
                                $u = get_userdata($app->user_id);
                                $p = $wpdb->get_row($wpdb->prepare("SELECT title FROM {$wpdb->prefix}board_programs WHERE id = %d", $app->program_id));
                                ?>
                                <tr>
                                    <td><?php echo $app->created_at; ?></td>
                                    <td><strong><?php echo $u ? $u->display_name : 'User'; ?></strong></td>
                                    <td><?php echo $p ? $p->title : 'Program'; ?></td>
                                    <td><button class="board-btn-black board-btn-small board-btn-outline view-app-data" data-data="<?php echo esc_attr($app->data); ?>"><?php _e('View App', 'board'); ?></button></td>
                                    <td><span class="status-badge status-<?php echo $app->status; ?>"><?php echo esc_html($app->status); ?></span></td>
                                    <td>
                                        <select class="app-status-change" data-id="<?php echo $app->id; ?>" style="padding: 5px; font-size: 11px;">
                                            <option value="pending" <?php selected($app->status, 'pending'); ?>>Review Pending</option>
                                            <option value="approved" <?php selected($app->status, 'approved'); ?>>Approve Enrollment</option>
                                            <option value="rejected" <?php selected($app->status, 'rejected'); ?>>Reject Application</option>
                                        </select>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr><td colspan="6" style="text-align: center;"><?php _e('No active applications in workflow.', 'board'); ?></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($tab == 'exams') : ?>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3><?php _e('Manage Exams', 'board'); ?></h3>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <form action="<?php echo admin_url('admin-post.php'); ?>" method="post" enctype="multipart/form-data" style="display: flex; gap: 5px; align-items: center;">
                        <?php wp_nonce_field('board_import_nonce'); ?>
                        <input type="hidden" name="action" value="board_import_exams">
                        <input type="file" name="import_file" style="font-size: 11px;" required>
                        <button type="submit" class="board-btn-black" style="width: auto; padding: 5px 10px; font-size: 11px;"><?php _e('Import CSV', 'board'); ?></button>
                    </form>
                    <button class="board-btn-black" id="open-add-exam" style="width: auto; padding: 5px 15px; font-size: 12px;"><?php _e('Create Exam', 'board'); ?></button>
                </div>
            </div>

            <div style="margin-bottom: 30px; max-width: 100%; position: relative;">
                <label style="display: block; font-size: 11px; font-weight: bold; margin-bottom: 5px; text-transform: uppercase;"><?php _e('Search Exams', 'board'); ?></label>
                <input type="text" id="exam-search" placeholder="<?php _e('Search by title or code...', 'board'); ?>" style="width: 100%; padding: 12px; border: 1px solid var(--board-black);" autocomplete="off">
                <div id="exam-search-suggestions" class="board-search-suggestions"></div>
            </div>

            <!-- Add Exam Form -->
            <div id="add-exam-section" style="display: none; background: #f9f9f9; padding: 20px; border: 1px solid var(--board-black); margin-bottom: 30px;">
                <h4><?php _e('Create New Exam', 'board'); ?></h4>
                <form id="board-save-exam-form">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <div class="board-form-field"><input type="text" name="title" placeholder="Exam Title" required></div>
                        <div class="board-form-field"><input type="text" name="exam_code" placeholder="Exam Code" required></div>
                        <div class="board-form-field">
                            <select name="program_id">
                                <option value=""><?php _e('Link to Program (Optional)', 'board'); ?></option>
                                <?php foreach($progs as $p) echo "<option value='{$p->id}'>{$p->title}</option>"; ?>
                            </select>
                        </div>
                        <div class="board-form-field"><input type="date" name="exam_due"></div>
                    </div>
                    <button type="submit" class="board-btn-black" style="width: auto;"><?php _e('Save Exam', 'board'); ?></button>
                    <button type="button" id="close-add-exam" class="board-btn-black" style="width: auto; background: grey;"><?php _e('Cancel', 'board'); ?></button>
                </form>
            </div>

            <hr>
            <h3><?php _e('Assign Exams to Users', 'board'); ?></h3>
            <form id="board-assign-exam-form" style="margin-bottom: 30px;">
                <div class="board-form-field" style="position: relative;">
                    <label><?php _e('Select User Account', 'board'); ?></label>
                    <input type="text" class="board-user-lookup-input" placeholder="<?php _e('Search by name or email...', 'board'); ?>" autocomplete="off" required>
                    <input type="hidden" name="user_id" value="">
                    <div class="board-user-lookup-results board-search-suggestions"></div>
                </div>
                <div class="board-form-field">
                    <label><?php _e('Target Exam', 'board'); ?></label>
                    <select name="exam_id" required>
                        <option value=""><?php _e('Select Exam', 'board'); ?></option>
                        <?php
                        $exams = DB::get_exams();
                        foreach($exams as $e) echo "<option value='{$e->id}'>{$e->title}</option>";
                        ?>
                    </select>
                </div>
                <button type="submit" class="board-btn-black" style="width: auto;"><?php _e('Assign Exam', 'board'); ?></button>
            </form>
        <?php endif; ?>

        <?php if ($tab == 'requests') : ?>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3><?php _e('Membership Requests & Members', 'board'); ?></h3>
                <button class="board-btn-black" id="open-add-membership" style="width: auto; padding: 5px 15px; font-size: 12px;"><?php _e('Add Manual Record', 'board'); ?></button>
            </div>

            <!-- Manual Membership Form -->
            <div id="add-membership-section" style="display: none; background: #f9f9f9; padding: 20px; border: 1px solid var(--board-black); margin-bottom: 20px;">
                <h4><?php _e('Create Membership Record', 'board'); ?></h4>
                <form id="board-membership-form">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="board-form-field">
                            <label><?php _e('Full Name', 'board'); ?></label>
                            <input type="text" name="full_name" required>
                        </div>
                        <div class="board-form-field" style="position: relative;">
                            <label><?php _e('Linked User (Optional)', 'board'); ?></label>
                            <input type="text" class="board-user-lookup-input" placeholder="<?php _e('Search...', 'board'); ?>" autocomplete="off">
                            <input type="hidden" name="user_id" value="">
                            <div class="board-user-lookup-results board-search-suggestions"></div>
                        </div>
                        <div class="board-form-field">
                            <label><?php _e('Country', 'board'); ?></label>
                            <input type="text" name="country" required>
                        </div>
                        <div class="board-form-field">
                            <label><?php _e('Specialty', 'board'); ?></label>
                            <input type="text" name="specialty" required>
                        </div>
                    </div>
                    <button type="submit" class="board-btn-black" style="width: auto;"><?php _e('Save Record', 'board'); ?></button>
                    <button type="button" id="close-add-membership" class="board-btn-black board-btn-outline" style="width: auto;"><?php _e('Cancel', 'board'); ?></button>
                </form>
            </div>

            <table class="board-table">
                <thead><tr><th><?php _e('Date', 'board'); ?></th><th><?php _e('Applicant', 'board'); ?></th><th><?php _e('Country / Specialty', 'board'); ?></th><th><?php _e('Status', 'board'); ?></th><th><?php _e('Action', 'board'); ?></th></tr></thead>
                <tbody>
                    <?php
                    $all_memberships = DB::get_memberships();
                    if (!empty($all_memberships)) : ?>
                        <?php foreach ($all_memberships as $request) : ?>
                            <tr>
                                <td><?php echo $request->created_at; ?></td>
                                <td>
                                    <?php echo esc_html($request->full_name); ?>
                                    <?php if ($request->user_id) : ?>
                                        <br><small style="color: green;">Linked to UID: <?php echo $request->user_id; ?></small>
                                    <?php else : ?>
                                        <br><small style="color: darkred;">Unlinked</small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo esc_html($request->country . ' / ' . $request->specialty); ?></td>
                                <td><span class="status-badge status-<?php echo $request->status; ?>"><?php echo esc_html($request->status); ?></span></td>
                                <td>
                                    <div style="display: flex; gap: 5px; align-items: center;">
                                        <?php if ($request->status == 'pending') : ?>
                                            <button class="board-btn-black approve-request board-btn-small" data-id="<?php echo $request->id; ?>"><?php _e('Approve', 'board'); ?></button>
                                        <?php endif; ?>
                                        <?php if (!$request->user_id) : ?>
                                            <button class="board-btn-black board-btn-small open-link-membership" data-id="<?php echo $request->id; ?>"><?php _e('Link User', 'board'); ?></button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="5" style="text-align: center;"><?php _e('No records found.', 'board'); ?></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <?php if ($tab == 'certificates') : ?>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3><?php _e('Certificates Management', 'board'); ?></h3>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <form action="<?php echo admin_url('admin-post.php'); ?>" method="post" enctype="multipart/form-data" style="display: flex; gap: 5px; align-items: center;">
                        <?php wp_nonce_field('board_import_nonce'); ?>
                        <input type="hidden" name="action" value="board_import_certificates">
                        <input type="file" name="import_file" style="font-size: 11px;" required>
                        <button type="submit" class="board-btn-black" style="width: auto; padding: 5px 10px; font-size: 11px;"><?php _e('Import CSV', 'board'); ?></button>
                    </form>
                    <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=board_export_certificates'), 'board_export_certificates'); ?>" class="board-btn-black" style="width: auto; text-decoration: none; padding: 5px 15px; font-size: 12px;"><?php _e('Export Certificates', 'board'); ?></a>
                    <button class="board-btn-black" id="open-generate-cert" style="width: auto; padding: 5px 15px; font-size: 12px;"><?php _e('New Certificate', 'board'); ?></button>
                </div>
            </div>

            <div id="generate-cert-section" style="display: none; background: #f9f9f9; padding: 20px; border: 1px solid var(--board-black); margin-bottom: 30px;">
                <h4><?php _e('Create Certificate / Accreditation', 'board'); ?></h4>
                <form id="board-generate-cert-form">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <div class="board-form-field">
                            <label><?php _e('Certificate Title / Recipient Name', 'board'); ?></label>
                            <input type="text" name="title" placeholder="<?php _e('e.g., Dr. Jane Smith - Advanced Diploma', 'board'); ?>">
                        </div>
                        <div class="board-form-field" style="position: relative;">
                            <label><?php _e('Assigned User (Optional)', 'board'); ?></label>
                            <input type="text" class="board-user-lookup-input" placeholder="<?php _e('Type to search users...', 'board'); ?>" autocomplete="off">
                            <input type="hidden" name="user_id" value="">
                            <div class="board-user-lookup-results board-search-suggestions"></div>
                        </div>
                        <div class="board-form-field">
                            <label><?php _e('Accreditation Type', 'board'); ?></label>
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

            <div style="margin-bottom: 30px; display: flex; gap: 15px; align-items: flex-end;">
                <div style="flex-grow: 1; position: relative;">
                    <label style="display: block; font-size: 12px; font-weight: bold; margin-bottom: 5px; text-transform: uppercase;"><?php _e('Search Certificates', 'board'); ?></label>
                    <input type="text" id="cert-search" placeholder="<?php _e('Search by name, serial or type...', 'board'); ?>" style="width: 100%; padding: 12px; border: 1px solid var(--board-black);" autocomplete="off">
                    <div id="cert-search-suggestions" class="board-search-suggestions"></div>
                </div>
                <div>
                    <label style="display: block; font-size: 12px; font-weight: bold; margin-bottom: 5px; text-transform: uppercase;"><?php _e('Filter by Status', 'board'); ?></label>
                    <select id="cert-status-filter" style="padding: 12px; border: 1px solid var(--board-black); min-width: 150px;">
                        <option value=""><?php _e('All Statuses', 'board'); ?></option>
                        <option value="active"><?php _e('Active', 'board'); ?></option>
                        <option value="revoked"><?php _e('Revoked', 'board'); ?></option>
                    </select>
                </div>
            </div>

            <div class="board-programs-grid" id="admin-certs-grid" style="padding: 0;">
                <?php
                $certs = DB::get_certificates();
                if (!empty($certs)) :
                    foreach ($certs as $c) :
                        ?>
                        <div class="board-program-card" data-title="<?php echo strtolower($c->title . ' ' . $c->serial_number); ?>">
                            <h4><?php echo esc_html($c->title); ?></h4>
                            <p style="font-size: 12px; margin-bottom: 10px;">
                                <strong><?php _e('Type:', 'board'); ?></strong> <?php echo esc_html($c->type); ?> |
                                <strong><?php _e('Status:', 'board'); ?></strong> <span class="status-badge status-<?php echo $c->status; ?>"><?php echo esc_html($c->status); ?></span>
                            </p>
                            <p style="font-size: 13px;"><code><?php echo esc_html($c->serial_number); ?></code></p>
                            <p style="font-size: 11px; margin-top: 5px; color: grey;">
                                <?php _e('Issued:', 'board'); ?> <?php echo $c->issue_date; ?> |
                                <?php if ($c->user_id) : ?>
                                    <strong>Linked to UID: <?php echo $c->user_id; ?></strong>
                                <?php else : ?>
                                    <span style="color: darkred; font-weight: bold;"><?php _e('Unlinked', 'board'); ?></span>
                                <?php endif; ?>
                            </p>
                            <div style="margin-top: 15px; display: flex; gap: 10px; flex-wrap: wrap;">
                                <?php if (!$c->user_id) : ?>
                                    <button class="board-btn-black board-btn-small open-link-cert" data-id="<?php echo $c->id; ?>" data-tooltip="<?php _e('Link this certificate to a user account', 'board'); ?>"><?php _e('Link User', 'board'); ?></button>
                                <?php endif; ?>
                                <button class="board-btn-black board-btn-small board-btn-destructive revoke-cert" data-id="<?php echo $c->id; ?>" data-tooltip="<?php _e('Invalidate this certificate', 'board'); ?>"><?php _e('Revoke', 'board'); ?></button>
                                <button class="board-btn-black board-btn-small board-btn-destructive delete-cert" data-id="<?php echo $c->id; ?>" data-tooltip="<?php _e('Permanently delete record', 'board'); ?>"><?php _e('Delete', 'board'); ?></button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p><?php _e('No certificates issued yet.', 'board'); ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($tab == 'verification') : ?>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3><?php _e('Verification Management', 'board'); ?></h3>
                <input type="text" id="verify-mgmt-search" placeholder="<?php _e('Search codes...', 'board'); ?>" style="padding: 8px; border: 1px solid var(--board-black);">
            </div>

            <table class="board-table" id="verify-mgmt-table">
                <thead>
                    <tr>
                        <th><?php _e('Code', 'board'); ?></th>
                        <th><?php _e('Assigned To', 'board'); ?></th>
                        <th><?php _e('Type', 'board'); ?></th>
                        <th><?php _e('Status', 'board'); ?></th>
                        <th><?php _e('Action', 'board'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Membership Codes
                    $mem_users = get_users(array('meta_key' => 'verification_code'));
                    foreach ($mem_users as $u) :
                        $code = get_user_meta($u->ID, 'verification_code', true);
                        $status = get_user_meta($u->ID, 'membership_status', true) ?: 'active';
                        ?>
                        <tr>
                            <td><code><?php echo esc_html($code); ?></code></td>
                            <td><?php echo esc_html($u->display_name); ?></td>
                            <td><?php _e('Membership', 'board'); ?></td>
                            <td><span style="text-transform: capitalize;"><?php echo $status; ?></span></td>
                            <td><button class="board-btn-black update-verify-status" data-type="user" data-id="<?php echo $u->ID; ?>" style="width: auto; padding: 3px 8px; font-size: 10px; background: grey;"><?php _e('Invalidate', 'board'); ?></button></td>
                        </tr>
                    <?php endforeach; ?>

                    <?php
                    // Certificate Codes
                    $cert_list = DB::get_certificates();
                    foreach ($cert_list as $c) :
                        $uinfo = get_userdata($c->user_id);
                        ?>
                        <tr>
                            <td><code><?php echo esc_html($c->serial_number); ?></code></td>
                            <td><?php echo $uinfo ? esc_html($uinfo->display_name) : 'Unknown'; ?></td>
                            <td><?php echo esc_html($c->type); ?></td>
                            <td><span style="text-transform: capitalize;"><?php echo esc_html($c->status); ?></span></td>
                            <td><button class="board-btn-black update-verify-status" data-type="cert" data-id="<?php echo $c->id; ?>" style="width: auto; padding: 3px 8px; font-size: 10px; background: grey;"><?php _e('Revoke', 'board'); ?></button></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <?php if ($tab == 'reports') : ?>
            <h3><?php _e('System Analytics & Reports', 'board'); ?></h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                <div class="board-program-card">
                    <h4><?php _e('Program Engagement', 'board'); ?></h4>
                    <p style="font-size: 13px;"><?php _e('Total enrollments and completion rates across all professional courses.', 'board'); ?></p>
                </div>
                <div class="board-program-card">
                    <h4><?php _e('Certification Growth', 'board'); ?></h4>
                    <p style="font-size: 13px;"><?php _e('Monthly trend of new certified members and issued diplomas.', 'board'); ?></p>
                </div>
            </div>
            <div style="background: #f9f9f9; padding: 30px; border: 1px solid var(--board-black);">
                <h4><?php _e('Data Export', 'board'); ?></h4>
                <p style="font-size: 14px; margin-bottom: 25px; color: var(--board-grey-dark);"><?php _e('Download comprehensive system data for auditing and performance tracking.', 'board'); ?></p>
                <div style="display: flex; gap: 15px;">
                        <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=board_export_users'), 'board_export_users'); ?>" class="board-btn-black" style="width: auto; text-decoration: none;"><?php _e('Users Report', 'board'); ?></a>
                        <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=board_export_certificates'), 'board_export_certificates'); ?>" class="board-btn-black board-btn-outline" style="width: auto; text-decoration: none;"><?php _e('Certificates Report', 'board'); ?></a>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($tab == 'logs') : ?>
            <h3><?php _e('Activity Logs', 'board'); ?></h3>
            <table class="board-table">
                <thead><tr><th><?php _e('Date', 'board'); ?></th><th><?php _e('Action', 'board'); ?></th><th><?php _e('User', 'board'); ?></th><th><?php _e('Details', 'board'); ?></th></tr></thead>
                <tbody>
                    <?php
                    global $wpdb;
                    $logs = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}board_logs ORDER BY created_at DESC LIMIT 50");
                    foreach ($logs as $log) :
                        $uinfo = get_userdata($log->user_id);
                        ?>
                        <tr><td><?php echo $log->created_at; ?></td><td><?php echo esc_html($log->action); ?></td><td><?php echo $uinfo ? esc_html($uinfo->display_name) : 'System'; ?></td><td><?php echo esc_html($log->details); ?></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <?php if ($tab == 'applications') : ?>
            <h3><?php _e('Program Enrollment Applications', 'board'); ?></h3>
            <table class="board-table">
                <thead><tr><th><?php _e('Date', 'board'); ?></th><th><?php _e('Applicant', 'board'); ?></th><th><?php _e('Program', 'board'); ?></th><th><?php _e('Status', 'board'); ?></th><th><?php _e('Action', 'board'); ?></th></tr></thead>
                <tbody>
                    <?php
                    $apps = DB::get_applications();
                    if (!empty($apps)) :
                        foreach ($apps as $app) :
                            $u = get_userdata($app->user_id);
                            $p = $wpdb->get_row($wpdb->prepare("SELECT title FROM {$wpdb->prefix}board_programs WHERE id = %d", $app->program_id));
                            ?>
                            <tr>
                                <td><?php echo $app->created_at; ?></td>
                                <td><?php echo $u ? $u->display_name : 'Deleted User'; ?></td>
                                <td><?php echo $p ? $p->title : 'Deleted Program'; ?></td>
                                <td><span class="status-badge status-<?php echo $app->status; ?>"><?php echo esc_html($app->status); ?></span></td>
                                <td>
                                    <select class="app-status-change" data-id="<?php echo $app->id; ?>" style="padding: 5px; font-size: 11px;">
                                        <option value="pending" <?php selected($app->status, 'pending'); ?>>Pending</option>
                                        <option value="approved" <?php selected($app->status, 'approved'); ?>>Approved</option>
                                        <option value="rejected" <?php selected($app->status, 'rejected'); ?>>Rejected</option>
                                    </select>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="5" style="text-align: center;"><?php _e('No applications found.', 'board'); ?></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <?php if ($tab == 'settings') : ?>
            <h3><?php _e('System Settings', 'board'); ?></h3>
            <?php $set_tab = isset($_GET['set_tab']) ? $_GET['set_tab'] : 'general'; ?>

            <div style="display: flex; border-bottom: 1px solid var(--board-black); margin-bottom: 30px; overflow-x: auto; background: var(--board-grey-100); padding: 5px;">
                <a href="?cp_tab=settings&set_tab=general" style="padding: 12px 25px; text-decoration: none; color: black; font-weight: 600; font-size: 13px; <?php echo $set_tab == 'general' ? 'background: white; border: 1px solid var(--board-black); border-bottom: none;' : ''; ?>"><?php _e('General Configuration', 'board'); ?></a>
                <a href="?cp_tab=settings&set_tab=design" style="padding: 12px 25px; text-decoration: none; color: black; font-weight: 600; font-size: 13px; <?php echo $set_tab == 'design' ? 'background: white; border: 1px solid var(--board-black); border-bottom: none;' : ''; ?>"><?php _e('Design & Visual Identity', 'board'); ?></a>
                <a href="?cp_tab=settings&set_tab=email" style="padding: 12px 25px; text-decoration: none; color: black; font-weight: 600; font-size: 13px; <?php echo $set_tab == 'email' ? 'background: white; border: 1px solid var(--board-black); border-bottom: none;' : ''; ?>"><?php _e('System Email & SMTP', 'board'); ?></a>
                <a href="?cp_tab=settings&set_tab=logs" style="padding: 12px 25px; text-decoration: none; color: black; font-weight: 600; font-size: 13px; <?php echo $set_tab == 'logs' ? 'background: white; border: 1px solid var(--board-black); border-bottom: none;' : ''; ?>"><?php _e('Audit & Activity Logs', 'board'); ?></a>
                <a href="?cp_tab=settings&set_tab=backup" style="padding: 12px 25px; text-decoration: none; color: black; font-weight: 600; font-size: 13px; <?php echo $set_tab == 'backup' ? 'background: white; border: 1px solid var(--board-black); border-bottom: none;' : ''; ?>"><?php _e('Data Backup & Recovery', 'board'); ?></a>
                <a href="?cp_tab=settings&set_tab=advanced" style="padding: 12px 25px; text-decoration: none; color: black; font-weight: 600; font-size: 13px; <?php echo $set_tab == 'advanced' ? 'background: white; border: 1px solid var(--board-black); border-bottom: none;' : ''; ?>"><?php _e('Advanced System Ops', 'board'); ?></a>
            </div>

            <?php if ($set_tab == 'general') : ?>
                <form id="board-general-settings-form">
                    <div class="board-form-field">
                        <label><?php _e('Organization Name', 'board'); ?></label>
                        <input type="text" name="org_name" value="<?php echo esc_attr(get_option('board_org_name', 'GSHB')); ?>">
                    </div>
                    <div class="board-form-field">
                        <label><?php _e('Date Format', 'board'); ?></label>
                        <input type="text" name="date_format" value="<?php echo esc_attr(get_option('board_date_format', 'Y-m-d')); ?>">
                    </div>
                    <div class="board-form-field">
                        <label><?php _e('Default User Role', 'board'); ?></label>
                        <select name="default_role">
                            <option value="board_member" <?php selected(get_option('board_default_role'), 'board_member'); ?>><?php _e('Member', 'board'); ?></option>
                            <option value="certified_member" <?php selected(get_option('board_default_role'), 'certified_member'); ?>><?php _e('Certified Member', 'board'); ?></option>
                        </select>
                    </div>
                    <div class="board-form-field">
                        <label><?php _e('Contact Email', 'board'); ?></label>
                        <input type="email" name="contact_email" value="<?php echo esc_attr(get_option('board_contact_email', get_option('admin_email'))); ?>">
                    </div>
                    <div class="board-form-field">
                        <label><?php _e('Notification Email', 'board'); ?></label>
                        <input type="email" name="notify_email" value="<?php echo esc_attr(get_option('board_notify_email', get_option('admin_email'))); ?>">
                    </div>
                    <div class="board-form-field">
                        <label><?php _e('Timezone', 'board'); ?></label>
                        <input type="text" name="timezone" value="<?php echo esc_attr(get_option('board_timezone', 'UTC')); ?>">
                    </div>
                    <button type="submit" class="board-btn-black" style="width: auto;"><?php _e('Save General Settings', 'board'); ?></button>
                </form>
            <?php endif; ?>

            <?php if ($set_tab == 'email') : ?>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
                    <div>
                        <h4><?php _e('SMTP Infrastructure', 'board'); ?></h4>
                        <form id="board-email-settings-form">
                            <div class="board-form-field">
                                <label><?php _e('SMTP Enable', 'board'); ?></label>
                                <select name="email_smtp_enabled">
                                    <option value="off" <?php selected(get_option('board_email_smtp_enabled'), 'off'); ?>>Off</option>
                                    <option value="on" <?php selected(get_option('board_email_smtp_enabled'), 'on'); ?>>On</option>
                                </select>
                            </div>
                            <div class="board-form-field">
                                <label><?php _e('SMTP Host', 'board'); ?></label>
                                <input type="text" name="email_smtp_host" value="<?php echo esc_attr(get_option('board_email_smtp_host')); ?>" placeholder="smtp.example.com">
                            </div>
                            <div class="board-form-field">
                                <label><?php _e('SMTP Port', 'board'); ?></label>
                                <input type="number" name="email_smtp_port" value="<?php echo esc_attr(get_option('board_email_smtp_port', 587)); ?>">
                            </div>
                            <div class="board-form-field">
                                <label><?php _e('SMTP Username', 'board'); ?></label>
                                <input type="text" name="email_smtp_user" value="<?php echo esc_attr(get_option('board_email_smtp_user')); ?>">
                            </div>
                            <div class="board-form-field">
                                <label><?php _e('SMTP Password', 'board'); ?></label>
                                <input type="password" name="email_smtp_pass" value="<?php echo esc_attr(get_option('board_email_smtp_pass')); ?>">
                            </div>
                            <div class="board-form-field">
                                <label><?php _e('Encryption', 'board'); ?></label>
                                <select name="email_smtp_secure">
                                    <option value="tls" <?php selected(get_option('board_email_smtp_secure'), 'tls'); ?>>TLS</option>
                                    <option value="ssl" <?php selected(get_option('board_email_smtp_secure'), 'ssl'); ?>>SSL</option>
                                </select>
                            </div>
                            <hr>
                            <div class="board-form-field">
                                <label><?php _e('Sender Identity (Email)', 'board'); ?></label>
                                <input type="email" name="email_from_address" value="<?php echo esc_attr(get_option('board_email_from_address', get_option('admin_email'))); ?>">
                            </div>
                            <div class="board-form-field">
                                <label><?php _e('Sender Identity (Name)', 'board'); ?></label>
                                <input type="text" name="email_from_name" value="<?php echo esc_attr(get_option('board_email_from_name', get_bloginfo('name'))); ?>">
                            </div>
                            <button type="submit" class="board-btn-black"><?php _e('Save Email Config', 'board'); ?></button>
                        </form>
                    </div>

                    <div>
                        <h4><?php _e('Email Template Manager', 'board'); ?></h4>
                        <p style="font-size: 12px; color: grey; margin-bottom: 20px;"><?php _e('Customize the content of system-generated emails. Use placeholders like {name}, {code}, {title}.', 'board'); ?></p>

                        <?php
                        $templates = array(
                            'registration' => __('Account Registration', 'board'),
                            'membership_request' => __('Membership Request Received', 'board'),
                            'membership_approval' => __('Membership Approval', 'board'),
                            'certificate_issue' => __('Certificate Issuance', 'board'),
                            'password_otp' => __('Password Reset OTP', 'board')
                        );
                        foreach ($templates as $tid => $tlabel) : ?>
                            <div style="background: white; border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 6px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                    <strong style="font-size: 13px;"><?php echo $tlabel; ?></strong>
                                    <select name="template_<?php echo $tid; ?>_enabled" style="width: auto; padding: 2px 10px; font-size: 11px;">
                                        <option value="on" <?php selected(get_option('board_email_template_'.$tid.'_enabled', 'on'), 'on'); ?>>Enabled</option>
                                        <option value="off" <?php selected(get_option('board_email_template_'.$tid.'_enabled'), 'off'); ?>>Disabled</option>
                                    </select>
                                </div>
                                <div class="board-form-field">
                                    <label style="font-size: 11px;"><?php _e('Subject Line', 'board'); ?></label>
                                    <input type="text" name="template_<?php echo $tid; ?>_subject" value="<?php echo esc_attr(get_option('board_email_template_'.$tid.'_subject')); ?>" style="padding: 5px 10px; font-size: 12px;">
                                </div>
                                <div class="board-form-field" style="margin-bottom: 0;">
                                    <label style="font-size: 11px;"><?php _e('Email Body', 'board'); ?></label>
                                    <textarea name="template_<?php echo $tid; ?>_body" rows="4" style="font-size: 12px;"><?php echo esc_textarea(get_option('board_email_template_'.$tid.'_body')); ?></textarea>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <button type="button" class="board-btn-black" id="save-all-email-templates"><?php _e('Update All Templates', 'board'); ?></button>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($set_tab == 'design') : ?>
                <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 50px;">
                    <form id="board-design-settings-form" enctype="multipart/form-data">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="board-form-field">
                                <label><?php _e('Global Layout Style', 'board'); ?></label>
                                <select name="layout_style">
                                    <option value="compact" <?php selected(get_option('board_layout_style'), 'compact'); ?>>Compact Professional</option>
                                    <option value="spacious" <?php selected(get_option('board_layout_style'), 'spacious'); ?>>Spacious Modern</option>
                                </select>
                            </div>
                            <div class="board-form-field">
                                <label><?php _e('Interface Density', 'board'); ?></label>
                                <select name="ui_density">
                                    <option value="normal" <?php selected(get_option('board_ui_density'), 'normal'); ?>>Standard</option>
                                    <option value="high" <?php selected(get_option('board_ui_density'), 'high'); ?>>High Performance (Reduced Padding)</option>
                                </select>
                            </div>
                        </div>

                        <div class="board-form-field">
                            <label><?php _e('Upload Logo', 'board'); ?></label>
                            <input type="file" name="board_logo">
                            <?php if ($logo_url = get_option('board_logo_url')) : ?>
                                <img src="<?php echo esc_url($logo_url); ?>" style="max-height: 50px; display: block; margin-top: 10px; filter: grayscale(100%);">
                            <?php endif; ?>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="board-form-field">
                                <label><?php _e('Brand Primary (Mono)', 'board'); ?></label>
                                <input type="color" name="primary_color" value="<?php echo esc_attr(get_option('board_primary_color', '#000000')); ?>">
                            </div>
                            <div class="board-form-field">
                                <label><?php _e('Typography Family', 'board'); ?></label>
                                <select name="font_family">
                                    <option value="-apple-system, system-ui" <?php selected(get_option('board_font_family'), "-apple-system, system-ui"); ?>>System Modern</option>
                                    <option value="'Inter', sans-serif" <?php selected(get_option('board_font_family'), "'Inter', sans-serif"); ?>>Inter Professional</option>
                                    <option value="'Roboto Mono', monospace" <?php selected(get_option('board_font_family'), "'Roboto Mono', monospace"); ?>>Technical Mono</option>
                                </select>
                            </div>
                        </div>

                        <div class="board-form-field">
                            <label><?php _e('Interaction Behavior', 'board'); ?></label>
                            <div style="background: #fff; border: 1px solid #ddd; padding: 15px; border-radius: 6px;">
                                <label style="display: flex; align-items: center; gap: 10px; font-weight: normal; margin-bottom: 10px;">
                                    <input type="checkbox" name="enable_animations" value="on" <?php checked(get_option('board_enable_animations', 'on'), 'on'); ?> style="width: auto;"> <?php _e('Enable Smooth UI Transitions', 'board'); ?>
                                </label>
                                <label style="display: flex; align-items: center; gap: 10px; font-weight: normal; margin-bottom: 0;">
                                    <input type="checkbox" name="sticky_header" value="on" <?php checked(get_option('board_sticky_header'), 'on'); ?> style="width: auto;"> <?php _e('Enable Sticky Navigation Header', 'board'); ?>
                                </label>
                            </div>
                        </div>

                        <div class="board-form-field">
                            <label><?php _e('Global Custom CSS', 'board'); ?></label>
                            <textarea name="custom_css" rows="6" style="font-family: monospace; font-size: 12px;"><?php echo esc_textarea(get_option('board_custom_css')); ?></textarea>
                        </div>
                        <button type="submit" class="board-btn-black"><?php _e('Apply Visual Structure', 'board'); ?></button>
                    </form>

                    <div id="design-preview">
                        <h4><?php _e('Live Preview Mockup', 'board'); ?></h4>
                        <div class="board-program-card" style="border: 2px solid var(--board-black);">
                            <h4><?php _e('Sample Program Title', 'board'); ?></h4>
                            <p style="font-size: 12px; margin-bottom: 10px;"><strong><?php _e('Type:', 'board'); ?></strong> Course | <strong><?php _e('Duration:', 'board'); ?></strong> 6 Months</p>
                            <p style="font-size: 13px;"><?php _e('This is how your program cards will look across the site.', 'board'); ?></p>
                            <button class="board-btn-black" style="margin-top: 15px; width: auto; font-size: 12px;"><?php _e('Sample Button', 'board'); ?></button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($set_tab == 'logs') : ?>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h4><?php _e('Audit Logs', 'board'); ?></h4>
                    <input type="text" id="log-search" placeholder="<?php _e('Search logs...', 'board'); ?>" style="padding: 8px; border: 1px solid var(--board-black);">
                </div>
                <table class="board-table" id="audit-logs-table">
                    <thead><tr><th><?php _e('Date', 'board'); ?></th><th><?php _e('Action', 'board'); ?></th><th><?php _e('User', 'board'); ?></th><th><?php _e('Details', 'board'); ?></th></tr></thead>
                    <tbody>
                        <?php
                        global $wpdb;
                        $logs = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}board_logs ORDER BY created_at DESC LIMIT 100");
                        foreach ($logs as $log) :
                            $uinfo = get_userdata($log->user_id);
                            ?>
                            <tr>
                                <td><?php echo $log->created_at; ?></td>
                                <td><strong><?php echo esc_html($log->action); ?></strong></td>
                                <td><?php echo $uinfo ? esc_html($uinfo->display_name) : 'System'; ?></td>
                                <td><?php echo esc_html($log->details); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <?php if ($set_tab == 'backup') : ?>
                <h4><?php _e('Data Portability', 'board'); ?></h4>
                <div style="display: flex; gap: 20px; flex-wrap: wrap; align-items: flex-end;">
                    <button class="board-btn-black" id="board-full-backup-json" style="width: auto;"><?php _e('Full Backup (JSON)', 'board'); ?></button>
                    <form action="<?php echo admin_url('admin-post.php'); ?>" method="post" enctype="multipart/form-data" style="display: inline-flex; gap: 10px; align-items: flex-end;">
                        <?php wp_nonce_field('board_restore_nonce'); ?>
                        <input type="hidden" name="action" value="board_restore_backup">
                        <div>
                            <label style="display: block; font-size: 11px; font-weight: bold; margin-bottom: 5px; text-transform: uppercase;"><?php _e('Restore from File', 'board'); ?></label>
                            <input type="file" name="backup_file" required style="font-size: 11px; padding: 10px; border: 1px solid #ccc;">
                        </div>
                        <button type="submit" class="board-btn-black board-btn-outline" style="width: auto;"><?php _e('Restore Data', 'board'); ?></button>
                    </form>
                </div>
            <?php endif; ?>

            <?php if ($set_tab == 'advanced') : ?>
                <h4><?php _e('Advanced Configuration', 'board'); ?></h4>
                <form id="board-advanced-settings-form">
                    <div class="board-form-field">
                        <label><?php _e('Debug Mode', 'board'); ?></label>
                        <select name="debug_mode">
                            <option value="off"><?php _e('Off', 'board'); ?></option>
                            <option value="on"><?php _e('On (Logging)', 'board'); ?></option>
                        </select>
                    </div>
                    <div class="board-form-field">
                        <label><?php _e('Verification API Endpoint', 'board'); ?></label>
                        <input type="text" value="<?php echo home_url('/wp-json/board/v1/verify'); ?>" disabled>
                    </div>
                    <button type="submit" class="board-btn-black" style="width: auto;"><?php _e('Save Advanced Settings', 'board'); ?></button>
                </form>
            <?php endif; ?>
        <?php endif; ?>

    </main>
</div>

<script>
jQuery(document).ready(function($) {
    $('#open-add-user').on('click', function() { $('#add-user-section').slideDown(); });
    $('#close-add-user').on('click', function() { $('#add-user-section').slideUp(); });
    $('#open-add-program').on('click', function() { $('#add-program-section').slideDown(); });
    $('#close-add-program').on('click', function() { $('#add-program-section').slideUp(); });
    $('#open-add-membership').on('click', function() { $('#add-membership-section').slideDown(); });
    $('#close-add-membership').on('click', function() { $('#add-membership-section').slideUp(); });
    $('#open-add-exam').on('click', function() { $('#add-exam-section').slideDown(); });
    $('#close-add-exam').on('click', function() { $('#add-exam-section').slideUp(); });
    $('#open-generate-cert').on('click', function() { $('#generate-cert-section').slideDown(); });
    $('#close-generate-cert').on('click', function() { $('#generate-cert-section').slideUp(); });

    $(document).on('click', '.open-link-cert, .open-link-membership', function() {
        var id = $(this).data('id');
        var action = $(this).hasClass('open-link-cert') ? 'board_link_certificate' : 'board_link_membership';
        var dataKey = $(this).hasClass('open-link-cert') ? 'cert_id' : 'membership_id';

        var userId = prompt("Enter the User ID to link to this record:");
        if (userId) {
            var postData = { action: action, nonce: board_ajax.nonce, user_id: userId };
            postData[dataKey] = id;
            $.post(board_ajax.ajax_url, postData, function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error linking record.');
                }
            });
        }
    });
});
</script>
