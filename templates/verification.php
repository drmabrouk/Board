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
        var btn = $(this).find('button');

        btn.prop('disabled', true).text('Verifying...');

        $.post(board_ajax.ajax_url, {
            action: 'board_verify_document',
            nonce: board_ajax.nonce,
            verify_code: code
        }, function(response) {
            btn.prop('disabled', false).text('<?php _e('Verify Document', 'board'); ?>');
            $('#verify-result').show();
            if (response.success && response.data.valid) {
                var status = response.data.is_active ? '<span style="color: green; font-weight: bold;">✔ <?php _e('Valid', 'board'); ?></span>' : '<span style="color: grey; font-weight: bold;">✘ <?php _e('Expired', 'board'); ?></span>';
                var html = '<p><strong><?php _e('Status:', 'board'); ?></strong> ' + status + '</p>' +
                           '<p><strong><?php _e('Holder:', 'board'); ?></strong> ' + response.data.name + '</p>' +
                           '<p><strong><?php _e('Specialty:', 'board'); ?></strong> ' + response.data.specialty + '</p>' +
                           '<p><strong><?php _e('Expires:', 'board'); ?></strong> ' + response.data.expiry + '</p>';
                $('#verify-content').html(html);
            } else {
                $('#verify-content').html('<p style="color: red; font-weight: bold;">✘ ' + (response.data.message || '<?php _e('Invalid or Expired Code', 'board'); ?>') + '</p><p><?php _e('Please check the code and try again.', 'board'); ?></p>');
            }
        });
    });
});
</script>
