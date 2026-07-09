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
    if (!isset($data['employee_id']) || empty($data['employee_id'])) {
        errorResponse('Employee ID is required', 400);
    }
    
    $employeeId = intval($data['employee_id']);
    $today = date('Y-m-d');
    $now = date('Y-m-d H:i:s');
    
    // Verify employee exists
    $checkStmt = $conn->prepare("SELECT id FROM employees WHERE id = ?");
    $checkStmt->bind_param("i", $employeeId);
    $checkStmt->execute();
    if ($checkStmt->get_result()->num_rows === 0) {
        errorResponse('Employee not found', 404);
    }
    
    // Check if checked in today
    $existingStmt = $conn->prepare(
        "SELECT id, check_in FROM attendance WHERE employee_id = ? AND date = ? AND check_in IS NOT NULL"
    );
    $existingStmt->bind_param("is", $employeeId, $today);
    $existingStmt->execute();
    $existing = $existingStmt->get_result()->fetch_assoc();
    
    if (!$existing) {
        errorResponse('Employee has not checked in today', 400);
    }
    
    // Check if already checked out
    if ($existing['check_out']) {
        errorResponse('Employee already checked out today', 400);
    }
    
    // Update check-out time
    $updateStmt = $conn->prepare(
        "UPDATE attendance SET check_out = ? WHERE employee_id = ? AND date = ?"
    );
    $updateStmt->bind_param("sis", $now, $employeeId, $today);
    
    if (!$updateStmt->execute()) {
        errorResponse('Error recording check-out: ' . $updateStmt->error, 500);
    }
    
    // Log the action
    $logStmt = $conn->prepare(
        "INSERT INTO attendance_logs (employee_id, action, details) 
         VALUES (?, 'check_out', JSON_OBJECT('check_out_time', ?))"
    );
    $logStmt->bind_param("is", $employeeId, $now);
    $logStmt->execute();
    
    // Fetch the updated record
    $selectStmt = $conn->prepare(
        "SELECT * FROM attendance WHERE employee_id = ? AND date = ?"
    );
    $selectStmt->bind_param("is", $employeeId, $today);
    $selectStmt->execute();
    $record = $selectStmt->get_result()->fetch_assoc();
    
    successResponse($record, 'Check-out recorded successfully');
    
} catch (Exception $e) {
    errorResponse('Error during check-out: ' . $e->getMessage(), 500);
} finally {
    $conn->close();
}
?>
