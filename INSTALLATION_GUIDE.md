# Complete Installation & Setup Guide

This guide walks you through setting up the Attendance Management System from scratch.

## Prerequisites Checklist

Before starting, ensure you have:

- [ ] **Node.js 18+** installed
- [ ] **PHP 7.4+** installed  
- [ ] **MySQL 5.7+** installed and running
- [ ] **pnpm** or **npm** installed
- [ ] **Git** (optional, for cloning)
- [ ] A text editor or IDE (VS Code recommended)
- [ ] Command line/Terminal experience

## Part 1: MySQL Database Setup

### Step 1: Start MySQL Server

**Windows:**
```cmd
# Using MySQL Command Line Client
# Or start via Services (MySQL Service)
```

**macOS (using Homebrew):**
```bash
brew services start mysql
```

**Ubuntu/Linux:**
```bash
sudo systemctl start mysql
```

Verify MySQL is running:
```bash
mysql -u root -p
```

### Step 2: Create Database

Open MySQL command line:
```bash
mysql -u root -p
```

Execute these commands:
```sql
-- Create database
CREATE DATABASE attendance_system;

-- Use the database
USE attendance_system;

-- Import schema
SOURCE /path/to/backend/db/schema.sql;

-- Verify tables were created
SHOW TABLES;
```

Expected output:
```
+-----------------------------+
| Tables_in_attendance_system |
+-----------------------------+
| attendance                  |
| employees                   |
+-----------------------------+
```

### Step 3: Verify Sample Data

```sql
-- Check employees table
SELECT * FROM employees;

-- Should show these 6 employees:
-- 1. Alice Johnson (IT, Senior Developer)
-- 2. Bob Smith (Sales, Sales Manager)
-- 3. Carol Davis (HR, HR Specialist)
-- 4. David Wilson (Marketing, Marketing Lead)
-- 5. Emma Brown (IT, Junior Developer)
-- 6. Frank Miller (Operations, Operations Manager)
```

### Step 4: Configure Database Connection

**Option A: Using Config File**

Edit `backend/config.php`:
```php
define('DB_HOST', 'localhost');    // Your MySQL host
define('DB_USER', 'root');         // Your MySQL user
define('DB_PASS', '');             // Your MySQL password
define('DB_NAME', 'attendance_system');
define('DB_PORT', 3306);
```

**Option B: Using Environment Variables**

Create `backend/.env`:
```env
DB_HOST=localhost
DB_USER=root
DB_PASS=your_mysql_password
DB_NAME=attendance_system
DB_PORT=3306
API_DEBUG=false
```

Then in `backend/config.php`, update to read from .env:
```php
$dotenv = parse_ini_file('.env');
define('DB_HOST', $dotenv['DB_HOST'] ?? 'localhost');
define('DB_USER', $dotenv['DB_USER'] ?? 'root');
define('DB_PASS', $dotenv['DB_PASS'] ?? '');
define('DB_NAME', $dotenv['DB_NAME'] ?? 'attendance_system');
define('DB_PORT', $dotenv['DB_PORT'] ?? 3306);
```

## Part 2: PHP Backend Setup

### Step 1: Verify PHP Installation

```bash
php --version

# Output should be PHP 7.4 or higher
# PHP 8.2.0 (cli) (built: Oct 2 2024 15:49:51) (ZTS)
```

### Step 2: Install PHP Extensions

**Check if mysql extension is installed:**
```bash
php -m | grep mysql

# Should show: mysqli
```

If missing, install it:

**Ubuntu/Debian:**
```bash
sudo apt-get install php-mysql
sudo systemctl restart apache2
```

**macOS (Homebrew):**
```bash
brew install php@8.2
```

### Step 3: Start PHP Development Server

```bash
# Navigate to backend directory
cd backend

# Start PHP development server on port 8000
php -S localhost:8000

# You should see:
# [Mon Jan 15 10:30:45 2024] PHP 8.2.0 Development Server started at http://localhost:8000
# [Mon Jan 15 10:30:45 2024] Listening on http://localhost:8000
# Document root is /path/to/backend
```

Keep this terminal open while developing.

### Step 4: Test Backend API

Open a new terminal and test the API:

```bash
# Test employees endpoint
curl http://localhost:8000/api/employees/read

# Should return JSON with employee list:
# {
#   "success": true,
#   "data": [
#     {
#       "id": 1,
#       "name": "Alice Johnson",
#       "email": "alice@example.com",
#       ...
#     }
#   ]
# }
```

### Step 5: Test Other Endpoints

```bash
# Check-in an employee
curl -X POST http://localhost:8000/api/attendance/checkin \
  -H "Content-Type: application/json" \
  -d '{"employee_id":1}'

# Get attendance statistics
curl http://localhost:8000/api/attendance/stats

# Search employees
curl "http://localhost:8000/api/employees/search?q=Alice"
```

## Part 3: React Frontend Setup

### Step 1: Install Dependencies

```bash
# Navigate to project root (NOT backend folder)
cd /path/to/project

# Install dependencies using pnpm
pnpm install

# Or if you don't have pnpm:
npm install
```

This will:
- Install all npm packages
- Create `node_modules/` folder
- Generate lock file (`pnpm-lock.yaml` or `package-lock.json`)

Expected output:
```
added 300 packages in 15s
```

### Step 2: Configure Environment

Create `.env.local` in project root:

```env
# Backend API URL
NEXT_PUBLIC_API_URL=http://localhost:8000/api
```

This tells the frontend where to find the PHP backend.

### Step 3: Verify Configuration

Check that these files exist and are configured:
```bash
# Frontend config
cat .env.local

# Backend config
cat backend/config.php
```

### Step 4: Start Frontend Development Server

```bash
# Make sure you're in project root (NOT backend)
pnpm dev

# You should see:
# > my-v0-project@0.1.0 dev /path/to/project
# ▲ Next.js 15.5.18
# - Local:        http://localhost:3001
# - Environments: .env.local
```

Keep this terminal open while developing.

### Step 5: Test Frontend

Open your browser and visit: `http://localhost:3001`

You should see:
- Dashboard page loading
- Employee statistics displayed
- List of 5 sample employees
- Navigation menu working

## Part 4: Running the Complete System

### All Terminals Open (Final Setup)

You need **3 terminal windows** running simultaneously:

**Terminal 1: MySQL**
```bash
# Start MySQL (if not already running)
brew services start mysql  # macOS
sudo systemctl start mysql # Linux
# Windows: Start MySQL via Services
```

**Terminal 2: PHP Backend**
```bash
cd backend
php -S localhost:8000
```

**Terminal 3: React Frontend**
```bash
# From project root
pnpm dev
```

Now you can:
- Access app: http://localhost:3001
- Test API: http://localhost:8000/api/employees/read
- View database: Connect MySQL client to localhost:3306

## Part 5: Testing the Application

### 1. Test Dashboard
- [ ] Visit http://localhost:3001
- [ ] See employee statistics
- [ ] See recent activity
- [ ] Dashboard loads without errors

### 2. Test Employee Management
- [ ] Go to "Employees" section
- [ ] See list of 5 employees
- [ ] Click "Add Employee" button
- [ ] Fill form with test data:
  - Name: "Test Employee"
  - Email: "test@example.com"
  - Department: "IT"
  - Position: "Developer"
  - Phone: "555-1234"
- [ ] Click "Add Employee"
- [ ] New employee appears in list
- [ ] Employee count increases

### 3. Test Employee Search/Filter
- [ ] Type employee name in search box
- [ ] List filters in real-time
- [ ] Select department from dropdown
- [ ] List filters by department

### 4. Test Edit/Delete
- [ ] Click edit icon on an employee
- [ ] Modal opens with employee data
- [ ] Modify a field (e.g., position)
- [ ] Click "Update Employee"
- [ ] List updates
- [ ] Click delete icon on an employee
- [ ] Confirm deletion
- [ ] Employee removed from list

### 5. Check Browser Console
- [ ] Open DevTools (F12)
- [ ] Go to Console tab
- [ ] Should NOT see any JavaScript errors
- [ ] May see "Using mock data" message (normal)

### 6. Check Network Requests
- [ ] In DevTools, go to Network tab
- [ ] Refresh page
- [ ] Should see API calls to `localhost:8000/api/`
- [ ] All requests should return 200 status

## Part 6: Build for Production

### Frontend Build

```bash
# From project root
pnpm build

# Output:
# ✓ Compiled successfully in 18.3s
# ...
# Route (app)                      Size  First Load JS
# ├ ○ /                          177 kB         280 kB
```

Test production build:
```bash
pnpm start

# Visit http://localhost:3000
```

### Backend Considerations

For production PHP deployment:
1. Upload backend folder to production server
2. Configure database with production credentials
3. Set `API_DEBUG=false` in `.env`
4. Configure CORS with production frontend URL
5. Enable HTTPS
6. Set up database backups

See production deployment section in main README.

## Troubleshooting

### MySQL Connection Error
```
Error: Access denied for user 'root'@'localhost'
```

**Solution:**
- Verify MySQL is running
- Check password in `backend/config.php`
- Try: `mysql -u root -p` (enter password when prompted)

### PHP Not Found
```
bash: php: command not found
```

**Solution:**
- Reinstall PHP
- macOS: `brew install php@8.2`
- Linux: `sudo apt-get install php`
- Windows: Download from https://windows.php.net/

### Port Already in Use

If port 8000 or 3001 is already in use:
```bash
# Use different port for backend
php -S localhost:8001

# Update frontend .env to match
NEXT_PUBLIC_API_URL=http://localhost:8001/api
```

### API Returns 404

Ensure:
1. Backend is running (`php -S localhost:8000`)
2. `.htaccess` file exists in backend folder
3. Apache has `mod_rewrite` enabled
4. URL is correct: `http://localhost:8000/api/employees/read`

### No Employees Showing

Check:
1. Database tables exist: `SHOW TABLES;`
2. Sample data loaded: `SELECT * FROM employees;`
3. Backend returned data: `curl http://localhost:8000/api/employees/read`
4. Frontend env var correct: Check `.env.local`

### Build Fails with TypeScript Error

Run type check and fix:
```bash
# Check types
pnpm tsc --noEmit

# Fix issues and rebuild
pnpm build
```

## Next Steps

After successful setup:

1. **Customize** - Modify colors, fonts, add your logo
2. **Add Authentication** - User login and sessions
3. **Deploy** - Push to production servers
4. **Extend** - Add new features and reports
5. **Secure** - Add HTTPS, input validation, rate limiting

## Support Resources

- **React Docs**: https://react.dev
- **Next.js Docs**: https://nextjs.org/docs
- **PHP Manual**: https://www.php.net/manual
- **MySQL Docs**: https://dev.mysql.com/doc
- **Tailwind CSS**: https://tailwindcss.com/docs
- **shadcn/ui**: https://ui.shadcn.com/docs

## Quick Reference

| Component | Port | Start Command | URL |
|-----------|------|---------------|-----|
| MySQL | 3306 | Auto/Manual | localhost:3306 |
| PHP Backend | 8000 | `php -S localhost:8000` | http://localhost:8000 |
| React Frontend | 3001 | `pnpm dev` | http://localhost:3001 |

---

**Installation Complete!** Your attendance system is ready to use.
