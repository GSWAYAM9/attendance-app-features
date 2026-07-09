<?php
/**
 * API Client for connecting to Employee Attendance REST API
 */

if (!defined('ABSPATH')) {
    exit;
}

class EAS_API_Client {

    /**
     * Get all employees with optional filtering
     */
    public function get_employees($search = '', $department = '') {
        $url = EAS_API_BASE_URL . '/employees/read';
        
        // Add query parameters
        $params = [];
        if (!empty($search)) {
            $params['search'] = sanitize_text_field($search);
        }
        if (!empty($department)) {
            $params['department'] = sanitize_text_field($department);
        }

        if (!empty($params)) {
            $url = add_query_arg($params, $url);
        }

        return $this->make_request($url);
    }

    /**
     * Get attendance statistics
     */
    public function get_attendance_stats() {
        $url = EAS_API_BASE_URL . '/attendance/stats';
        
        // Check cache first
        $cache_key = 'eas_attendance_stats';
        $cached = get_transient($cache_key);
        
        if ($cached !== false) {
            return $cached;
        }

        $response = $this->make_request($url);
        
        if ($response) {
            // Cache for 5 minutes
            set_transient($cache_key, $response, 5 * MINUTE_IN_SECONDS);
        }

        return $response;
    }

    /**
     * Get attendance history
     */
    public function get_attendance_history($filters = []) {
        $url = EAS_API_BASE_URL . '/attendance/get-history';

        if (!empty($filters)) {
            $url = add_query_arg($filters, $url);
        }

        return $this->make_request($url);
    }

    /**
     * Make HTTP request to API
     */
    private function make_request($url) {
        $args = [
            'timeout' => 10,
            'sslverify' => false, // For local development
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ];

        $response = wp_remote_get($url, $args);

        if (is_wp_error($response)) {
            error_log('EAS API Error: ' . $response->get_error_message());
            return null;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (isset($data['success']) && $data['success']) {
            return $data['data'] ?? null;
        }

        return null;
    }

    /**
     * Get departments list
     */
    public function get_departments() {
        $employees = $this->get_employees();
        
        if (!$employees) {
            return [];
        }

        $departments = [];
        foreach ($employees as $employee) {
            if (isset($employee['department'])) {
                $departments[$employee['department']] = $employee['department'];
            }
        }

        return array_values($departments);
    }
}
