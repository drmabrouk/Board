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
                    <input type="text" name="username" placeholder="<?php _e('Username or Email', 'board'); ?>" required>
                </div>
                <div class="board-form-field">
                    <input type="password" name="password" placeholder="<?php _e('Password', 'board'); ?>" required>
                    <span class="toggle-password"><?php _e('Show', 'board'); ?></span>
                </div>
                <button type="submit" class="board-btn-black"><?php _e('Sign In', 'board'); ?></button>
                <div class="board-auth-toggle">
                    <a data-target="board-register-view"><?php _e('Create Professional Account', 'board'); ?></a> |
                    <a data-target="board-reset-view"><?php _e('Recover Password', 'board'); ?></a>
                </div>
            </form>
        </div>

        <!-- Multi-Step Registration Form -->
        <div id="board-register-view" class="board-auth-view" style="display: none;">
            <h2 style="margin-bottom: 30px;"><?php _e('Professional Registration', 'board'); ?></h2>

            <form id="board-auth-form-reg" data-action="board_register">
                <!-- Step 1: Personal Information -->
                <div class="reg-step" id="reg-step-1">
                    <h3 style="font-size: 14px; text-transform: uppercase; color: var(--board-grey-600); margin-bottom: 20px;"><?php _e('Step 1: Personal Identity', 'board'); ?></h3>
                    <div class="board-form-field">
                        <input type="text" name="full_name" placeholder="<?php _e('Full Legal Name', 'board'); ?>" required>
                    </div>
                    <div class="board-form-field">
                        <input type="text" name="username" placeholder="<?php _e('Desired Username', 'board'); ?>" required>
                    </div>
                    <div class="board-form-field">
                        <input type="email" name="email" placeholder="<?php _e('Professional Email Address', 'board'); ?>" required>
                    </div>
                    <button type="button" class="board-btn-black reg-next" data-next="2"><?php _e('Continue', 'board'); ?></button>
                </div>

                <!-- Step 2: Credentials & Identity -->
                <div class="reg-step" id="reg-step-2" style="display: none;">
                    <h3 style="font-size: 14px; text-transform: uppercase; color: var(--board-grey-600); margin-bottom: 20px;"><?php _e('Step 2: Credentials', 'board'); ?></h3>
                    <div class="board-form-field">
                        <input type="password" name="password" placeholder="<?php _e('Secure Password', 'board'); ?>" required>
                        <span class="toggle-password"><?php _e('Show', 'board'); ?></span>
                    </div>
                    <div class="board-form-field">
                        <input type="text" name="palestinian_id" placeholder="<?php _e('Palestinian/International ID (Optional)', 'board'); ?>">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 10px;">
                        <button type="button" class="board-btn-black board-btn-outline reg-prev" data-prev="1"><?php _e('Back', 'board'); ?></button>
                        <button type="button" class="board-btn-black" id="send-reg-otp"><?php _e('Verify Email', 'board'); ?></button>
                    </div>
                </div>

                <!-- Step 3: Email Verification (OTP) -->
                <div class="reg-step" id="reg-step-3" style="display: none;">
                    <h3 style="font-size: 14px; text-transform: uppercase; color: var(--board-grey-600); margin-bottom: 20px;"><?php _e('Step 3: Authentication', 'board'); ?></h3>
                    <p style="font-size: 13px; color: var(--board-grey-700); margin-bottom: 20px;"><?php _e('We have sent a 6-digit verification code to your email. Please enter it below to finalize your account.', 'board'); ?></p>
                    <div class="board-form-field">
                        <input type="text" name="reg_otp" maxlength="6" pattern="\d{6}" placeholder="<?php _e('Enter OTP Code', 'board'); ?>" required style="text-align: center; font-size: 24px; letter-spacing: 8px;">
                    </div>
                    <button type="submit" class="board-btn-black"><?php _e('Complete Registration', 'board'); ?></button>
                </div>

                <div class="board-auth-toggle" style="margin-top: 30px;">
                    <a data-target="board-login-view"><?php _e('Already have an account? Sign In', 'board'); ?></a>
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
                <p style="font-size: 13px; margin-bottom: 20px; color: var(--board-grey-dark);"><?php _e('Enter your username or email address and we will send you a 6-digit OTP code.', 'board'); ?></p>
                <button type="submit" class="board-btn-black"><?php _e('Get OTP Code', 'board'); ?></button>
                <div class="board-auth-toggle">
                    <a data-target="board-login-view"><?php _e('Back to Login', 'board'); ?></a>
                </div>
            </form>

            <form id="board-auth-form-otp" data-action="board_verify_otp" style="display: none;">
                <div class="board-form-field">
                    <label><?php _e('Enter 6-Digit OTP', 'board'); ?></label>
                    <input type="text" name="otp" maxlength="6" pattern="\d{6}" placeholder="123456" required style="text-align: center; font-size: 24px; letter-spacing: 5px;">
                    <input type="hidden" name="username">
                </div>
                <button type="submit" class="board-btn-black"><?php _e('Verify OTP', 'board'); ?></button>
            </form>

            <form id="board-auth-form-new-pass" data-action="board_reset_password_final" style="display: none;">
                <div class="board-form-field">
                    <label><?php _e('New Password', 'board'); ?></label>
                    <input type="password" name="password" required>
                    <span class="toggle-password"><?php _e('Show', 'board'); ?></span>
                </div>
                <input type="hidden" name="username">
                <input type="hidden" name="otp">
                <button type="submit" class="board-btn-black"><?php _e('Update Password', 'board'); ?></button>
            </form>
        </div>

    </div>
</div>
