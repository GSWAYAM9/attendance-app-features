<?php
require_once '../../config.php';

setCORSHeaders();

$method = getRequestMethod();

if ($method !== 'GET') {
    errorResponse('Method not allowed', 405);
}

$conn = getDBConnection();

try {
    $employeeId = isset($_GET['employee_id']) ? intval($_GET['employee_id']) : null;
    $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : null;
    $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : null;
    $department = isset($_GET['department']) ? $_GET['department'] : null;
    $status = isset($_GET['status']) ? $_GET['status'] : null;
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 100;
    $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
    
    // Validate dates
    if ($startDate && !strtotime($startDate)) {
        errorResponse('Invalid start date format', 400);
    }
    if ($endDate && !strtotime($endDate)) {
        errorResponse('Invalid end date format', 400);
    }
    
    // Build query
    $sql = "SELECT a.*, e.name, e.email, e.department FROM attendance a 
            LEFT JOIN employees e ON a.employee_id = e.id 
            WHERE 1=1";
    $params = [];
    $types = "";
    
    if ($employeeId) {
        $sql .= " AND a.employee_id = ?";
        $params[] = $employeeId;
        $types .= "i";
    }
    
    if ($startDate) {
        $sql .= " AND a.date >= ?";
        $params[] = $startDate;
        $types .= "s";
    }
    
    if ($endDate) {
        $sql .= " AND a.date <= ?";
        $params[] = $endDate;
        $types .= "s";
    }
    
    if ($department) {
        $sql .= " AND e.department = ?";
        $params[] = $department;
        $types .= "s";
    }
    
    if ($status) {
        $sql .= " AND a.status = ?";
        $params[] = $status;
        $types .= "s";
    }
    
    $sql .= " ORDER BY a.date DESC, a.check_in DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $types .= "ii";
    
    // Get total count
    $countSql = "SELECT COUNT(*) as total FROM attendance a 
                 LEFT JOIN employees e ON a.employee_id = e.id 
                 WHERE 1=1";
    
    // Add same conditions to count query
    if ($employeeId) $countSql .= " AND a.employee_id = $employeeId";
    if ($startDate) $countSql .= " AND a.date >= '$startDate'";
    if ($endDate) $countSql .= " AND a.date <= '$endDate'";
    if ($department) $countSql .= " AND e.department = '$department'";
    if ($status) $countSql .= " AND a.status = '$status'";
    
    $countResult = $conn->query($countSql);
    $countRow = $countResult->fetch_assoc();
    $total = $countRow['total'];
    
    // Execute main query
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    $records = [];
    while ($row = $result->fetch_assoc()) {
        $records[] = $row;
    }
    
    successResponse([
        'records' => $records,
        'total' => $total,
        'limit' => $limit,
        'offset' => $offset
    ], 'Attendance history retrieved successfully');
    
} catch (Exception $e) {
    errorResponse('Error retrieving attendance history: ' . $e->getMessage(), 500);
} finally {
    $conn->close();
}
?>
