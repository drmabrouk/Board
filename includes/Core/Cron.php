<?php
namespace GSHB\Board\Core;

if (!defined('ABSPATH')) {
    exit;
}

class Cron {

    public function __construct() {
        add_action('board_daily_cleanup', array($this, 'check_membership_expiry'));

        if (!wp_next_scheduled('board_daily_cleanup')) {
            wp_schedule_event(time(), 'daily', 'board_daily_cleanup');
        }
    }

    public function check_membership_expiry() {
        $users = get_users(array(
            'role' => 'certified_member',
            'meta_query' => array(
                array(
                    'key' => 'membership_expiry_date',
                    'value' => current_time('mysql'),
                    'compare' => '<',
                    'type' => 'DATETIME'
                )
            )
        ));

        foreach ($users as $user) {
            $u = new \WP_User($user->ID);
            $u->set_role('board_member');
            update_user_meta($user->ID, 'membership_status', 'expired');
            // Log for debugging/admin
            error_log("Board: Reverted user {$user->ID} to regular member due to expiry.");
        }
    }

    public static function deactivate() {
        wp_clear_scheduled_hook('board_daily_cleanup');
    }
}
