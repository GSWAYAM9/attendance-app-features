<?php
require_once '../../config.php';

setCORSHeaders();

$method = getRequestMethod();

if ($method !== 'POST') {
    errorResponse('Method not allowed', 405);
}

$data = getRequestBody();
$conn = getDBConnection();

try {
    // Validate required fields
    if (!isset($data['name']) || empty(trim($data['name']))) {
        errorResponse('Employee name is required', 400);
    }
    if (!isset($data['email']) || empty(trim($data['email']))) {
        errorResponse('Email is required', 400);
    }
    
    // Validate email format
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        errorResponse('Invalid email format', 400);
    }
    
    // Sanitize input
    $name = trim($data['name']);
    $email = trim($data['email']);
    $department = isset($data['department']) ? trim($data['department']) : null;
    $position = isset($data['position']) ? trim($data['position']) : null;
    $phone = isset($data['phone']) ? trim($data['phone']) : null;
    
    // Check if email already exists
    $checkStmt = $conn->prepare("SELECT id FROM employees WHERE email = ?");
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    if ($checkStmt->get_result()->num_rows > 0) {
        errorResponse('Email already exists', 400);
    }
    
    // Insert employee
    $stmt = $conn->prepare(
        "INSERT INTO employees (name, email, department, position, phone) 
         VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("sssss", $name, $email, $department, $position, $phone);
    
    if (!$stmt->execute()) {
        errorResponse('Error creating employee: ' . $stmt->error, 500);
    }
    
    // Get inserted employee
    $newId = $conn->insert_id;
    $selectStmt = $conn->prepare("SELECT * FROM employees WHERE id = ?");
    $selectStmt->bind_param("i", $newId);
    $selectStmt->execute();
    $employee = $selectStmt->get_result()->fetch_assoc();
    
    successResponse($employee, 'Employee created successfully', 201);
    
} catch (Exception $e) {
    errorResponse('Error creating employee: ' . $e->getMessage(), 500);
} finally {
    $conn->close();
}
?>
