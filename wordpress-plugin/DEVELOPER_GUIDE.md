# WordPress Plugin Developer Guide

## Plugin Architecture

### Class Structure

```
Employee_Attendance_System (Main)
├── EAS_API_Client
├── EAS_Shortcode_Handler
├── EAS_Admin_Page
├── EAS_Contact_Form
└── EAS_DB_Setup
```

### Main Plugin Class

**File**: `employee-attendance-system.php`

Handles:
- Plugin initialization
- Asset enqueueing
- Hook registration
- AJAX handler setup
- Activation/deactivation

### API Client Class

**File**: `includes/class-api-client.php`

Methods:
- `get_employees($search, $department)` - Fetch employees with filtering
- `get_attendance_stats()` - Get statistics with caching
- `get_attendance_history($filters)` - Fetch attendance records
- `get_departments()` - Get unique departments list

Features:
- Automatic caching (5 min transients)
- Error handling with null returns
- SSLVERIFY disabled for local dev
- JSON parsing and validation

### Shortcode Handler Class

**File**: `includes/class-shortcode-handler.php`

Methods:
- `render_shortcode($atts)` - Main shortcode renderer
- `render_statistics()` - Statistics display
- `render_search_filter()` - Search UI
- `render_contact_form()` - Form UI

Features:
- Shortcode attributes support
- HTML output buffering
- Proper escaping for security

### Admin Page Class

**File**: `includes/class-admin-page.php`

Methods:
- `add_admin_menu()` - Register menu pages
- `register_settings()` - Settings API registration
- `render_admin_page()` - Main dashboard
- `render_settings_page()` - Settings form
- `render_enquiries_page()` - Enquiries table

Features:
- Multiple submenu pages
- Settings API integration
- Database query display

### Contact Form Class

**File**: `includes/class-contact-form.php`

Methods:
- `process_form()` - Form processing
- `save_enquiry()` - Store in database
- `get_email_body()` - Admin email content
- `get_user_email_body()` - User confirmation email

Features:
- Input validation
- Email sanitization
- Database storage
- Email notifications

### Database Setup Class

**File**: `includes/class-db-setup.php`

Methods:
- `create_tables()` - Create enquiries table
- `drop_tables()` - Drop tables (optional)

Features:
- Uses WordPress dbDelta
- Charset collation support
- Indexes on key fields

## Adding New Features

### Add a New API Endpoint

1. **Update API Client**:
   ```php
   public function get_custom_data($param) {
       $url = EAS_API_BASE_URL . '/custom/endpoint';
       if (!empty($param)) {
           $url = add_query_arg('param', $param, $url);
       }
       return $this->make_request($url);
   }
   ```

2. **Use in shortcode**:
   ```php
   $data = $this->api_client->get_custom_data($value);
   ```

### Add a New Admin Page

1. **Add to Admin Class**:
   ```php
   add_submenu_page(
       'employee-attendance',
       'New Page',
       'New Page',
       'manage_options',
       'new-page',
       [$this, 'render_new_page']
   );
   ```

2. **Create render method**:
   ```php
   public function render_new_page() {
       // Output HTML
   }
   ```

### Add a New Shortcode

1. **Update Shortcode Handler**:
   ```php
   add_shortcode('new_shortcode', [$this, 'render_new_shortcode']);
   ```

2. **Create render method**:
   ```php
   public function render_new_shortcode($atts) {
       ob_start();
       // Output HTML
       return ob_get_clean();
   }
   ```

### Add AJAX Handler

1. **Add action hooks in main file**:
   ```php
   add_action('wp_ajax_eas_custom_action', 'eas_ajax_custom_action');
   add_action('wp_ajax_nopriv_eas_custom_action', 'eas_ajax_custom_action');
   ```

2. **Create handler function**:
   ```php
   function eas_ajax_custom_action() {
       check_ajax_referer('eas-nonce', 'nonce');
       // Process request
       wp_send_json_success($data);
   }
   ```

3. **Call from JavaScript**:
   ```javascript
   $.ajax({
       url: easData.ajaxUrl,
       type: 'POST',
       data: {
           action: 'eas_custom_action',
           nonce: easData.nonce,
       },
       success: function(response) {
           // Handle response
       }
   });
   ```

## Coding Standards

### WordPress Coding Standards

All code follows WordPress standards:
- 4-space indentation (not tabs)
- Single quotes for strings
- Double quotes for HTML attributes
- Proper escaping with `esc_html()`, `esc_attr()`, etc.

### File Organization

```php
<?php
// File header comment
if (!defined('ABSPATH')) {
    exit;
}

class Class_Name {
    // Class docblock
    
    // Properties
    // Methods
}
```

### Naming Conventions

- Classes: `EAS_Class_Name`
- Functions: `eas_function_name()`
- Hooks: `eas_hook_name`
- Variables: `$variable_name`
- Constants: `EAS_CONSTANT`

## Security Practices

### Input Handling

```php
// Sanitize input
$search = sanitize_text_field($_POST['search']);
$email = sanitize_email($_POST['email']);
$content = sanitize_textarea_field($_POST['content']);

// Validate input
if (!is_email($email)) {
    // Handle error
}
```

### Output Escaping

```php
// In HTML
echo esc_html($variable);
echo esc_attr($attribute);

// In URLs
echo esc_url($url);

// In JavaScript
echo wp_json_encode($data);
```

### Nonce Verification

```php
// Create nonce
wp_create_nonce('eas-nonce');

// Verify nonce
check_ajax_referer('eas-nonce', 'nonce');
```

## Debugging

### Enable Debug Mode

Add to `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

### Logging

```php
error_log('Message: ' . print_r($data, true));
```

### Check Logs

```bash
tail -f wp-content/debug.log
```

## Testing

### Manual Testing

1. Activate plugin
2. Check all pages load
3. Test search functionality
4. Submit contact form
5. Check admin pages
6. View enquiries

### API Testing

```bash
# Test employees endpoint
curl http://localhost:8000/api/employees/read

# Test statistics endpoint
curl http://localhost:8000/api/attendance/stats

# Test with filters
curl "http://localhost:8000/api/employees/read?search=john"
```

## Performance Optimization

### Caching

The API client caches statistics for 5 minutes:
```php
$cache_key = 'eas_attendance_stats';
$cached = get_transient($cache_key);

if ($cached !== false) {
    return $cached;
}

// Make request
set_transient($cache_key, $response, 5 * MINUTE_IN_SECONDS);
```

Clear cache manually:
```php
delete_transient('eas_attendance_stats');
```

### Database Optimization

Enquiries table has indexes on:
- `email` - For quick lookups
- `created_at` - For sorting/filtering

## Troubleshooting

### Class Not Found

Make sure file is included:
```php
require_once EAS_PLUGIN_DIR . 'includes/class-name.php';
```

### AJAX Not Working

- Check nonce is correct
- Verify `check_ajax_referer()` passes
- Check JavaScript console for errors
- Verify `wp_localize_script()` works

### Styling Issues

- Check CSS file is enqueued
- Check media queries work
- Test in multiple browsers
- Use browser DevTools

### Database Issues

- Check table exists: `SHOW TABLES LIKE 'wp_eas%'`
- Check WordPress has database write permission
- Verify collation matches WordPress

## Common Customizations

### Change API URL

In Settings or directly in config:
```php
define('EAS_API_BASE_URL', 'https://api.yourdomain.com/api');
```

### Add Custom Columns to Table

Edit `render_contact_form()` in shortcode handler:
```php
<th>New Column</th>
// In loop:
<td><?php echo esc_html($employee->custom_field); ?></td>
```

### Change Email Recipient

Edit `EAS_Contact_Form::get_email_body()`:
```php
$admin_email = 'custom@email.com'; // Instead of get_option
```

### Custom Styling

Override CSS in theme:
```css
.eas-button-primary {
    background: #yourcolor;
}
```

## Deployment Checklist

- [ ] API URL configured correctly
- [ ] CORS headers set on API
- [ ] Email sending tested
- [ ] Database tables created
- [ ] Admin can see enquiries
- [ ] Public page displays employees
- [ ] Search/filter working
- [ ] Contact form submits
- [ ] Styling responsive
- [ ] No console errors

## Version Management

Current version: 1.0.0

To update version:
1. Edit version in main plugin file
2. Update readme
3. Test all features
4. Update changelog

## Support Resources

- WordPress Plugin Development: https://developer.wordpress.org/plugins/
- WordPress Coding Standards: https://developer.wordpress.org/coding-standards/
- PHP Manual: https://www.php.net/manual/
- REST API Docs: https://developer.wordpress.org/rest-api/
