<?php
/**
 * Plugin Name: Board
 * Description: Professional plugin for Global Sports Health Board (GSHB).
 * Version: 1.0.0
 * Author: Jules
 * Text Domain: board
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

define('BOARD_PATH', plugin_dir_path(__FILE__));
define('BOARD_URL', plugin_dir_url(__FILE__));

// Require the autoloader or individual classes
require_once BOARD_PATH . 'includes/class-board-activator.php';
require_once BOARD_PATH . 'includes/class-board-roles.php';
require_once BOARD_PATH . 'includes/class-board-shortcodes.php';
require_once BOARD_PATH . 'includes/class-board-auth.php';
require_once BOARD_PATH . 'includes/class-board-branding.php';
require_once BOARD_PATH . 'includes/class-board-cpt.php';
require_once BOARD_PATH . 'includes/class-board-admin.php';
require_once BOARD_PATH . 'includes/class-board-cron.php';

/**
 * The main plugin class
 */
class Board {

    public function __construct() {
        $this->init_hooks();
    }

    private function init_hooks() {
        add_action('init', array($this, 'add_rewrite_rules'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
        add_action('wp_head', array($this, 'inject_custom_css'), 100);
        add_action('template_redirect', array($this, 'enforce_page_access'));

        // Initialize components
        new Board_Roles();
        new Board_Shortcodes();
        new Board_Auth();
        new Board_Branding();
        new Board_CPT();
        new Board_Admin();
        new Board_Cron();
    }

    public static function log($title, $message = '', $user_id = 0) {
        if (!$user_id) $user_id = get_current_user_id();

        wp_insert_post(array(
            'post_title'   => $title,
            'post_content' => $message,
            'post_status'  => 'publish',
            'post_type'    => 'board_log',
            'meta_input'   => array(
                'user_id' => $user_id
            )
        ));
    }

    public function add_rewrite_rules() {
        add_rewrite_rule('^certificate/([^/]+)/?', 'index.php?board_cert_serial=$matches[1]', 'top');
        add_filter('query_vars', function($vars) {
            $vars[] = 'board_cert_serial';
            return $vars;
        });
    }

    public function enforce_page_access() {
        if (is_admin()) return;

        $current_user_id = get_current_user_id();

        if (is_page('cp') && !Board_Roles::can_access_cp($current_user_id)) {
            wp_redirect(home_url('/registration'));
            exit;
        }

        if (is_page('mb') && !Board_Roles::can_access_mb($current_user_id)) {
            wp_redirect(home_url('/registration'));
            exit;
        }

        if (is_page('cm-request') && !Board_Roles::is_member($current_user_id)) {
            wp_redirect(home_url('/registration'));
            exit;
        }

        if ((is_page('qb') || is_page('programs')) && !is_user_logged_in()) {
            wp_redirect(home_url('/registration'));
            exit;
        }

        // Handle Certificate Details Template
        $cert_serial = get_query_var('board_cert_serial');
        if ($cert_serial) {
            $certs = get_posts(array(
                'post_type' => 'board_certificate',
                'meta_key' => 'serial_number',
                'meta_value' => $cert_serial,
                'posts_per_page' => 1
            ));

            if (!empty($certs)) {
                $cert = $certs[0];
                Board::log(__('Certificate Viewed', 'board'), sprintf(__('Certificate %s was viewed.', 'board'), $cert_serial));
                include BOARD_PATH . 'templates/certificate-details.php';
                exit;
            } else {
                wp_redirect(home_url('/verify?error=notfound'));
                exit;
            }
        }
    }

    public function inject_custom_css() {
        $custom_css = get_option('board_custom_css');
        $primary_color = get_option('board_primary_color', '#000000');

        echo '<style type="text/css">';
        if ($custom_css) echo $custom_css;
        if ($primary_color !== '#000000') {
            echo ".board-btn-black { background-color: {$primary_color} !important; }";
            echo ".board-cp-header, .board-cp-sidebar, .board-table th { background: {$primary_color} !important; }";
            echo ".board-form-field input, .board-form-field select, .board-form-field textarea { border-color: {$primary_color} !important; }";
        }
        echo '</style>';
    }

    public function enqueue_assets() {
        wp_enqueue_style('dashicons');
        wp_enqueue_style('board-style', BOARD_URL . 'assets/css/style.css', array('dashicons'), '1.0.0');
        wp_enqueue_script('board-scripts', BOARD_URL . 'assets/js/scripts.js', array('jquery'), '1.0.0', true);

        wp_localize_script('board-scripts', 'board_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('board_nonce'),
            'mb_url'   => home_url('/mb')
        ));
    }
}

// Activation and Deactivation hooks
register_activation_hook(__FILE__, array('Board_Activator', 'activate'));
register_deactivation_hook(__FILE__, function() {
    Board_Activator::deactivate();
    if (class_exists('Board_Cron')) {
        Board_Cron::deactivate();
    }
});

// Initialize the plugin
function run_board() {
    $plugin = new Board();
}
run_board();
