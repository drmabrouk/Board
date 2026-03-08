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
            'supports'    => array('title', 'editor', 'thumbnail', 'excerpt'),
            'show_in_rest' => true,
            'show_in_menu' => false
        ));

        // Exams
        register_post_type('board_exam', array(
            'labels'      => array('name' => __('Exams', 'board')),
            'public'      => false,
            'show_ui'     => true,
            'supports'    => array('title', 'editor'),
            'show_in_rest' => true,
            'show_in_menu' => false
        ));

        // Membership Requests
        register_post_type('board_request', array(
            'labels'      => array('name' => __('Membership Requests', 'board')),
            'public'      => false,
            'show_ui'     => true,
            'supports'    => array('title'),
            'show_in_rest' => false,
            'show_in_menu' => false
        ));

        // Activity Logs
        register_post_type('board_log', array(
            'labels'      => array('name' => __('Activity Logs', 'board')),
            'public'      => false,
            'show_ui'     => true,
            'supports'    => array('title', 'editor'),
            'show_in_rest' => false,
            'show_in_menu' => false
        ));

        // Certificates
        register_post_type('board_certificate', array(
            'labels'      => array('name' => __('Certificates', 'board')),
            'public'      => false,
            'show_ui'     => true,
            'supports'    => array('title', 'thumbnail'),
            'show_in_rest' => false,
            'show_in_menu' => false
        ));
    }
}
