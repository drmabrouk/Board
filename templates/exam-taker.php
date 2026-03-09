<?php
if (!defined('ABSPATH')) exit;

use GSHB\Board\Database\Manager as DB;

$exam_id = isset($_GET['exam_id']) ? intval($_GET['exam_id']) : 0;
global $wpdb;
$exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}board_exams WHERE id = %d", $exam_id));

if (!$exam) {
    echo '<p>' . __('Invalid assessment session.', 'board') . '</p>';
    return;
}

$questions = DB::get_exam_questions($exam_id);
$time_limit = $exam->time_limit ?: 30; // Minutes
?>

<div class="board-container">
    <div id="exam-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; background: #000; color: #fff; padding: 20px 40px; border-radius: 8px;">
        <div>
            <h2 style="color: #fff; margin: 0; font-size: 24px;"><?php echo esc_html($exam->title); ?></h2>
            <p style="margin: 5px 0 0 0; font-size: 13px; opacity: 0.8;"><?php printf(__('Professional Certification Exam | Passing Grade: %d%%', 'board'), $exam->passing_percentage); ?></p>
        </div>
        <div id="exam-timer-box" style="text-align: right;">
            <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 1px; opacity: 0.7;"><?php _e('Time Remaining', 'board'); ?></div>
            <div id="exam-countdown" style="font-size: 32px; font-weight: 800; font-family: monospace;" data-limit="<?php echo $time_limit; ?>">00:00</div>
        </div>
    </div>

    <div id="exam-taker-interface" style="max-width: 900px; margin: 0 auto;">
        <form id="board-exam-submission-form">
            <input type="hidden" name="exam_id" value="<?php echo $exam_id; ?>">

            <?php if (!empty($questions)) : ?>
                <div id="questions-container">
                    <?php foreach ($questions as $index => $q) : ?>
                        <div class="exam-question-card" id="q-<?php echo $index; ?>" style="<?php echo $index === 0 ? '' : 'display: none;'; ?> background: #fff; padding: 40px; border: 1px solid #000; border-radius: 12px; margin-bottom: 30px;">
                            <div style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: flex-start;">
                                <span style="background: #000; color: #fff; padding: 5px 15px; font-size: 11px; font-weight: 700; text-transform: uppercase;"><?php printf(__('Question %d of %d', 'board'), $index + 1, count($questions)); ?></span>
                                <span style="font-size: 11px; color: #999; text-transform: uppercase;"><?php echo esc_html($q->category); ?></span>
                            </div>
                            <h3 style="font-size: 20px; line-height: 1.5; margin-bottom: 30px;"><?php echo esc_html($q->question_text); ?></h3>

                            <?php if ($q->type == 'MCQ') :
                                $options = json_decode($q->options, true);
                                ?>
                                <div class="exam-options" style="display: grid; gap: 15px;">
                                    <?php foreach ($options as $opt_idx => $opt) : if (empty($opt)) continue; ?>
                                        <label style="display: flex; align-items: center; gap: 15px; padding: 20px; border: 1px solid #eee; border-radius: 8px; cursor: pointer; transition: all 0.2s;">
                                            <input type="radio" name="answer[<?php echo $q->id; ?>]" value="<?php echo esc_attr($opt); ?>" style="width: auto; margin: 0;">
                                            <span style="font-size: 15px;"><?php echo esc_html($opt); ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            <?php else : ?>
                                <div class="exam-written-response">
                                    <textarea name="answer[<?php echo $q->id; ?>]" rows="8" placeholder="<?php _e('Type your detailed response here...', 'board'); ?>" style="width: 100%; padding: 20px; border: 1px solid #eee; border-radius: 8px;"></textarea>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div id="exam-navigation" style="display: flex; justify-content: space-between; align-items: center; margin-top: 40px;">
                    <button type="button" id="prev-q" class="board-btn-black board-btn-outline" style="display: none;"><?php _e('Previous', 'board'); ?></button>
                    <div style="flex-grow: 1;"></div>
                    <button type="button" id="next-q" class="board-btn-black"><?php _e('Next Question', 'board'); ?></button>
                    <button type="submit" id="submit-exam-final" class="board-btn-black" style="display: none; background: #000;"><?php _e('Complete Assessment', 'board'); ?></button>
                </div>
            <?php else : ?>
                <p style="text-align: center; py-100;"><?php _e('No questions found for this examination.', 'board'); ?></p>
            <?php endif; ?>
        </form>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    var currentQ = 0;
    var totalQs = $('.exam-question-card').length;
    var timerSeconds = $('#exam-countdown').data('limit') * 60;

    function updateNav() {
        $('.exam-question-card').hide();
        $('#q-' + currentQ).fadeIn();

        $('#prev-q').toggle(currentQ > 0);

        if (currentQ === totalQs - 1) {
            $('#next-q').hide();
            $('#submit-exam-final').show();
        } else {
            $('#next-q').show();
            $('#submit-exam-final').hide();
        }
    }

    $('#next-q').on('click', function() { if (currentQ < totalQs - 1) { currentQ++; updateNav(); } });
    $('#prev-q').on('click', function() { if (currentQ > 0) { currentQ--; updateNav(); } });

    // Countdown Timer
    var timerInterval = setInterval(function() {
        var mins = Math.floor(timerSeconds / 60);
        var secs = timerSeconds % 60;
        $('#exam-countdown').text((mins < 10 ? '0' : '') + mins + ':' + (secs < 10 ? '0' : '') + secs);

        if (timerSeconds <= 0) {
            clearInterval(timerInterval);
            alert('Time has expired. Your assessment will be submitted automatically.');
            $('#board-exam-submission-form').submit();
        }
        timerSeconds--;
    }, 1000);

    // Styling for radio selections
    $(document).on('change', 'input[type="radio"]', function() {
        $(this).closest('.exam-options').find('label').css({'background': '#fff', 'border-color': '#eee', 'color': '#000'});
        $(this).closest('label').css({'background': '#000', 'border-color': '#000', 'color': '#fff'});
    });
});
</script>
