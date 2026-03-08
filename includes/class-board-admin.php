<?php

if (!defined('ABSPATH')) {
    exit;
}

class Board_Admin {

    public function __construct() {
        add_action('wp_ajax_board_approve_request', array($this, 'handle_approval'));
        add_action('wp_ajax_board_save_program', array($this, 'handle_save_program'));
        add_action('wp_ajax_board_assign_exam', array($this, 'handle_assign_exam'));
        add_action('wp_ajax_board_generate_certificate', array($this, 'handle_generate_certificate'));
        add_action('wp_ajax_board_delete_user', array($this, 'handle_delete_user'));
        add_action('admin_post_board_export_users', array($this, 'handle_export_users'));
    }

    public function handle_generate_certificate() {
        check_ajax_referer('board_nonce', 'nonce');
        if (!Board_Roles::can_access_cp()) wp_send_json_error();

        $user_id = intval($_POST['user_id']);
        $type = sanitize_text_field($_POST['cert_type']);
        $user = get_userdata($user_id);

        $serial = 'GSHB-' . strtoupper(wp_generate_password(10, false));

        $cert_id = wp_insert_post(array(
            'post_title' => $user->display_name . ' - ' . $type,
            'post_status' => 'publish',
            'post_type' => 'board_certificate',
            'meta_input' => array(
                'user_id' => $user_id,
                'cert_type' => $type,
                'serial_number' => $serial
            )
        ));

        if (is_wp_error($cert_id)) wp_send_json_error();

        Board::log(__('Certificate Generated', 'board'), sprintf(__('Certificate %s generated for user %d.', $serial, $user_id)));
        wp_send_json_success(array('message' => __('Certificate generated successfully.', 'board'), 'serial' => $serial));
    }

    public function handle_delete_user() {
        check_ajax_referer('board_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error();

        $user_id = intval($_POST['user_id']);
        if ($user_id == get_current_user_id()) wp_send_json_error(array('message' => 'Cannot delete yourself.'));

        require_once(ABSPATH . 'wp-admin/includes/user.php');
        if (wp_delete_user($user_id)) {
            Board::log(__('User Deleted', 'board'), sprintf(__('User ID %d was deleted.', $user_id)));
            wp_send_json_success(array('message' => __('User deleted.', 'board')));
        } else {
            wp_send_json_error();
        }
    }

    public function handle_export_users() {
        if (!Board_Roles::can_access_cp()) wp_die(__('Unauthorized', 'board'));

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=users_export.csv');
        $output = fopen('php://output', 'w');
        fputcsv($output, array('ID', 'Name', 'Email', 'Roles', 'Registration Date'));

        $users = get_users();
        foreach ($users as $u) {
            fputcsv($output, array($u->ID, $u->display_name, $u->user_email, implode(',', $u->roles), $u->user_registered));
        }
        fclose($output);
        exit;
    }

    public function handle_save_program() {
        check_ajax_referer('board_nonce', 'nonce');
        if (!Board_Roles::can_access_cp()) {
            wp_send_json_error(array('message' => __('Unauthorized access.', 'board')));
        }

        $title = sanitize_text_field($_POST['title']);
        $desc = sanitize_textarea_field($_POST['desc']);
        $code = sanitize_text_field($_POST['code']);

        $post_id = wp_insert_post(array(
            'post_title' => $title,
            'post_content' => $desc,
            'post_status' => 'publish',
            'post_type' => 'board_program',
            'meta_input' => array('program_code' => $code)
        ));

        if (is_wp_error($post_id)) {
            wp_send_json_error(array('message' => $post_id->get_error_message()));
        } else {
            Board::log(__('Program Created', 'board'), sprintf(__('Program %s created.', 'board'), $title));
            wp_send_json_success(array('message' => __('Program saved.', 'board')));
        }
    }

    public function handle_assign_exam() {
        check_ajax_referer('board_nonce', 'nonce');
        if (!Board_Roles::can_access_cp()) {
            wp_send_json_error(array('message' => __('Unauthorized access.', 'board')));
        }

        $user_id = intval($_POST['user_id']);
        $exam_id = intval($_POST['exam_id']);

        $assigned = get_user_meta($user_id, 'assigned_exams', true);
        if (!is_array($assigned)) {
            $assigned = array();
        }

        if (!in_array($exam_id, $assigned)) {
            $assigned[] = $exam_id;
            update_user_meta($user_id, 'assigned_exams', $assigned);
        }

        wp_send_json_success(array('message' => __('Exam assigned to user.', 'board')));
    }

    public function handle_approval() {
        check_ajax_referer('board_nonce', 'nonce');

        if (!Board_Roles::can_access_cp()) {
            wp_send_json_error(array('message' => __('Unauthorized access.', 'board')));
        }

        $request_id = intval($_POST['request_id']);
        $user_id = get_post_meta($request_id, 'user_id', true);

        if (!$user_id) {
            wp_send_json_error(array('message' => __('User not found for this request.', 'board')));
        }

        // Upgrade user to Certified Member
        $user = new WP_User($user_id);
        $user->set_role('certified_member');

        // Store approval info
        update_user_meta($user_id, 'membership_status', 'active');
        update_user_meta($user_id, 'membership_approval_date', current_time('mysql'));
        update_user_meta($user_id, 'membership_expiry_date', date('Y-m-d H:i:s', strtotime('+1 year')));

        // Generate a verification code
        $verify_code = 'GSHB-' . strtoupper(wp_generate_password(8, false));
        update_user_meta($user_id, 'verification_code', $verify_code);

        // Generate Certificate Record
        wp_insert_post(array(
            'post_title' => $user->display_name,
            'post_status' => 'publish',
            'post_type' => 'board_certificate',
            'meta_input' => array(
                'user_id' => $user_id,
                'cert_type' => __('Membership', 'board'),
                'serial_number' => $verify_code
            )
        ));

        // Update request status
        update_post_meta($request_id, 'status', 'approved');
        wp_update_post(array(
            'ID' => $request_id,
            'post_status' => 'private' // Hide approved requests
        ));

        Board::log(__('Membership Approved', 'board'), sprintf(__('User %d approved for certified membership.', $user_id)), get_current_user_id());

        // Send Email Notification
        $to = $user->user_email;
        $subject = __('Certified Membership Approved - GSHB', 'board');
        $body = sprintf(__('Hello %s, your certified membership application has been approved. Your verification code is: %s', 'board'), $user->display_name, $verify_code);
        wp_mail($to, $subject, $body);

        wp_send_json_success(array(
            'message' => __('Membership approved and user upgraded.', 'board'),
            'code' => $verify_code
        ));
    }

    public static function get_pending_requests() {
        return get_posts(array(
            'post_type' => 'board_request',
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => 'status',
                    'value' => 'pending'
                )
            )
        ));
    }
}
