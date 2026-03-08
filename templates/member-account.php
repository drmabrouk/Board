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

            <h3><?php _e('Exam Results', 'board'); ?></h3>
            <table class="board-table" style="margin-bottom: 30px;">
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
                        foreach ($completed as $res) :
                            $exam_title = get_the_title($res['exam_id']);
                            ?>
                            <tr>
                                <td><?php echo $exam_title; ?></td>
                                <td><?php echo $res['date']; ?></td>
                                <td><?php echo $res['score']; ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="3" style="text-align: center;"><?php _e('No exams completed yet.', 'board'); ?></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <h3><?php _e('My Certificates', 'board'); ?></h3>
            <table class="board-table" style="margin-bottom: 30px;">
                <thead>
                    <tr>
                        <th><?php _e('Certificate', 'board'); ?></th>
                        <th><?php _e('Serial Number', 'board'); ?></th>
                        <th><?php _e('Status', 'board'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $user_certs = get_posts(array(
                        'post_type' => 'board_certificate',
                        'meta_key' => 'user_id',
                        'meta_value' => $user->ID
                    ));
                    if (!empty($user_certs)) :
                        foreach ($user_certs as $cert) :
                            $status = get_post_meta($cert->ID, 'cert_status', true) ?: 'active';
                            ?>
                            <tr>
                                <td><?php echo $cert->post_title; ?></td>
                                <td><code><?php echo get_post_meta($cert->ID, 'serial_number', true); ?></code></td>
                                <td style="text-transform: capitalize;"><?php echo $status; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="3" style="text-align: center;"><?php _e('No certificates issued yet.', 'board'); ?></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

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
                    $requests = get_posts(array(
                        'post_type' => 'board_request',
                        'meta_key'  => 'user_id',
                        'meta_value' => $user->ID
                    ));
                    if (!empty($requests)) :
                        foreach ($requests as $req) : ?>
                            <tr>
                                <td>#<?php echo $req->ID; ?></td>
                                <td><?php _e('Membership', 'board'); ?></td>
                                <td><?php echo get_the_date('', $req->ID); ?></td>
                                <td><?php echo ucfirst(get_post_meta($req->ID, 'status', true)); ?></td>
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
