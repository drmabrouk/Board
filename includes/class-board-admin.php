<?php

if (!defined('ABSPATH')) {
    exit;
}

class Board_Admin {

    public function __construct() {
        add_action('wp_ajax_board_approve_request', array($this, 'handle_approval'));
        add_action('wp_ajax_board_save_program', array($this, 'handle_save_program'));
        add_action('wp_ajax_board_delete_program', array($this, 'handle_delete_program'));
        add_action('wp_ajax_board_assign_exam', array($this, 'handle_assign_exam'));
        add_action('wp_ajax_board_update_user_role', array($this, 'handle_update_role'));
        add_action('wp_ajax_board_update_user_status', array($this, 'handle_update_status'));
        add_action('wp_ajax_board_generate_certificate', array($this, 'handle_generate_certificate'));
        add_action('wp_ajax_board_delete_user', array($this, 'handle_delete_user'));
        add_action('wp_ajax_board_add_user', array($this, 'handle_add_user'));
        add_action('admin_post_board_export_users', array($this, 'handle_export_users'));
        add_action('admin_post_board_export_programs', array($this, 'handle_export_programs'));
        add_action('admin_post_board_import_users', array($this, 'handle_import_users'));
    }

    public function handle_export_programs() {
        if (!Board_Roles::can_access_cp()) wp_die(__('Unauthorized', 'board'));

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=gshb_programs_export.csv');
        $output = fopen('php://output', 'w');
        fputcsv($output, array('ID', 'Title', 'Code', 'Type', 'Duration', 'Status'));

        $progs = get_posts(array('post_type' => 'board_program', 'posts_per_page' => -1));
        foreach ($progs as $p) {
            fputcsv($output, array(
                $p->ID,
                $p->post_title,
                get_post_meta($p->ID, 'program_code', true),
                get_post_meta($p->ID, 'program_type', true),
                get_post_meta($p->ID, 'program_duration', true),
                get_post_status($p->ID)
            ));
        }
        fclose($output);
        exit;
    }

    public function handle_add_user() {
        check_ajax_referer('board_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error();

        $user_login = sanitize_text_field($_POST['username']);
        $user_email = sanitize_email($_POST['email']);
        $user_pass  = $_POST['password'];
        $role       = sanitize_text_field($_POST['role']);

        if (username_exists($user_login) || email_exists($user_email)) {
            wp_send_json_error(array('message' => __('Username or email already exists.', 'board')));
        }

        $user_id = wp_create_user($user_login, $user_pass, $user_email);

        if (is_wp_error($user_id)) {
            wp_send_json_error(array('message' => $user_id->get_error_message()));
        } else {
            $user = new WP_User($user_id);
            $user->set_role($role);
            update_user_meta($user_id, 'membership_status', 'active');
            update_user_meta($user_id, 'verification_code', 'GSHB-' . strtoupper(wp_generate_password(8, false)));

            Board::log(__('User Created', 'board'), sprintf(__('New user %s created manually.', 'board'), $user_login), get_current_user_id());
            wp_send_json_success(array('message' => __('User created successfully.', 'board')));
        }
    }

    public function handle_import_users() {
        if (!current_user_can('manage_options')) wp_die(__('Unauthorized', 'board'));

        if (!empty($_FILES['import_file']['tmp_name'])) {
            $file = fopen($_FILES['import_file']['tmp_name'], 'r');
            fgetcsv($file); // skip header
            while (($row = fgetcsv($file)) !== FALSE) {
                $email = isset($row[3]) ? $row[3] : '';
                if ($email && !email_exists($email)) {
                    $user_login = isset($row[2]) ? $row[2] : $email;
                    $user_id = wp_create_user($user_login, wp_generate_password(), $email);
                    if (!is_wp_error($user_id)) {
                        if (isset($row[1])) update_user_meta($user_id, 'verification_code', $row[1]);
                        if (isset($row[5])) update_user_meta($user_id, 'membership_status', $row[5]);
                        if (isset($row[4])) {
                            $user = new WP_User($user_id);
                            $user->set_role($row[4]);
                        }
                    }
                }
            }
            fclose($file);
            Board::log(__('Users Imported', 'board'), __('Users imported via CSV.', 'board'), get_current_user_id());
        }
        wp_redirect(home_url('/cp?cp_tab=users&import=success'));
        exit;
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

        Board::log(__('Certificate Generated', 'board'), sprintf(__('Certificate %s generated for user %d.', 'board'), $serial, $user_id), get_current_user_id());
        wp_send_json_success(array('message' => __('Certificate generated successfully.', 'board'), 'serial' => $serial));
    }

    public function handle_update_role() {
        check_ajax_referer('board_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error();
        $user_id = intval($_POST['user_id']);
        $role = sanitize_text_field($_POST['role']);
        $user = new WP_User($user_id);
        $user->set_role($role);
        Board::log(__('Role Updated', 'board'), sprintf(__('User %d role changed to %s.', 'board'), $user_id, $role), get_current_user_id());
        wp_send_json_success(array('message' => __('Role updated.', 'board')));
    }

    public function handle_update_status() {
        check_ajax_referer('board_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error();
        $user_id = intval($_POST['user_id']);
        $status = sanitize_text_field($_POST['status']);
        update_user_meta($user_id, 'membership_status', $status);
        Board::log(__('Status Updated', 'board'), sprintf(__('User %d status changed to %s.', 'board'), $user_id, $status), get_current_user_id());
        wp_send_json_success(array('message' => __('Status updated.', 'board')));
    }

    public function handle_delete_user() {
        check_ajax_referer('board_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error();

        $user_id = intval($_POST['user_id']);
        if ($user_id == get_current_user_id()) wp_send_json_error(array('message' => __('Cannot delete yourself.', 'board')));

        require_once(ABSPATH . 'wp-admin/includes/user.php');
        if (wp_delete_user($user_id)) {
            Board::log(__('User Deleted', 'board'), sprintf(__('User ID %d was deleted.', 'board'), $user_id), get_current_user_id());
            wp_send_json_success(array('message' => __('User deleted.', 'board')));
        } else {
            wp_send_json_error();
        }
    }

    public function handle_export_users() {
        if (!Board_Roles::can_access_cp()) wp_die(__('Unauthorized', 'board'));

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=gshb_users_export.csv');
        $output = fopen('php://output', 'w');
        fputcsv($output, array('ID', 'Membership ID', 'Name', 'Email', 'Roles', 'Status', 'Certs Count', 'Exams Count', 'Registration Date'));

        $users = get_users();
        foreach ($users as $u) {
            $id_code = get_user_meta($u->ID, 'verification_code', true) ?: 'N/A';
            $status = get_user_meta($u->ID, 'membership_status', true) ?: 'active';
            $certs_count = count(get_posts(array('post_type' => 'board_certificate', 'meta_key' => 'user_id', 'meta_value' => $u->ID)));
            $exams_count = count(get_user_meta($u->ID, 'assigned_exams', true) ?: array());

            fputcsv($output, array(
                $u->ID,
                $id_code,
                $u->display_name,
                $u->user_email,
                implode(',', $u->roles),
                $status,
                $certs_count,
                $exams_count,
                $u->user_registered
            ));
        }
        fclose($output);
        exit;
    }

    public function handle_save_program() {
        check_ajax_referer('board_nonce', 'nonce');
        if (!Board_Roles::can_access_cp()) wp_send_json_error();

        $title = sanitize_text_field($_POST['title']);
        $desc = sanitize_textarea_field($_POST['desc']);
        $code = sanitize_text_field($_POST['code']);
        $type = sanitize_text_field($_POST['type']);
        $duration = sanitize_text_field($_POST['duration']);

        $post_id = wp_insert_post(array(
            'post_title' => $title,
            'post_content' => $desc,
            'post_status' => 'publish',
            'post_type' => 'board_program',
            'meta_input' => array(
                'program_code' => $code,
                'program_type' => $type,
                'program_duration' => $duration
            )
        ));

        if (is_wp_error($post_id)) {
            wp_send_json_error(array('message' => $post_id->get_error_message()));
        } else {
            Board::log(__('Program Created', 'board'), sprintf(__('Program %s created.', 'board'), $title), get_current_user_id());
            wp_send_json_success(array('message' => __('Program saved.', 'board')));
        }
    }

    public function handle_delete_program() {
        check_ajax_referer('board_nonce', 'nonce');
        if (!Board_Roles::can_access_cp()) wp_send_json_error();
        $id = intval($_POST['program_id']);
        if (wp_delete_post($id)) {
            Board::log(__('Program Deleted', 'board'), sprintf(__('Program ID %d deleted.', 'board'), $id), get_current_user_id());
            wp_send_json_success(array('message' => __('Program deleted.', 'board')));
        } else {
            wp_send_json_error();
        }
    }

    public function handle_assign_exam() {
        check_ajax_referer('board_nonce', 'nonce');
        if (!Board_Roles::can_access_cp()) wp_send_json_error();

        $user_id = intval($_POST['user_id']);
        $exam_id = intval($_POST['exam_id']);

        $assigned = get_user_meta($user_id, 'assigned_exams', true);
        if (!is_array($assigned)) $assigned = array();

        if (!in_array($exam_id, $assigned)) {
            $assigned[] = $exam_id;
            update_user_meta($user_id, 'assigned_exams', $assigned);
        }

        Board::log(__('Exam Assigned', 'board'), sprintf(__('Exam %d assigned to user %d.', 'board'), $exam_id, $user_id), get_current_user_id());
        wp_send_json_success(array('message' => __('Exam assigned to user.', 'board')));
    }

    public function handle_approval() {
        check_ajax_referer('board_nonce', 'nonce');
        if (!Board_Roles::can_access_cp()) wp_send_json_error();

        $request_id = intval($_POST['request_id']);
        $user_id = get_post_meta($request_id, 'user_id', true);

        if (!$user_id) wp_send_json_error();

        $user = new WP_User($user_id);
        $user->set_role('certified_member');

        $country = get_post_meta($request_id, 'country', true);
        $specialty = get_post_meta($request_id, 'specialty', true);
        update_user_meta($user_id, 'country', $country);
        update_user_meta($user_id, 'specialty', $specialty);

        update_user_meta($user_id, 'membership_status', 'active');
        update_user_meta($user_id, 'membership_expiry_date', date('Y-m-d H:i:s', strtotime('+1 year')));

        $verify_code = 'GSHB-' . strtoupper(wp_generate_password(8, false));
        update_user_meta($user_id, 'verification_code', $verify_code);

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

        update_post_meta($request_id, 'status', 'approved');
        wp_update_post(array('ID' => $request_id, 'post_status' => 'private'));

        Board::log(__('Membership Approved', 'board'), sprintf(__('User %d approved for certified membership.', 'board'), $user_id), get_current_user_id());

        wp_mail($user->user_email, __('Certified Membership Approved - GSHB', 'board'), sprintf(__('Hello %s, your certified membership has been approved. Code: %s', 'board'), $user->display_name, $verify_code));

        wp_send_json_success(array('message' => __('Membership approved.', 'board'), 'code' => $verify_code));
    }

    public static function get_pending_requests() {
        return get_posts(array(
            'post_type' => 'board_request',
            'post_status' => 'publish',
            'meta_query' => array(array('key' => 'status', 'value' => 'pending'))
        ));
    }
}
