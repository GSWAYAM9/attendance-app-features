# Employee Attendance System - WordPress Plugin Installation Guide

## Overview

The Employee Attendance System is a custom WordPress plugin that integrates with the Employee Attendance REST API. It displays employee records, attendance statistics, and provides a contact form for employee-related enquiries.

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Active REST API backend running at `http://localhost:8000/api`

## Installation Steps

### Step 1: Copy Plugin Files

1. Download or copy the `wordpress-plugin` folder from the project
2. Rename it to `employee-attendance-system`
3. Upload it to your WordPress installation:
   ```
   wp-content/plugins/employee-attendance-system/
   ```

### Step 2: Activate the Plugin

1. Go to WordPress Admin Dashboard
2. Navigate to **Plugins**
3. Find **Employee Attendance Management System** in the list
4. Click **Activate**

The plugin will automatically:
- Create a database table for enquiries
- Create a page named "Employee Attendance" with the shortcode `[employee_attendance]`
- Register the custom shortcode handler
- Set up admin menus

### Step 3: Configure Settings

1. Go to **Employee Attendance > Settings** in the admin menu
2. Verify or update the API Base URL (default: `http://localhost:8000/api`)
3. Click **Save Changes**

### Step 4: Verify Installation

1. Go to **Pages** and find the "Employee Attendance" page
2. Click **View** to see the public page
3. You should see:
   - Attendance statistics summary
   - Employee directory table
   - Search and filter options
   - Contact form

## Plugin Structure

```
employee-attendance-system/
├── employee-attendance-system.php    # Main plugin file
├── includes/
│   ├── class-api-client.php         # REST API client
│   ├── class-shortcode-handler.php  # Shortcode processor
│   ├── class-admin-page.php         # Admin pages
│   ├── class-contact-form.php       # Contact form handler
│   └── class-db-setup.php           # Database setup
├── assets/
│   ├── css/
│   │   ├── frontend.css             # Frontend styles
│   │   └── admin.css                # Admin panel styles
│   └── js/
│       ├── frontend.js              # Frontend interactivity
│       └── admin.js                 # Admin interactivity
├── README.md                        # This file
└── INSTALLATION.md                  # Installation guide
```

## Features

### Required Features (All Implemented)

- ✓ Create a WordPress page named "Employee Attendance"
- ✓ Display employee records on the page
- ✓ Fetch employee data from the REST API
- ✓ Add basic styling with clean presentation
- ✓ Search/filter option for employee records
- ✓ Contact/enquiry form for employee-related queries

### Optional Plus Points (All Implemented)

- ✓ Custom shortcode: `[employee_attendance]`
- ✓ Custom WordPress plugin (not theme files)
- ✓ Hover effects, table styling, buttons
- ✓ Responsive layout (mobile, tablet, desktop)
- ✓ Attendance summary (total, present, absent, late)
- ✓ AJAX-based search and filtering
- ✓ Admin panel to view enquiries

## Using the Shortcode

### Basic Usage

```
[employee_attendance]
```

### With Attributes

```
[employee_attendance 
  show_stats="true" 
  show_search="true" 
  show_contact_form="true" 
  per_page="10"
]
```

### Attributes

- `show_stats` - Display attendance statistics (true/false, default: true)
- `show_search` - Display search and filter options (true/false, default: true)
- `show_contact_form` - Display contact form (true/false, default: true)
- `per_page` - Employees per page (default: 10)

## Admin Pages

### Dashboard
**Menu: Employee Attendance**
- Quick overview of the plugin
- Quick links to settings, enquiries, and public page

### Settings
**Menu: Employee Attendance > Settings**
- Configure API Base URL
- View and manage the Employee Attendance page

### Enquiries
**Menu: Employee Attendance > Enquiries**
- View all submitted enquiries
- See sender details and messages
- Reply directly to sender emails

## API Integration

The plugin connects to the REST API at:
- Base URL: `http://localhost:8000/api`
- Endpoints used:
  - `GET /employees/read` - List employees
  - `GET /attendance/stats` - Get statistics

Configure the API URL in **Settings** if your API runs on a different address.

## Database Tables

The plugin creates one table:
- `wp_eas_enquiries` - Stores contact form submissions
  - Fields: id, name, email, subject, employee_name, message, status, created_at

## Frequently Asked Questions

### How do I change the page where the shortcode appears?

1. Go to **Pages** in WordPress Admin
2. Either edit the "Employee Attendance" page or create a new page
3. Add the shortcode: `[employee_attendance]`

### How do I display only employees without statistics?

Use the shortcode with attributes:
```
[employee_attendance show_stats="false"]
```

### Can I use this on multiple pages?

Yes! You can add the shortcode to any page or post.

### How do I change the styling?

You have two options:

1. **Override CSS** - Add custom CSS in your theme's `style.css` or via WordPress customizer
2. **Modify plugin CSS** - Edit `/assets/css/frontend.css` (not recommended for updates)

### How do I test if the API is connected?

1. Go to **Employee Attendance > Settings**
2. Check the API URL
3. Make sure the PHP backend is running: `php -S localhost:8000`
4. Test the endpoint: `curl http://localhost:8000/api/employees/read`

## Troubleshooting

### Plugin appears inactive after activation

1. Check PHP version (must be 7.4+)
2. Check file permissions
3. Look at WordPress error logs: `wp-content/debug.log`

### "Loading employees..." message stays

1. Check if the REST API is running
2. Verify the API URL in Settings
3. Open browser console (F12) to see JavaScript errors

### No statistics shown

1. Verify the API backend is running
2. Check that the attendance table exists in the database
3. Test the stats endpoint: `curl http://localhost:8000/api/attendance/stats`

### Contact form not sending emails

1. Verify WordPress can send emails (check `wp_mail()`)
2. Check server SMTP settings
3. Look for errors in WordPress debug log

## Support & Maintenance

### Update the Plugin

When updating the REST API or changing requirements:
1. Go to Employee Attendance > Settings
2. Update the API URL if needed
3. Clear any cache if using caching plugins

### Backup Before Updates

Always backup your WordPress database before updating:
1. Export the `wp_eas_enquiries` table
2. Or use a backup plugin like BackWPup

### Security Considerations

- The plugin uses WordPress nonces for form security
- All form inputs are sanitized
- All outputs are escaped to prevent XSS
- Admin access required for settings and enquiries

## Development Notes

### For Developers

- Plugin uses OOP architecture with separate classes for each concern
- AJAX endpoints for search and contact form
- Proper error handling and logging
- Follows WordPress coding standards

### Key Classes

1. `EAS_API_Client` - Handles API requests with caching
2. `EAS_Shortcode_Handler` - Renders the public-facing content
3. `EAS_Contact_Form` - Processes form submissions
4. `EAS_Admin_Page` - Manages admin pages
5. `EAS_DB_Setup` - Database operations

### Extending the Plugin

To add new features:

1. Add new methods to `EAS_API_Client` for new API endpoints
2. Create new admin pages in `EAS_Admin_Page`
3. Add new AJAX handlers in the main plugin file
4. Add new CSS in `assets/css/frontend.css`

## License

GPL v2 or later

## Support

For issues or questions:
1. Check this installation guide
2. Review the troubleshooting section
3. Check WordPress error logs
4. Test API endpoints manually

## Version History

### 1.0.0 (Initial Release)
- All required features implemented
- All optional plus points implemented
- Fully responsive design
- AJAX search and filtering
- Admin panel for enquiry management
