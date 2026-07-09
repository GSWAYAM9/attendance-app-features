# Quick Start Guide

Get the attendance system running in 10 minutes!

## Prerequisites

- Node.js 18+
- PHP 7.4+
- MySQL 5.7+

## 1️⃣ Database Setup (2 minutes)

```bash
# Create database
mysql -u root -p -e "CREATE DATABASE attendance_system;"

# Import schema
mysql -u root -p attendance_system < backend/db/schema.sql

# Verify (should show 3 tables)
mysql -u root -p attendance_system -e "SHOW TABLES;"
```

## 2️⃣ Backend Setup (2 minutes)

```bash
cd backend

# Configure database connection
cp .env.example .env
# Edit .env with your MySQL credentials

# Start PHP server
php -S localhost:8000
```

**Test backend:**
```bash
curl http://localhost:8000/api/employees/read
```

## 3️⃣ Frontend Setup (3 minutes)

```bash
# From project root (not backend folder!)
cd ..

# Install dependencies
pnpm install

# Configure API URL
cp .env.example .env.local
# (default is already http://localhost:8000/api)

# Start development server
pnpm dev
```

## 4️⃣ Open Application

Open your browser: **http://localhost:3000**

## ✅ What You'll See

- **Dashboard** - Shows live statistics from database
- **Employee Management** - List of 6 sample employees
- **Check In/Out** - Attendance tracking
- **Reports** - Attendance history

## 🧪 Quick Test

### Test 1: View Employees
1. Click "Employee Management"
2. See list of employees from database
3. Try searching for "John"

### Test 2: Check In
1. Click "Check In/Out"
2. Select an employee
3. Click "Check In"
4. Refresh Dashboard - should see "Present Today" increase

### Test 3: Dashboard Stats
1. Click "Dashboard"
2. Stats update in real-time
3. Recent activity shows your check-in

## 🐛 Troubleshooting

| Problem | Solution |
|---------|----------|
| Database connection failed | Check MySQL is running, credentials in `.env` |
| 404 on API calls | Ensure backend PHP server is running on :8000 |
| Empty dashboard | Check frontend `.env.local` has correct API URL |
| CORS errors | Update `ALLOWED_ORIGINS` in `backend/config.php` |

## 📁 Key Files

| File | Purpose |
|------|---------|
| `lib/api.ts` | API endpoints |
| `backend/config.php` | Database config |
| `backend/db/schema.sql` | Database schema |
| `.env.example` | Environment template |

## 🚀 Next Steps

1. **Read full docs**: See `SETUP.md` for detailed setup
2. **API docs**: See `backend/README.md` for all endpoints
3. **Add features**: Explore components in `/components/`
4. **Customize**: Edit styles, add fields, extend functionality

## 📞 Need Help?

- **Setup issues**: Check `SETUP.md` troubleshooting
- **API questions**: Check `backend/README.md`
- **Component help**: Check individual component files

---

**Done! Your attendance system is running.** 🎉
