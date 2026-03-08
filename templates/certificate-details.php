<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Variables available from board.php: $cert
 */
$serial = get_post_meta($cert->ID, 'serial_number', true);
$type = get_post_meta($cert->ID, 'cert_type', true);
$status = get_post_meta($cert->ID, 'cert_status', true) ?: 'active';
$issue_date = get_post_meta($cert->ID, 'issue_date', true);
$user_id = get_post_meta($cert->ID, 'user_id', true);
$uinfo = get_userdata($user_id);

$qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode(home_url("/certificate/{$serial}"));

// Access Control
$current_user_id = get_current_user_id();
$is_admin = Board_Roles::can_access_cp($current_user_id);
$is_owner = ($current_user_id == $user_id);

get_header();
?>

<div class="board-container" style="padding: 50px 20px;">
    <div class="board-program-card" style="max-width: 900px; margin: 0 auto; border: 2px solid var(--board-black); padding: 40px;">

        <!-- Header Section -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid var(--board-black); padding-bottom: 30px; margin-bottom: 30px;">
            <div>
                <h1 style="margin: 0; font-size: 28px;"><?php echo esc_html($cert->post_title); ?></h1>
                <p style="font-size: 16px; margin-top: 10px;"><strong><?php _e('Type:', 'board'); ?></strong> <?php echo esc_html($type); ?></p>
                <div style="display: inline-block; padding: 5px 15px; background: <?php echo ($status == 'active' ? 'green' : 'red'); ?>; color: white; border-radius: 3px; font-size: 12px; text-transform: uppercase; margin-top: 10px;">
                    <?php echo esc_html($status); ?>
                </div>
            </div>
            <div style="text-align: right;">
                <strong><?php echo esc_html(get_option('board_org_name', 'GSHB')); ?></strong><br><?php _e('Global Sports Health Board', 'board'); ?>
            </div>
        </div>

        <!-- Metadata Section -->
        <div style="display: grid; grid-template-columns: 1fr <?php echo ($is_admin || $is_owner) ? '200px' : '0'; ?>; gap: 40px;">
            <div>
                <h3><?php _e('Certificate Details', 'board'); ?></h3>
                <p><strong><?php _e('Serial Number:', 'board'); ?></strong> <code><?php echo esc_html($serial); ?></code></p>
                <p><strong><?php _e('Issue Date:', 'board'); ?></strong> <?php echo esc_html($issue_date); ?></p>

                <h3 style="margin-top: 30px;"><?php _e('Associated Member', 'board'); ?></h3>
                <p><strong><?php _e('Name:', 'board'); ?></strong> <?php echo esc_html($uinfo->display_name); ?></p>

                <?php if ($is_admin || $is_owner) : ?>
                    <p><strong><?php _e('Membership ID:', 'board'); ?></strong> <?php echo esc_html(get_user_meta($user_id, 'verification_code', true)); ?></p>
                    <p><strong><?php _e('Country:', 'board'); ?></strong> <?php echo esc_html(get_user_meta($user_id, 'country', true)); ?></p>
                    <p><strong><?php _e('Specialty:', 'board'); ?></strong> <?php echo esc_html(get_user_meta($user_id, 'specialty', true)); ?></p>
                <?php else : ?>
                    <p style="color: grey; font-style: italic;"><?php _e('Additional member data is restricted to authorized users.', 'board'); ?></p>
                <?php endif; ?>
            </div>
            <div style="text-align: center;">
                <img src="<?php echo esc_url($qr_url); ?>" alt="QR Code" style="border: 1px solid var(--board-black); padding: 10px; background: white;">
                <p style="font-size: 11px; margin-top: 10px;"><?php _e('Scan to verify authenticity', 'board'); ?></p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div style="margin-top: 50px; display: flex; gap: 15px; border-top: 1px solid var(--board-black); padding-top: 30px;">
            <?php if ($is_admin || $is_owner) : ?>
                <button class="board-btn-black" onclick="window.print()" style="width: auto;"><?php _e('Print Certificate', 'board'); ?></button>
            <?php endif; ?>
            <button class="board-btn-black" id="copy-serial" data-serial="<?php echo esc_attr($serial); ?>" style="width: auto; background: grey;"><?php _e('Copy Serial Code', 'board'); ?></button>
            <a href="<?php echo home_url('/verify'); ?>" class="board-btn-black" style="width: auto; text-decoration: none;"><?php _e('Verification Portal', 'board'); ?></a>
        </div>

        <!-- Audit History -->
        <div style="margin-top: 40px; font-size: 12px; color: grey;">
            <p><?php printf(__('Recorded on: %s', 'board'), get_the_date('Y-m-d H:i', $cert->ID)); ?></p>
            <p><?php _e('This certificate is a property of GSHB and is issued for professional recognition.', 'board'); ?></p>
        </div>

    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('#copy-serial').on('click', function() {
        var serial = $(this).data('serial');
        navigator.clipboard.writeText(serial).then(function() {
            alert('<?php _e('Serial code copied to clipboard!', 'board'); ?>');
        });
    });
});
</script>

<?php get_footer(); ?>
