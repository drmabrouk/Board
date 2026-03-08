<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="board-container">
    <div style="text-align: center; margin-bottom: 50px;">
        <h1><?php _e('Global Sports Health Board (GSHB)', 'board'); ?></h1>
        <p><?php _e('Professional Management System', 'board'); ?></p>
    </div>

    <div class="board-auth-box" style="max-width: 900px; text-align: left;">
        <h2><?php _e('About the GSHB Board Plugin', 'board'); ?></h2>
        <p><?php _e('The "Board" plugin is a high-end, monochromatic management solution designed to provide a unified experience for GSHB staff and members. It handles the entire lifecycle of professional certifications, from initial registration and membership requests to exam assessments and secure certificate verification.', 'board'); ?></p>

        <h3><?php _e('Key Features:', 'board'); ?></h3>
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

        <h3 style="margin-top: 30px;"><?php _e('Integration & Usage', 'board'); ?></h3>
        <p><?php _e('The system is fully modular and responsive. Use the following shortcodes to integrate GSHB components into your theme:', 'board'); ?></p>
        <p><code>[board_main]</code> - <?php _e('This Hub Page', 'board'); ?></p>
        <p><code>[board_registration]</code> - <?php _e('Registration & Login Box', 'board'); ?></p>
        <p><code>[board_mb]</code> - <?php _e('Member Account Dashboard', 'board'); ?></p>
        <p><code>[board_programs]</code> - <?php _e('Professional Programs & Courses', 'board'); ?></p>
        <p><code>[board_verify]</code> - <?php _e('Public Verification Portal', 'board'); ?></p>
        <p><code>[board_members]</code> - <?php _e('Certified Members Directory', 'board'); ?></p>
    </div>
</div>
