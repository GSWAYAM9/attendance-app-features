// Employee interface
export interface Employee {
  id: number
  name: string
  email: string
  department: string
  position: string
  phone: string
  created_at: string
  updated_at: string
}

// Attendance record interface
export interface AttendanceRecord {
  id: number
  employee_id: number
  check_in: string | null
  check_out: string | null
  date: string
  status: "present" | "absent" | "late" | "half-day"
  notes: string | null
  created_at: string
  employee?: Employee
}

// API response wrapper
export interface ApiResponse<T> {
  success: boolean
  data: T
  message: string
}

// Employee with attendance summary
export interface EmployeeWithAttendance extends Employee {
  isCheckedIn: boolean
  checkInTime: string | null
  checkOutTime: string | null
  attendanceToday: AttendanceRecord | null
}

// Dashboard stats
export interface DashboardStats {
  totalEmployees: number
  presentToday: number
  absentToday: number
  lateToday: number
  attendancePercentage: number
}

// Report filter options
export interface ReportFilter {
  startDate: string
  endDate: string
  department?: string
  employeeId?: number
  status?: string
}
