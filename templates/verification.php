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
                <input type="text" id="verify_code" name="verify_code" placeholder="<?php _e('Enter Verification Code', 'board'); ?>" required>
            </div>
            <button type="submit" class="board-btn-black"><?php _e('Verify Document', 'board'); ?></button>
        </form>
    </div>

    <div id="verify-result" style="margin-top: 40px; display: none;">
        <div class="board-program-card" style="border-width: 2px;">
            <h3 id="verify-title"><?php _e('Verification Result', 'board'); ?></h3>
            <div id="verify-content">
                <!-- Result content will be injected here -->
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('#board-verify-form').on('submit', function(e) {
        e.preventDefault();
        var code = $('#verify_code').val();

        // Simulating verification logic for now
        $('#verify-result').show();
        if (code === 'GSHB-VALID') {
            $('#verify-content').html('<p style="color: green; font-weight: bold;">✔ Valid Document</p><p><strong>Holder:</strong> John Doe</p><p><strong>Type:</strong> Certified Membership</p><p><strong>Expires:</strong> 2025-12-31</p>');
        } else {
            $('#verify-content').html('<p style="color: red; font-weight: bold;">✘ Invalid or Expired Code</p><p>Please check the code and try again.</p>');
        }
    });
});
</script>
