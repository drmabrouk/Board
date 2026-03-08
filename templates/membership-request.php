<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="board-container">
    <div style="text-align: center; margin-bottom: 40px;">
        <h2><?php _e('Membership Request', 'board'); ?></h2>
        <p><?php _e('Apply for Certified Membership to unlock professional benefits.', 'board'); ?></p>
    </div>

    <div class="board-auth-box" style="max-width: 800px; text-align: left;">
        <form id="board-membership-form">
            <div id="cm-request-steps">
                <div class="cm-step" id="step-1">
                    <h3><?php _e('Step 1: Personal Details', 'board'); ?></h3>
                    <div class="board-form-field">
                        <input type="text" name="full_name" placeholder="<?php _e('Full Name', 'board'); ?>" required>
                    </div>
                    <div class="board-form-field">
                        <input type="text" name="country" placeholder="<?php _e('Country', 'board'); ?>" required>
                    </div>
                    <button type="button" class="board-btn-black next-step" data-next="2"><?php _e('Next Step', 'board'); ?></button>
                </div>

                <div class="cm-step" id="step-2" style="display: none;">
                    <h3><?php _e('Step 2: Professional Information', 'board'); ?></h3>
                    <div class="board-form-field">
                        <input type="text" name="specialty" placeholder="<?php _e('Specialty', 'board'); ?>" required>
                    </div>
                    <div class="board-form-field">
                        <input type="text" name="institution" placeholder="<?php _e('Institution', 'board'); ?>" required>
                    </div>
                    <button type="button" class="board-btn-black next-step" data-next="3"><?php _e('Next Step', 'board'); ?></button>
                </div>

                <div class="cm-step" id="step-3" style="display: none;">
                    <h3><?php _e('Step 3: Document Upload', 'board'); ?></h3>
                    <p><?php _e('Please upload your CV and professional certificates (PDF or JPG).', 'board'); ?></p>
                    <div class="board-form-field">
                        <input type="file" name="documents[]" multiple>
                    </div>
                    <button type="submit" id="submit-cm-request" class="board-btn-black"><?php _e('Submit Request', 'board'); ?></button>
                </div>
            </div>
        </form>

        <div id="cm-request-success" style="display: none; text-align: center;">
            <h3 style="color: green;"><?php _e('Request Submitted Successfully!', 'board'); ?></h3>
            <p><?php _e('Your application is being reviewed. You will be notified via email.', 'board'); ?></p>
            <a href="<?php echo home_url('/mb'); ?>" class="board-btn-black" style="display: inline-block; text-decoration: none; width: auto;"><?php _e('Back to Account', 'board'); ?></a>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('.next-step').on('click', function() {
        var next = $(this).data('next');
        $('.cm-step').hide();
        $('#step-' + next).show();
    });

    $('#board-membership-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var btn = form.find('button[type="submit"]');
        var formData = new FormData(this);
        formData.append('action', 'board_membership_request');
        formData.append('nonce', board_ajax.nonce);

        btn.prop('disabled', true).text('Processing...');

        $.ajax({
            url: board_ajax.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#cm-request-steps').hide();
                    $('#cm-request-success').show();
                } else {
                    alert(response.data.message);
                    btn.prop('disabled', false).text('Try Again');
                }
            }
        });
    });
});
</script>
