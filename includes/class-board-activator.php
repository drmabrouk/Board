<?php

if (!defined('ABSPATH')) {
    exit;
}

class Board_Activator {

    public static function activate() {
        self::add_roles();
        self::create_pages();
        flush_rewrite_rules();
    }

    public static function deactivate() {
        flush_rewrite_rules();
    }

    private static function add_roles() {
        add_role('board_admin', __('Board Administrator', 'board'), array('read' => true));
        add_role('programs_manager', __('Programs & Exams Manager', 'board'), array('read' => true));
        add_role('certs_manager', __('Certifications & Memberships Manager', 'board'), array('read' => true));
        add_role('academic_supervisor', __('Academic Supervisor', 'board'), array('read' => true));
        add_role('certified_member', __('Certified Member', 'board'), array('read' => true));
        add_role('board_member', __('Member', 'board'), array('read' => true));
    }

    private static function create_pages() {
        $pages = array(
            'registration' => array(
                'title'   => 'Registration / Login',
                'content' => '[board_registration]',
                'slug'    => 'registration'
            ),
            'cp' => array(
                'title'   => 'Control Panel',
                'content' => '[board_cp]',
                'slug'    => 'cp'
            ),
            'mb' => array(
                'title'   => 'Member Account',
                'content' => '[board_mb]',
                'slug'    => 'mb'
            ),
            'qb' => array(
                'title'   => 'Exams',
                'content' => '[board_qb]',
                'slug'    => 'qb'
            ),
            'verify' => array(
                'title'   => 'Verification Portal',
                'content' => '[board_verify]',
                'slug'    => 'verify'
            ),
            'cm-request' => array(
                'title'   => 'Membership Request',
                'content' => '[board_cm_request]',
                'slug'    => 'cm-request'
            ),
            'members' => array(
                'title'   => 'Certified Members Directory',
                'content' => '[board_members]',
                'slug'    => 'members'
            ),
            'programs' => array(
                'title'   => 'Programs & Exams',
                'content' => '[board_programs]',
                'slug'    => 'programs'
            ),
        );

        foreach ($pages as $key => $page) {
            if (!get_page_by_path($page['slug'])) {
                wp_insert_post(array(
                    'post_title'   => $page['title'],
                    'post_content' => $page['content'],
                    'post_status'  => 'publish',
                    'post_type'    => 'page',
                    'post_name'    => $page['slug']
                ));
            }
        }
    }
}
