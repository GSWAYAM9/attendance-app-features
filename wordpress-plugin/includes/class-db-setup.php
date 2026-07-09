<?php
/**
 * Database Setup for Plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class EAS_DB_Setup {

    /**
     * Create enquiries table
     */
    public static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'eas_enquiries';

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            subject VARCHAR(255) NOT NULL,
            employee_name VARCHAR(255),
            message LONGTEXT NOT NULL,
            status VARCHAR(50) DEFAULT 'unread',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_email (email),
            INDEX idx_created_at (created_at)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    /**
     * Drop enquiries table
     */
    public static function drop_tables() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'eas_enquiries';
        
        $wpdb->query("DROP TABLE IF EXISTS $table_name");
    }
}
