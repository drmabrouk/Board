<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="board-container">
    <div class="board-auth-box">
        <?php if ($logo_url = get_option('board_logo_url')) : ?>
            <img src="<?php echo esc_url($logo_url); ?>" style="max-height: 60px; margin-bottom: 20px;">
        <?php endif; ?>

        <!-- Login Form -->
        <div id="board-login-view" class="board-auth-view">
            <h2><?php _e('Login', 'board'); ?></h2>
            <form id="board-auth-form" data-action="board_login">
                <div class="board-form-field">
                    <label><?php _e('Username or Email', 'board'); ?></label>
                    <input type="text" name="username" placeholder="johndoe@example.com" required>
                </div>
                <div class="board-form-field">
                    <label><?php _e('Password', 'board'); ?></label>
                    <input type="password" name="password" placeholder="••••••••" required>
                    <span class="toggle-password"><?php _e('Show', 'board'); ?></span>
                </div>
                <button type="submit" class="board-btn-black"><?php _e('Login', 'board'); ?></button>
                <div class="board-auth-toggle">
                    <a data-target="board-register-view"><?php _e('Create Account', 'board'); ?></a> |
                    <a data-target="board-reset-view"><?php _e('Forgot Password?', 'board'); ?></a>
                </div>
            </form>
        </div>

        <!-- Registration Form -->
        <div id="board-register-view" class="board-auth-view" style="display: none;">
            <h2><?php _e('Register', 'board'); ?></h2>
            <form id="board-auth-form-reg" data-action="board_register">
                <div class="board-form-field">
                    <label><?php _e('Username', 'board'); ?></label>
                    <input type="text" name="username" placeholder="johndoe" required>
                </div>
                <div class="board-form-field">
                    <label><?php _e('Email Address', 'board'); ?></label>
                    <input type="email" name="email" placeholder="john@example.com" required>
                </div>
                <div class="board-form-field">
                    <label><?php _e('Password', 'board'); ?></label>
                    <input type="password" name="password" placeholder="••••••••" required>
                    <span class="toggle-password"><?php _e('Show', 'board'); ?></span>
                </div>
                <button type="submit" class="board-btn-black"><?php _e('Register', 'board'); ?></button>
                <div class="board-auth-toggle">
                    <a data-target="board-login-view"><?php _e('Back to Login', 'board'); ?></a>
                </div>
            </form>
        </div>

        <!-- Password Reset Form -->
        <div id="board-reset-view" class="board-auth-view" style="display: none;">
            <h2><?php _e('Reset Password', 'board'); ?></h2>
            <form id="board-auth-form-reset" data-action="board_reset">
                <div class="board-form-field">
                    <label><?php _e('Username or Email', 'board'); ?></label>
                    <input type="text" name="username" placeholder="johndoe@example.com" required>
                </div>
                <p style="font-size: 13px; margin-bottom: 20px; color: var(--board-grey-dark);"><?php _e('Enter your username or email address and we will send you a password reset link.', 'board'); ?></p>
                <button type="submit" class="board-btn-black"><?php _e('Send Link', 'board'); ?></button>
                <div class="board-auth-toggle">
                    <a data-target="board-login-view"><?php _e('Back to Login', 'board'); ?></a>
                </div>
            </form>
        </div>

    </div>
</div>
