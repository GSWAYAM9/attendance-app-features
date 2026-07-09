# Attendance System - Complete Setup Guide

This document provides step-by-step instructions to set up and run the PHP/React Attendance Management System.

## System Architecture

```
┌─────────────────────┐         ┌──────────────────────┐
│   React Frontend    │         │   PHP Backend API    │
│  (Next.js on :3000) │────────→│  (Apache on :8000)   │
│                     │         │                      │
│ - Dashboard         │         │ - Employees CRUD     │
│ - Employee Mgmt     │         │ - Attendance Check   │
│ - Check In/Out      │         │ - Reports & Stats    │
│ - Reports           │         │ - Data Validation    │
└─────────────────────┘         └──────────────────────┘
                                         │
                                         ↓
                                 ┌──────────────┐
                                 │ MySQL        │
                                 │ Database     │
                                 │ (localhost)  │
                                 └──────────────┘
```

## Prerequisites

- **Node.js** 18+ (with npm or pnpm)
- **PHP** 7.4+
- **MySQL** 5.7+
- **Apache** with mod_rewrite enabled (or PHP's built-in server for development)

## Part 1: Database Setup

### Step 1: Create MySQL Database

```bash
# Connect to MySQL
mysql -u root -p

# In MySQL console, run:
CREATE DATABASE attendance_system;
USE attendance_system;

# Import the schema
EXIT;

# From your project directory:
mysql -u root -p attendance_system < backend/db/schema.sql
```

### Step 2: Verify Database

```bash
mysql -u root -p attendance_system -e "SHOW TABLES;"
```

You should see:
- `employees`
- `attendance`
- `attendance_logs`

And see sample employees already inserted.

## Part 2: PHP Backend Setup

### Step 1: Configure Backend

Copy the environment template:

```bash
cd backend
cp .env.example .env
```

Edit `.env` with your database credentials:

```env
DB_HOST=localhost
DB_USER=root
DB_PASS=your_password
DB_NAME=attendance_system
DB_PORT=3306
API_DEBUG=true  # Set to false in production
```

### Step 2: Set Up Web Server

#### Option A: Apache (Recommended for Production)

```bash
# 1. Copy backend folder to Apache document root
sudo cp -r backend /var/www/html/attendance-api

# 2. Set proper permissions
sudo chown -R www-data:www-data /var/www/html/attendance-api

# 3. Create Apache virtual host config
sudo nano /etc/apache2/sites-available/attendance-api.conf
```

Add the following configuration:

```apache
<VirtualHost *:8000>
    ServerAdmin admin@localhost
    ServerName localhost
    DocumentRoot /var/www/html/attendance-api

    <Directory /var/www/html/attendance-api>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

```bash
# 4. Enable the site and modules
sudo a2ensite attendance-api.conf
sudo a2enmod rewrite
sudo a2enmod headers

# 5. Restart Apache
sudo systemctl restart apache2

# 6. Verify it's running on port 8000
# Add Listen 8000 to /etc/apache2/ports.conf if not already there
sudo nano /etc/apache2/ports.conf
# Add: Listen 8000
```

#### Option B: PHP Built-in Server (Development Only)

```bash
cd backend
php -S localhost:8000
```

This is simpler for development but not suitable for production.

### Step 3: Test Backend API

```bash
# Test if API is running
curl http://localhost:8000/api/employees/read

# Expected response:
# {
#   "success": true,
#   "data": {
#     "employees": [...],
#     "total": 6,
#     ...
#   },
#   "message": "Employees retrieved successfully"
# }
```

If you get a 404, check:
- Is Apache/PHP server running?
- Is .htaccess in the backend folder?
- Is mod_rewrite enabled?
- Is NEXT_PUBLIC_API_URL correctly configured?

## Part 3: React Frontend Setup

### Step 1: Install Dependencies

```bash
# From project root
pnpm install
# or npm install / yarn install
```

### Step 2: Configure Environment

Copy the environment template:

```bash
cp .env.example .env.local
```

Edit `.env.local`:

```env
# For local development:
NEXT_PUBLIC_API_URL=http://localhost:8000/api

# For production, use your actual backend domain:
# NEXT_PUBLIC_API_URL=https://api.yourdomain.com/api
```

### Step 3: Start Development Server

```bash
pnpm dev
```

The app will be available at: **http://localhost:3000**

### Step 4: Test the Application

1. Open http://localhost:3000 in your browser
2. Navigate to the Dashboard - should show stats from the database
3. Go to Employee Management and try:
   - Viewing employees (should show the 6 sample employees)
   - Searching for an employee
   - Adding a new employee
   - Editing an employee
4. Go to Check In/Out and test the attendance tracking
5. Go to Attendance Reports to see the history

## Part 4: Testing the Full Stack

### Frontend to Backend Connectivity Test

1. Open browser DevTools (F12)
2. Go to the Network tab
3. In the app, navigate to Dashboard
4. Check that API requests to `http://localhost:8000/api/*` are returning 200 OK

### Sample API Calls (Using cURL)

```bash
# Get all employees
curl http://localhost:8000/api/employees/read

# Search employees
curl "http://localhost:8000/api/employees/search?q=john"

# Create employee
curl -X POST http://localhost:8000/api/employees/create \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Jane Smith",
    "email": "jane@example.com",
    "department": "Finance",
    "position": "Analyst",
    "phone": "555-0107"
  }'

# Check in
curl -X POST http://localhost:8000/api/attendance/checkin \
  -H "Content-Type: application/json" \
  -d '{"employee_id": 1}'

# Get stats
curl http://localhost:8000/api/attendance/stats

# Get attendance history (with filters)
curl "http://localhost:8000/api/attendance/get-history?start_date=2024-01-01&end_date=2024-01-31"
```

## File Structure

```
project-root/
├── app/
│   ├── layout.tsx
│   └── page.tsx                    # Main app entry point
├── components/
│   ├── dashboard.tsx               # Dashboard with live stats
│   ├── employee-management.tsx     # Employee CRUD
│   ├── check-in-out.tsx            # Attendance tracking
│   ├── attendance-reports.tsx      # Reports & filtering
│   ├── app-sidebar.tsx             # Navigation
│   └── ui/                         # shadcn UI components
├── lib/
│   ├── api.ts                      # API client with endpoints
│   ├── types.ts                    # TypeScript interfaces
│   └── utils.ts                    # Utility functions
├── backend/
│   ├── index.php                   # Main router
│   ├── config.php                  # Configuration & helpers
│   ├── .htaccess                   # URL rewriting & CORS
│   ├── .env.example                # Environment template
│   ├── api/
│   │   ├── employees/
│   │   │   ├── read.php            # GET employees
│   │   │   ├── create.php          # POST create employee
│   │   │   ├── update.php          # PUT update employee
│   │   │   ├── delete.php          # DELETE employee
│   │   │   └── search.php          # GET search employees
│   │   └── attendance/
│   │       ├── checkin.php         # POST check in
│   │       ├── checkout.php        # POST check out
│   │       ├── get-history.php     # GET attendance records
│   │       └── stats.php           # GET dashboard stats
│   ├── db/
│   │   └── schema.sql              # Database schema
│   └── README.md                   # Backend API documentation
├── .env.example                    # Environment template
├── SETUP.md                        # This file
└── package.json
```

## Troubleshooting

### Issue: CORS Error in Browser Console

**Error:** "Access to XMLHttpRequest blocked by CORS policy"

**Solution:**
1. Check that your frontend URL is in `backend/config.php` ALLOWED_ORIGINS
2. Add your frontend domain to the list:
   ```php
   define('ALLOWED_ORIGINS', [
       'http://localhost:3000',
       'http://localhost:8000',
       'http://127.0.0.1:3000',
       'https://yourdomain.com'  // Add your domain
   ]);
   ```

### Issue: 404 Not Found on API Endpoints

**Error:** "Endpoint not found"

**Solution:**
1. Verify `.htaccess` is in the `/backend` folder
2. Enable mod_rewrite: `sudo a2enmod rewrite`
3. Check Apache config allows overrides in the directory
4. Test with: `curl http://localhost:8000/api/employees/read`

### Issue: Database Connection Error

**Error:** "Database connection failed"

**Solution:**
1. Verify MySQL is running: `sudo systemctl status mysql`
2. Check credentials in `.env` match your setup
3. Verify database exists: `mysql -u root -p -e "SHOW DATABASES;"`
4. Check user has access: `mysql -u root -p -e "SHOW GRANTS FOR 'root'@'localhost';"`

### Issue: "Employee already checked in today"

**Error:** When trying to check in twice

**Solution:** This is intentional - an employee can only check in once per day. To reset for testing:

```bash
# Delete today's attendance records
mysql -u root -p attendance_system -e "DELETE FROM attendance WHERE DATE(date) = CURDATE();"
```

### Issue: Frontend shows empty dashboard

**Solution:**
1. Check browser console (F12) for errors
2. Check Network tab - are API calls being made?
3. Verify NEXT_PUBLIC_API_URL in `.env.local`
4. Verify backend is running and responding to requests
5. Check both servers are running (frontend on :3000, backend on :8000)

## Production Deployment

### Frontend Deployment (Vercel)

```bash
# Build the Next.js app
pnpm build

# Deploy to Vercel
npm install -g vercel
vercel
```

Set environment variable on Vercel:
```
NEXT_PUBLIC_API_URL=https://your-backend-api-domain.com/api
```

### Backend Deployment (PHP Hosting/VPS)

1. Upload backend folder to your hosting provider
2. Create database and import schema
3. Update `.env` with production credentials
4. Configure SSL/HTTPS on your web server
5. Update CORS origins in `config.php` to include production frontend URL
6. Enable mod_rewrite and mod_headers in Apache

## API Documentation

See `/backend/README.md` for detailed API endpoint documentation.

## Features Implemented

✅ **Employee Management**
- View all employees
- Add new employees
- Edit employee details
- Delete employees
- Search employees by name/email/phone

✅ **Attendance Tracking**
- Check-in with timestamp
- Check-out with timestamp
- Automatic late detection (after 9:00 AM)
- Prevent duplicate check-ins

✅ **Dashboard**
- Live statistics (total employees, present today, late, absent)
- Attendance percentage
- Department distribution
- Recent activity feed
- Real-time updates

✅ **Reports**
- Filter by date range
- Filter by employee
- Filter by department
- Export-ready data

✅ **Search & Filtering**
- Search employees by multiple fields
- Filter attendance records
- Date range filtering

## Next Steps

After setup, you might want to:

1. **Add Authentication** - Implement login/logout with JWT tokens
2. **Add Authorization** - Restrict features by user role (admin, manager, employee)
3. **Export Reports** - Add PDF/CSV export functionality
4. **Mobile App** - Build a React Native mobile attendance app
5. **Notifications** - Add email/SMS notifications for late arrivals
6. **Advanced Analytics** - Add more dashboard charts and insights
7. **Backup & Recovery** - Set up automated database backups
8. **Monitoring** - Add error logging and performance monitoring

## Support

For issues or questions:

1. Check the troubleshooting section above
2. Review the API documentation in `/backend/README.md`
3. Check browser console for JavaScript errors
4. Check PHP error logs for backend issues

Good luck! 🚀
