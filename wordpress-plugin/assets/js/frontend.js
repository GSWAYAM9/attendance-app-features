/**
 * Employee Attendance System - Frontend JavaScript
 */

jQuery(document).ready(function($) {
    'use strict';

    // Initialize on page load
    loadEmployees();

    // Search button click
    $('#eas-search-btn').on('click', function(e) {
        e.preventDefault();
        loadEmployees();
    });

    // Reset button click
    $('#eas-reset-btn').on('click', function(e) {
        e.preventDefault();
        $('#eas-search').val('');
        $('#eas-department-filter').val('');
        loadEmployees();
    });

    // Enter key in search input
    $('#eas-search').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            loadEmployees();
        }
    });

    // Department filter change
    $('#eas-department-filter').on('change', function() {
        loadEmployees();
    });

    // Contact form submission
    $('#eas-contact-form').on('submit', function(e) {
        e.preventDefault();
        submitContactForm();
    });

    /**
     * Load employees via AJAX
     */
    function loadEmployees() {
        const search = $('#eas-search').val();
        const department = $('#eas-department-filter').val();

        // Show loading state
        $('#eas-employees-list tbody').html(
            '<tr class="eas-loading"><td colspan="5" style="padding: 20px; text-align: center;">Loading...</td></tr>'
        );

        $.ajax({
            url: easData.ajaxUrl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'eas_get_employees',
                nonce: easData.nonce,
                search: search,
                department: department,
            },
            success: function(response) {
                if (response.success) {
                    renderEmployeeTable(response.data);
                } else {
                    showError('Failed to load employees: ' + response.data);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                showError('An error occurred while loading employees.');
            },
        });
    }

    /**
     * Render employee table
     */
    function renderEmployeeTable(employees) {
        const tbody = $('#eas-employees-list tbody');

        if (employees.length === 0) {
            tbody.html(
                '<tr class="eas-loading"><td colspan="5" style="padding: 20px; text-align: center;">No employees found.</td></tr>'
            );
            return;
        }

        let html = '';
        $.each(employees, function(index, employee) {
            html += '<tr>';
            html += '<td>' + escapeHtml(employee.name || '') + '</td>';
            html += '<td><a href="mailto:' + escapeHtml(employee.email || '') + '">' + escapeHtml(employee.email || '') + '</a></td>';
            html += '<td>' + escapeHtml(employee.department || '') + '</td>';
            html += '<td>' + escapeHtml(employee.position || '') + '</td>';
            html += '<td>' + escapeHtml(employee.phone || '') + '</td>';
            html += '</tr>';
        });

        tbody.html(html);
    }

    /**
     * Submit contact form
     */
    function submitContactForm() {
        const form = $('#eas-contact-form');
        const formData = new FormData(form[0]);
        const messageDiv = $('#eas-form-message');

        // Add nonce
        formData.append('nonce', easData.nonce);
        formData.append('action', 'eas_submit_contact_form');

        // Disable submit button
        form.find('button[type="submit"]').prop('disabled', true).text('Sending...');

        $.ajax({
            url: easData.ajaxUrl,
            type: 'POST',
            dataType: 'json',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                form.find('button[type="submit"]').prop('disabled', false).text('Send Enquiry');

                if (response.success) {
                    messageDiv.removeClass('error').addClass('success');
                    messageDiv.text(response.data.message || 'Enquiry sent successfully!');
                    messageDiv.show();

                    // Reset form
                    form[0].reset();

                    // Hide message after 5 seconds
                    setTimeout(function() {
                        messageDiv.fadeOut();
                    }, 5000);
                } else {
                    messageDiv.removeClass('success').addClass('error');
                    messageDiv.text(response.data || 'Failed to send enquiry. Please try again.');
                    messageDiv.show();
                }
            },
            error: function(xhr, status, error) {
                form.find('button[type="submit"]').prop('disabled', false).text('Send Enquiry');
                console.error('AJAX Error:', error);
                messageDiv.removeClass('success').addClass('error');
                messageDiv.text('An error occurred. Please try again.');
                messageDiv.show();
            },
        });
    }

    /**
     * Show error message
     */
    function showError(message) {
        const table = $('#eas-employees-list tbody');
        table.html(
            '<tr class="eas-loading"><td colspan="5" style="padding: 20px; text-align: center; color: red;">' + 
            escapeHtml(message) + 
            '</td></tr>'
        );
    }

    /**
     * Escape HTML to prevent XSS
     */
    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
});
