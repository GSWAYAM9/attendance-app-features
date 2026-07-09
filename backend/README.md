# Attendance System - PHP Backend API

## Overview

This is a PHP REST API for an employee attendance management system. It provides endpoints for managing employees, tracking attendance (check-in/check-out), and generating reports.

## Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- A web server with mod_rewrite enabled (Apache recommended)

## Setup Instructions

### 1. Database Setup

Create a new MySQL database and import the schema:

```bash
mysql -u root -p < backend/db/schema.sql
```

Or manually:

```sql
CREATE DATABASE attendance_system;
USE attendance_system;
-- Then run all SQL from backend/db/schema.sql
```

### 2. Environment Configuration

Create a `.env` file in the backend directory (optional - will use defaults otherwise):

```env
DB_HOST=localhost
DB_USER=root
DB_PASS=your_password
DB_NAME=attendance_system
DB_PORT=3306
API_DEBUG=false
```

Or set environment variables directly on your server.

### 3. Server Setup

**For Apache:**

Ensure `.htaccess` file is in place and mod_rewrite is enabled:

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

**For Local Development:**

If running locally with PHP's built-in server:

```bash
cd backend
php -S localhost:8000
```

**For Nginx:**

Use the following location block in your Nginx config:

```nginx
location /api {
    rewrite ^/api/(.*)$ /index.php?path=$1 last;
}
```

## API Endpoints

### Base URL

`http://your-domain.com/api`

(For local development: `http://localhost:8000/api`)

---

## Employees API

### GET `/employees/read`

Get all employees with pagination

**Query Parameters:**
- `id` (optional) - Get single employee by ID
- `limit` (optional) - Limit results (default: 50, max: 100)
- `offset` (optional) - Pagination offset (default: 0)

**Response:**

```json
{
  "success": true,
  "data": {
    "employees": [
      {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "department": "IT",
        "position": "Developer",
        "phone": "555-0101",
        "created_at": "2024-01-15 10:00:00",
        "updated_at": "2024-01-15 10:00:00"
      }
    ],
    "total": 45,
    "limit": 50,
    "offset": 0
  },
  "message": "Employees retrieved successfully"
}
```

### POST `/employees/create`

Create a new employee

**Request Body:**

```json
{
  "name": "Jane Doe",
  "email": "jane@example.com",
  "department": "HR",
  "position": "Manager",
  "phone": "555-0102"
}
```

**Response:** (201 Created)

```json
{
  "success": true,
  "data": { /* employee object */ },
  "message": "Employee created successfully"
}
```

### GET `/employees/search`

Search employees

**Query Parameters:**
- `q` - Search query (name, email, phone)
- `department` (optional) - Filter by department

**Response:**

```json
{
  "success": true,
  "data": [ /* matching employees */ ],
  "message": "Search completed successfully"
}
```

### PUT `/employees/update`

Update an employee

**Request Body:**

```json
{
  "id": 1,
  "name": "John Smith",
  "department": "Sales"
}
```

**Response:**

```json
{
  "success": true,
  "data": { /* updated employee */ },
  "message": "Employee updated successfully"
}
```

### DELETE `/employees/delete`

Delete an employee

**Request Body:**

```json
{
  "id": 1
}
```

**Response:**

```json
{
  "success": true,
  "data": { "id": 1 },
  "message": "Employee deleted successfully"
}
```

---

## Attendance API

### POST `/attendance/checkin`

Record employee check-in

**Request Body:**

```json
{
  "employee_id": 1
}
```

**Response:** (201 Created)

```json
{
  "success": true,
  "data": {
    "id": 42,
    "employee_id": 1,
    "date": "2024-01-20",
    "check_in": "2024-01-20 08:45:00",
    "check_out": null,
    "status": "present",
    "notes": null,
    "created_at": "2024-01-20 08:45:00"
  },
  "message": "Check-in recorded successfully"
}
```

### POST `/attendance/checkout`

Record employee check-out

**Request Body:**

```json
{
  "employee_id": 1
}
```

**Response:**

```json
{
  "success": true,
  "data": {
    "id": 42,
    "employee_id": 1,
    "date": "2024-01-20",
    "check_in": "2024-01-20 08:45:00",
    "check_out": "2024-01-20 17:30:00",
    "status": "present",
    "notes": null,
    "created_at": "2024-01-20 08:45:00"
  },
  "message": "Check-out recorded successfully"
}
```

### GET `/attendance/get-history`

Get attendance records with filtering

**Query Parameters:**
- `employee_id` (optional) - Filter by employee
- `start_date` (optional) - Start date (YYYY-MM-DD)
- `end_date` (optional) - End date (YYYY-MM-DD)
- `department` (optional) - Filter by department
- `status` (optional) - Filter by status (present/absent/late/half-day)
- `limit` (optional) - Limit results (default: 100)
- `offset` (optional) - Pagination offset

**Response:**

```json
{
  "success": true,
  "data": {
    "records": [ /* attendance records */ ],
    "total": 150,
    "limit": 100,
    "offset": 0
  },
  "message": "Attendance history retrieved successfully"
}
```

### GET `/attendance/stats`

Get dashboard statistics

**Response:**

```json
{
  "success": true,
  "data": {
    "totalEmployees": 50,
    "presentToday": 45,
    "absentToday": 5,
    "lateToday": 3,
    "attendancePercentage": 90.00,
    "departments": [
      { "department": "IT", "count": 15 },
      { "department": "HR", "count": 8 }
    ],
    "recentActivity": [ /* last 10 check-ins/outs */ ]
  },
  "message": "Dashboard statistics retrieved successfully"
}
```

---

## Error Responses

### 400 Bad Request

```json
{
  "success": false,
  "message": "Employee name is required",
  "data": null
}
```

### 404 Not Found

```json
{
  "success": false,
  "message": "Employee not found",
  "data": null
}
```

### 500 Internal Server Error

```json
{
  "success": false,
  "message": "Error creating employee: Database error details",
  "data": null
}
```

---

## Testing the API

### Using cURL

```bash
# Get all employees
curl http://localhost:8000/api/employees/read

# Create employee
curl -X POST http://localhost:8000/api/employees/create \
  -H "Content-Type: application/json" \
  -d '{"name":"Jane Doe","email":"jane@example.com","department":"HR"}'

# Check in
curl -X POST http://localhost:8000/api/attendance/checkin \
  -H "Content-Type: application/json" \
  -d '{"employee_id":1}'

# Get stats
curl http://localhost:8000/api/attendance/stats
```

### Using Postman

1. Import these endpoints as a collection
2. Set base URL to `http://localhost:8000/api`
3. Each endpoint is configured with the correct method and parameters

---

## Database Schema

### employees table

- `id` - Primary key
- `name` - Employee name
- `email` - Email (unique)
- `department` - Department name
- `position` - Job position
- `phone` - Phone number
- `created_at` - Creation timestamp
- `updated_at` - Last update timestamp

### attendance table

- `id` - Primary key
- `employee_id` - Foreign key to employees
- `date` - Attendance date
- `check_in` - Check-in timestamp
- `check_out` - Check-out timestamp
- `status` - Attendance status (present/absent/late/half-day)
- `notes` - Additional notes
- `created_at` - Creation timestamp

### attendance_logs table (audit trail)

- `id` - Primary key
- `employee_id` - Foreign key to employees
- `action` - Action taken (check_in/check_out)
- `details` - JSON details
- `timestamp` - Action timestamp

---

## CORS Configuration

The API is configured to accept requests from:

- `http://localhost:3000` (development)
- `http://localhost:8000` (local backend)
- `http://127.0.0.1:3000` (alternative localhost)

To add more origins, update `ALLOWED_ORIGINS` in `config.php`.

---

## Troubleshooting

### Database Connection Failed

- Verify MySQL is running
- Check credentials in `config.php`
- Ensure database exists: `CREATE DATABASE attendance_system;`

### 404 Not Found

- Check `.htaccess` is in backend folder
- Verify `mod_rewrite` is enabled: `sudo a2enmod rewrite`
- Restart Apache: `sudo systemctl restart apache2`

### CORS Errors in Frontend

- Verify frontend URL is in `ALLOWED_ORIGINS` in `config.php`
- Check browser console for actual error message
- Test API directly with curl to isolate the issue

### File Permissions

```bash
chmod 755 backend/
chmod 644 backend/*.php
chmod 755 backend/api/
chmod 644 backend/api/*/*.php
```

---

## Security Considerations

1. **SQL Injection Protection** - All queries use prepared statements with parameterized inputs
2. **CORS** - Only specified origins can access the API
3. **Input Validation** - All inputs are validated before database operations
4. **Error Handling** - Errors are logged without exposing system details in production

### Production Recommendations

1. Use HTTPS only
2. Implement API authentication (JWT, API keys)
3. Add rate limiting
4. Set `API_DEBUG = false` in production
5. Use strong database credentials
6. Implement proper logging and monitoring
7. Keep PHP and MySQL updated

---

## License

This project is provided as-is for educational and internal use.
