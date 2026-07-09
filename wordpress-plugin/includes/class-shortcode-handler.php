<?php
/**
 * Shortcode Handler for [employee_attendance] shortcode
 */

if (!defined('ABSPATH')) {
    exit;
}

class EAS_Shortcode_Handler {

    private $api_client;

    public function __construct($api_client) {
        $this->api_client = $api_client;
        add_shortcode('employee_attendance', [$this, 'render_shortcode']);
    }

    /**
     * Render the shortcode
     */
    public function render_shortcode($atts) {
        $atts = shortcode_atts([
            'show_stats' => 'true',
            'show_search' => 'true',
            'show_contact_form' => 'true',
            'per_page' => 10,
        ], $atts);

        ob_start();
        ?>
        <div class="eas-container">
            
            <?php if ($atts['show_stats'] === 'true'): ?>
                <?php $this->render_statistics(); ?>
            <?php endif; ?>

            <div class="eas-content-wrapper">
                
                <?php if ($atts['show_search'] === 'true'): ?>
                    <?php $this->render_search_filter(); ?>
                <?php endif; ?>

                <div class="eas-employees-section">
                    <h2>Employee Directory</h2>
                    
                    <div id="eas-employees-list" class="eas-employees-table-wrapper">
                        <table class="eas-employees-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Department</th>
                                    <th>Position</th>
                                    <th>Phone</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="eas-loading">
                                    <td colspan="5" style="text-align: center; padding: 20px;">
                                        <p>Loading employee data...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <?php if ($atts['show_contact_form'] === 'true'): ?>
                <div class="eas-contact-section">
                    <?php $this->render_contact_form(); ?>
                </div>
            <?php endif; ?>

        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Render statistics section
     */
    private function render_statistics() {
        $stats = $this->api_client->get_attendance_stats();

        if (!$stats) {
            return;
        }

        $total = $stats['totalEmployees'] ?? 0;
        $present = $stats['presentToday'] ?? 0;
        $absent = $stats['absentToday'] ?? 0;
        $late = $stats['lateToday'] ?? 0;
        $percentage = $stats['attendancePercentage'] ?? 0;
        ?>

        <div class="eas-statistics">
            <h2>Attendance Summary</h2>
            
            <div class="eas-stats-grid">
                
                <div class="eas-stat-card eas-stat-total">
                    <div class="eas-stat-value"><?php echo esc_html($total); ?></div>
                    <div class="eas-stat-label">Total Employees</div>
                </div>

                <div class="eas-stat-card eas-stat-present">
                    <div class="eas-stat-value"><?php echo esc_html($present); ?></div>
                    <div class="eas-stat-label">Present Today</div>
                </div>

                <div class="eas-stat-card eas-stat-absent">
                    <div class="eas-stat-value"><?php echo esc_html($absent); ?></div>
                    <div class="eas-stat-label">Absent Today</div>
                </div>

                <div class="eas-stat-card eas-stat-late">
                    <div class="eas-stat-value"><?php echo esc_html($late); ?></div>
                    <div class="eas-stat-label">Late Arrivals</div>
                </div>

                <div class="eas-stat-card eas-stat-percentage">
                    <div class="eas-stat-value"><?php echo esc_html($percentage); ?>%</div>
                    <div class="eas-stat-label">Attendance Rate</div>
                </div>

            </div>
        </div>

        <?php
    }

    /**
     * Render search and filter section
     */
    private function render_search_filter() {
        $departments = $this->api_client->get_departments();
        ?>

        <div class="eas-search-filter">
            <h2>Search & Filter</h2>
            
            <div class="eas-search-form">
                
                <div class="eas-search-input-group">
                    <label for="eas-search">Search by Name or Email:</label>
                    <input 
                        type="text" 
                        id="eas-search" 
                        class="eas-search-input" 
                        placeholder="Enter employee name or email..."
                    >
                </div>

                <div class="eas-filter-input-group">
                    <label for="eas-department-filter">Filter by Department:</label>
                    <select id="eas-department-filter" class="eas-department-select">
                        <option value="">All Departments</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?php echo esc_attr($dept); ?>">
                                <?php echo esc_html($dept); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button id="eas-search-btn" class="eas-button eas-button-primary">Search</button>
                <button id="eas-reset-btn" class="eas-button eas-button-secondary">Reset</button>

            </div>
        </div>

        <?php
    }

    /**
     * Render contact form section
     */
    private function render_contact_form() {
        ?>

        <div class="eas-contact-form-wrapper">
            <h2>Employee-Related Query</h2>
            <p>Have a question about employee records or attendance? Send us a message.</p>

            <form id="eas-contact-form" class="eas-contact-form">
                
                <div class="eas-form-group">
                    <label for="eas-name">Your Name *</label>
                    <input 
                        type="text" 
                        id="eas-name" 
                        name="name" 
                        class="eas-form-input" 
                        required
                        placeholder="Enter your name"
                    >
                </div>

                <div class="eas-form-group">
                    <label for="eas-email">Your Email *</label>
                    <input 
                        type="email" 
                        id="eas-email" 
                        name="email" 
                        class="eas-form-input" 
                        required
                        placeholder="Enter your email"
                    >
                </div>

                <div class="eas-form-group">
                    <label for="eas-subject">Subject *</label>
                    <input 
                        type="text" 
                        id="eas-subject" 
                        name="subject" 
                        class="eas-form-input" 
                        required
                        placeholder="Enter subject"
                    >
                </div>

                <div class="eas-form-group">
                    <label for="eas-employee-name">Employee Name (if applicable)</label>
                    <input 
                        type="text" 
                        id="eas-employee-name" 
                        name="employee_name" 
                        class="eas-form-input" 
                        placeholder="Enter employee name"
                    >
                </div>

                <div class="eas-form-group">
                    <label for="eas-message">Message *</label>
                    <textarea 
                        id="eas-message" 
                        name="message" 
                        class="eas-form-textarea" 
                        rows="6" 
                        required
                        placeholder="Enter your message"
                    ></textarea>
                </div>

                <div class="eas-form-group">
                    <button type="submit" class="eas-button eas-button-primary">Send Enquiry</button>
                </div>

                <div id="eas-form-message" class="eas-form-message" style="display: none;"></div>

            </form>
        </div>

        <?php
    }
}
