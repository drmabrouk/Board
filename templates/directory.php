<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Real implementation: Query users with 'certified_member' role
 */
$certified_members = array();
$users_query = get_users(array(
    'role' => 'certified_member',
    'fields' => 'all'
));

if (!empty($users_query)) {
    foreach ($users_query as $user) {
        $certified_members[] = array(
            'name' => $user->display_name,
            'id' => get_user_meta($user->ID, 'verification_code', true) ?: 'N/A',
            'country' => get_user_meta($user->ID, 'country', true) ?: 'N/A',
            'specialty' => get_user_meta($user->ID, 'specialty', true) ?: 'N/A',
            'expiry' => get_user_meta($user->ID, 'membership_expiry_date', true)
        );
    }
}

?>

<div class="board-container">
    <div style="text-align: center; margin-bottom: 40px;">
        <h2><?php _e('Certified Members Directory', 'board'); ?></h2>
        <p><?php _e('List of globally approved GSHB certified members.', 'board'); ?></p>
    </div>

    <div style="margin-bottom: 20px;">
        <input type="text" id="directory-search" placeholder="<?php _e('Search by name, ID or specialty...', 'board'); ?>" style="width: 100%; padding: 12px; border: 1px solid var(--board-black);">
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
                                echo '<span style="color: green; font-weight: bold;">' . __('Active', 'board') . '</span>';
                            } else {
                                echo '<span style="color: grey;">' . __('Expired', 'board') . '</span>';
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

<script>
jQuery(document).ready(function($) {
    $('#directory-search').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('#directory-table tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
});
</script>
