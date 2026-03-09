<?php
namespace GSHB\Board\Auth;

use GSHB\Board\Core\Roles;
use GSHB\Board\Board as Plugin;

if (!defined('ABSPATH')) {
    exit;
}

class Handler {

    public function __construct() {
        add_action('wp_ajax_nopriv_board_login', array($this, 'handle_login'));
        add_action('wp_ajax_nopriv_board_register', array($this, 'handle_register'));
        add_action('wp_ajax_nopriv_board_reset', array($this, 'handle_reset'));
        add_action('wp_ajax_nopriv_board_send_reg_otp', array($this, 'handle_send_reg_otp'));
        add_action('wp_ajax_nopriv_board_verify_otp', array($this, 'handle_verify_otp'));
        add_action('wp_ajax_nopriv_board_reset_password_final', array($this, 'handle_reset_password_final'));

        // Also allow logged in users (though they shouldn't see it)
        add_action('wp_ajax_board_login', array($this, 'handle_login'));

        add_action('wp_ajax_board_membership_request', array($this, 'handle_membership_request'));
        add_action('wp_ajax_nopriv_board_verify_document', array($this, 'handle_verification'));
        add_action('wp_ajax_board_verify_document', array($this, 'handle_verification'));
        add_action('wp_ajax_board_submit_exam', array($this, 'handle_exam_submission'));
        add_action('wp_ajax_board_submit_fellowship', array($this, 'handle_submit_fellowship'));
        add_action('wp_ajax_board_request_exam', array($this, 'handle_exam_request'));

        add_filter('login_redirect', array($this, 'custom_login_redirect'), 10, 3);
        add_action('wp_logout', array($this, 'custom_logout_redirect'));
        add_action('template_redirect', array($this, 'handle_auth_page_redirect'));
    }

    public function handle_auth_page_redirect() {
        if (!is_page('registration') || !is_user_logged_in()) {
            return;
        }

        $user_id = get_current_user_id();
        if (Roles::can_access_cp($user_id)) {
            wp_redirect(home_url('/cp'));
            exit;
        } elseif (Roles::can_access_mb($user_id)) {
            wp_redirect(home_url('/mb'));
            exit;
        }
    }

    public function handle_login() {
        check_ajax_referer('board_nonce', 'nonce');

        $creds = array(
            'user_login'    => sanitize_text_field($_POST['username']),
            'user_password' => $_POST['password'],
            'remember'      => true
        );

        $user = wp_signon($creds, false);

        if (is_wp_error($user)) {
            wp_send_json_error(array('message' => $user->get_error_message()));
        } else {
            Plugin::log(__('User Login', 'board'), sprintf(__('User %s logged in.', 'board'), $user->user_login), $user->ID);
            $redirect_url = $this->custom_login_redirect(home_url(), '', $user);
            wp_send_json_success(array(
                'message' => __('Login successful! Redirecting...', 'board'),
                'redirect' => $redirect_url
            ));
        }
    }

    public function custom_login_redirect($redirect_to, $request, $user) {
        if ($user && isset($user->roles) && is_array($user->roles)) {
            if (Roles::can_access_cp($user->ID)) {
                return home_url('/cp');
            } elseif (Roles::can_access_mb($user->ID)) {
                return home_url('/mb');
            }
        }
        return $redirect_to;
    }

    public function custom_logout_redirect() {
        wp_redirect(home_url());
        exit;
    }

    public function handle_send_reg_otp() {
        check_ajax_referer('board_nonce', 'nonce');
        $email = sanitize_email($_POST['email']);
        $username = sanitize_text_field($_POST['username']);

        if (username_exists($username) || email_exists($email)) {
            wp_send_json_error(array('message' => __('Username or email already exists.', 'board')));
        }

        $otp = sprintf('%06d', mt_rand(1, 999999));
        set_transient('board_reg_otp_' . md5($email), $otp, 1800); // 30 mins

        \GSHB\Board\Core\Email::send($email, 'password_otp', array(
            'name' => $username,
            'code' => $otp
        ));

        wp_send_json_success(array('message' => __('Verification code sent to your email.', 'board')));
    }

    public function handle_register() {
        check_ajax_referer('board_nonce', 'nonce');

        $username = sanitize_text_field($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];
        $otp = sanitize_text_field($_POST['reg_otp']);
        $full_name = sanitize_text_field($_POST['full_name']);
        $pal_id = sanitize_text_field($_POST['palestinian_id']);

        $stored_otp = get_transient('board_reg_otp_' . md5($email));
        if ($stored_otp !== $otp) {
            wp_send_json_error(array('message' => __('Invalid or expired verification code.', 'board')));
        }

        if (username_exists($username) || email_exists($email)) {
            wp_send_json_error(array('message' => __('Username or email already exists.', 'board')));
        }

        $user_id = wp_create_user($username, $password, $email);

        if (is_wp_error($user_id)) {
            wp_send_json_error(array('message' => $user_id->get_error_message()));
        } else {
            delete_transient('board_reg_otp_' . md5($email));

            $user = new \WP_User($user_id);
            $user->set_role('board_member');

            wp_update_user(array('ID' => $user_id, 'display_name' => $full_name));
            update_user_meta($user_id, 'membership_status', 'active');
            update_user_meta($user_id, 'palestinian_id', $pal_id);
            update_user_meta($user_id, 'verification_code', 'GSHB-' . strtoupper(wp_generate_password(8, false)));

            wp_set_current_user($user_id);
            wp_set_auth_cookie($user_id);

            \GSHB\Board\Core\Email::send($email, 'registration', array(
                'name' => $full_name
            ));

            wp_send_json_success(array('message' => __('Registration successful!', 'board')));
        }
    }

    public function handle_reset() {
        check_ajax_referer('board_nonce', 'nonce');

        $user_login = sanitize_text_field($_POST['username']);
        $user = get_user_by('login', $user_login);
        if (!$user) $user = get_user_by('email', $user_login);

        if (!$user) {
            wp_send_json_error(array('message' => __('User not found.', 'board')));
        }

        $otp = sprintf('%06d', mt_rand(1, 999999));
        update_user_meta($user->ID, 'board_password_reset_otp', $otp);
        update_user_meta($user->ID, 'board_password_reset_otp_time', time());

        \GSHB\Board\Core\Email::send($user->user_email, 'password_otp', array(
            'name' => $user->display_name,
            'code' => $otp
        ));

        wp_send_json_success(array(
            'message' => __('A 6-digit OTP has been sent to your email.', 'board'),
            'step' => 'otp_verify',
            'username' => $user->user_login
        ));
    }

    public function handle_verify_otp() {
        check_ajax_referer('board_nonce', 'nonce');
        $username = sanitize_text_field($_POST['username']);
        $otp = sanitize_text_field($_POST['otp']);

        $user = get_user_by('login', $username);
        if (!$user) wp_send_json_error(array('message' => __('Invalid request.', 'board')));

        $stored_otp = get_user_meta($user->ID, 'board_password_reset_otp', true);
        $otp_time = get_user_meta($user->ID, 'board_password_reset_otp_time', true);

        if ($stored_otp === $otp && (time() - $otp_time) < 1800) { // 30 mins
            wp_send_json_success(array('message' => __('OTP verified. Please enter your new password.', 'board')));
        } else {
            wp_send_json_error(array('message' => __('Invalid or expired OTP.', 'board')));
        }
    }

    public function handle_reset_password_final() {
        check_ajax_referer('board_nonce', 'nonce');
        $username = sanitize_text_field($_POST['username']);
        $otp = sanitize_text_field($_POST['otp']);
        $new_pass = $_POST['password'];

        $user = get_user_by('login', $username);
        $stored_otp = get_user_meta($user->ID, 'board_password_reset_otp', true);

        if ($stored_otp !== $otp) wp_send_json_error();

        wp_set_password($new_pass, $user->ID);
        delete_user_meta($user->ID, 'board_password_reset_otp');

        Plugin::log(__('Password Reset', 'board'), sprintf(__('User %s reset their password via OTP.', $username), $user->ID));

        wp_send_json_success(array('message' => __('Password updated successfully. You can now log in.', 'board')));
    }

    public function handle_verification() {
        check_ajax_referer('board_nonce', 'nonce');
        $code = sanitize_text_field($_POST['verify_code']);

        // 1. Check User Membership Codes
        $users = get_users(array(
            'meta_key' => 'verification_code',
            'meta_value' => $code,
            'number' => 1
        ));

        if (!empty($users)) {
            $user = $users[0];
            $expiry = get_user_meta($user->ID, 'membership_expiry_date', true);
            $is_active = (!$expiry || strtotime($expiry) > time());

            wp_send_json_success(array(
                'valid' => true,
                'is_active' => $is_active,
                'name' => $user->display_name,
                'type' => __('Certified Membership', 'board'),
                'specialty' => get_user_meta($user->ID, 'specialty', true) ?: 'N/A',
                'expiry' => $expiry ?: __('Never', 'board')
            ));
        }

        // 2. Check Certificate Serial Numbers using Custom DB table
        global $wpdb;
        $table = $wpdb->prefix . 'board_certificates';
        $cert = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE serial_number = %s", $code));

        if ($cert) {
            $uid = $cert->user_id;
            $user = $uid ? get_userdata($uid) : null;

            wp_send_json_success(array(
                'valid' => true,
                'is_active' => ($cert->status === 'active'),
                'name' => $user ? $user->display_name : ($cert->title ?: 'N/A'),
                'type' => $cert->type,
                'specialty' => $uid ? (get_user_meta($uid, 'specialty', true) ?: 'N/A') : 'N/A',
                'expiry' => __('N/A', 'board'),
                'url' => home_url("/certificate/{$code}")
            ));
        }

        wp_send_json_error(array('message' => __('Invalid verification code.', 'board')));
    }

    public function handle_exam_submission() {
        check_ajax_referer('board_nonce', 'nonce');
        if (!is_user_logged_in()) wp_send_json_error();

        $user_id = get_current_user_id();
        $exam_id = intval($_POST['exam_id']);
        $answers = $_POST['answer'];

        // Fetch questions to calculate score if possible
        $questions = \GSHB\Board\Database\Manager::get_exam_questions($exam_id);
        $total_qs = count($questions);
        $correct_count = 0;
        $processed_answers = array();

        foreach ($questions as $q) {
            $user_ans = isset($answers[$q->id]) ? $answers[$q->id] : '';
            $processed_answers[$q->id] = $user_ans;

            if ($q->type == 'MCQ' && !empty($q->correct_answer)) {
                if (trim($user_ans) == trim($q->correct_answer)) {
                    $correct_count++;
                }
            }
        }

        $score = ($total_qs > 0) ? round(($correct_count / $total_qs) * 100) : 0;

        global $wpdb;
        $wpdb->insert($wpdb->prefix . 'board_exam_results', array(
            'user_id' => $user_id,
            'exam_id' => $exam_id,
            'answers' => wp_json_encode($processed_answers),
            'score'   => $score,
            'status'  => 'completed'
        ));

        // Sync to legacy meta for profile overview
        $completed = get_user_meta($user_id, 'completed_exams', true) ?: array();
        $completed[] = array('exam_id' => $exam_id, 'score' => $score, 'date' => current_time('mysql'));
        update_user_meta($user_id, 'completed_exams', $completed);

        Plugin::log(__('Assessment Completed', 'board'), sprintf(__('User %d completed exam %d with auto-calculated score: %d%%.', $user_id, $exam_id, $score)), $user_id);

        wp_send_json_success(array('message' => __('Assessment submitted successfully. Your score: ' . $score . '%', 'board'), 'score' => $score));
    }

    public function handle_membership_request() {
        check_ajax_referer('board_nonce', 'nonce');

        $user_id = !empty($_POST['user_id']) ? intval($_POST['user_id']) : (is_user_logged_in() ? get_current_user_id() : null);

        if (!$user_id && !Roles::can_access_cp()) {
            wp_send_json_error(array('message' => __('You must be logged in to apply.', 'board')));
        }

        $full_name = sanitize_text_field($_POST['full_name']);
        $country = sanitize_text_field($_POST['country']);
        $specialty = sanitize_text_field($_POST['specialty']);
        $institution = sanitize_text_field($_POST['institution']);

        // Handle File Uploads
        $doc_url = '';
        if (!empty($_FILES['documents'])) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');

            $files = $_FILES['documents'];
            foreach ($files['name'] as $key => $value) {
                if ($files['name'][$key]) {
                    $file = array(
                        'name'     => $files['name'][$key],
                        'type'     => $files['type'][$key],
                        'tmp_name' => $files['tmp_name'][$key],
                        'error'    => $files['error'][$key],
                        'size'     => $files['size'][$key]
                    );

                    $attachment_id = media_handle_sideload($file, 0);
                    if (!is_wp_error($attachment_id)) {
                        $doc_url = wp_get_attachment_url($attachment_id);
                        break; // Just take the first one for simplicity in the custom table
                    }
                }
            }
        }

        $saved = \GSHB\Board\Database\Manager::save_membership(array(
            'user_id' => $user_id,
            'full_name' => $full_name,
            'country' => $country,
            'specialty' => $specialty,
            'document_url' => $doc_url,
            'status' => 'pending'
        ));

        if (!$saved) {
            wp_send_json_error(array('message' => __('Failed to save request.', 'board')));
        } else {
            Plugin::log(__('Membership Request', 'board'), sprintf(__('User %d submitted a membership request.', 'board'), $user_id), $user_id);

            $notification_users = get_users(array('role__in' => array('academic_supervisor', 'certs_manager', 'board_admin')));
            $emails = array_map(function($u) { return $u->user_email; }, $notification_users);

            if (!empty($emails)) {
                foreach ($emails as $email) {
                    \GSHB\Board\Core\Email::send($email, 'membership_request', array(
                        'name' => $full_name
                    ));
                }
            }

            wp_send_json_success(array('message' => __('Your membership application has been submitted.', 'board')));
        }
    }

    public function handle_exam_request() {
        check_ajax_referer('board_nonce', 'nonce');
        if (!is_user_logged_in()) wp_send_json_error();

        $user_id = get_current_user_id();
        $exam_id = intval($_POST['exam_id']);

        \GSHB\Board\Database\Manager::save_exam_request(array(
            'user_id' => $user_id,
            'exam_id' => $exam_id,
            'status' => 'pending'
        ));

        Plugin::log(__('Exam Request', 'board'), sprintf(__('User %d requested access to exam %d.', $user_id, $exam_id)), $user_id);
        wp_send_json_success(array('message' => __('Your exam request has been submitted for approval.', 'board')));
    }

    public function handle_submit_fellowship() {
        check_ajax_referer('board_nonce', 'nonce');
        if (!is_user_logged_in()) wp_send_json_error();

        $user_id = get_current_user_id();
        $data = array(
            'user_id' => $user_id,
            'full_name' => sanitize_text_field($_POST['full_name']),
            'qualifications' => sanitize_textarea_field($_POST['qualifications']),
            'experience' => sanitize_textarea_field($_POST['experience']),
            'skills' => sanitize_textarea_field($_POST['skills']),
            'achievements' => sanitize_textarea_field($_POST['achievements']),
            'references_data' => sanitize_textarea_field($_POST['references_data']),
            'status' => 'pending'
        );

        // Handle File Evidence
        if (!empty($_FILES['evidence'])) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');

            $files = $_FILES['evidence'];
            foreach ($files['name'] as $key => $value) {
                if ($files['name'][$key]) {
                    $file = array(
                        'name'     => $files['name'][$key],
                        'type'     => $files['type'][$key],
                        'tmp_name' => $files['tmp_name'][$key],
                        'error'    => $files['error'][$key],
                        'size'     => $files['size'][$key]
                    );
                    $attachment_id = media_handle_sideload($file, 0);
                    if (!is_wp_error($attachment_id)) {
                        $data['evidence_url'] = wp_get_attachment_url($attachment_id);
                        break;
                    }
                }
            }
        }

        if (\GSHB\Board\Database\Manager::save_fellowship($data)) {
            Plugin::log(__('Fellowship Application', 'board'), sprintf(__('User %d applied for Fellowship recognition.', 'board'), $user_id), $user_id);
            wp_send_json_success(array('message' => __('Your Fellowship application has been submitted for peer-review.', 'board')));
        } else {
            wp_send_json_error();
        }
    }
}
