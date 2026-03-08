<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="board-container">
    <div style="text-align: center; margin-bottom: 50px;">
        <h1><?php _e('GSHB Board Plugin Information', 'board'); ?></h1>
        <p><?php _e('Professional Management System for Global Sports Health Board', 'board'); ?></p>
    </div>

    <div class="board-auth-box" style="max-width: 900px; text-align: left;">
        <h2><?php _e('Plugin Description', 'board'); ?></h2>
        <p><?php _e('The "Board" plugin is a comprehensive, monochromatic management solution designed specifically for GSHB. It automates user registration, membership lifecycle, exam assignments, and certification verification.', 'board'); ?></p>

        <h3 style="margin-top: 30px;"><?php _e('Primary Shortcode', 'board'); ?></h3>
        <p><?php _e('Use the following shortcode to render the main user hub:', 'board'); ?></p>
        <code style="background: #eee; padding: 5px 10px; display: inline-block;">[board_main]</code>

        <h3 style="margin-top: 30px;"><?php _e('Managed Pages', 'board'); ?></h3>
        <ul>
            <li><a href="<?php echo home_url('/registration'); ?>"><?php _e('Login / Registration', 'board'); ?></a></li>
            <li><a href="<?php echo home_url('/cp'); ?>"><?php _e('Management Control Panel', 'board'); ?></a></li>
            <li><a href="<?php echo home_url('/mb'); ?>"><?php _e('Member Account', 'board'); ?></a></li>
            <li><a href="<?php echo home_url('/qb'); ?>"><?php _e('Exams Portal', 'board'); ?></a></li>
            <li><a href="<?php echo home_url('/verify'); ?>"><?php _e('Verification Gateway', 'board'); ?></a></li>
            <li><a href="<?php echo home_url('/members'); ?>"><?php _e('Certified Members Directory', 'board'); ?></a></li>
            <li><a href="<?php echo home_url('/programs'); ?>"><?php _e('Professional Programs', 'board'); ?></a></li>
        </ul>
    </div>
</div>
