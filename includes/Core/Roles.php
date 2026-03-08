<?php
namespace GSHB\Board\Core;

if (!defined('ABSPATH')) {
    exit;
}

class Roles {

    public function __construct() {
        add_action('after_setup_theme', array($this, 'hide_admin_bar'));
        add_action('admin_init', array($this, 'restrict_admin_access'));
    }

    /**
     * Hide admin bar for all roles except Site Administrator
     */
    public function hide_admin_bar() {
        if (!current_user_can('manage_options')) {
            show_admin_bar(false);
        }
    }

    /**
     * Restrict wp-admin access to Site Administrator
     */
    public function restrict_admin_access() {
        if (defined('DOING_AJAX') && DOING_AJAX) {
            return;
        }

        if (!current_user_can('manage_options')) {
            wp_redirect(home_url());
            exit;
        }
    }

    /**
     * Helper methods to check for specific roles
     */
    public static function is_board_admin($user_id = 0) {
        return self::check_role('board_admin', $user_id);
    }

    public static function is_programs_manager($user_id = 0) {
        return self::check_role('programs_manager', $user_id);
    }

    public static function is_certs_manager($user_id = 0) {
        return self::check_role('certs_manager', $user_id);
    }

    public static function is_academic_supervisor($user_id = 0) {
        return self::check_role('academic_supervisor', $user_id);
    }

    public static function is_certified_member($user_id = 0) {
        return self::check_role('certified_member', $user_id);
    }

    public static function is_member($user_id = 0) {
        return self::check_role('board_member', $user_id);
    }

    public static function can_access_cp($user_id = 0) {
        return (
            current_user_can('manage_options') ||
            self::is_board_admin($user_id) ||
            self::is_programs_manager($user_id) ||
            self::is_certs_manager($user_id) ||
            self::is_academic_supervisor($user_id)
        );
    }

    public static function can_access_mb($user_id = 0) {
        return (
            self::is_certified_member($user_id) ||
            self::is_member($user_id)
        );
    }

    private static function check_role($role, $user_id = 0) {
        if ($user_id == 0) {
            $user = wp_get_current_user();
        } else {
            $user = get_userdata($user_id);
        }

        if (!$user || !($user instanceof \WP_User)) {
            return false;
        }

        return in_array($role, (array) $user->roles);
    }
}
