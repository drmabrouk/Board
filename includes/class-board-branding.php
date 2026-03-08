<?php

if (!defined('ABSPATH')) {
    exit;
}

class Board_Branding {

    public function __construct() {
        // Hide WP version and generator tags
        add_filter('the_generator', '__return_empty_string');
        remove_action('wp_head', 'wp_generator');

        // Clean up footer branding in WP admin
        add_filter('admin_footer_text', array($this, 'custom_admin_footer'));
        add_filter('update_footer', array($this, 'custom_update_footer'), 999);

        // Remove WP version from scripts/styles
        add_filter('style_loader_src', array($this, 'remove_wp_version_strings'), 999);
        add_filter('script_loader_src', array($this, 'remove_wp_version_strings'), 999);
    }

    public function custom_admin_footer() {
        return '<span>' . __('GSHB - Global Sports Health Board', 'board') . '</span>';
    }

    public function custom_update_footer() {
        return '<span>' . __('Professional Management System', 'board') . '</span>';
    }

    public function remove_wp_version_strings($src) {
        if (strpos($src, 'ver=' . get_bloginfo('version'))) {
            $src = remove_query_arg('ver', $src);
        }
        return $src;
    }
}
