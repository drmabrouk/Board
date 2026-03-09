<?php
if (!defined('ABSPATH')) exit;

use GSHB\Board\Database\Manager as DB;

$fellows = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}board_fellowships WHERE status = 'approved' ORDER BY updated_at DESC");
?>

<div class="board-container">
    <div style="text-align: center; margin-bottom: 50px;">
        <h2 style="font-size: 32px; font-weight: 800; letter-spacing: -1px;"><?php _e('Directory of Fellows', 'board'); ?></h2>
        <p style="font-size: 18px; color: #666; max-width: 700px; margin: 20px auto;"><?php _e('Recognizing individuals who have demonstrated exceptional experience, achievements, and contributions to the field of Global Sports Health.', 'board'); ?></p>
        <div style="width: 60px; height: 3px; background: black; margin: 0 auto;"></div>
    </div>

    <div class="board-programs-grid">
        <?php if (!empty($fellows)) : ?>
            <?php foreach ($fellows as $f) : ?>
                <div class="board-program-card" style="text-align: center; padding: 40px;">
                    <div style="margin-bottom: 20px;">
                        <span class="dashicons dashicons-awards" style="font-size: 40px; width: 40px; height: 40px; color: #000;"></span>
                    </div>
                    <h3 style="margin-bottom: 5px; font-size: 20px;"><?php echo esc_html($f->full_name); ?></h3>
                    <p style="text-transform: uppercase; font-size: 11px; font-weight: 700; color: #999; letter-spacing: 1px; margin-bottom: 20px;"><?php _e('Fellow of the GSHB', 'board'); ?></p>

                    <div style="font-size: 13px; color: #444; margin-bottom: 25px; line-height: 1.6;">
                        <strong><?php _e('Key Expertise:', 'board'); ?></strong><br>
                        <?php echo esc_html(wp_trim_words($f->skills, 20)); ?>
                    </div>

                    <a href="<?php echo home_url('/verify?verify_code=GSHB-FEL-' . date('Y', strtotime($f->updated_at)) . '-' . str_pad($f->id, 4, '0', STR_PAD_LEFT)); ?>" class="board-btn-black board-btn-small board-btn-outline" style="width: auto; padding: 8px 25px;"><?php _e('Verify Credentials', 'board'); ?></a>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p style="grid-column: 1/-1; text-align: center; color: #999; py-50;"><?php _e('The directory is currently being updated. Please check back later.', 'board'); ?></p>
        <?php endif; ?>
    </div>
</div>
