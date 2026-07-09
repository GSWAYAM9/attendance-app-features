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

## 🔧 Development

### Prerequisites
- Node.js 18+
- PHP 7.4+
- MySQL 5.7+
- pnpm or npm

### Run Locally

**Terminal 1 - Backend:**
```bash
cd backend
php -S localhost:8000
```

**Terminal 2 - Frontend:**
```bash
pnpm dev
```

Open http://localhost:3001 in your browser.

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

## 🌐 Deployment

### Frontend (Vercel)
```bash
pnpm build
vercel deploy
```

### Backend (PHP Hosting)
Upload backend folder and configure database.

See [`SETUP.md`](SETUP.md) for production deployment guide.

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

## 📝 Environment Variables

### Frontend (.env.local)
```env
NEXT_PUBLIC_API_URL=http://localhost:8000/api
```

### Backend (backend/.env)
```env
DB_HOST=localhost
DB_USER=root
DB_PASS=your_password
DB_NAME=attendance_system
DB_PORT=3306
API_DEBUG=false
```

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
