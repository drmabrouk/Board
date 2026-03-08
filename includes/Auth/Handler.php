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

        // Also allow logged in users (though they shouldn't see it)
        add_action('wp_ajax_board_login', array($this, 'handle_login'));

        add_action('wp_ajax_board_membership_request', array($this, 'handle_membership_request'));
        add_action('wp_ajax_nopriv_board_verify_document', array($this, 'handle_verification'));
        add_action('wp_ajax_board_verify_document', array($this, 'handle_verification'));
        add_action('wp_ajax_board_submit_exam', array($this, 'handle_exam_submission'));

        add_filter('login_redirect', array($this, 'custom_login_redirect'), 10, 3);
        add_action('wp_logout', array($this, 'custom_logout_redirect'));
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

    public function handle_register() {
        check_ajax_referer('board_nonce', 'nonce');

        $username = sanitize_text_field($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];

        if (username_exists($username) || email_exists($email)) {
            wp_send_json_error(array('message' => __('Username or email already exists.', 'board')));
        }

        $user_id = wp_create_user($username, $password, $email);

        if (is_wp_error($user_id)) {
            wp_send_json_error(array('message' => $user_id->get_error_message()));
        } else {
            // Set default role to board_member
            $user = new \WP_User($user_id);
            $user->set_role('board_member');

            // Initialize metadata
            update_user_meta($user_id, 'membership_status', 'active');
            update_user_meta($user_id, 'verification_code', 'GSHB-' . strtoupper(wp_generate_password(8, false)));

            // Log the user in
            wp_set_current_user($user_id);
            wp_set_auth_cookie($user_id);

            wp_send_json_success(array('message' => __('Registration successful!', 'board')));
        }
    }

    public function handle_reset() {
        check_ajax_referer('board_nonce', 'nonce');

        $user_login = sanitize_text_field($_POST['username']);

        // Use native WP password retrieval logic
        $errors = retrieve_password($user_login);

        if (is_wp_error($errors)) {
            wp_send_json_error(array('message' => $errors->get_error_message()));
        }

        wp_send_json_success(array('message' => __('Password reset link sent to your email.', 'board')));
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

        // 2. Check Certificate Serial Numbers
        $certs = get_posts(array(
            'post_type' => 'board_certificate',
            'meta_key' => 'serial_number',
            'meta_value' => $code,
            'posts_per_page' => 1
        ));

        if (!empty($certs)) {
            $cert = $certs[0];
            $uid = get_post_meta($cert->ID, 'user_id', true);
            $user = get_userdata($uid);
            $status = get_post_meta($cert->ID, 'cert_status', true);

            wp_send_json_success(array(
                'valid' => true,
                'is_active' => ($status === 'active'),
                'name' => $user ? $user->display_name : 'Unknown',
                'type' => get_post_meta($cert->ID, 'cert_type', true),
                'specialty' => get_user_meta($uid, 'specialty', true) ?: 'N/A',
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
        $score = intval($_POST['score']);

        $completed = get_user_meta($user_id, 'completed_exams', true);
        if (!is_array($completed)) $completed = array();

        $completed[] = array(
            'exam_id' => $exam_id,
            'score'   => $score,
            'date'    => current_time('mysql')
        );

        update_user_meta($user_id, 'completed_exams', $completed);

        Plugin::log(__('Exam Submitted', 'board'), sprintf(__('User %d submitted exam %d with score %d.', $user_id, $exam_id, $score)), $user_id);

        wp_send_json_success(array('message' => __('Exam submitted successfully.', 'board')));
    }

    public function handle_membership_request() {
        check_ajax_referer('board_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => __('You must be logged in to apply.', 'board')));
        }

        $user_id = get_current_user_id();
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
                $subject = __('New Membership Request - GSHB', 'board');
                $body = sprintf(__('A new membership request has been submitted by %s. Please review it in the Control Panel.', 'board'), $full_name);
                wp_mail($emails, $subject, $body);
            }

            wp_send_json_success(array('message' => __('Your membership application has been submitted.', 'board')));
        }
    }
}
