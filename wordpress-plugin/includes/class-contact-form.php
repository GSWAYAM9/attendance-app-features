<?php
/**
 * Contact Form Handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class EAS_Contact_Form {

    /**
     * Process contact form submission
     */
    public function process_form() {
        // Validate nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'eas-nonce')) {
            return [
                'success' => false,
                'message' => 'Security check failed',
            ];
        }

        // Get and sanitize form data
        $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        $subject = isset($_POST['subject']) ? sanitize_text_field($_POST['subject']) : '';
        $employee_name = isset($_POST['employee_name']) ? sanitize_text_field($_POST['employee_name']) : '';
        $message = isset($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '';

        // Validate required fields
        if (empty($name) || empty($email) || empty($subject) || empty($message)) {
            return [
                'success' => false,
                'message' => 'Please fill in all required fields',
            ];
        }

        // Validate email
        if (!is_email($email)) {
            return [
                'success' => false,
                'message' => 'Please enter a valid email address',
            ];
        }

        // Save enquiry to WordPress
        $this->save_enquiry($name, $email, $subject, $employee_name, $message);

        // Send email to admin
        $admin_email = get_option('admin_email');
        $headers = [
            'Content-Type: text/plain; charset=UTF-8',
            'From: ' . $email,
        ];

        $email_subject = 'New Employee Attendance Enquiry from ' . $name;
        $email_body = $this->get_email_body($name, $email, $subject, $employee_name, $message);

        wp_mail($admin_email, $email_subject, $email_body, $headers);

        // Send confirmation email to user
        $user_email_subject = 'We received your enquiry - Employee Attendance System';
        $user_email_body = $this->get_user_email_body($name);

        wp_mail($email, $user_email_subject, $user_email_body);

        return [
            'success' => true,
            'message' => 'Thank you for your enquiry. We will get back to you soon.',
        ];
    }

    /**
     * Save enquiry to WordPress
     */
    private function save_enquiry($name, $email, $subject, $employee_name, $message) {
        global $wpdb;

        $table = $wpdb->prefix . 'eas_enquiries';

        $wpdb->insert($table, [
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'employee_name' => $employee_name,
            'message' => $message,
            'created_at' => current_time('mysql'),
        ]);
    }

    /**
     * Get email body for admin
     */
    private function get_email_body($name, $email, $subject, $employee_name, $message) {
        $body = "New Employee Attendance Enquiry\n";
        $body .= "================================\n\n";
        $body .= "Name: " . $name . "\n";
        $body .= "Email: " . $email . "\n";
        $body .= "Subject: " . $subject . "\n";
        
        if (!empty($employee_name)) {
            $body .= "Employee Name: " . $employee_name . "\n";
        }

        $body .= "\nMessage:\n";
        $body .= $message . "\n\n";
        $body .= "---\n";
        $body .= "This is an automated email from the Employee Attendance System.\n";
        $body .= "Please reply to: " . $email . "\n";

        return $body;
    }

    /**
     * Get email body for user
     */
    private function get_user_email_body($name) {
        $body = "Hello " . $name . ",\n\n";
        $body .= "Thank you for sending us your enquiry regarding the Employee Attendance System.\n";
        $body .= "We have received your message and will get back to you as soon as possible.\n\n";
        $body .= "If you have any urgent concerns, please contact us directly.\n\n";
        $body .= "Best regards,\n";
        $body .= "Employee Attendance Team\n";

        return $body;
    }
}
