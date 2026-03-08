<?php
if (!defined('ABSPATH')) {
    exit;
}

$user = wp_get_current_user();
?>

<div class="board-container">
    <div class="board-cp-header" style="margin-bottom: 30px;">
        <div class="board-cp-logo">
            <strong>GSHB</strong> <?php _e('Member Account', 'board'); ?>
        </div>
        <div class="board-cp-user">
            <?php printf(__('Welcome, %s', 'board'), $user->display_name); ?>
        </div>
    </div>

    <div class="board-cp-layout">
        <aside class="board-cp-sidebar">
            <ul>
                <li><a href="#"><?php _e('My Profile', 'board'); ?></a></li>
                <li><a href="#"><?php _e('My Requests', 'board'); ?></a></li>
                <li><a href="#"><?php _e('My Exams', 'board'); ?></a></li>
                <li><a href="#"><?php _e('Certifications', 'board'); ?></a></li>
                <li><a href="<?php echo home_url('/cm-request'); ?>"><?php _e('Upgrade Membership', 'board'); ?></a></li>
                <li><a href="<?php echo wp_logout_url(home_url('/registration')); ?>"><?php _e('Logout', 'board'); ?></a></li>
            </ul>
        </aside>

        <main class="board-cp-main" style="padding: 30px;">
            <h2><?php _e('Account Overview', 'board'); ?></h2>

            <div style="background: var(--board-grey); padding: 20px; border: 1px solid var(--board-border); margin-bottom: 30px;">
                <p><strong><?php _e('Name:', 'board'); ?></strong> <?php echo $user->display_name; ?></p>
                <p><strong><?php _e('Email:', 'board'); ?></strong> <?php echo $user->user_email; ?></p>
                <p><strong><?php _e('Member Since:', 'board'); ?></strong> <?php echo date('F j, Y', strtotime($user->user_registered)); ?></p>
                <p><strong><?php _e('Status:', 'board'); ?></strong>
                    <?php
                    if (Board_Roles::is_certified_member()) {
                        echo '<span style="color: green; font-weight: bold;">' . __('Certified Member', 'board') . '</span>';
                    } else {
                        echo __('Regular Member', 'board');
                    }
                    ?>
                </p>
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
                    <tr>
                        <td colspan="4" style="text-align: center;"><?php _e('No active requests.', 'board'); ?></td>
                    </tr>
                </tbody>
            </table>
        </main>
    </div>
</div>
