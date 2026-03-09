<?php
if (!defined('ABSPATH')) exit;

use GSHB\Board\Database\Manager as DB;

$user_id = get_current_user_id();
$fellowships = DB::get_fellowships($user_id);
?>

<div class="board-container">
    <div style="text-align: center; margin-bottom: 40px;">
        <h2><?php _e('Fellowship Pathway', 'board'); ?></h2>
        <p><?php _e('Higher professional recognition based on demonstrated experience and achievements.', 'board'); ?></p>
    </div>

    <?php if (!empty($fellowships)) : ?>
        <div style="margin-bottom: 50px;">
            <h3><?php _e('Application Tracking', 'board'); ?></h3>
            <table class="board-table">
                <thead>
                    <tr>
                        <th><?php _e('Date', 'board'); ?></th>
                        <th><?php _e('Status', 'board'); ?></th>
                        <th><?php _e('Feedback', 'board'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($fellowships as $f) : ?>
                        <tr>
                            <td><?php echo $f->created_at; ?></td>
                            <td><span class="status-badge status-<?php echo $f->status; ?>"><?php echo str_replace('_', ' ', $f->status); ?></span></td>
                            <td><?php echo ($f->status == 'approved') ? __('Congratulations! You are officially a Fellow of the Global Council for Sports Health.', 'board') : __('Review in progress...', 'board'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <div class="board-auth-box" style="max-width: 800px; text-align: left;">
        <h3 style="margin-bottom: 25px; border-bottom: 2px solid black; padding-bottom: 10px;"><?php _e('Fellowship Application Form', 'board'); ?></h3>
        <form id="board-fellowship-form">
            <div class="board-form-field">
                <label><?php _e('Full Name', 'board'); ?></label>
                <input type="text" name="full_name" required>
            </div>
            <div class="board-form-field">
                <label><?php _e('Academic & Professional Qualifications', 'board'); ?></label>
                <textarea name="qualifications" rows="4" placeholder="<?php _e('List your degrees, certifications, and academic honors...', 'board'); ?>" required></textarea>
            </div>
            <div class="board-form-field">
                <label><?php _e('Professional Experience', 'board'); ?></label>
                <textarea name="experience" rows="4" placeholder="<?php _e('Describe your career path and years of practice...', 'board'); ?>" required></textarea>
            </div>
            <div class="board-form-field">
                <label><?php _e('Demonstrated Skills & Documentation', 'board'); ?></label>
                <textarea name="skills" rows="4" placeholder="<?php _e('Outline specific professional skills and attach documentation below...', 'board'); ?>" required></textarea>
            </div>
            <div class="board-form-field">
                <label><?php _e('Achievements & Professional Contributions', 'board'); ?></label>
                <textarea name="achievements" rows="4" placeholder="<?php _e('Publications, awards, lectures, and significant contributions to the field...', 'board'); ?>" required></textarea>
            </div>
            <div class="board-form-field">
                <label><?php _e('Professional References', 'board'); ?></label>
                <textarea name="references_data" rows="3" placeholder="<?php _e('Provide contact details for at least two professional references...', 'board'); ?>" required></textarea>
            </div>
            <div class="board-form-field">
                <label><?php _e('Supporting Evidence (URL or File)', 'board'); ?></label>
                <input type="file" name="evidence[]" multiple>
                <p style="font-size: 11px; color: grey; margin-top: 5px;"><?php _e('Upload portfolios, certificates, or verification links.', 'board'); ?></p>
            </div>

            <div style="margin-top: 30px;">
                <button type="submit" class="board-btn-black" style="width: auto; padding: 15px 50px;"><?php _e('Submit for Peer-Review', 'board'); ?></button>
            </div>
        </form>
    </div>
</div>
