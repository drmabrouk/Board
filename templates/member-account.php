<?php
if (!defined('ABSPATH')) {
    exit;
}

$user = wp_get_current_user();
?>

<div class="board-container">
    <div class="board-cp-header" style="margin-bottom: 30px;">
        <div class="board-cp-logo" style="display: flex; align-items: center; gap: 10px;">
            <?php if ($logo_url = get_option('board_logo_url')) : ?>
                <img src="<?php echo esc_url($logo_url); ?>" style="max-height: 30px;">
            <?php endif; ?>
            <strong><?php echo esc_html(get_option('board_org_name', 'GSHB')); ?></strong> <?php _e('Member Account', 'board'); ?>
        </div>
        <div class="board-cp-user">
            <?php printf(__('Welcome, %s', 'board'), $user->display_name); ?>
        </div>
    </div>

    <div class="board-cp-layout">
        <aside class="board-cp-sidebar">
            <ul>
                <li><a href="#"><span class="dashicons dashicons-admin-users"></span> <?php _e('My Profile', 'board'); ?></a></li>
                <li><a href="#"><span class="dashicons dashicons-email-alt"></span> <?php _e('My Requests', 'board'); ?></a></li>
                <li><a href="#"><span class="dashicons dashicons-clipboard"></span> <?php _e('My Exams', 'board'); ?></a></li>
                <li><a href="#"><span class="dashicons dashicons-awards"></span> <?php _e('Certifications', 'board'); ?></a></li>
                <li><a href="<?php echo home_url('/cm-request'); ?>"><span class="dashicons dashicons-plus"></span> <?php _e('Upgrade Membership', 'board'); ?></a></li>
                <li><a href="<?php echo wp_logout_url(home_url('/registration')); ?>"><span class="dashicons dashicons-logout"></span> <?php _e('Logout', 'board'); ?></a></li>
            </ul>
        </aside>

        <main class="board-cp-main">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 30px;">
                <div>
                    <h2><?php _e('Account Overview', 'board'); ?></h2>
                    <p style="font-size: 14px; color: grey;"><?php _e('Manage your professional certifications and exam progress.', 'board'); ?></p>
                </div>
                <?php if (\GSHB\Board\Core\Roles::is_certified_member()) : ?>
                    <div style="text-align: center; background: #000; color: #fff; padding: 10px 20px; border-radius: 0;">
                        <span class="dashicons dashicons-awards" style="font-size: 30px; width: 30px; height: 30px; display: block; margin: 0 auto 5px;"></span>
                        <small style="text-transform: uppercase; font-weight: 800; font-size: 10px; letter-spacing: 1px;"><?php _e('Certified Professional', 'board'); ?></small>
                    </div>
                <?php endif; ?>
            </div>

            <div class="board-stat-card" style="text-align: left; margin-bottom: 40px; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                <div>
                    <label style="display: block; font-size: 11px; font-weight: bold; text-transform: uppercase; color: grey;"><?php _e('Full Name', 'board'); ?></label>
                    <p style="font-size: 16px; font-weight: 600; margin: 5px 0 0 0;"><?php echo $user->display_name; ?></p>
                </div>
                <div>
                    <label style="display: block; font-size: 11px; font-weight: bold; text-transform: uppercase; color: grey;"><?php _e('Email Address', 'board'); ?></label>
                    <p style="font-size: 16px; font-weight: 600; margin: 5px 0 0 0;"><?php echo $user->user_email; ?></p>
                </div>
                <div>
                    <label style="display: block; font-size: 11px; font-weight: bold; text-transform: uppercase; color: grey;"><?php _e('Membership Progress', 'board'); ?></label>
                    <div style="margin-top: 8px;">
                        <div style="display: flex; justify-content: space-between; font-size: 11px; margin-bottom: 3px;">
                            <span><?php echo \GSHB\Board\Core\Roles::is_certified_member() ? __('Full Access', 'board') : __('Pending Requirements', 'board'); ?></span>
                            <span><?php echo \GSHB\Board\Core\Roles::is_certified_member() ? '100%' : '25%'; ?></span>
                        </div>
                        <div style="height: 6px; background: #eee; border: 1px solid #000;">
                            <div style="height: 100%; width: <?php echo \GSHB\Board\Core\Roles::is_certified_member() ? '100%' : '25%'; ?>; background: #000;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; margin-bottom: 40px;">
                <div>
                    <h3><?php _e('Recent Exam Activity', 'board'); ?></h3>
                    <table class="board-table">
                        <thead>
                            <tr>
                                <th><?php _e('Exam', 'board'); ?></th>
                                <th><?php _e('Date', 'board'); ?></th>
                                <th><?php _e('Score', 'board'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $completed = get_user_meta($user->ID, 'completed_exams', true);
                            if (!empty($completed)) :
                                $completed = array_reverse($completed);
                                $count = 0;
                                foreach ($completed as $res) :
                                    if (++$count > 5) break;
                                    global $wpdb;
                                    $exam_title = $wpdb->get_var($wpdb->prepare("SELECT title FROM {$wpdb->prefix}board_exams WHERE id = %d", $res['exam_id']));
                                    ?>
                                    <tr>
                                        <td><?php echo esc_html($exam_title); ?></td>
                                        <td><?php echo $res['date']; ?></td>
                                        <td><strong><?php echo $res['score']; ?>%</strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr><td colspan="3" style="text-align: center; color: grey;"><?php _e('No exams completed yet.', 'board'); ?></td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div>
                    <h3><?php _e('Upcoming Tasks', 'board'); ?></h3>
                    <div class="board-program-card" style="padding: 20px;">
                        <?php
                        $assigned = get_user_meta($user->ID, 'assigned_exams', true) ?: array();
                        $completed_ids = !empty($completed) ? array_column($completed, 'exam_id') : array();
                        $upcoming = array_diff($assigned, $completed_ids);

                        if (!empty($upcoming)) :
                            foreach (array_slice($upcoming, 0, 3) as $ex_id) :
                                global $wpdb;
                                $ex_data = $wpdb->get_row($wpdb->prepare("SELECT title, due_date FROM {$wpdb->prefix}board_exams WHERE id = %d", $ex_id));
                                ?>
                                <div style="padding-bottom: 10px; margin-bottom: 10px; border-bottom: 1px solid #eee;">
                                    <p style="margin: 0; font-weight: bold; font-size: 13px;"><?php echo esc_html($ex_data->title); ?></p>
                                    <small style="color: grey;"><?php echo $ex_data->due_date ?: __('No due date', 'board'); ?></small>
                                </div>
                            <?php endforeach; ?>
                            <a href="<?php echo home_url('/qb'); ?>" class="board-btn-black board-btn-small" style="width: 100%;"><?php _e('Start Next Exam', 'board'); ?></a>
                        <?php else : ?>
                            <p style="font-size: 13px; color: grey; text-align: center; margin: 20px 0;"><?php _e('All clear! No pending exams.', 'board'); ?></p>
                            <a href="<?php echo home_url('/programs'); ?>" class="board-btn-black board-btn-small" style="width: 100%;"><?php _e('Explore Programs', 'board'); ?></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <h3><?php _e('Earned Credentials', 'board'); ?></h3>
            <div class="board-programs-grid" style="margin-bottom: 40px;">
                <?php
                $user_certs = \GSHB\Board\Database\Manager::get_certificates($user->ID);
                if (!empty($user_certs)) :
                    foreach ($user_certs as $cert) :
                        $status = $cert->status ?: 'active';
                        $serial = $cert->serial_number;
                        ?>
                        <div class="board-program-card" style="padding: 20px; border-top: 5px solid #000;">
                            <h4 style="margin: 0;"><?php echo $cert->title; ?></h4>
                            <p style="font-family: monospace; font-size: 13px; margin: 10px 0;"><?php echo $serial; ?></p>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px;">
                                <span style="font-size: 10px; text-transform: uppercase; font-weight: bold; border-bottom: 1px solid #000;"><?php echo $status; ?></span>
                                <a href="<?php echo home_url("/certificate/{$serial}"); ?>" class="board-btn-black board-btn-small" style="width: auto;"><?php _e('View Full Details', 'board'); ?></a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <div class="board-program-card" style="grid-column: 1 / -1; text-align: center; padding: 40px; border: 1px dashed #ccc;">
                        <span class="dashicons dashicons-awards" style="font-size: 40px; width: 40px; height: 40px; margin-bottom: 10px; color: #ccc;"></span>
                        <p><?php _e('You have not earned any certificates yet.', 'board'); ?></p>
                        <a href="<?php echo home_url('/programs'); ?>" class="board-btn-black board-btn-small" style="display: inline-block; margin-top: 15px;"><?php _e('Browse Programs', 'board'); ?></a>
                    </div>
                <?php endif; ?>
            </div>

            <h3><?php _e('Recent Requests', 'board'); ?></h3>
            <table class="board-table">
                <thead>
                    <tr>
                        <th><?php _e('Request ID', 'board'); ?></th>
                        <th><?php _e('Type', 'board'); ?></th>
                        <th><?php _e('Date', 'board'); ?></th>
                        <th><?php _e('Status', 'board'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    global $wpdb;
                    $requests = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}board_memberships WHERE user_id = %d ORDER BY created_at DESC", $user->ID));
                    if (!empty($requests)) :
                        foreach ($requests as $req) : ?>
                            <tr>
                                <td>#<?php echo $req->id; ?></td>
                                <td><?php _e('Membership', 'board'); ?></td>
                                <td><?php echo $req->created_at; ?></td>
                                <td style="text-transform: capitalize;"><?php echo $req->status; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="4" style="text-align: center;"><?php _e('No active requests.', 'board'); ?></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
</div>
