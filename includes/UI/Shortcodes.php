<?php
namespace GSHB\Board\UI;

use GSHB\Board\Core\Roles;

if (!defined('ABSPATH')) {
    exit;
}

class Shortcodes {

    public function __construct() {
        add_shortcode('board_info', array($this, 'render_info'));
        add_shortcode('board_main', array($this, 'render_main'));
        add_shortcode('board_registration', array($this, 'render_registration'));
        add_shortcode('board_cp', array($this, 'render_cp'));
        add_shortcode('board_mb', array($this, 'render_mb'));
        add_shortcode('board_qb', array($this, 'render_qb'));
        add_shortcode('board_verify', array($this, 'render_verify'));
        add_shortcode('board_cm_request', array($this, 'render_cm_request'));
        add_shortcode('board_members', array($this, 'render_members'));
        add_shortcode('board_programs', array($this, 'render_programs'));
        add_shortcode('board_fellowship', array($this, 'render_fellowship'));
        add_shortcode('board_fellows_directory', array($this, 'render_fellows_directory'));
        add_shortcode('gshb_certificate', array($this, 'render_single_certificate'));
    }

    public function render_info() {
        return $this->load_template('plugin-info.php');
    }

    public function render_main() {
        return $this->load_template('main-hub.php');
    }

    public function render_registration() {
        return $this->load_template('registration.php');
    }

    public function render_cp() {
        if (!Roles::can_access_cp()) {
            return '<p>' . __('You do not have permission to access the Control Panel.', 'board') . '</p>';
        }
        return $this->load_template('control-panel.php');
    }

    public function render_mb() {
        if (!Roles::can_access_mb()) {
            return '<p>' . __('Please log in to access your member account.', 'board') . '</p>';
        }
        return $this->load_template('member-account.php');
    }

    public function render_qb() {
        // Logic for individual exam access will be in the template or a separate method
        return $this->load_template('exams.php');
    }

    public function render_verify() {
        return $this->load_template('verification.php');
    }

    public function render_cm_request() {
        if (!is_user_logged_in()) {
            return '<p>' . __('Please log in to submit a membership request.', 'board') . '</p>';
        }
        return $this->load_template('membership-request.php');
    }

    public function render_members() {
        return $this->load_template('directory.php');
    }

    public function render_programs() {
        return $this->load_template('programs.php');
    }

    public function render_fellowship() {
        if (!Roles::can_access_mb()) {
            return '<p>' . __('Please log in to access the Fellowship pathway.', 'board') . '</p>';
        }
        return $this->load_template('fellowship-application.php');
    }

    public function render_fellows_directory() {
        return $this->load_template('fellows-directory.php');
    }

    public function render_single_certificate($atts) {
        $a = shortcode_atts(array(
            'id' => '',
            'serial' => ''
        ), $atts);

        global $wpdb;
        $table = $wpdb->prefix . 'board_certificates';
        $cert = null;

        if (!empty($a['id'])) {
            $cert = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", intval($a['id'])));
        } elseif (!empty($a['serial'])) {
            $cert = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE serial_number = %s", sanitize_text_field($a['serial'])));
        } else {
            return '<p>' . __('Please provide a certificate ID or Serial Number.', 'board') . '</p>';
        }

        if (empty($cert)) {
            return '<p>' . __('Certificate not found.', 'board') . '</p>';
        }

        return $this->load_template('certificate-card.php', array('cert' => $cert));
    }

    private function load_template($template_name, $args = array()) {
        $path = BOARD_PATH . 'templates/' . $template_name;
        if (file_exists($path)) {
            ob_start();
            extract($args);
            include $path;
            return ob_get_clean();
        }
        return '';
    }
}
