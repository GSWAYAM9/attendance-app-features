# WordPress Plugin - Quick Start (5 Minutes)

## One-Minute Setup

### 1. Copy Plugin Folder
```bash
cp -r wordpress-plugin /path/to/wordpress/wp-content/plugins/employee-attendance-system
```

### 2. Activate in WordPress Admin
- Go to **Plugins**
- Find **Employee Attendance Management System**
- Click **Activate**

### 3. View Your Page
- Go to **Pages**
- Click on **Employee Attendance**
- Click **View Page**

That's it! Your employee directory is now live.

## What You Get

### On the Public Page
- Employee statistics (total, present, absent, late)
- Searchable employee directory table
- Department filter
- Contact form for enquiries

### In Admin Dashboard
- **Employee Attendance** menu with quick links
- **Settings** page to configure API URL
- **Enquiries** page to view contact form submissions

## Shortcode Usage

Add this to any page or post:
```
[employee_attendance]
```

### Hide Specific Sections
```
[employee_attendance show_stats="false" show_contact_form="false"]
```

## Troubleshooting

### Page shows "Loading employees..."
- Make sure PHP backend is running: `php -S localhost:8000` in `backend/` folder
- Check API URL in **Settings** matches your backend

### No contact form emails
- Verify WordPress can send emails
- Check admin email in WordPress Settings
- Look for errors in `wp-content/debug.log`

### Styling looks broken
- Hard refresh browser (Ctrl+Shift+R)
- Check browser console for CSS errors

## Admin Access

- **Dashboard**: Employee Attendance main menu
- **Settings**: Configure API connection
- **Enquiries**: View all submitted contact forms

## Next Steps

1. **Customize Styling**
   - Add custom CSS via WordPress customizer
   - Or edit `wordpress-plugin/assets/css/frontend.css`

2. **Configure API URL**
   - Go to Employee Attendance > Settings
   - Update API Base URL if needed

3. **Test Email Notifications**
   - Submit a test enquiry
   - Verify email received by admin

4. **Deploy to Production**
   - Ensure REST API backend is running
   - Configure CORS if on different domains

## File Structure

```
wordpress-plugin/
├── employee-attendance-system.php    # Main plugin (activate this)
├── includes/                         # PHP classes
├── assets/                           # CSS and JavaScript
├── INSTALLATION.md                   # Full installation guide
└── QUICK_START.md                    # This file
```

## Support

- **Installation Issues**: See INSTALLATION.md
- **Features**: Shortcode, search, statistics, contact form
- **API Integration**: Connects to `http://localhost:8000/api`

Enjoy your Employee Attendance WordPress Plugin!
