<?php
namespace GSHB\Board\Admin;

use GSHB\Board\Core\Roles;
use GSHB\Board\Board as Plugin;
use GSHB\Board\Database\Manager as DB;

if (!defined('ABSPATH')) {
    exit;
}

class Manager {

    public function __construct() {
        add_action('wp_ajax_board_approve_request', array($this, 'handle_approval'));
        add_action('wp_ajax_board_save_general_settings', array($this, 'handle_save_general_settings'));
        add_action('wp_ajax_board_save_exam', array($this, 'handle_save_exam'));
        add_action('wp_ajax_board_save_design_settings', array($this, 'handle_save_design_settings'));
        add_action('wp_ajax_board_save_advanced_settings', array($this, 'handle_save_advanced_settings'));
        add_action('wp_ajax_board_save_email_settings', array($this, 'handle_save_email_settings'));
        add_action('wp_ajax_board_save_email_templates', array($this, 'handle_save_email_templates'));
        add_action('wp_ajax_board_export_json', array($this, 'handle_export_json'));
        add_action('admin_post_board_restore_backup', array($this, 'handle_restore_backup'));
        add_action('wp_ajax_board_save_program', array($this, 'handle_save_program'));
        add_action('wp_ajax_board_delete_program', array($this, 'handle_delete_program'));
        add_action('wp_ajax_board_assign_exam', array($this, 'handle_assign_exam'));
        add_action('wp_ajax_board_update_user_role', array($this, 'handle_update_role'));
        add_action('wp_ajax_board_update_user_status', array($this, 'handle_update_status'));
        add_action('wp_ajax_board_generate_certificate', array($this, 'handle_generate_certificate'));
        add_action('wp_ajax_board_link_certificate', array($this, 'handle_link_certificate'));
        add_action('wp_ajax_board_link_membership', array($this, 'handle_link_membership'));
        add_action('wp_ajax_board_user_lookup', array($this, 'handle_user_lookup'));
        add_action('wp_ajax_board_revoke_certificate', array($this, 'handle_revoke_certificate'));
        add_action('wp_ajax_board_submit_program_application', array($this, 'handle_submit_application'));
        add_action('wp_ajax_board_update_application_status', array($this, 'handle_update_application_status'));
        add_action('wp_ajax_board_update_fellowship_status', array($this, 'handle_update_fellowship_status'));
        add_action('wp_ajax_board_get_fellowship_details', array($this, 'handle_get_fellowship_details'));
        add_action('wp_ajax_board_save_question', array($this, 'handle_save_question'));
        add_action('wp_ajax_board_process_exam_request', array($this, 'handle_process_exam_request'));
        add_action('wp_ajax_board_link_exam_questions', array($this, 'handle_link_exam_questions'));
        add_action('wp_ajax_board_delete_certificate', array($this, 'handle_delete_certificate'));
        add_action('wp_ajax_board_delete_question', array($this, 'handle_delete_question'));
        add_action('wp_ajax_board_delete_exam', array($this, 'handle_delete_exam'));
        add_action('wp_ajax_board_delete_user', array($this, 'handle_delete_user'));
        add_action('wp_ajax_board_add_user', array($this, 'handle_add_user'));
        add_action('admin_post_board_export_users', array($this, 'handle_export_users'));
        add_action('admin_post_board_export_programs', array($this, 'handle_export_programs'));
        add_action('admin_post_board_export_certificates', array($this, 'handle_export_certificates'));
        add_action('admin_post_board_import_users', array($this, 'handle_import_users'));
        add_action('admin_post_board_import_programs', array($this, 'handle_import_programs'));
        add_action('admin_post_board_import_exams', array($this, 'handle_import_exams'));
        add_action('admin_post_board_import_certificates', array($this, 'handle_import_certificates'));
    }

    public function handle_export_certificates() {
        check_admin_referer('board_export_certificates');
        if (!Roles::can_access_cp()) wp_die(__('Unauthorized', 'board'));

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=gshb_certificates_export.csv');
        $output = fopen('php://output', 'w');
        fputcsv($output, array('ID', 'Title', 'Type', 'Serial Number', 'Status', 'Issue Date'));

        $certs = DB::get_certificates();
        foreach ($certs as $c) {
            fputcsv($output, array(
                $c->id,
                $c->title,
                $c->type,
                $c->serial_number,
                $c->status,
                $c->issue_date
            ));
        }
        fclose($output);
        exit;
    }

    public function handle_revoke_certificate() {
        check_ajax_referer('board_nonce', 'nonce');
        if (!Roles::can_access_cp()) wp_send_json_error();
        $id = intval($_POST['cert_id']);
        DB::update_certificate_status($id, 'revoked');
        Plugin::log(__('Certificate Revoked', 'board'), sprintf(__('Certificate ID %d revoked.', 'board'), $id), get_current_user_id());
        wp_send_json_success(array('message' => __('Certificate revoked.', 'board')));
    }

    public function handle_delete_certificate() {
        check_ajax_referer('board_nonce', 'nonce');
        if (!Roles::can_access_cp()) wp_send_json_error();
        $id = intval($_POST['cert_id']);
        if (DB::delete_certificate($id)) {
            Plugin::log(__('Certificate Deleted', 'board'), sprintf(__('Certificate ID %d deleted.', 'board'), $id), get_current_user_id());
            wp_send_json_success(array('message' => __('Record deleted.', 'board')));
        } else {
            wp_send_json_error();
        }
    }

    public function handle_delete_question() {
        check_ajax_referer('board_nonce', 'nonce');
        if (!Roles::can_access_cp()) wp_send_json_error();
        $id = intval($_POST['question_id']);
        global $wpdb;
        $wpdb->delete($wpdb->prefix . 'board_questions', array('id' => $id));
        $wpdb->delete($wpdb->prefix . 'board_exam_questions', array('question_id' => $id));
        wp_send_json_success(array('message' => __('Question deleted.', 'board')));
    }

    public function handle_delete_exam() {
        check_ajax_referer('board_nonce', 'nonce');
        if (!Roles::can_access_cp()) wp_send_json_error();
        $id = intval($_POST['exam_id']);
        if (DB::delete_exam($id)) {
            Plugin::log(__('Exam Deleted', 'board'), sprintf(__('Exam ID %d deleted.', 'board'), $id), get_current_user_id());
            wp_send_json_success(array('message' => __('Exam deleted.', 'board')));
        } else {
            wp_send_json_error();
        }
    }

    public function handle_export_programs() {
        check_admin_referer('board_export_programs');
        if (!Roles::can_access_cp()) wp_die(__('Unauthorized', 'board'));

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=gshb_programs_export.csv');
        $output = fopen('php://output', 'w');
        fputcsv($output, array('ID', 'Title', 'Code', 'Type', 'Duration'));

        $progs = DB::get_programs();
        foreach ($progs as $p) {
            fputcsv($output, array(
                $p->id,
                $p->title,
                $p->code,
                $p->type,
                $p->duration
            ));
        }
        fclose($output);
        exit;
    }

    public function handle_add_user() {
        check_ajax_referer('board_nonce', 'nonce');
        if (!Roles::can_access_cp()) wp_send_json_error();

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
            $user = new \WP_User($user_id);
            $user->set_role($role);
            update_user_meta($user_id, 'membership_status', 'active');
            update_user_meta($user_id, 'verification_code', 'GSHB-' . strtoupper(wp_generate_password(8, false)));

            Plugin::log(__('User Created', 'board'), sprintf(__('New user %s created manually.', 'board'), $user_login), get_current_user_id());
            wp_send_json_success(array('message' => __('User created successfully.', 'board')));
        }
    }

    public function handle_import_users() {
        check_admin_referer('board_import_nonce');
        if (!Roles::can_access_cp()) wp_die(__('Unauthorized', 'board'));

        $errors = array();
        $imported = 0;

        if (!empty($_FILES['import_file']['tmp_name'])) {
            $file = fopen($_FILES['import_file']['tmp_name'], 'r');
            $headers = fgetcsv($file); // skip header

            while (($row = fgetcsv($file)) !== FALSE) {
                $email = isset($row[3]) ? sanitize_email($row[3]) : '';
                if (!$email || !is_email($email)) {
                    $errors[] = sprintf(__('Invalid email at row %d', 'board'), $imported + 2);
                    continue;
                }

                if (email_exists($email)) {
                    $errors[] = sprintf(__('User %s already exists', 'board'), $email);
                    continue;
                }

                $user_login = isset($row[2]) ? sanitize_user($row[2]) : $email;
                $user_id = wp_create_user($user_login, wp_generate_password(), $email);

                if (is_wp_error($user_id)) {
                    $errors[] = $user_id->get_error_message();
                } else {
                    if (isset($row[1])) update_user_meta($user_id, 'verification_code', sanitize_text_field($row[1]));
                    if (isset($row[5])) update_user_meta($user_id, 'membership_status', sanitize_text_field($row[5]));
                    if (isset($row[4])) {
                        $user = new \WP_User($user_id);
                        $user->set_role(sanitize_text_field($row[4]));
                    }
                    $imported++;
                }
            }
            fclose($file);
            Plugin::log(__('Users Imported', 'board'), sprintf(__('%d users imported via CSV.', 'board'), $imported), get_current_user_id());
        }

        if (!empty($errors)) {
            set_transient('board_import_errors', $errors, 30);
            wp_redirect(home_url('/cp?cp_tab=users&import=error'));
        } else {
            wp_redirect(home_url('/cp?cp_tab=users&import=success'));
        }
        exit;
    }

    public function handle_generate_certificate() {
        check_ajax_referer('board_nonce', 'nonce');
        if (!Roles::can_access_cp()) wp_send_json_error();

        $user_id = !empty($_POST['user_id']) ? intval($_POST['user_id']) : null;
        $type = sanitize_text_field($_POST['cert_type']);
        $title = !empty($_POST['title']) ? sanitize_text_field($_POST['title']) : '';

        if ($user_id && empty($title)) {
            $user = get_userdata($user_id);
            $title = $user->display_name . ' - ' . $type;
        }

        $type_map = array(
            'Course' => 'CRS',
            'Diploma' => 'DIP',
            'Board Membership' => 'BRD',
            'Exam Certificate' => 'EXM',
            'Membership' => 'MEM'
        );
        $prefix = isset($type_map[$type]) ? $type_map[$type] : 'GEN';

        global $wpdb;
        $max_id = $wpdb->get_var("SELECT MAX(id) FROM {$wpdb->prefix}board_certificates");
        $next_id = ($max_id ? intval($max_id) : 0) + 1;
        $serial = 'GSHB-' . $prefix . '-' . date('Y') . '-' . str_pad($next_id, 4, '0', STR_PAD_LEFT);

        DB::save_certificate(array(
            'user_id' => $user_id,
            'title' => $title,
            'serial_number' => $serial,
            'type' => $type,
            'status' => 'active'
        ));

        Plugin::log(__('Certificate Generated', 'board'), sprintf(__('Certificate %s generated.', 'board'), $serial));

        if ($user_id) {
            $user = get_userdata($user_id);
            \GSHB\Board\Core\Email::send($user->user_email, 'certificate_issue', array(
                'name'  => $user->display_name,
                'title' => $title,
                'code'  => $serial
            ));
        }

        wp_send_json_success(array('message' => __('Certificate generated successfully.', 'board'), 'serial' => $serial));
    }

    public function handle_link_certificate() {
        check_ajax_referer('board_nonce', 'nonce');
        if (!Roles::can_access_cp()) wp_send_json_error();

        $cert_id = intval($_POST['cert_id']);
        $user_id = intval($_POST['user_id']);

        global $wpdb;
        $table = $wpdb->prefix . 'board_certificates';
        $wpdb->update($table, array('user_id' => $user_id), array('id' => $cert_id));

        Plugin::log(__('Certificate Linked', 'board'), sprintf(__('Certificate ID %d linked to user ID %d.', 'board'), $cert_id, $user_id));
        wp_send_json_success(array('message' => __('Certificate linked successfully.', 'board')));
    }

    public function handle_link_membership() {
        check_ajax_referer('board_nonce', 'nonce');
        if (!Roles::can_access_cp()) wp_send_json_error();

        $membership_id = intval($_POST['membership_id']);
        $user_id = intval($_POST['user_id']);

        global $wpdb;
        $table = $wpdb->prefix . 'board_memberships';
        $wpdb->update($table, array('user_id' => $user_id), array('id' => $membership_id));

        Plugin::log(__('Membership Linked', 'board'), sprintf(__('Membership ID %d linked to user ID %d.', 'board'), $membership_id, $user_id));
        wp_send_json_success(array('message' => __('Membership linked successfully.', 'board')));
    }

    public function handle_update_role() {
        check_ajax_referer('board_nonce', 'nonce');
        if (!Roles::can_access_cp()) wp_send_json_error();
        $user_id = intval($_POST['user_id']);
        $role = sanitize_text_field($_POST['role']);
        $user = new \WP_User($user_id);
        $user->set_role($role);
        Plugin::log(__('Role Updated', 'board'), sprintf(__('User %d role changed to %s.', 'board'), $user_id, $role));
        wp_send_json_success(array('message' => __('Role updated.', 'board')));
    }

    public function handle_update_status() {
        check_ajax_referer('board_nonce', 'nonce');
        if (!Roles::can_access_cp()) wp_send_json_error();
        $user_id = intval($_POST['user_id']);
        $status = sanitize_text_field($_POST['status']);
        update_user_meta($user_id, 'membership_status', $status);
        Plugin::log(__('Status Updated', 'board'), sprintf(__('User %d status changed to %s.', 'board'), $user_id, $status));
        wp_send_json_success(array('message' => __('Status updated.', 'board')));
    }

    public function handle_delete_user() {
        check_ajax_referer('board_nonce', 'nonce');
        if (!Roles::can_access_cp()) wp_send_json_error();

        $user_id = intval($_POST['user_id']);
        if ($user_id == get_current_user_id()) wp_send_json_error(array('message' => __('Cannot delete yourself.', 'board')));

        require_once(ABSPATH . 'wp-admin/includes/user.php');
        if (wp_delete_user($user_id)) {
            Plugin::log(__('User Deleted', 'board'), sprintf(__('User ID %d was deleted.', 'board'), $user_id));
            wp_send_json_success(array('message' => __('User deleted.', 'board')));
        } else {
            wp_send_json_error();
        }
    }

    public function handle_export_users() {
        check_admin_referer('board_export_users');
        if (!Roles::can_access_cp()) wp_die(__('Unauthorized', 'board'));

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=gshb_users_export.csv');
        $output = fopen('php://output', 'w');
        fputcsv($output, array('ID', 'Membership ID', 'Name', 'Email', 'Roles', 'Status', 'Registration Date'));

        $users = get_users();
        foreach ($users as $u) {
            $id_code = get_user_meta($u->ID, 'verification_code', true) ?: 'N/A';
            $status = get_user_meta($u->ID, 'membership_status', true) ?: 'active';

            fputcsv($output, array(
                $u->ID,
                $id_code,
                $u->display_name,
                $u->user_email,
                implode(',', $u->roles),
                $status,
                $u->user_registered
            ));
        }
        fclose($output);
        exit;
    }

    public function handle_save_exam() {
        check_ajax_referer('board_nonce', 'nonce');
        if (!Roles::can_access_cp()) wp_send_json_error();

        $id = !empty($_POST['exam_id']) ? intval($_POST['exam_id']) : null;
        $title = sanitize_text_field($_POST['title']);
        $code = sanitize_text_field($_POST['exam_code']);

        // Auto-generate code if missing and new
        if (!$id && empty($code)) {
            global $wpdb;
            $max_id = $wpdb->get_var("SELECT MAX(id) FROM {$wpdb->prefix}board_exams");
            $next_id = ($max_id ? intval($max_id) : 0) + 1;
            $code = 'EXM-' . date('Y') . '-' . str_pad($next_id, 4, '0', STR_PAD_LEFT);
        }

        $pid = !empty($_POST['program_id']) ? intval($_POST['program_id']) : null;
        $due = sanitize_text_field($_POST['exam_due']);
        $passing = intval($_POST['passing_percentage'] ?: 60);
        $timer = intval($_POST['time_limit'] ?: 30);

        $exam_data = array(
            'title' => $title,
            'code' => $code,
            'program_id' => $pid,
            'due_date' => $due,
            'passing_percentage' => $passing,
            'time_limit' => $timer
        );
        if ($id) $exam_data['id'] = $id;

        DB::save_exam($exam_data);

        Plugin::log(__('Exam Configured', 'board'), sprintf(__('Exam %s (Code: %s) saved with passing criteria: %d%%.', $title, $code, $passing)));
        wp_send_json_success(array('message' => __('Exam saved.', 'board')));
    }

    public function handle_save_program() {
        check_ajax_referer('board_nonce', 'nonce');
        if (!Roles::can_access_cp()) wp_send_json_error();

        $id = !empty($_POST['program_id']) ? intval($_POST['program_id']) : null;
        $title = sanitize_text_field($_POST['title']);
        $desc = sanitize_textarea_field($_POST['desc']);
        $type = sanitize_text_field($_POST['type']);
        $category = sanitize_text_field($_POST['category']);
        $instructor = sanitize_text_field($_POST['instructor']);
        $credits = intval($_POST['credits']);
        $duration = sanitize_text_field($_POST['duration']);

        // Generate Unique Code if new
        if (!$id) {
            global $wpdb;
            $max_id = $wpdb->get_var("SELECT MAX(id) FROM {$wpdb->prefix}board_programs");
            $next_id = ($max_id ? intval($max_id) : 0) + 1;
            $code = 'GSHB-PROG-' . str_pad($next_id, 4, '0', STR_PAD_LEFT);
        } else {
            $code = sanitize_text_field($_POST['code']);
        }

        $data = array(
            'title' => $title,
            'description' => $desc,
            'code' => $code,
            'type' => $type,
            'category' => $category,
            'instructor' => $instructor,
            'credits' => $credits,
            'duration' => $duration
        );
        if ($id) $data['id'] = $id;

        DB::save_program($data);

        Plugin::log(__('Program Saved', 'board'), sprintf(__('Program %s processed.', 'board'), $title));
        wp_send_json_success(array('message' => __('Program saved.', 'board'), 'code' => $code));
    }

    public function handle_delete_program() {
        check_ajax_referer('board_nonce', 'nonce');
        if (!Roles::can_access_cp()) wp_send_json_error();
        $id = intval($_POST['program_id']);
        if (DB::delete_program($id)) {
            Plugin::log(__('Program Deleted', 'board'), sprintf(__('Program ID %d deleted.', 'board'), $id));
            wp_send_json_success(array('message' => __('Program deleted.', 'board')));
        } else {
            wp_send_json_error();
        }
    }

    public function handle_assign_exam() {
        check_ajax_referer('board_nonce', 'nonce');
        if (!Roles::can_access_cp()) wp_send_json_error();

        $user_id = !empty($_POST['user_id']) ? intval($_POST['user_id']) : null;
        $exam_id = intval($_POST['exam_id']);

        if ($user_id) {
            $assigned = get_user_meta($user_id, 'assigned_exams', true);
            if (!is_array($assigned)) $assigned = array();

            if (!in_array($exam_id, $assigned)) {
                $assigned[] = $exam_id;
                update_user_meta($user_id, 'assigned_exams', $assigned);
            }
            Plugin::log(__('Exam Assigned', 'board'), sprintf(__('Exam %d assigned to user %d.', 'board'), $exam_id, $user_id));
        } else {
            // Log manual entry for exam without user link
            Plugin::log(__('Exam Record Created', 'board'), sprintf(__('Exam %d record created manually.', 'board'), $exam_id));
        }

        wp_send_json_success(array('message' => __('Exam record processed.', 'board')));
    }

    public function handle_approval() {
        check_ajax_referer('board_nonce', 'nonce');
        if (!Roles::can_access_cp()) wp_send_json_error();

        $request_id = intval($_POST['request_id']);
        $request = DB::get_membership_by_id($request_id);

        if (!$request) wp_send_json_error();

        $user_id = $request->user_id;
        $user = get_userdata($user_id);
        $u = new \WP_User($user_id);
        $u->set_role('certified_member');

        update_user_meta($user_id, 'country', $request->country);
        update_user_meta($user_id, 'specialty', $request->specialty);
        update_user_meta($user_id, 'membership_status', 'active');

        $expiry = date('Y-m-d H:i:s', strtotime('+1 year'));
        update_user_meta($user_id, 'membership_expiry_date', $expiry);

        $verify_code = 'GSHB-' . strtoupper(wp_generate_password(8, false));
        update_user_meta($user_id, 'verification_code', $verify_code);

        DB::save_certificate(array(
            'user_id' => $user_id,
            'title' => $user->display_name,
            'serial_number' => $verify_code,
            'type' => __('Membership', 'board'),
            'status' => 'active'
        ));

        DB::save_membership(array(
            'id' => $request_id,
            'status' => 'active',
            'expiry_date' => $expiry
        ));

        Plugin::log(__('Membership Approved', 'board'), sprintf(__('User %d approved for certified membership.', 'board'), $user_id));

        \GSHB\Board\Core\Email::send($user->user_email, 'membership_approval', array(
            'name' => $user->display_name,
            'code' => $verify_code
        ));

        wp_send_json_success(array('message' => __('Membership approved.', 'board'), 'code' => $verify_code));
    }

    public function handle_save_general_settings() {
        check_ajax_referer('board_nonce', 'nonce');
        if (!Roles::can_access_cp()) wp_send_json_error();

        update_option('board_org_name', sanitize_text_field($_POST['org_name']));
        update_option('board_date_format', sanitize_text_field($_POST['date_format']));
        update_option('board_default_role', sanitize_text_field($_POST['default_role']));
        update_option('board_contact_email', sanitize_email($_POST['contact_email']));
        update_option('board_notify_email', sanitize_email($_POST['notify_email']));
        update_option('board_timezone', sanitize_text_field($_POST['timezone']));

        Plugin::log(__('Settings Updated', 'board'), __('General settings were updated.', 'board'), get_current_user_id());
        wp_send_json_success(array('message' => __('General settings saved.', 'board')));
    }

    public function handle_save_design_settings() {
        check_ajax_referer('board_nonce', 'nonce');
        if (!Roles::can_access_cp()) wp_send_json_error();

        if (!empty($_FILES['board_logo']['name'])) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attachment_id = media_handle_upload('board_logo', 0);
            if (!is_wp_error($attachment_id)) {
                update_option('board_logo_url', wp_get_attachment_url($attachment_id));
            }
        }

        update_option('board_primary_color', sanitize_hex_color($_POST['primary_color']));
        update_option('board_font_family', sanitize_text_field($_POST['font_family']));
        update_option('board_layout_style', sanitize_text_field($_POST['layout_style']));
        update_option('board_ui_density', sanitize_text_field($_POST['ui_density']));
        update_option('board_enable_animations', isset($_POST['enable_animations']) ? 'on' : 'off');
        update_option('board_sticky_header', isset($_POST['sticky_header']) ? 'on' : 'off');
        update_option('board_custom_css', wp_strip_all_tags($_POST['custom_css']));

        Plugin::log(__('Design Updated', 'board'), __('Design and branding settings were updated.', 'board'), get_current_user_id());
        wp_send_json_success(array('message' => __('Design settings saved.', 'board')));
    }

    public function handle_save_advanced_settings() {
        check_ajax_referer('board_nonce', 'nonce');
        if (!Roles::can_access_cp()) wp_send_json_error();
        update_option('board_debug_mode', sanitize_text_field($_POST['debug_mode']));
        Plugin::log(__('Settings Updated', 'board'), __('Advanced settings were updated.', 'board'), get_current_user_id());
        wp_send_json_success(array('message' => __('Advanced settings saved.', 'board')));
    }

    public function handle_save_email_settings() {
        check_ajax_referer('board_nonce', 'nonce');
        if (!Roles::can_access_cp()) wp_send_json_error();

        update_option('board_email_smtp_enabled', sanitize_text_field($_POST['email_smtp_enabled']));
        update_option('board_email_smtp_host', sanitize_text_field($_POST['email_smtp_host']));
        update_option('board_email_smtp_port', intval($_POST['email_smtp_port']));
        update_option('board_email_smtp_user', sanitize_text_field($_POST['email_smtp_user']));
        if (!empty($_POST['email_smtp_pass'])) {
            update_option('board_email_smtp_pass', $_POST['email_smtp_pass']);
        }
        update_option('board_email_smtp_secure', sanitize_text_field($_POST['email_smtp_secure']));
        update_option('board_email_from_address', sanitize_email($_POST['email_from_address']));
        update_option('board_email_from_name', sanitize_text_field($_POST['email_from_name']));

        Plugin::log(__('Email Settings Updated', 'board'), __('Email and SMTP settings were updated.', 'board'), get_current_user_id());
        wp_send_json_success(array('message' => __('Email configuration saved.', 'board')));
    }

    public function handle_save_email_templates() {
        check_ajax_referer('board_nonce', 'nonce');
        if (!Roles::can_access_cp()) wp_send_json_error();

        foreach ($_POST as $key => $val) {
            if (strpos($key, 'template_') === 0) {
                update_option('board_email_' . $key, wp_kses_post($val));
            }
        }

        Plugin::log(__('Email Templates Updated', 'board'), __('Email templates were customized.', 'board'), get_current_user_id());
        wp_send_json_success(array('message' => __('All templates updated.', 'board')));
    }

    public function handle_restore_backup() {
        check_admin_referer('board_restore_nonce');
        if (!Roles::can_access_cp()) wp_die(__('Unauthorized', 'board'));
        if (!empty($_FILES['backup_file']['tmp_name'])) {
            $data = json_decode(file_get_contents($_FILES['backup_file']['tmp_name']), true);
            if ($data) {
                if (isset($data['settings'])) {
                    foreach ($data['settings'] as $key => $val) {
                        update_option('board_' . $key, $val);
                    }
                }

                global $wpdb;
                if (isset($data['programs'])) {
                    $table = $wpdb->prefix . 'board_programs';
                    foreach ($data['programs'] as $p) {
                        $wpdb->replace($table, (array)$p);
                    }
                }
                if (isset($data['exams'])) {
                    $table = $wpdb->prefix . 'board_exams';
                    foreach ($data['exams'] as $e) {
                        $wpdb->replace($table, (array)$e);
                    }
                }
                if (isset($data['certificates'])) {
                    $table = $wpdb->prefix . 'board_certificates';
                    foreach ($data['certificates'] as $c) {
                        $wpdb->replace($table, (array)$c);
                    }
                }
                Plugin::log(__('System Restored', 'board'), __('System data was restored from backup.', 'board'), get_current_user_id());
            }
        }
        wp_redirect(home_url('/cp?cp_tab=settings&set_tab=backup&restore=success'));
        exit;
    }

    public function handle_import_programs() {
        check_admin_referer('board_import_nonce');
        if (!Roles::can_access_cp()) wp_die(__('Unauthorized', 'board'));

        if (!empty($_FILES['import_file']['tmp_name'])) {
            $file = fopen($_FILES['import_file']['tmp_name'], 'r');
            fgetcsv($file);
            while (($row = fgetcsv($file)) !== FALSE) {
                if (isset($row[1], $row[2])) {
                    DB::save_program(array(
                        'title' => sanitize_text_field($row[1]),
                        'code'  => sanitize_text_field($row[2]),
                        'type'  => sanitize_text_field($row[3] ?: 'Course'),
                        'duration' => sanitize_text_field($row[4])
                    ));
                }
            }
            fclose($file);
        }
        wp_redirect(home_url('/cp?cp_tab=programs&import=success'));
        exit;
    }

    public function handle_import_exams() {
        check_admin_referer('board_import_nonce');
        if (!Roles::can_access_cp()) wp_die(__('Unauthorized', 'board'));

        if (!empty($_FILES['import_file']['tmp_name'])) {
            $file = fopen($_FILES['import_file']['tmp_name'], 'r');
            fgetcsv($file);
            while (($row = fgetcsv($file)) !== FALSE) {
                if (isset($row[2], $row[3])) {
                    DB::save_exam(array(
                        'program_id' => intval($row[1]),
                        'title' => sanitize_text_field($row[2]),
                        'code'  => sanitize_text_field($row[3]),
                        'due_date' => sanitize_text_field($row[4])
                    ));
                }
            }
            fclose($file);
        }
        wp_redirect(home_url('/cp?cp_tab=exams&import=success'));
        exit;
    }

    public function handle_import_certificates() {
        check_admin_referer('board_import_nonce');
        if (!Roles::can_access_cp()) wp_die(__('Unauthorized', 'board'));

        if (!empty($_FILES['import_file']['tmp_name'])) {
            $file = fopen($_FILES['import_file']['tmp_name'], 'r');
            fgetcsv($file);
            while (($row = fgetcsv($file)) !== FALSE) {
                if (isset($row[1], $row[3])) {
                    DB::save_certificate(array(
                        'user_id' => intval($row[1]),
                        'title' => sanitize_text_field($row[2]),
                        'serial_number'  => sanitize_text_field($row[3]),
                        'type'  => sanitize_text_field($row[4]),
                        'status'  => sanitize_text_field($row[5] ?: 'active')
                    ));
                }
            }
            fclose($file);
        }
        wp_redirect(home_url('/cp?cp_tab=certificates&import=success'));
        exit;
    }

    public function handle_export_json() {
        check_ajax_referer('board_nonce', 'nonce');
        if (!Roles::can_access_cp()) wp_send_json_error();

        $data = array(
            'settings' => array(
                'org_name' => get_option('board_org_name'),
                'contact_email' => get_option('board_contact_email'),
                'primary_color' => get_option('board_primary_color'),
                'date_format' => get_option('board_date_format'),
                'default_role' => get_option('board_default_role'),
                'notify_email' => get_option('board_notify_email'),
                'timezone' => get_option('board_timezone'),
                'custom_css' => get_option('board_custom_css'),
            ),
            'programs' => DB::get_programs(),
            'exams' => DB::get_exams(),
            'certificates' => DB::get_certificates(),
        );

        Plugin::log(__('Backup Generated', 'board'), __('A full JSON backup was generated.', 'board'), get_current_user_id());
        wp_send_json_success($data);
    }


    public static function get_pending_requests() {
        global $wpdb;
        $table = $wpdb->prefix . 'board_memberships';
        return $wpdb->get_results("SELECT * FROM $table WHERE status = 'pending' ORDER BY created_at DESC");
    }

    public function handle_submit_application() {
        check_ajax_referer('board_nonce', 'nonce');
        if (!is_user_logged_in()) wp_send_json_error();

        $user_id = get_current_user_id();
        $program_id = intval($_POST['program_id']);
        $data = $_POST['data'];

        DB::save_application(array(
            'user_id' => $user_id,
            'program_id' => $program_id,
            'status' => 'pending',
            'data' => $data,
            'step' => 2
        ));

        Plugin::log(__('Program Application', 'board'), sprintf(__('User %d applied for program %d.', $user_id, $program_id)));
        wp_send_json_success(array('message' => __('Application submitted successfully.', 'board')));
    }

    public function handle_update_application_status() {
        check_ajax_referer('board_nonce', 'nonce');
        if (!Roles::can_access_cp()) wp_send_json_error();

        $id = intval($_POST['app_id']);
        $status = sanitize_text_field($_POST['status']);

        DB::save_application(array(
            'id' => $id,
            'status' => $status
        ));

        Plugin::log(__('Application Status Updated', 'board'), sprintf(__('Application %d status changed to %s.', $id, $status)));
        wp_send_json_success(array('message' => __('Status updated.', 'board')));
    }

    public function handle_get_fellowship_details() {
        check_ajax_referer('board_nonce', 'nonce');
        if (!Roles::can_access_cp()) wp_send_json_error();

        $id = intval($_POST['fellow_id']);
        $fellow = DB::get_fellowship_by_id($id);

        if (!$fellow) wp_send_json_error();

        wp_send_json_success($fellow);
    }

    public function handle_save_question() {
        check_ajax_referer('board_nonce', 'nonce');
        if (!Roles::can_access_cp()) wp_send_json_error();

        $options = isset($_POST['options'][0]) ? explode("\n", str_replace("\r", "", $_POST['options'][0])) : array();
        $options = array_filter(array_map('trim', $options));

        $data = array(
            'category' => sanitize_text_field($_POST['category']),
            'specialization' => sanitize_text_field($_POST['specialization']),
            'type' => sanitize_text_field($_POST['type']),
            'question_text' => sanitize_textarea_field($_POST['question_text']),
            'options' => wp_json_encode(array_values($options)),
            'correct_answer' => sanitize_text_field($_POST['correct_answer'])
        );

        if (!empty($_POST['question_id'])) $data['id'] = intval($_POST['question_id']);

        DB::save_question($data);
        wp_send_json_success(array('message' => __('Question saved to bank.', 'board')));
    }

    public function handle_link_exam_questions() {
        check_ajax_referer('board_nonce', 'nonce');
        if (!Roles::can_access_cp()) wp_send_json_error();

        $exam_id = intval($_POST['exam_id']);
        $question_ids = array_map('intval', $_POST['question_ids']);

        global $wpdb;
        $table = $wpdb->prefix . 'board_exam_questions';

        // Clear existing links
        $wpdb->delete($table, array('exam_id' => $exam_id));

        // Insert new links
        foreach ($question_ids as $idx => $qid) {
            $wpdb->insert($table, array(
                'exam_id' => $exam_id,
                'question_id' => $qid,
                'q_order' => $idx
            ));
        }

        wp_send_json_success(array('message' => __('Exam structure updated.', 'board')));
    }

    public function handle_process_exam_request() {
        check_ajax_referer('board_nonce', 'nonce');
        if (!Roles::can_access_cp()) wp_send_json_error();

        $id = intval($_POST['request_id']);
        $status = sanitize_text_field($_POST['status']);

        global $wpdb;
        $wpdb->update($wpdb->prefix . 'board_exam_requests', array('status' => $status), array('id' => $id));

        // If approved, add to user meta so it appears in their portal
        if ($status === 'approved') {
            $req = $wpdb->get_row($wpdb->prepare("SELECT user_id, exam_id FROM {$wpdb->prefix}board_exam_requests WHERE id = %d", $id));
            if ($req) {
                $assigned = get_user_meta($req->user_id, 'assigned_exams', true) ?: array();
                if (!is_array($assigned)) $assigned = array();

                if (!in_array((int)$req->exam_id, $assigned)) {
                    $assigned[] = (int)$req->exam_id;
                    update_user_meta($req->user_id, 'assigned_exams', $assigned);
                }

                $u = get_userdata($req->user_id);
                $e = $wpdb->get_row($wpdb->prepare("SELECT title FROM {$wpdb->prefix}board_exams WHERE id = %d", $req->exam_id));

                Plugin::log(__('Exam Approved', 'board'), sprintf(__('User %d was approved for exam %d.', $req->user_id, $req->exam_id)), $req->user_id);
            }
        }

        wp_send_json_success(array('message' => __('Request processed.', 'board')));
    }

    public function handle_update_fellowship_status() {
        check_ajax_referer('board_nonce', 'nonce');
        if (!Roles::can_access_cp()) wp_send_json_error();

        $id = intval($_POST['fellow_id']);
        $status = sanitize_text_field($_POST['status']);
        $fellow = DB::get_fellowship_by_id($id);

        if (!$fellow) wp_send_json_error();

        DB::save_fellowship(array(
            'id' => $id,
            'status' => $status
        ));

        $user = get_userdata($fellow->user_id);

        if ($status == 'approved') {
            // Auto generate certificate
            $serial = 'GSHB-FEL-' . date('Y') . '-' . str_pad($id, 4, '0', STR_PAD_LEFT);
            DB::save_certificate(array(
                'user_id' => $fellow->user_id,
                'title' => __('Fellow of the Global Council for Sports Health', 'board'),
                'serial_number' => $serial,
                'type' => 'Fellowship',
                'status' => 'active'
            ));

            \GSHB\Board\Core\Email::send($user->user_email, 'membership_approval', array(
                'name' => $fellow->full_name,
                'code' => $serial
            ));
        } else {
            // Notify of other status changes
            \GSHB\Board\Core\Email::send($user->user_email, 'membership_request', array(
                'name' => $fellow->full_name,
                'status' => str_replace('_', ' ', $status)
            ));
        }

        Plugin::log(__('Fellowship Status Updated', 'board'), sprintf(__('Fellowship application %d changed to %s.', $id, $status)));
        wp_send_json_success(array('message' => __('Fellowship status updated.', 'board')));
    }

    public function handle_user_lookup() {
        check_ajax_referer('board_nonce', 'nonce');
        if (!Roles::can_access_cp()) wp_send_json_error();

        $search = sanitize_text_field($_POST['term']);
        if (strlen($search) < 2) wp_send_json_success(array());

        $users = get_users(array(
            'search'         => '*' . $search . '*',
            'search_columns' => array('user_login', 'user_nicename', 'user_email', 'display_name'),
            'number'         => 10
        ));

        $results = array();
        foreach ($users as $u) {
            $results[] = array(
                'id' => $u->ID,
                'text' => sprintf('%s (@%s) - %s', $u->display_name, $u->user_login, $u->user_email)
            );
        }

        wp_send_json_success($results);
    }
}
