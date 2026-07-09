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

// Mock data for development/preview
let MOCK_DATA = {
  employees: [
    { id: 1, name: "Alice Johnson", email: "alice@example.com", department: "IT", position: "Senior Developer", phone: "555-0101", created_at: "2024-01-15T08:00:00Z", updated_at: "2024-01-15T08:00:00Z" },
    { id: 2, name: "Bob Smith", email: "bob@example.com", department: "Sales", position: "Sales Manager", phone: "555-0102", created_at: "2024-01-15T08:00:00Z", updated_at: "2024-01-15T08:00:00Z" },
    { id: 3, name: "Carol Davis", email: "carol@example.com", department: "HR", position: "HR Specialist", phone: "555-0103", created_at: "2024-01-15T08:00:00Z", updated_at: "2024-01-15T08:00:00Z" },
    { id: 4, name: "David Wilson", email: "david@example.com", department: "Marketing", position: "Marketing Lead", phone: "555-0104", created_at: "2024-01-15T08:00:00Z", updated_at: "2024-01-15T08:00:00Z" },
    { id: 5, name: "Emma Brown", email: "emma@example.com", department: "IT", position: "Junior Developer", phone: "555-0105", created_at: "2024-01-15T08:00:00Z", updated_at: "2024-01-15T08:00:00Z" },
  ],
  nextEmployeeId: 6,
  attendance: [
    { id: 1, employee_id: 1, name: "Alice Johnson", department: "IT", check_in: new Date(Date.now() - 4 * 60 * 60 * 1000).toISOString(), check_out: new Date(Date.now() - 0.5 * 60 * 60 * 1000).toISOString(), status: "present", created_at: new Date().toISOString(), updated_at: new Date().toISOString() },
    { id: 2, employee_id: 2, name: "Bob Smith", department: "Sales", check_in: new Date(Date.now() - 3.5 * 60 * 60 * 1000).toISOString(), check_out: null, status: "present", created_at: new Date().toISOString(), updated_at: new Date().toISOString() },
    { id: 3, employee_id: 3, name: "Carol Davis", department: "HR", check_in: new Date(Date.now() - 3 * 60 * 60 * 1000).toISOString(), check_out: null, status: "late", created_at: new Date().toISOString(), updated_at: new Date().toISOString() },
    { id: 4, employee_id: 4, name: "David Wilson", department: "Marketing", check_in: new Date(Date.now() - 5 * 60 * 60 * 1000).toISOString(), check_out: new Date(Date.now() - 1 * 60 * 60 * 1000).toISOString(), status: "present", created_at: new Date().toISOString(), updated_at: new Date().toISOString() },
  ],
}

// Generic fetch wrapper with error handling and mock fallback
async function fetchApi<T>(endpoint: string, options: RequestInit = {}): Promise<T> {
  try {
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
  } catch (err) {
    console.log("Using mock data - PHP backend not available yet. Set up the backend per SETUP.md to use real data.")
    let body: any = undefined
    if (options.body) {
      try {
        body = JSON.parse(options.body as string)
      } catch (e) {
        // ignore
      }
    }
    return getMockData(endpoint, body) as Promise<T>
  }
}

// Return mock data based on endpoint
function getMockData(endpoint: string, body?: any): any {
  if (endpoint.includes("/employees/read")) {
    return { success: true, data: MOCK_DATA.employees }
  }
  if (endpoint.includes("/employees/create")) {
    const newEmployee = {
      id: MOCK_DATA.nextEmployeeId++,
      ...body,
      created_at: new Date().toISOString(),
      updated_at: new Date().toISOString(),
    }
    MOCK_DATA.employees.push(newEmployee)
    return { success: true, data: newEmployee }
  }
  if (endpoint.includes("/employees/update")) {
    const index = MOCK_DATA.employees.findIndex((e) => e.id === body?.id)
    if (index !== -1) {
      MOCK_DATA.employees[index] = {
        ...MOCK_DATA.employees[index],
        ...body,
        updated_at: new Date().toISOString(),
      }
      return { success: true, data: MOCK_DATA.employees[index] }
    }
    return { success: false, data: null }
  }
  if (endpoint.includes("/employees/delete")) {
    MOCK_DATA.employees = MOCK_DATA.employees.filter((e) => e.id !== body?.id)
    return { success: true, data: { id: body?.id } }
  }
  if (endpoint.includes("/attendance/stats")) {
    return {
      success: true,
      data: {
        totalEmployees: MOCK_DATA.employees.length,
        presentToday: Math.min(4, MOCK_DATA.employees.length),
        lateToday: 1,
        absentToday: 0,
        attendancePercentage: 100,
        departments: [
          { department: "IT", count: MOCK_DATA.employees.filter((e) => e.department === "IT").length },
          { department: "Sales", count: MOCK_DATA.employees.filter((e) => e.department === "Sales").length },
          { department: "HR", count: MOCK_DATA.employees.filter((e) => e.department === "HR").length },
          { department: "Marketing", count: MOCK_DATA.employees.filter((e) => e.department === "Marketing").length },
        ].filter((d) => d.count > 0),
        recentActivity: MOCK_DATA.attendance,
      },
    }
  }
  if (endpoint.includes("/employees/search")) {
    return { success: true, data: MOCK_DATA.employees }
  }
  return { success: true, data: [] }
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
