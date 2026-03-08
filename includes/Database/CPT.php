<?php
namespace GSHB\Board\Database;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Custom Post Types are no longer used as primary data storage.
 * Core data has been migrated to custom SQL tables for performance.
 * We keep this file empty or for legacy support if needed.
 */
class CPT {
    public function __construct() {}
    public function register_cpts() {}
}
