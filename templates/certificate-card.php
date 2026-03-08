<?php
if (!defined('ABSPATH')) {
    exit;
}

$serial = get_post_meta($cert->ID, 'serial_number', true);
$type = get_post_meta($cert->ID, 'cert_type', true);
$status = get_post_meta($cert->ID, 'cert_status', true) ?: 'active';
?>

<div class="board-program-card" style="border: 1px solid var(--board-black); padding: 20px; position: relative;">
    <h4 style="margin: 0 0 10px 0;"><?php echo esc_html($cert->post_title); ?></h4>
    <p style="font-size: 12px; margin-bottom: 5px;"><strong><?php _e('Type:', 'board'); ?></strong> <?php echo esc_html($type); ?></p>
    <p style="font-size: 13px; font-family: monospace;"><?php echo esc_html($serial); ?></p>

    <div style="margin-top: 15px; display: flex; justify-content: space-between; align-items: center;">
        <span style="font-size: 11px; text-transform: uppercase; color: <?php echo ($status == 'active' ? 'green' : 'red'); ?>;">
            <?php echo esc_html($status); ?>
        </span>
        <a href="<?php echo home_url("/certificate/{$serial}"); ?>" class="board-btn-black" style="width: auto; padding: 5px 15px; font-size: 11px; text-decoration: none;">
            <?php _e('View Full Details', 'board'); ?>
        </a>
    </div>
</div>
