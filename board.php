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
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));

        // Initialize components
        new Board_Roles();
        new Board_Shortcodes();
        new Board_Auth();
        new Board_Branding();
        new Board_CPT();
        new Board_Admin();
        new Board_Cron();
    }

    public function enqueue_assets() {
        wp_enqueue_style('board-style', BOARD_URL . 'assets/css/style.css', array(), '1.0.0');
        wp_enqueue_script('board-scripts', BOARD_URL . 'assets/js/scripts.js', array('jquery'), '1.0.0', true);

        wp_localize_script('board-scripts', 'board_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('board_nonce')
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
