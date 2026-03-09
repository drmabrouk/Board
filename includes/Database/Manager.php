<?php
namespace GSHB\Board\Database;

if (!defined('ABSPATH')) {
    exit;
}

class Manager {

    public static function get_programs() {
        global $wpdb;
        $table = $wpdb->prefix . 'board_programs';
        return $wpdb->get_results("SELECT * FROM $table ORDER BY created_at DESC");
    }

    public static function save_program($data) {
        global $wpdb;
        $table = $wpdb->prefix . 'board_programs';

        if (isset($data['id'])) {
            $id = $data['id'];
            unset($data['id']);
            return $wpdb->update($table, $data, array('id' => $id));
        }

        return $wpdb->insert($table, $data);
    }

    public static function delete_program($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'board_programs';
        return $wpdb->delete($table, array('id' => $id));
    }

    public static function get_exams($program_id = null) {
        global $wpdb;
        $table = $wpdb->prefix . 'board_exams';
        if ($program_id) {
            return $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE program_id = %d", $program_id));
        }
        return $wpdb->get_results("SELECT * FROM $table");
    }

    public static function save_exam($data) {
        global $wpdb;
        $table = $wpdb->prefix . 'board_exams';
        return $wpdb->insert($table, $data);
    }

    public static function get_certificates($user_id = null) {
        global $wpdb;
        $table = $wpdb->prefix . 'board_certificates';
        if ($user_id) {
            return $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE user_id = %d", $user_id));
        }
        return $wpdb->get_results("SELECT * FROM $table ORDER BY issue_date DESC");
    }

    public static function save_certificate($data) {
        global $wpdb;
        $table = $wpdb->prefix . 'board_certificates';
        return $wpdb->insert($table, $data);
    }

    public static function update_certificate_status($id, $status) {
        global $wpdb;
        $table = $wpdb->prefix . 'board_certificates';
        return $wpdb->update($table, array('status' => $status), array('id' => $id));
    }

    public static function delete_certificate($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'board_certificates';
        return $wpdb->delete($table, array('id' => $id));
    }

    public static function get_memberships($status = null) {
        global $wpdb;
        $table = $wpdb->prefix . 'board_memberships';
        if ($status) {
            return $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE status = %s", $status));
        }
        return $wpdb->get_results("SELECT * FROM $table");
    }

    public static function save_membership($data) {
        global $wpdb;
        $table = $wpdb->prefix . 'board_memberships';
        if (isset($data['id'])) {
            $id = $data['id'];
            unset($data['id']);
            return $wpdb->update($table, $data, array('id' => $id));
        }
        return $wpdb->insert($table, $data);
    }

    public static function get_membership_by_id($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'board_memberships';
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id));
    }

    public static function get_logs($limit = 50) {
        global $wpdb;
        $table = $wpdb->prefix . 'board_logs';
        return $wpdb->get_results("SELECT * FROM $table ORDER BY created_at DESC LIMIT " . intval($limit));
    }

    public static function save_application($data) {
        global $wpdb;
        $table = $wpdb->prefix . 'board_applications';
        if (isset($data['id'])) {
            $id = $data['id'];
            unset($data['id']);
            return $wpdb->update($table, $data, array('id' => $id));
        }
        return $wpdb->insert($table, $data);
    }

    public static function get_applications($user_id = null, $program_id = null) {
        global $wpdb;
        $table = $wpdb->prefix . 'board_applications';
        $query = "SELECT * FROM $table WHERE 1=1";
        if ($user_id) $query .= $wpdb->prepare(" AND user_id = %d", $user_id);
        if ($program_id) $query .= $wpdb->prepare(" AND program_id = %d", $program_id);
        return $wpdb->get_results($query . " ORDER BY created_at DESC");
    }

    public static function get_program_by_code($code) {
        global $wpdb;
        $table = $wpdb->prefix . 'board_programs';
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE code = %s", $code));
    }
}
