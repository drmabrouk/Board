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
        new Core\Email();
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
        add_rewrite_rule('^program/([^/]+)/?', 'index.php?board_prog_code=$matches[1]', 'top');
        add_filter('query_vars', function($vars) {
            $vars[] = 'board_cert_serial';
            $vars[] = 'board_prog_code';
            return $vars;
        });
    }

    public function enforce_page_access() {
        if (is_admin()) return;

        $current_user_id = get_current_user_id();

        // Strict isolation for Control Panel
        if (is_page('cp')) {
            if (!Core\Roles::can_access_cp($current_user_id)) {
                wp_safe_redirect(is_user_logged_in() ? home_url() : home_url('/registration'));
                exit;
            }
        }

        // Strict isolation for Member Dashboard
        if (is_page('mb')) {
            if (!Core\Roles::can_access_mb($current_user_id)) {
                wp_safe_redirect(is_user_logged_in() ? home_url() : home_url('/registration'));
                exit;
            }
        }

        // Permissions for requests and pathways
        if (is_page('cm-request') || is_page('fellowship')) {
            if (!Core\Roles::is_member($current_user_id) && !Core\Roles::is_certified_member($current_user_id)) {
                wp_safe_redirect(home_url('/registration'));
                exit;
            }
        }

        // Public but restricted areas
        if ((is_page('qb') || is_page('programs') || is_page('members') || is_page('fellows')) && !is_user_logged_in()) {
            wp_safe_redirect(home_url('/registration'));
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
                wp_safe_redirect(home_url('/verify?error=notfound'));
                exit;
            }
        }

        // Handle Program Details Template
        $prog_code = get_query_var('board_prog_code');
        if ($prog_code) {
            $program = \GSHB\Board\Database\Manager::get_program_by_code($prog_code);
            if ($program) {
                include BOARD_PATH . 'templates/program-details.php';
                exit;
            } else {
                wp_safe_redirect(home_url('/programs?error=notfound'));
                exit;
            }
        }
    }

    public function inject_custom_css() {
        $custom_css = get_option('board_custom_css');
        $primary_color = get_option('board_primary_color', '#000000');
        $font_family = get_option('board_font_family', '-apple-system, system-ui');
        $layout_style = get_option('board_layout_style', 'compact');
        $ui_density = get_option('board_ui_density', 'normal');
        $enable_animations = get_option('board_enable_animations', 'on');
        $sticky_header = get_option('board_sticky_header', 'off');

        echo '<style type="text/css">';
        echo ":root { --board-font-family: {$font_family}; }";

        if ($layout_style === 'spacious') {
            echo ".board-container { max-width: 1600px; padding: 50px; }";
            echo ".board-cp-main { padding: 80px; }";
        }

        if ($ui_density === 'high') {
            echo ".board-table th, .board-table td { padding: 10px 15px; }";
            echo ".board-program-card { padding: 20px; }";
            echo ".board-form-field { margin-bottom: 12px; }";
        }

        if ($enable_animations === 'off') {
            echo "* { transition: none !important; animation: none !important; }";
        }

        if ($sticky_header === 'on') {
            echo ".board-cp-header { position: sticky; top: 0; z-index: 1000; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }";
        }

        if ($custom_css) echo $custom_css;

        if ($primary_color !== '#000000') {
            echo ".board-btn-black { background-color: {$primary_color} !important; border-color: {$primary_color} !important; }";
            echo ".board-cp-header, .board-cp-sidebar, .board-table th { background: {$primary_color} !important; }";
            echo ".board-form-field input:focus, .board-form-field select:focus, .board-form-field textarea:focus { border-color: {$primary_color} !important; }";
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
