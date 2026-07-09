<?php
require_once '../../config.php';

setCORSHeaders();

$method = getRequestMethod();

if ($method !== 'DELETE') {
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
    
    // Delete employee (cascade will handle attendance records)
    $stmt = $conn->prepare("DELETE FROM employees WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if (!$stmt->execute()) {
        errorResponse('Error deleting employee: ' . $stmt->error, 500);
    }
    
    successResponse(['id' => $id], 'Employee deleted successfully');
    
} catch (Exception $e) {
    errorResponse('Error deleting employee: ' . $e->getMessage(), 500);
} finally {
    $conn->close();
}
?>
