<?php
/**
 * Plugin Name: Board
 * Description: Professional plugin for Global Sports Health Board (GSHB).
 * Version: 1.0.0
 * Author: Jules
 * Text Domain: board
 * Domain Path: /languages
 */

namespace GSHB\Board;

if (!defined('ABSPATH')) {
    exit;
}

define('BOARD_PATH', plugin_dir_path(__FILE__));
define('BOARD_URL', plugin_dir_url(__FILE__));

/**
 * Autoloader for GSHB Board
 */
spl_autoload_register(function ($class) {
    $prefix = 'GSHB\\Board\\';
    $base_dir = BOARD_PATH . 'includes/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

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
        new Core\Roles();
        new UI\Shortcodes();
        new Auth\Handler();
        new UI\Branding();
        new Database\CPT();
        new Admin\Manager();
        new Core\Cron();
    }

    public static function log($action, $details = '', $user_id = 0) {
        if (!$user_id) $user_id = get_current_user_id();
        global $wpdb;
        $table = $wpdb->prefix . 'board_logs';
        $wpdb->insert($table, array(
            'user_id' => $user_id,
            'action' => $action,
            'details' => $details,
            'ip_address' => $_SERVER['REMOTE_ADDR']
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

        if (is_page('cp') && !Core\Roles::can_access_cp($current_user_id)) {
            wp_redirect(home_url('/registration'));
            exit;
        }

        if (is_page('mb') && !Core\Roles::can_access_mb($current_user_id)) {
            wp_redirect(home_url('/registration'));
            exit;
        }

        if (is_page('cm-request') && !Core\Roles::is_member($current_user_id)) {
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
            global $wpdb;
            $table = $wpdb->prefix . 'board_certificates';
            $cert = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE serial_number = %s", $cert_serial));

            if ($cert) {
                self::log(__('Certificate Viewed', 'board'), sprintf(__('Certificate %s was viewed.', 'board'), $cert_serial));
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
register_activation_hook(__FILE__, array(__NAMESPACE__ . '\\Core\\Activator', 'activate'));
register_deactivation_hook(__FILE__, function() {
    Core\Activator::deactivate();
    if (class_exists(__NAMESPACE__ . '\\Core\\Cron')) {
        Core\Cron::deactivate();
    }
});

// Initialize the plugin
function run_board() {
    new Board();
}
run_board();
