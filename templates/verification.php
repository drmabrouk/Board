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

    <div id="verify-result" style="margin-top: 50px; display: none; max-width: 700px; margin-left: auto; margin-right: auto;">
        <div class="board-program-card" style="border: 2px solid var(--board-black); padding: 50px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
            <div style="text-align: center; margin-bottom: 30px;">
                <span class="dashicons dashicons-shield" style="font-size: 48px; width: 48px; height: 48px; color: var(--board-black);"></span>
                <h3 id="verify-title" style="margin-top: 15px; font-size: 24px; text-transform: uppercase; letter-spacing: 1px;"><?php _e('Verification Status', 'board'); ?></h3>
            </div>

            <div id="verify-content" style="border-top: 1px solid #eee; padding-top: 30px;">
                <!-- Result content will be injected here -->
            </div>

            <div style="margin-top: 40px; text-align: center; border-top: 1px solid #eee; padding-top: 20px;">
                <p style="font-size: 11px; color: #999; text-transform: uppercase; letter-spacing: 1px;"><?php _e('Global Sports Health Board Security Protocol', 'board'); ?></p>
            </div>
        </div>
    </div>
</div>
