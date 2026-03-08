<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Custom Table Integration
 */
$certified_members = array();
$db_members = \GSHB\Board\Database\Manager::get_memberships('active');

if (!empty($db_members)) {
    foreach ($db_members as $m) {
        $user = get_userdata($m->user_id);
        if ($user) {
            $certified_members[] = array(
                'name' => $user->display_name,
                'id' => get_user_meta($user->ID, 'verification_code', true) ?: 'N/A',
                'country' => $m->country ?: 'N/A',
                'specialty' => $m->specialty ?: 'N/A',
                'expiry' => $m->expiry_date
            );
        }
    }
}

?>

<div class="board-container">
    <div style="text-align: center; margin-bottom: 40px;">
        <h2><?php _e('Certified Members Directory', 'board'); ?></h2>
        <p><?php _e('List of globally approved GSHB certified members.', 'board'); ?></p>
    </div>

    <div style="margin-bottom: 30px; max-width: 800px; margin-left: auto; margin-right: auto;">
        <label style="display: block; font-size: 11px; font-weight: bold; margin-bottom: 5px; text-transform: uppercase; text-align: left;"><?php _e('Search Directory', 'board'); ?></label>
        <input type="text" id="directory-search" placeholder="<?php _e('Search by name, ID or specialty...', 'board'); ?>" style="width: 100%; padding: 15px; border: 1px solid var(--board-black);">
    </div>

    <table class="board-table" id="directory-table">
        <thead>
            <tr>
                <th><?php _e('Name', 'board'); ?></th>
                <th><?php _e('Membership ID', 'board'); ?></th>
                <th><?php _e('Country', 'board'); ?></th>
                <th><?php _e('Specialty', 'board'); ?></th>
                <th><?php _e('Status', 'board'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($certified_members)) : ?>
                <?php foreach ($certified_members as $member) : ?>
                    <tr>
                        <td><strong><?php echo esc_html($member['name']); ?></strong></td>
                        <td><?php echo esc_html($member['id']); ?></td>
                        <td><?php echo esc_html($member['country']); ?></td>
                        <td><?php echo esc_html($member['specialty']); ?></td>
                        <td>
                            <?php
                            if ($member['expiry'] && strtotime($member['expiry']) > time()) {
                                echo '<span class="status-badge status-completed">' . __('Active', 'board') . '</span>';
                            } else {
                                echo '<span class="status-badge status-expired">' . __('Expired', 'board') . '</span>';
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="5" style="text-align: center;"><?php _e('No certified members found.', 'board'); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

