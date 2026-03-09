<?php
namespace GSHB\Board\Database;

if (!defined('ABSPATH')) {
    exit;
}

class Schema {

    public static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        // Programs Table
        $table_programs = $wpdb->prefix . 'board_programs';
        $sql_programs = "CREATE TABLE $table_programs (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            code varchar(50) NOT NULL,
            type varchar(50) DEFAULT 'Course',
            category varchar(100) DEFAULT NULL,
            instructor varchar(255) DEFAULT NULL,
            credits int(11) DEFAULT 0,
            duration varchar(100) DEFAULT NULL,
            description text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY code (code)
        ) $charset_collate;";
        dbDelta($sql_programs);

        // Exams Table
        $table_exams = $wpdb->prefix . 'board_exams';
        $sql_exams = "CREATE TABLE $table_exams (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            program_id bigint(20) DEFAULT NULL,
            title varchar(255) NOT NULL,
            code varchar(50) NOT NULL,
            due_date date DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY code (code)
        ) $charset_collate;";
        dbDelta($sql_exams);

        // Certificates Table
        $table_certificates = $wpdb->prefix . 'board_certificates';
        $sql_certificates = "CREATE TABLE $table_certificates (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) DEFAULT NULL,
            title varchar(255) NOT NULL,
            serial_number varchar(100) NOT NULL,
            type varchar(50) NOT NULL,
            status varchar(20) DEFAULT 'active',
            issue_date datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY serial_number (serial_number)
        ) $charset_collate;";
        dbDelta($sql_certificates);

        // Memberships / Requests Table
        $table_memberships = $wpdb->prefix . 'board_memberships';
        $sql_memberships = "CREATE TABLE $table_memberships (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            full_name varchar(255) DEFAULT NULL,
            status varchar(20) DEFAULT 'pending',
            specialty varchar(255) DEFAULT NULL,
            country varchar(100) DEFAULT NULL,
            document_url text DEFAULT NULL,
            expiry_date datetime DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_memberships);

        // Logs Table
        $table_logs = $wpdb->prefix . 'board_logs';
        $sql_logs = "CREATE TABLE $table_logs (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) DEFAULT NULL,
            action varchar(255) NOT NULL,
            details text DEFAULT NULL,
            ip_address varchar(45) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_logs);

        // Program Applications Table
        $table_applications = $wpdb->prefix . 'board_applications';
        $sql_applications = "CREATE TABLE $table_applications (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            program_id bigint(20) NOT NULL,
            status varchar(20) DEFAULT 'pending',
            data text DEFAULT NULL,
            step int(11) DEFAULT 1,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_applications);

        // Fellowships Table
        $table_fellowships = $wpdb->prefix . 'board_fellowships';
        $sql_fellowships = "CREATE TABLE $table_fellowships (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            full_name varchar(255) NOT NULL,
            qualifications text DEFAULT NULL,
            experience text DEFAULT NULL,
            skills text DEFAULT NULL,
            achievements text DEFAULT NULL,
            references_data text DEFAULT NULL,
            evidence_url text DEFAULT NULL,
            status varchar(30) DEFAULT 'pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_fellowships);

        // Questions Table
        $table_questions = $wpdb->prefix . 'board_questions';
        $sql_questions = "CREATE TABLE $table_questions (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            category varchar(100) DEFAULT NULL,
            specialization varchar(100) DEFAULT NULL,
            type varchar(20) DEFAULT 'MCQ',
            question_text text NOT NULL,
            options text DEFAULT NULL,
            correct_answer text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_questions);

        // Exam-Question Linking Table
        $table_exam_questions = $wpdb->prefix . 'board_exam_questions';
        $sql_exam_questions = "CREATE TABLE $table_exam_questions (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            exam_id bigint(20) NOT NULL,
            question_id bigint(20) NOT NULL,
            q_order int(11) DEFAULT 0,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_exam_questions);

        // Exam Requests Table
        $table_exam_requests = $wpdb->prefix . 'board_exam_requests';
        $sql_exam_requests = "CREATE TABLE $table_exam_requests (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            exam_id bigint(20) NOT NULL,
            status varchar(20) DEFAULT 'pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_exam_requests);

        // Exam Results / Submissions Table
        $table_exam_results = $wpdb->prefix . 'board_exam_results';
        $sql_exam_results = "CREATE TABLE $table_exam_results (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            exam_id bigint(20) NOT NULL,
            answers text DEFAULT NULL,
            score int(11) DEFAULT 0,
            status varchar(20) DEFAULT 'pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_exam_results);
    }
}
