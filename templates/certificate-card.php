<?php
if (!defined('ABSPATH')) {
    exit;
}

$serial = $cert->serial_number;
$type = $cert->type;
$status = $cert->status ?: 'active';
?>

<div class="board-program-card" style="border: 1px solid var(--board-black); padding: 20px; position: relative;">
    <h4 style="margin: 0 0 10px 0;"><?php echo esc_html($cert->title); ?></h4>
    <p style="font-size: 12px; margin-bottom: 5px;"><strong><?php _e('Type:', 'board'); ?></strong> <?php echo esc_html($type); ?></p>
    <p style="font-size: 13px; font-family: monospace;"><?php echo esc_html($serial); ?></p>

    <div style="margin-top: 20px; display: flex; justify-content: space-between; align-items: center;">
        <span style="font-size: 10px; font-weight: bold; text-transform: uppercase; padding: 4px 10px; background: <?php echo ($status == 'active' ? 'var(--board-black)' : 'var(--board-grey)'); ?>; color: <?php echo ($status == 'active' ? 'white' : 'black'); ?>; border: 1px solid var(--board-black);">
            <?php echo esc_html($status); ?>
        </span>
        <a href="<?php echo home_url("/certificate/{$serial}"); ?>" class="board-btn-black board-btn-small" style="text-decoration: none;">
            <?php _e('View Details', 'board'); ?>
        </a>
    </div>
</div>
