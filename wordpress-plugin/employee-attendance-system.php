<?php
/**
 * Plugin Name: Employee Attendance Management System
 * Plugin URI: https://attendance-system.local
 * Description: Display and manage employee attendance records from the REST API
 * Version: 1.0.0
 * Author: Attendance Team
 * Author URI: https://attendance-system.local
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: eas
 * Domain Path: /languages
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('EAS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('EAS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('EAS_PLUGIN_VERSION', '1.0.0');
define('EAS_API_BASE_URL', 'http://localhost:8000/api');

// Include required files
require_once EAS_PLUGIN_DIR . 'includes/class-api-client.php';
require_once EAS_PLUGIN_DIR . 'includes/class-shortcode-handler.php';
require_once EAS_PLUGIN_DIR . 'includes/class-admin-page.php';
require_once EAS_PLUGIN_DIR . 'includes/class-contact-form.php';
require_once EAS_PLUGIN_DIR . 'includes/class-db-setup.php';

/**
 * Main plugin class
 */
class Employee_Attendance_System {
    
    private static $instance = null;
    private $api_client;
    private $shortcode_handler;
    private $admin_page;
    private $contact_form;

    /**
     * Get plugin instance (singleton)
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->api_client = new EAS_API_Client();
        $this->shortcode_handler = new EAS_Shortcode_Handler($this->api_client);
        $this->admin_page = new EAS_Admin_Page();
        $this->contact_form = new EAS_Contact_Form();

        // Hooks
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
        add_action('plugins_loaded', [$this, 'load_textdomain']);
        
        // Activation and deactivation
        register_activation_hook(__FILE__, [$this, 'activate']);
        register_deactivation_hook(__FILE__, [$this, 'deactivate']);
    }

    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_scripts() {
        // Only enqueue on pages that use the shortcode
        global $post;
        
        if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'employee_attendance')) {
            wp_enqueue_style('eas-frontend', EAS_PLUGIN_URL . 'assets/css/frontend.css', [], EAS_PLUGIN_VERSION);
            wp_enqueue_script('eas-frontend', EAS_PLUGIN_URL . 'assets/js/frontend.js', ['jquery'], EAS_PLUGIN_VERSION, true);
            
            // Pass PHP data to JavaScript
            wp_localize_script('eas-frontend', 'easData', [
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'apiBaseUrl' => EAS_API_BASE_URL,
                'nonce' => wp_create_nonce('eas-nonce'),
            ]);
        }
    }

    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'employee-attendance') === false) {
            return;
        }

        wp_enqueue_style('eas-admin', EAS_PLUGIN_URL . 'assets/css/admin.css', [], EAS_PLUGIN_VERSION);
        wp_enqueue_script('eas-admin', EAS_PLUGIN_URL . 'assets/js/admin.js', ['jquery'], EAS_PLUGIN_VERSION, true);
    }

    /**
     * Load plugin text domain
     */
    public function load_textdomain() {
        load_plugin_textdomain('eas', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    /**
     * Activate plugin
     */
    public function activate() {
        // Create database tables
        EAS_DB_Setup::create_tables();

        // Create the Employee Attendance page if it doesn't exist
        $page = get_page_by_title('Employee Attendance');
        
        if (!$page) {
            $page_id = wp_insert_post([
                'post_type' => 'page',
                'post_title' => 'Employee Attendance',
                'post_content' => '[employee_attendance]',
                'post_status' => 'publish',
                'post_author' => get_current_user_id(),
            ]);

            update_option('eas_page_id', $page_id);
        }

        flush_rewrite_rules();
    }

    /**
     * Deactivate plugin
     */
    public function deactivate() {
        flush_rewrite_rules();
        // Note: Database tables are kept for data preservation
        // Uncomment below to drop tables on deactivation
        // EAS_DB_Setup::drop_tables();
    }
}

// Initialize plugin
Employee_Attendance_System::get_instance();

// AJAX handlers
add_action('wp_ajax_eas_get_employees', 'eas_ajax_get_employees');
add_action('wp_ajax_nopriv_eas_get_employees', 'eas_ajax_get_employees');

function eas_ajax_get_employees() {
    check_ajax_referer('eas-nonce', 'nonce');

    $api_client = new EAS_API_Client();
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    $department = isset($_POST['department']) ? sanitize_text_field($_POST['department']) : '';

    $employees = $api_client->get_employees($search, $department);

    if ($employees) {
        wp_send_json_success($employees);
    } else {
        wp_send_json_error('Failed to fetch employees');
    }
}

// AJAX handler for contact form
add_action('wp_ajax_eas_submit_contact_form', 'eas_ajax_submit_contact_form');
add_action('wp_ajax_nopriv_eas_submit_contact_form', 'eas_ajax_submit_contact_form');

function eas_ajax_submit_contact_form() {
    check_ajax_referer('eas-nonce', 'nonce');

    $contact_form = new EAS_Contact_Form();
    $result = $contact_form->process_form();

    if ($result['success']) {
        wp_send_json_success($result);
    } else {
        wp_send_json_error($result);
    }
}
