<?php
require_once '../../config.php';

setCORSHeaders();

$method = getRequestMethod();

if ($method !== 'PUT') {
    errorResponse('Method not allowed', 405);
}

$data = getRequestBody();
$conn = getDBConnection();

try {
    if (!isset($data['id']) || empty($data['id'])) {
        errorResponse('Employee ID is required', 400);
    }
    
    $id = intval($data['id']);
    
    // Check if employee exists
    $checkStmt = $conn->prepare("SELECT id FROM employees WHERE id = ?");
    $checkStmt->bind_param("i", $id);
    $checkStmt->execute();
    if ($checkStmt->get_result()->num_rows === 0) {
        errorResponse('Employee not found', 404);
    }
    
    // Build dynamic update query
    $updates = [];
    $params = [];
    $types = "";
    
    $updateFields = ['name', 'email', 'department', 'position', 'phone'];
    
    foreach ($updateFields as $field) {
        if (isset($data[$field])) {
            $updates[] = "$field = ?";
            $params[] = trim($data[$field]) ?: null;
            $types .= "s";
        }
    }
    
    if (empty($updates)) {
        errorResponse('No fields to update', 400);
    }
    
    // Check for duplicate email
    if (isset($data['email'])) {
        $newEmail = trim($data['email']);
        $checkEmailStmt = $conn->prepare("SELECT id FROM employees WHERE email = ? AND id != ?");
        $checkEmailStmt->bind_param("si", $newEmail, $id);
        $checkEmailStmt->execute();
        if ($checkEmailStmt->get_result()->num_rows > 0) {
            errorResponse('Email already exists', 400);
        }
    }
    
    // Execute update
    $params[] = $id;
    $types .= "i";
    
    $sql = "UPDATE employees SET " . implode(", ", $updates) . " WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    
    if (!$stmt->execute()) {
        errorResponse('Error updating employee: ' . $stmt->error, 500);
    }
    
    // Return updated employee
    $selectStmt = $conn->prepare("SELECT * FROM employees WHERE id = ?");
    $selectStmt->bind_param("i", $id);
    $selectStmt->execute();
    $employee = $selectStmt->get_result()->fetch_assoc();
    
    successResponse($employee, 'Employee updated successfully');
    
} catch (Exception $e) {
    errorResponse('Error updating employee: ' . $e->getMessage(), 500);
} finally {
    $conn->close();
}
?>
