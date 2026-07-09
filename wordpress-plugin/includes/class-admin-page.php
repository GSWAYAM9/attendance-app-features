<?php
/**
 * Admin Settings Page
 */

if (!defined('ABSPATH')) {
    exit;
}

class EAS_Admin_Page {

    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            'Employee Attendance',
            'Employee Attendance',
            'manage_options',
            'employee-attendance',
            [$this, 'render_admin_page'],
            'dashicons-groups',
            25
        );

        add_submenu_page(
            'employee-attendance',
            'Settings',
            'Settings',
            'manage_options',
            'employee-attendance-settings',
            [$this, 'render_settings_page']
        );

        add_submenu_page(
            'employee-attendance',
            'Enquiries',
            'Enquiries',
            'manage_options',
            'employee-attendance-enquiries',
            [$this, 'render_enquiries_page']
        );
    }

    /**
     * Register plugin settings
     */
    public function register_settings() {
        register_setting('eas_settings_group', 'eas_api_url');
        register_setting('eas_settings_group', 'eas_page_id');
    }

    /**
     * Render main admin page
     */
    public function render_admin_page() {
        ?>
        <div class="wrap">
            <h1>Employee Attendance System</h1>
            
            <div class="eas-admin-intro">
                <p>Welcome to the Employee Attendance Management System!</p>
                
                <h2>Quick Links:</h2>
                <ul>
                    <li><a href="<?php echo esc_url(admin_url('admin.php?page=employee-attendance-settings')); ?>">Settings</a></li>
                    <li><a href="<?php echo esc_url(admin_url('admin.php?page=employee-attendance-enquiries')); ?>">View Enquiries</a></li>
                    <li><a href="<?php echo esc_url(get_page_link(get_option('eas_page_id'))); ?>" target="_blank">View Public Page</a></li>
                </ul>

                <h2>About This Plugin:</h2>
                <p>This plugin integrates with the Employee Attendance REST API to display employee records, attendance statistics, and provides a contact form for employee-related enquiries.</p>

                <h2>Features:</h2>
                <ul>
                    <li>Display employee directory</li>
                    <li>Search and filter employees by name and department</li>
                    <li>Show attendance statistics</li>
                    <li>Contact form for employee-related queries</li>
                    <li>Admin panel to manage enquiries</li>
                </ul>

                <h2>Getting Started:</h2>
                <ol>
                    <li>Go to <strong>Settings</strong> and configure the API URL</li>
                    <li>Make sure the REST API backend is running</li>
                    <li>Visit the <strong>Employee Attendance</strong> page to see the employee directory</li>
                    <li>Users can search, filter, and submit enquiries</li>
                </ol>
            </div>
        </div>
        <?php
    }

    /**
     * Render settings page
     */
    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1>Employee Attendance Settings</h1>

            <form action="options.php" method="post">
                <?php settings_fields('eas_settings_group'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="eas_api_url">API Base URL:</label>
                        </th>
                        <td>
                            <input 
                                type="url" 
                                id="eas_api_url" 
                                name="eas_api_url" 
                                value="<?php echo esc_attr(get_option('eas_api_url', EAS_API_BASE_URL)); ?>" 
                                class="regular-text"
                                placeholder="http://localhost:8000/api"
                            >
                            <p class="description">Enter the base URL of the REST API</p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="eas_page_id">Employee Attendance Page:</label>
                        </th>
                        <td>
                            <?php
                            $page_id = get_option('eas_page_id');
                            $page = $page_id ? get_post($page_id) : null;
                            ?>
                            <?php if ($page): ?>
                                <p>
                                    <strong><?php echo esc_html($page->post_title); ?></strong>
                                    <br>
                                    <a href="<?php echo esc_url(get_permalink($page)); ?>" target="_blank">View Page</a> | 
                                    <a href="<?php echo esc_url(get_edit_post_link($page)); ?>">Edit</a>
                                </p>
                            <?php else: ?>
                                <p>No page assigned. The plugin will create one on activation.</p>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>

                <?php submit_button(); ?>
            </form>

            <div class="eas-info-box">
                <h3>Plugin Information:</h3>
                <ul>
                    <li>Version: <?php echo esc_html(EAS_PLUGIN_VERSION); ?></li>
                    <li>Shortcode: <code>[employee_attendance]</code></li>
                    <li>API Base URL: <code><?php echo esc_html(EAS_API_BASE_URL); ?></code></li>
                </ul>
            </div>
        </div>
        <?php
    }

    /**
     * Render enquiries page
     */
    public function render_enquiries_page() {
        global $wpdb;
        $table = $wpdb->prefix . 'eas_enquiries';

        $enquiries = $wpdb->get_results("SELECT * FROM $table ORDER BY created_at DESC LIMIT 100");
        ?>
        <div class="wrap">
            <h1>Employee Enquiries</h1>

            <?php if (empty($enquiries)): ?>
                <p>No enquiries yet.</p>
            <?php else: ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Employee Name</th>
                            <th>Message</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($enquiries as $enquiry): ?>
                            <tr>
                                <td><?php echo esc_html($enquiry->name); ?></td>
                                <td><a href="mailto:<?php echo esc_attr($enquiry->email); ?>"><?php echo esc_html($enquiry->email); ?></a></td>
                                <td><?php echo esc_html($enquiry->subject); ?></td>
                                <td><?php echo esc_html($enquiry->employee_name ?: '-'); ?></td>
                                <td>
                                    <details>
                                        <summary>View</summary>
                                        <p><?php echo wp_kses_post(wpautop($enquiry->message)); ?></p>
                                    </details>
                                </td>
                                <td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($enquiry->created_at))); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        <?php
    }
}
