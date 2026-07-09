<?php
require_once '../../config.php';

setCORSHeaders();

$method = getRequestMethod();

if ($method !== 'GET') {
    errorResponse('Method not allowed', 405);
}

$conn = getDBConnection();

try {
    $today = date('Y-m-d');
    
    // Total employees
    $totalStmt = $conn->query("SELECT COUNT(*) as total FROM employees");
    $totalRow = $totalStmt->fetch_assoc();
    $totalEmployees = $totalRow['total'];
    
    // Present today
    $presentStmt = $conn->query(
        "SELECT COUNT(DISTINCT employee_id) as count FROM attendance 
         WHERE date = '$today' AND check_in IS NOT NULL"
    );
    $presentRow = $presentStmt->fetch_assoc();
    $presentToday = $presentRow['count'];
    
    // Absent today
    $absentStmt = $conn->query(
        "SELECT COUNT(DISTINCT e.id) as count FROM employees e 
         LEFT JOIN attendance a ON e.id = a.employee_id AND a.date = '$today'
         WHERE a.id IS NULL"
    );
    $absentRow = $absentStmt->fetch_assoc();
    $absentToday = $absentRow['count'];
    
    // Late today
    $lateStmt = $conn->query(
        "SELECT COUNT(*) as count FROM attendance 
         WHERE date = '$today' AND status = 'late'"
    );
    $lateRow = $lateStmt->fetch_assoc();
    $lateToday = $lateRow['count'];
    
    // Attendance percentage
    $attendancePercentage = $totalEmployees > 0 ? round(($presentToday / $totalEmployees) * 100, 2) : 0;
    
    // Department distribution
    $deptStmt = $conn->query(
        "SELECT e.department, COUNT(*) as count FROM attendance a 
         JOIN employees e ON a.employee_id = e.id 
         WHERE a.date = '$today' AND a.check_in IS NOT NULL
         GROUP BY e.department"
    );
    
    $departments = [];
    while ($row = $deptStmt->fetch_assoc()) {
        $departments[] = $row;
    }
    
    // Recent activity
    $activityStmt = $conn->query(
        "SELECT a.*, e.name, e.department FROM attendance a 
         JOIN employees e ON a.employee_id = e.id 
         WHERE a.date = '$today'
         ORDER BY a.check_in DESC LIMIT 10"
    );
    
    $activity = [];
    while ($row = $activityStmt->fetch_assoc()) {
        $activity[] = $row;
    }
    
    successResponse([
        'totalEmployees' => $totalEmployees,
        'presentToday' => $presentToday,
        'absentToday' => $absentToday,
        'lateToday' => $lateToday,
        'attendancePercentage' => $attendancePercentage,
        'departments' => $departments,
        'recentActivity' => $activity
    ], 'Dashboard statistics retrieved successfully');
    
} catch (Exception $e) {
    errorResponse('Error retrieving statistics: ' . $e->getMessage(), 500);
} finally {
    $conn->close();
}
?>
