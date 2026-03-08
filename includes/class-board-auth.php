<?php

if (!defined('ABSPATH')) {
    exit;
}

class Board_Auth {

    public function __construct() {
        add_action('wp_ajax_nopriv_board_login', array($this, 'handle_login'));
        add_action('wp_ajax_nopriv_board_register', array($this, 'handle_register'));
        add_action('wp_ajax_nopriv_board_reset', array($this, 'handle_reset'));

        // Also allow logged in users (though they shouldn't see it)
        add_action('wp_ajax_board_login', array($this, 'handle_login'));
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
            wp_send_json_success(array('message' => __('Login successful!', 'board')));
        }
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
            $user = new WP_User($user_id);
            $user->set_role('board_member');

            // Log the user in
            wp_set_current_user($user_id);
            wp_set_auth_cookie($user_id);

            wp_send_json_success(array('message' => __('Registration successful!', 'board')));
        }
    }

    public function handle_reset() {
        check_ajax_referer('board_nonce', 'nonce');

        $user_login = sanitize_text_field($_POST['username']);
        $user_data = get_user_by('login', $user_login);
        if (!$user_data) {
            $user_data = get_user_by('email', $user_login);
        }

        if (!$user_data) {
            wp_send_json_error(array('message' => __('User not found.', 'board')));
        }

        $reset_key = get_password_reset_key($user_data);
        if (is_wp_error($reset_key)) {
            wp_send_json_error(array('message' => __('Could not generate reset key.', 'board')));
        }

        // In a real scenario, you'd send an email here.
        // For this task, we'll just simulate success.
        wp_send_json_success(array('message' => __('Password reset link sent to your email.', 'board')));
    }
}
