<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="board-container">
    <div style="text-align: center; margin-bottom: 50px;">
        <?php if ($logo_url = get_option('board_logo_url')) : ?>
            <img src="<?php echo esc_url($logo_url); ?>" style="max-height: 80px; margin-bottom: 20px;">
        <?php endif; ?>
        <h1><?php echo esc_html(get_option('board_org_name', 'Global Sports Health Board (GSHB)')); ?></h1>
        <p><?php _e('Professional Management System', 'board'); ?></p>
    </div>

    <div class="board-auth-box" style="max-width: 1000px; text-align: left;">
        <div style="display: grid; grid-template-columns: 1fr 300px; gap: 50px;">
            <div>
                <h2><?php _e('About the GSHB Board Plugin', 'board'); ?></h2>
                <p><?php _e('The "Board" plugin is a high-end, monochromatic management solution designed to provide a unified experience for GSHB staff and members. It handles the entire lifecycle of professional certifications, from initial registration and membership requests to exam assessments and secure certificate verification.', 'board'); ?></p>

                <h3 style="margin-top: 30px; border-bottom: 1px solid #eee; padding-bottom: 10px;"><?php _e('Key Features:', 'board'); ?></h3>
                <ul style="padding-left: 20px;">
                    <li style="margin-bottom: 10px;"><?php _e('Custom GSHB Roles and Permissions', 'board'); ?></li>
                    <li style="margin-bottom: 10px;"><?php _e('Unified Registration, Login, and Password Reset', 'board'); ?></li>
                    <li style="margin-bottom: 10px;"><?php _e('Centralized Control Panel for Admins and Managers', 'board'); ?></li>
                    <li style="margin-bottom: 10px;"><?php _e('Dynamic Program and Exam Management', 'board'); ?></li>
                    <li style="margin-bottom: 10px;"><?php _e('Real-time Certification Verification Portal', 'board'); ?></li>
                    <li style="margin-bottom: 10px;"><?php _e('Automated Membership Lifecycle Management', 'board'); ?></li>
                </ul>
            </div>
            <div>
                <h3 style="margin-bottom: 20px;"><?php _e('Quick Access', 'board'); ?></h3>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <a href="<?php echo home_url('/registration'); ?>" class="board-btn-black"><span class="dashicons dashicons-lock"></span> <?php _e('Login / Register', 'board'); ?></a>
                    <a href="<?php echo home_url('/mb'); ?>" class="board-btn-black board-btn-outline"><span class="dashicons dashicons-admin-users"></span> <?php _e('Member Account', 'board'); ?></a>
                    <a href="<?php echo home_url('/programs'); ?>" class="board-btn-black board-btn-outline"><span class="dashicons dashicons-welcome-learn-more"></span> <?php _e('Programs & Courses', 'board'); ?></a>
                    <a href="<?php echo home_url('/verify'); ?>" class="board-btn-black board-btn-outline"><span class="dashicons dashicons-shield-alt"></span> <?php _e('Verify Certificate', 'board'); ?></a>
                    <a href="<?php echo home_url('/members'); ?>" class="board-btn-black board-btn-outline"><span class="dashicons dashicons-groups"></span> <?php _e('Members Directory', 'board'); ?></a>
                    <a href="<?php echo home_url('/cp'); ?>" class="board-btn-black board-btn-outline"><span class="dashicons dashicons-admin-settings"></span> <?php _e('Control Panel', 'board'); ?></a>
                </div>
            </div>
        </div>

        <h3 style="margin-top: 40px; border-top: 1px solid #eee; padding-top: 30px;"><?php _e('Shortcode Integration', 'board'); ?></h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
            <div style="background: #f9f9f9; padding: 15px; border: 1px solid #eee;">
                <code style="display: block; margin-bottom: 5px;">[board_main]</code>
                <small><?php _e('This Hub Page', 'board'); ?></small>
            </div>
            <div style="background: #f9f9f9; padding: 15px; border: 1px solid #eee;">
                <code style="display: block; margin-bottom: 5px;">[board_registration]</code>
                <small><?php _e('Registration & Login Box', 'board'); ?></small>
            </div>
            <div style="background: #f9f9f9; padding: 15px; border: 1px solid #eee;">
                <code style="display: block; margin-bottom: 5px;">[board_mb]</code>
                <small><?php _e('Member Account Dashboard', 'board'); ?></small>
            </div>
            <div style="background: #f9f9f9; padding: 15px; border: 1px solid #eee;">
                <code style="display: block; margin-bottom: 5px;">[board_programs]</code>
                <small><?php _e('Professional Programs & Courses', 'board'); ?></small>
            </div>
            <div style="background: #f9f9f9; padding: 15px; border: 1px solid #eee;">
                <code style="display: block; margin-bottom: 5px;">[board_verify]</code>
                <small><?php _e('Public Verification Portal', 'board'); ?></small>
            </div>
            <div style="background: #f9f9f9; padding: 15px; border: 1px solid #eee;">
                <code style="display: block; margin-bottom: 5px;">[board_members]</code>
                <small><?php _e('Certified Members Directory', 'board'); ?></small>
            </div>
        </div>
    </div>
</div>
