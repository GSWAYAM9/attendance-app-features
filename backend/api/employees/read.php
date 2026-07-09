<?php
require_once '../../config.php';

setCORSHeaders();

$method = getRequestMethod();

if ($method !== 'GET') {
    errorResponse('Method not allowed', 405);
}

$conn = getDBConnection();

try {
    // Check if getting single employee by ID
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $stmt = $conn->prepare("SELECT * FROM employees WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $employee = $result->fetch_assoc();
        
        if (!$employee) {
            errorResponse('Employee not found', 404);
        }
        
        successResponse($employee, 'Employee retrieved successfully');
    }
    
    // Get all employees with pagination
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 50;
    $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
    
    // Validate limits
    $limit = min($limit, 100);
    $offset = max($offset, 0);
    
    // Get total count
    $countResult = $conn->query("SELECT COUNT(*) as total FROM employees");
    $countRow = $countResult->fetch_assoc();
    $total = $countRow['total'];
    
    // Get employees
    $stmt = $conn->prepare("SELECT * FROM employees ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $employees = [];
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
    
    successResponse([
        'employees' => $employees,
        'total' => $total,
        'limit' => $limit,
        'offset' => $offset
    ], 'Employees retrieved successfully');
    
} catch (Exception $e) {
    errorResponse('Error retrieving employees: ' . $e->getMessage(), 500);
} finally {
    $conn->close();
}
?>
