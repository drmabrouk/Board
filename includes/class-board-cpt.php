<?php

if (!defined('ABSPATH')) {
    exit;
}

class Board_CPT {

    public function __construct() {
        add_action('init', array($this, 'register_cpts'));
    }

    public function register_cpts() {
        // Programs
        register_post_type('board_program', array(
            'labels'      => array('name' => __('Programs', 'board')),
            'public'      => true,
            'has_archive' => true,
            'supports'    => array('title', 'editor', 'thumbnail'),
            'show_in_rest' => true,
            'menu_icon'   => 'dashicons-welcome-learn-more'
        ));

        // Exams
        register_post_type('board_exam', array(
            'labels'      => array('name' => __('Exams', 'board')),
            'public'      => false,
            'show_ui'     => true,
            'supports'    => array('title', 'editor'),
            'show_in_rest' => true,
            'menu_icon'   => 'dashicons-clipboard'
        ));

        // Membership Requests
        register_post_type('board_request', array(
            'labels'      => array('name' => __('Membership Requests', 'board')),
            'public'      => false,
            'show_ui'     => true,
            'supports'    => array('title'),
            'show_in_rest' => false,
            'menu_icon'   => 'dashicons-email-alt'
        ));

        // Activity Logs
        register_post_type('board_log', array(
            'labels'      => array('name' => __('Activity Logs', 'board')),
            'public'      => false,
            'show_ui'     => true,
            'supports'    => array('title', 'editor'),
            'show_in_rest' => false,
            'menu_icon'   => 'dashicons-list-view'
        ));

        // Certificates
        register_post_type('board_certificate', array(
            'labels'      => array('name' => __('Certificates', 'board')),
            'public'      => false,
            'show_ui'     => true,
            'supports'    => array('title'),
            'show_in_rest' => false,
            'menu_icon'   => 'dashicons-awards'
        ));
    }
}
