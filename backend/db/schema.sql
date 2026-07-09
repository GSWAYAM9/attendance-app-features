-- Create database
CREATE DATABASE IF NOT EXISTS attendance_system;
USE attendance_system;

-- Employees table
CREATE TABLE IF NOT EXISTS employees (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) UNIQUE,
  department VARCHAR(100),
  position VARCHAR(100),
  phone VARCHAR(20),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_department (department),
  INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Attendance table
CREATE TABLE IF NOT EXISTS attendance (
  id INT PRIMARY KEY AUTO_INCREMENT,
  employee_id INT NOT NULL,
  check_in DATETIME,
  check_out DATETIME,
  date DATE NOT NULL,
  status ENUM('present', 'absent', 'late', 'half-day') DEFAULT 'present',
  notes TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
  INDEX idx_employee_date (employee_id, date),
  INDEX idx_date (date),
  INDEX idx_status (status),
  UNIQUE KEY unique_employee_date (employee_id, date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Attendance logs table (optional - for audit trail)
CREATE TABLE IF NOT EXISTS attendance_logs (
  id INT PRIMARY KEY AUTO_INCREMENT,
  employee_id INT,
  action VARCHAR(50),
  details JSON,
  timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
  INDEX idx_employee_timestamp (employee_id, timestamp)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample data (optional)
INSERT INTO employees (name, email, department, position, phone) VALUES
('John Doe', 'john.doe@company.com', 'IT', 'Senior Developer', '555-0101'),
('Alice Johnson', 'alice.johnson@company.com', 'HR', 'HR Manager', '555-0102'),
('Bob Smith', 'bob.smith@company.com', 'Sales', 'Sales Representative', '555-0103'),
('Carol Davis', 'carol.davis@company.com', 'Marketing', 'Marketing Manager', '555-0104'),
('David Wilson', 'david.wilson@company.com', 'IT', 'Software Engineer', '555-0105'),
('Eve Martinez', 'eve.martinez@company.com', 'Finance', 'Finance Analyst', '555-0106');
