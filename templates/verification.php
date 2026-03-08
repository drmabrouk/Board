<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="board-container">
    <div style="text-align: center; margin-bottom: 40px;">
        <h2><?php _e('Verification Portal', 'board'); ?></h2>
        <p><?php _e('Verify certificates, memberships, and boards by entering the verification code below.', 'board'); ?></p>
    </div>

    <div class="board-auth-box" style="max-width: 600px;">
        <form id="board-verify-form">
            <div class="board-form-field">
                <label><?php _e('Verification Code', 'board'); ?></label>
                <input type="text" id="verify_code" name="verify_code" placeholder="GSHB-XXXX-XXXX-XXXX" required style="text-align: center; font-size: 18px; font-family: monospace; letter-spacing: 2px;">
            </div>
            <button type="submit" class="board-btn-black"><?php _e('Verify Document', 'board'); ?></button>
        </form>
    </div>

    <div id="verify-result" style="margin-top: 40px; display: none; max-width: 600px; margin-left: auto; margin-right: auto;">
        <div class="board-program-card" style="border-width: 2px; padding: 40px;">
            <h3 id="verify-title" style="border-bottom: 1px solid var(--board-black); padding-bottom: 15px; margin-bottom: 25px;"><?php _e('Verification Result', 'board'); ?></h3>
            <div id="verify-content">
                <!-- Result content will be injected here -->
            </div>
        </div>
    </div>
</div>
