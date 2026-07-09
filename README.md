# Attendance Management System - PHP/React

A modern, full-stack attendance tracking application with a React frontend and PHP backend API.

## 🚀 Features

- **Employee Management** - Add, edit, delete, and search employees
- **Attendance Tracking** - Real-time check-in/check-out with timestamps
- **Dashboard** - Live statistics and analytics
- **Reports** - Filter and view attendance history
- **Search & Filtering** - Find employees and records quickly
- **Responsive Design** - Works on desktop and mobile

## 📋 Tech Stack

### Frontend
- **Next.js 15** - React framework with App Router
- **React 19** - UI component library
- **TypeScript** - Type safety
- **Tailwind CSS** - Utility-first CSS
- **shadcn/ui** - Pre-built UI components

### Backend
- **PHP 7.4+** - Server-side logic
- **MySQL 5.7+** - Relational database
- **REST API** - JSON responses
- **Apache/Nginx** - Web server

## 📚 Getting Started

### Quick Start (10 minutes)
See [`QUICK_START.md`](QUICK_START.md) for fastest setup.

### Complete Setup (30 minutes)
See [`SETUP.md`](SETUP.md) for detailed instructions including:
- Database configuration
- PHP backend setup
- React frontend setup
- Testing procedures
- Troubleshooting

### API Documentation
See [`backend/README.md`](backend/README.md) for:
- All API endpoints
- Request/response formats
- Example cURL commands
- Error handling
- Security details

## 📁 Project Structure

```
.
├── app/                     # Next.js app directory
├── components/              # React components
├── lib/
│   ├── api.ts              # ⭐ API client for backend
│   ├── types.ts            # ⭐ TypeScript interfaces
│   └── utils.ts            # Utility functions
├── backend/                 # ⭐ PHP REST API
│   ├── api/                # Endpoint handlers
│   ├── db/
│   │   └── schema.sql      # Database schema
│   ├── config.php          # Configuration
│   ├── index.php           # Router
│   └── README.md           # API documentation
├── QUICK_START.md          # Quick setup guide
├── SETUP.md                # Detailed setup guide
└── README.md               # This file
```

⭐ = Files created for this project

## 🔧 Installation & Setup

### Prerequisites
- **Node.js 18+** - https://nodejs.org/
- **PHP 7.4+** - https://www.php.net/downloads
- **MySQL 5.7+** - https://dev.mysql.com/downloads/mysql/
- **pnpm** - `npm install -g pnpm` or use `npm`/`yarn`

### 1️⃣ Database Setup (MySQL)

**Step 1: Create Database**
```bash
# Open MySQL command line
mysql -u root -p

# Create database
CREATE DATABASE attendance_system;
USE attendance_system;

# Import schema
SOURCE backend/db/schema.sql;

# Verify
SHOW TABLES;
```

**Step 2: Verify Sample Data**
```sql
-- Check if sample employees were created
SELECT * FROM employees;

-- Should show 6 employees:
-- Alice Johnson, Bob Smith, Carol Davis, David Wilson, Emma Brown, Frank Miller
```

**Step 3: Configure Connection**

The database connection is configured in `backend/config.php`:

```php
// Database credentials (modify if different)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');           // Add your MySQL password
define('DB_NAME', 'attendance_system');
define('DB_PORT', 3306);
```

Or use environment variables in `backend/.env`:
```env
DB_HOST=localhost
DB_USER=root
DB_PASS=your_password
DB_NAME=attendance_system
DB_PORT=3306
API_DEBUG=false
```

### 2️⃣ PHP Backend Setup

**Step 1: Install PHP Extensions** (if needed)
```bash
# Ubuntu/Debian
sudo apt-get install php-mysql php-curl php-json

# macOS (using Homebrew)
brew install php
```

**Step 2: Start PHP Development Server**
```bash
cd backend
php -S localhost:8000

# You should see:
# [Mon Jan 15 10:00:00 2024] PHP 8.2.0 Development Server started at http://localhost:8000
```

**Step 3: Test Backend Connection**
```bash
# In another terminal, test the API
curl http://localhost:8000/api/employees/read

# Should return JSON with employees list
```

### 3️⃣ React Frontend Setup

**Step 1: Install Dependencies**
```bash
# From project root
pnpm install

# If you don't have pnpm, use npm instead:
# npm install
```

**Step 2: Configure Environment**

Create or verify `.env.local` file:
```env
# API connection (make sure backend is running on this URL)
NEXT_PUBLIC_API_URL=http://localhost:8000/api
```

**Step 3: Start Development Server**
```bash
# From project root
pnpm dev

# You should see:
# ▲ Next.js 15.5.18
# - Local:        http://localhost:3001
# - Environments: .env.local
```

**Step 4: Open in Browser**
- Visit http://localhost:3001
- You should see the Dashboard with mock employee data
- Navigate to "Employees" to manage employee records

### 4️⃣ Run Full Application

**Complete Setup (all terminals):**

```bash
# Terminal 1: MySQL
# Start MySQL server (if not already running)
# macOS: brew services start mysql
# Ubuntu: sudo systemctl start mysql

# Terminal 2: PHP Backend
cd backend
php -S localhost:8000

# Terminal 3: React Frontend  
cd ../
pnpm dev
```

Now you have:
- ✅ Database: MySQL on localhost:3306
- ✅ Backend API: http://localhost:8000/api
- ✅ Frontend: http://localhost:3001

## 📊 API Endpoints

### Employees
```
GET    /api/employees/read       - List employees
POST   /api/employees/create     - Add employee
GET    /api/employees/search     - Search employees
PUT    /api/employees/update     - Update employee
DELETE /api/employees/delete     - Delete employee
```

### Attendance
```
POST   /api/attendance/checkin        - Check in
POST   /api/attendance/checkout       - Check out
GET    /api/attendance/get-history    - Get history
GET    /api/attendance/stats          - Get statistics
```

See [`backend/README.md`](backend/README.md) for detailed endpoint documentation.

## 🗄️ Database Schema

Three main tables:

1. **employees** - Employee records
   - id, name, email, department, position, phone
   
2. **attendance** - Daily attendance records
   - id, employee_id, date, check_in, check_out, status
   
3. **attendance_logs** - Audit trail (optional)
   - id, employee_id, action, details, timestamp

Schema file: [`backend/db/schema.sql`](backend/db/schema.sql)

## 🔐 Security

- ✅ SQL injection prevention (prepared statements)
- ✅ CORS validation
- ✅ Input validation and sanitization
- ✅ Error handling
- ⚠️ Add authentication/authorization for production

## 🏗️ Building for Production

### Frontend Build

```bash
# Build Next.js application
pnpm build

# Test production build locally
pnpm start

# Visit http://localhost:3000 to verify
```

Build output:
- `.next/` - Optimized production build
- `.next/static/` - CSS, JavaScript assets
- Size ~280KB (first load)

### Frontend Deployment (Vercel)

```bash
# Install Vercel CLI
pnpm add -g vercel

# Deploy
vercel deploy --prod

# Set environment variable
vercel env add NEXT_PUBLIC_API_URL
# Enter backend URL: https://your-api-domain.com/api
```

### Backend Deployment (PHP Hosting)

1. **Upload to Server**
   ```bash
   # Upload backend folder to your PHP hosting
   # via FTP, SSH, or hosting control panel
   scp -r backend/ user@server.com:/var/www/html/api/
   ```

2. **Configure Database**
   ```bash
   # Update backend/.env with production credentials
   DB_HOST=your-db-host
   DB_USER=prod-user
   DB_PASS=strong-password
   DB_NAME=attendance_prod
   ```

3. **Verify Permissions**
   ```bash
   chmod 644 backend/*.php
   chmod 755 backend/api/
   ```

4. **Test API**
   ```bash
   curl https://your-api-domain.com/api/employees/read
   ```

See [`SETUP.md`](SETUP.md) for detailed production deployment guide.

## 📖 Documentation

- **Quick Start**: [`QUICK_START.md`](QUICK_START.md) - 10-minute setup
- **Detailed Setup**: [`SETUP.md`](SETUP.md) - Complete instructions
- **API Docs**: [`backend/README.md`](backend/README.md) - All endpoints
- **Summary**: [`IMPLEMENTATION_SUMMARY.md`](IMPLEMENTATION_SUMMARY.md) - Overview

## 🎨 Customization

The project is designed to be easily customizable:

1. **Add Database Fields** - Update schema and PHP endpoints
2. **Modify Attendance Rules** - Change "late" threshold in PHP
3. **Add API Endpoints** - Follow existing patterns in `/backend/api/`
4. **Update UI Components** - Use shadcn/ui components or build new ones
5. **Customize Styling** - Modify Tailwind config or component classes

## 🧪 Testing

### Test API Endpoints
```bash
# Get employees
curl http://localhost:8000/api/employees/read

# Check in
curl -X POST http://localhost:8000/api/attendance/checkin \
  -H "Content-Type: application/json" \
  -d '{"employee_id":1}'

# Get stats
curl http://localhost:8000/api/attendance/stats
```

### Test Frontend
1. Visit http://localhost:3001
2. Navigate through different sections
3. Check Network tab in DevTools for API calls

## 🐛 Troubleshooting

### CORS Error
- Add frontend URL to `ALLOWED_ORIGINS` in `backend/config.php`

### 404 API Error
- Ensure backend is running on port 8000
- Check `.htaccess` is in backend folder
- Verify Apache has mod_rewrite enabled

### Database Error
- Verify MySQL is running
- Check credentials in `.env`
- Ensure database exists

See [`SETUP.md`](SETUP.md) troubleshooting section for more help.

## 📦 Dependencies & Requirements

### Frontend Dependencies
```json
{
  "next": "^15.5.18",
  "react": "^19.0.0",
  "typescript": "^5.6.0",
  "tailwindcss": "^4.0.0",
  "@radix-ui/react-dialog": "^1.1.0",
  "lucide-react": "^0.371.0"
}
```

Install with:
```bash
pnpm install
```

### Backend Requirements
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache with mod_rewrite OR Nginx with URL rewriting
- php-mysql extension
- php-curl extension (optional)

Install PHP packages (Ubuntu/Debian):
```bash
sudo apt-get install php php-mysql php-curl apache2 libapache2-mod-php
```

### System Requirements
- **RAM**: 2GB minimum (development), 4GB recommended (production)
- **Disk**: 500MB for application + database
- **Bandwidth**: Low (API-only traffic)

## 📝 Environment Variables

### Frontend (.env.local)
Required for frontend to connect to backend:
```env
NEXT_PUBLIC_API_URL=http://localhost:8000/api
```

On production:
```env
NEXT_PUBLIC_API_URL=https://api.yourdomain.com/api
```

### Backend (backend/.env)
Database connection configuration:
```env
# Database connection
DB_HOST=localhost
DB_USER=root
DB_PASS=your_password
DB_NAME=attendance_system
DB_PORT=3306

# API settings
API_DEBUG=false                          # Set to true for debugging
ALLOWED_ORIGINS=http://localhost:3001  # Add production URL
```

## 🔧 WordPress Integration (Optional)

To add this attendance system to WordPress:

### Step 1: Create Custom Plugin
```bash
# Create plugin directory
mkdir wp-content/plugins/attendance-system
cd wp-content/plugins/attendance-system

# Create plugin file
touch attendance-system.php
```

### Step 2: Add Plugin Header (attendance-system.php)
```php
<?php
/**
 * Plugin Name: Attendance Management System
 * Plugin URI: https://yoursite.com
 * Description: Employee attendance tracking system
 * Version: 1.0.0
 * Author: Your Name
 * License: GPL v2 or later
 */

// Load the React build
function enqueue_attendance_app() {
    wp_enqueue_script(
        'attendance-app',
        plugins_url('/build/index.js', __FILE__),
        array(),
        '1.0.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'enqueue_attendance_app');

// Register shortcode
function attendance_shortcode() {
    return '<div id="root"></div>';
}
add_shortcode('attendance_system', 'attendance_shortcode');
?>
```

### Step 3: Build React App for WordPress
```bash
# Build for production
pnpm build

# Copy to plugin folder
cp -r .next/* wp-content/plugins/attendance-system/build/

# Or use as iframe
echo '<iframe src="https://attendance-app.com" width="100%" height="800"></iframe>';
```

### Step 4: Add to WordPress Page
```
[attendance_system]
```

Or use as standalone app via iframe on any WordPress page.

**Note**: For WordPress integration, you may want to:
- Add WordPress user authentication bridge
- Use WordPress database instead of separate MySQL
- Implement role-based access control using WordPress roles
- Add WordPress admin settings page for configuration

## 🚀 Next Steps

After setup, consider:

1. **Authentication** - Add user login/logout
2. **Authorization** - Implement role-based access
3. **Notifications** - Email alerts for late arrivals
4. **Mobile App** - React Native mobile version
5. **Export** - PDF/CSV report exports
6. **Analytics** - Advanced dashboards and insights

## 📄 License

This project is provided for educational and internal use.

## 💬 Support

For issues or questions:

1. Check relevant documentation file
2. Review troubleshooting sections
3. Check the code comments
4. Review API error responses

---

**Built with ❤️ using Next.js and PHP**

Frontend running on: http://localhost:3001
Backend running on: http://localhost:8000
Database: MySQL on localhost:3306
