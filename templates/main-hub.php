<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="board-container">
    <div style="text-align: center; margin-bottom: 50px;">
        <h1><?php _e('GSHB Management Hub', 'board'); ?></h1>
        <p><?php _e('Global Sports Health Board professional management system.', 'board'); ?></p>
    </div>

    <div class="board-auth-box" style="max-width: 800px; text-align: left;">
        <h3><?php _e('Plugin Overview', 'board'); ?></h3>
        <p><?php _e('The Board plugin provides a complete ecosystem for GSHB members and administrators. Features include:', 'board'); ?></p>
        <ul>
            <li><?php _e('Custom GSHB Roles and Permissions', 'board'); ?></li>
            <li><?php _e('Unified Registration, Login, and Password Reset', 'board'); ?></li>
            <li><?php _e('Centralized Control Panel for Admins and Managers', 'board'); ?></li>
            <li><?php _e('Dynamic Program and Exam Management', 'board'); ?></li>
            <li><?php _e('Real-time Certification Verification Portal', 'board'); ?></li>
            <li><?php _e('Automated Membership Lifecycle Management', 'board'); ?></li>
        </ul>

        <h3 style="margin-top: 30px;"><?php _e('Quick Links', 'board'); ?></h3>
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
            <a href="<?php echo home_url('/registration'); ?>" class="board-btn-black" style="text-decoration: none; text-align: center;"><?php _e('Login / Register', 'board'); ?></a>
            <a href="<?php echo home_url('/mb'); ?>" class="board-btn-black" style="text-decoration: none; text-align: center;"><?php _e('Member Account', 'board'); ?></a>
            <a href="<?php echo home_url('/programs'); ?>" class="board-btn-black" style="text-decoration: none; text-align: center;"><?php _e('Programs & Courses', 'board'); ?></a>
            <a href="<?php echo home_url('/verify'); ?>" class="board-btn-black" style="text-decoration: none; text-align: center;"><?php _e('Verify Certificate', 'board'); ?></a>
            <a href="<?php echo home_url('/members'); ?>" class="board-btn-black" style="text-decoration: none; text-align: center;"><?php _e('Members Directory', 'board'); ?></a>
            <a href="<?php echo home_url('/cp'); ?>" class="board-btn-black" style="text-decoration: none; text-align: center;"><?php _e('Control Panel (Staff)', 'board'); ?></a>
        </div>

        <h3 style="margin-top: 30px;"><?php _e('User Shortcodes', 'board'); ?></h3>
        <p><code>[board_registration]</code> - <?php _e('Auth Box', 'board'); ?></p>
        <p><code>[board_mb]</code> - <?php _e('Member Profile', 'board'); ?></p>
        <p><code>[board_programs]</code> - <?php _e('Programs Grid', 'board'); ?></p>
        <p><code>[board_verify]</code> - <?php _e('Search Portal', 'board'); ?></p>
    </div>
</div>
