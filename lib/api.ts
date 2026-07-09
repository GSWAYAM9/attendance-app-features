import {
  Employee,
  AttendanceRecord,
  ApiResponse,
  DashboardStats,
  ReportFilter,
  EmployeeWithAttendance,
} from "./types"

// Configure your PHP backend URL here
const API_BASE_URL = process.env.NEXT_PUBLIC_API_URL || "http://localhost:8000/api"

// Generic fetch wrapper with error handling
async function fetchApi<T>(endpoint: string, options: RequestInit = {}): Promise<T> {
  const url = `${API_BASE_URL}${endpoint}`

  const headers: HeadersInit = {
    "Content-Type": "application/json",
    ...options.headers,
  }

  const response = await fetch(url, {
    ...options,
    headers,
  })

  if (!response.ok) {
    const error = await response.text()
    throw new Error(`API Error: ${response.status} - ${error}`)
  }

  return response.json()
}

// ==================== EMPLOYEES API ====================

export const employeeApi = {
  // Get all employees
  async getAll(limit = 50, offset = 0): Promise<ApiResponse<Employee[]>> {
    return fetchApi(`/employees/read?limit=${limit}&offset=${offset}`)
  },

  // Search employees
  async search(query: string): Promise<ApiResponse<Employee[]>> {
    return fetchApi(`/employees/search?q=${encodeURIComponent(query)}`)
  },

  // Get single employee
  async getById(id: number): Promise<ApiResponse<Employee>> {
    return fetchApi(`/employees/read?id=${id}`)
  },

  // Create new employee
  async create(employee: Omit<Employee, "id" | "created_at" | "updated_at">): Promise<ApiResponse<Employee>> {
    return fetchApi("/employees/create", {
      method: "POST",
      body: JSON.stringify(employee),
    })
  },

  // Update employee
  async update(id: number, employee: Partial<Employee>): Promise<ApiResponse<Employee>> {
    return fetchApi("/employees/update", {
      method: "PUT",
      body: JSON.stringify({ id, ...employee }),
    })
  },

  // Delete employee
  async delete(id: number): Promise<ApiResponse<{ id: number }>> {
    return fetchApi("/employees/delete", {
      method: "DELETE",
      body: JSON.stringify({ id }),
    })
  },
}

// ==================== ATTENDANCE API ====================

export const attendanceApi = {
  // Check in
  async checkIn(employeeId: number): Promise<ApiResponse<AttendanceRecord>> {
    return fetchApi("/attendance/checkin", {
      method: "POST",
      body: JSON.stringify({ employee_id: employeeId }),
    })
  },

  // Check out
  async checkOut(employeeId: number): Promise<ApiResponse<AttendanceRecord>> {
    return fetchApi("/attendance/checkout", {
      method: "POST",
      body: JSON.stringify({ employee_id: employeeId }),
    })
  },

  // Get attendance history
  async getHistory(filters?: ReportFilter): Promise<ApiResponse<AttendanceRecord[]>> {
    const params = new URLSearchParams()
    if (filters) {
      if (filters.startDate) params.append("start_date", filters.startDate)
      if (filters.endDate) params.append("end_date", filters.endDate)
      if (filters.employeeId) params.append("employee_id", filters.employeeId.toString())
      if (filters.department) params.append("department", filters.department)
      if (filters.status) params.append("status", filters.status)
    }
    return fetchApi(`/attendance/get-history?${params.toString()}`)
  },

  // Get today's attendance
  async getTodayAttendance(): Promise<ApiResponse<AttendanceRecord[]>> {
    return fetchApi("/attendance/get-today")
  },

  // Get attendance for specific employee today
  async getTodayForEmployee(employeeId: number): Promise<ApiResponse<AttendanceRecord | null>> {
    return fetchApi(`/attendance/get-today-employee?employee_id=${employeeId}`)
  },
}

// ==================== REPORTS API ====================

export const reportsApi = {
  // Generate attendance report
  async generateReport(filters: ReportFilter): Promise<ApiResponse<any>> {
    return fetchApi("/attendance/reports", {
      method: "POST",
      body: JSON.stringify(filters),
    })
  },

  // Get dashboard stats
  async getDashboardStats(): Promise<ApiResponse<DashboardStats>> {
    return fetchApi("/attendance/stats")
  },

  // Get attendance by department
  async getByDepartment(date?: string): Promise<ApiResponse<any>> {
    const url = date ? `/attendance/by-department?date=${date}` : "/attendance/by-department"
    return fetchApi(url)
  },
}

// ==================== UTILITY FUNCTIONS ====================

// Format time for display
export function formatTime(dateString: string | null): string {
  if (!dateString) return "N/A"
  return new Date(dateString).toLocaleTimeString("en-US", {
    hour: "2-digit",
    minute: "2-digit",
    hour12: true,
  })
}

// Calculate duration between two times
export function calculateDuration(checkIn: string | null, checkOut: string | null): string {
  if (!checkIn || !checkOut) return "N/A"
  const start = new Date(checkIn)
  const end = new Date(checkOut)
  const diff = end.getTime() - start.getTime()
  const hours = Math.floor(diff / (1000 * 60 * 60))
  const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60))
  return `${hours}h ${minutes}m`
}

// Format date for display
export function formatDate(dateString: string): string {
  return new Date(dateString).toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  })
}
