/**
 * Employee Attendance System - Admin JavaScript
 */

jQuery(document).ready(function($) {
    'use strict';

    // Admin page enhancements can go here
    // Currently handled by WordPress native functionality

    // Example: Highlight menu item
    $(document).ready(function() {
        // Ensure admin menu is properly highlighted
        const currentPage = $('[data-page]').attr('data-page');
        if (currentPage) {
            $('a[href*="' + currentPage + '"]').parent().addClass('current');
        }
    });
});
