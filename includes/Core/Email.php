<?php
namespace GSHB\Board\Core;

if (!defined('ABSPATH')) {
    exit;
}

class Email {

    public function __construct() {
        add_action('phpmailer_init', array($this, 'configure_smtp'));
    }

    public function configure_smtp($phpmailer) {
        if (get_option('board_email_smtp_enabled') !== 'on') {
            return;
        }

        $phpmailer->isSMTP();
        $phpmailer->Host       = get_option('board_email_smtp_host');
        $phpmailer->SMTPAuth   = true;
        $phpmailer->Port       = get_option('board_email_smtp_port', 587);
        $phpmailer->Username   = get_option('board_email_smtp_user');
        $phpmailer->Password   = get_option('board_email_smtp_pass');
        $phpmailer->SMTPSecure = get_option('board_email_smtp_secure', 'tls');
        $phpmailer->From       = get_option('board_email_from_address', get_option('admin_email'));
        $phpmailer->FromName   = get_option('board_email_from_name', get_bloginfo('name'));
    }

    /**
     * Send templated email
     *
     * @param string $to
     * @param string $template_id (e.g. 'registration', 'approval')
     * @param array $placeholders
     * @return bool
     */
    public static function send($to, $template_id, $placeholders = array()) {
        $enabled = get_option('board_email_template_' . $template_id . '_enabled', 'on');
        if ($enabled !== 'on') {
            return false;
        }

        $subject = get_option('board_email_template_' . $template_id . '_subject', 'GSHB Notification');
        $body    = get_option('board_email_template_' . $template_id . '_body', '');

        if (empty($body)) {
            // Fallback to default if template is empty
            return false;
        }

        foreach ($placeholders as $key => $val) {
            $subject = str_replace('{' . $key . '}', $val, $subject);
            $body    = str_replace('{' . $key . '}', $val, $body);
        }

        $headers = array('Content-Type: text/html; charset=UTF-8');

        // Use custom from if provided in settings, otherwise WP default (which we hook via configure_smtp if enabled)
        $from_email = get_option('board_email_from_address');
        $from_name  = get_option('board_email_from_name');
        if ($from_email && $from_name) {
            $headers[] = "From: $from_name <$from_email>";
        }

        return wp_mail($to, $subject, nl2br($body), $headers);
    }
}
