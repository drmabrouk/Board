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
                    <h3 style="margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px;"><?php _e('Step 1: Personal Details', 'board'); ?></h3>
                    <div class="board-form-field">
                        <label><?php _e('Full Name', 'board'); ?></label>
                        <input type="text" name="full_name" placeholder="e.g., John Doe" required>
                    </div>
                    <div class="board-form-field">
                        <label><?php _e('Country of Residence', 'board'); ?></label>
                        <input type="text" name="country" placeholder="e.g., United Kingdom" required>
                    </div>
                    <button type="button" class="board-btn-black next-step" data-next="2" style="width: auto; padding: 12px 40px;"><?php _e('Next Step', 'board'); ?></button>
                </div>

                <div class="cm-step" id="step-2" style="display: none;">
                    <h3 style="margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px;"><?php _e('Step 2: Professional Information', 'board'); ?></h3>
                    <div class="board-form-field">
                        <label><?php _e('Medical Specialty / Field', 'board'); ?></label>
                        <input type="text" name="specialty" placeholder="e.g., Sports Medicine" required>
                    </div>
                    <div class="board-form-field">
                        <label><?php _e('Current Institution / Clinic', 'board'); ?></label>
                        <input type="text" name="institution" placeholder="e.g., General Hospital" required>
                    </div>
                    <button type="button" class="board-btn-black next-step" data-next="3" style="width: auto; padding: 12px 40px;"><?php _e('Next Step', 'board'); ?></button>
                </div>

                <div class="cm-step" id="step-3" style="display: none;">
                    <h3 style="margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px;"><?php _e('Step 3: Document Upload', 'board'); ?></h3>
                    <p style="font-size: 14px; margin-bottom: 20px; color: var(--board-grey-dark);"><?php _e('Please upload your professional CV and relevant certifications. Supported formats: PDF, JPG, PNG.', 'board'); ?></p>
                    <div class="board-form-field">
                        <label><?php _e('Select Files', 'board'); ?></label>
                        <input type="file" name="documents[]" multiple style="border: 2px dashed #ccc; padding: 30px; text-align: center;">
                    </div>
                    <button type="submit" id="submit-cm-request" class="board-btn-black" style="width: auto; padding: 12px 40px;"><?php _e('Submit Application', 'board'); ?></button>
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

