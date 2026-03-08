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
        <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid var(--board-black); padding-bottom: 30px; margin-bottom: 30px;">
            <div>
                <h1 style="margin: 0; font-size: 32px; letter-spacing: -1px;"><?php echo esc_html($cert->post_title); ?></h1>
                <p style="font-size: 18px; margin-top: 10px; opacity: 0.8;"><strong><?php _e('Official Certification', 'board'); ?></strong></p>
                <div style="display: inline-block; padding: 6px 20px; background: <?php echo ($status == 'active' ? 'var(--board-black)' : 'var(--board-grey-dark)'); ?>; color: white; font-size: 12px; font-weight: bold; text-transform: uppercase; margin-top: 10px; letter-spacing: 1px;">
                    <?php echo esc_html($status); ?>
                </div>
            </div>
            <div style="text-align: right;">
                <strong style="font-size: 18px;"><?php echo esc_html(get_option('board_org_name', 'GSHB')); ?></strong><br>
                <span style="font-size: 13px; text-transform: uppercase; letter-spacing: 1px;"><?php _e('Professional Accreditation', 'board'); ?></span>
            </div>
        </div>

        <!-- Metadata Section -->
        <div style="display: grid; grid-template-columns: 1fr <?php echo ($is_admin || $is_owner) ? '200px' : '0'; ?>; gap: 50px;">
            <div>
                <div style="margin-bottom: 30px;">
                    <h3 style="font-size: 14px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 5px;"><?php _e('Certificate Details', 'board'); ?></h3>
                    <p style="margin-bottom: 10px;"><strong><?php _e('Type:', 'board'); ?></strong> <?php echo esc_html($type); ?></p>
                    <p style="margin-bottom: 10px;"><strong><?php _e('Serial Number:', 'board'); ?></strong> <code style="background: #f4f4f4; padding: 3px 8px; font-size: 16px;"><?php echo esc_html($serial); ?></code></p>
                    <p><strong><?php _e('Issue Date:', 'board'); ?></strong> <?php echo esc_html($issue_date); ?></p>
                </div>

                <div>
                    <h3 style="font-size: 14px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 5px;"><?php _e('Recipient Information', 'board'); ?></h3>
                    <p style="font-size: 18px; font-weight: 700; margin-bottom: 10px;"><?php echo esc_html($uinfo->display_name); ?></p>

                    <?php if ($is_admin || $is_owner) : ?>
                        <p style="margin-bottom: 5px;"><strong><?php _e('Membership ID:', 'board'); ?></strong> <?php echo esc_html(get_user_meta($user_id, 'verification_code', true)); ?></p>
                        <p style="margin-bottom: 5px;"><strong><?php _e('Country:', 'board'); ?></strong> <?php echo esc_html(get_user_meta($user_id, 'country', true)); ?></p>
                        <p><strong><?php _e('Specialty:', 'board'); ?></strong> <?php echo esc_html(get_user_meta($user_id, 'specialty', true)); ?></p>
                    <?php else : ?>
                        <p style="color: grey; font-style: italic; font-size: 13px;"><?php _e('Private member data is restricted to authorized entities.', 'board'); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <div style="text-align: center;">
                <img src="<?php echo esc_url($qr_url); ?>" alt="QR Code" style="border: 1px solid var(--board-black); padding: 15px; background: white; margin-bottom: 15px;">
                <p style="font-size: 11px; color: grey; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 25px;"><?php _e('Scan for digital verification', 'board'); ?></p>

                <div style="border-top: 1px solid #eee; padding-top: 20px;">
                    <img src="https://bwipjs-api.metafloor.com/?bcid=code128&text=<?php echo urlencode($serial); ?>&scale=2&rotate=N&includetext=true" alt="Barcode" style="max-width: 100%;">
                    <p style="font-size: 10px; color: grey; text-transform: uppercase; letter-spacing: 1px; margin-top: 5px;"><?php _e('Standardized Barcode', 'board'); ?></p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div style="margin-top: 50px; display: flex; gap: 15px; border-top: 1px solid var(--board-black); padding-top: 30px;" class="hide-on-print">
            <?php if ($is_admin || $is_owner) : ?>
                <button class="board-btn-black" onclick="window.print()" style="width: auto;"><span class="dashicons dashicons-printer" style="margin-right: 5px;"></span> <?php _e('Print Certificate', 'board'); ?></button>
            <?php endif; ?>
            <button class="board-btn-black board-btn-outline" id="copy-serial" data-serial="<?php echo esc_attr($serial); ?>" style="width: auto;"><span class="dashicons dashicons-admin-page" style="margin-right: 5px;"></span> <?php _e('Copy Serial Code', 'board'); ?></button>
            <a href="<?php echo home_url('/verify'); ?>" class="board-btn-black board-btn-outline" style="width: auto;"><span class="dashicons dashicons-shield" style="margin-right: 5px;"></span> <?php _e('Verification Portal', 'board'); ?></a>
        </div>

        <!-- Audit History -->
        <div style="margin-top: 40px; font-size: 12px; color: grey;">
            <p><?php printf(__('Recorded on: %s', 'board'), get_the_date('Y-m-d H:i', $cert->ID)); ?></p>
            <p><?php _e('This certificate is a property of GSHB and is issued for professional recognition.', 'board'); ?></p>
        </div>

    </div>
</div>


<?php get_footer(); ?>
