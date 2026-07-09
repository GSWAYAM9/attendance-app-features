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
    
    // Check if already checked in today
    $existingStmt = $conn->prepare(
        "SELECT id, check_in FROM attendance WHERE employee_id = ? AND date = ? AND check_in IS NOT NULL"
    );
    $existingStmt->bind_param("is", $employeeId, $today);
    $existingStmt->execute();
    $existing = $existingStmt->get_result()->fetch_assoc();
    
    if ($existing) {
        errorResponse('Employee already checked in today', 400);
    }
    
    // Determine status (late if after 9 AM)
    $status = 'present';
    $checkInTime = new DateTime($now);
    $nineAM = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d') . ' 09:00:00');
    if ($checkInTime > $nineAM) {
        $status = 'late';
    }
    
    // Insert or update attendance record
    $upsertStmt = $conn->prepare(
        "INSERT INTO attendance (employee_id, date, check_in, status) 
         VALUES (?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE check_in = VALUES(check_in), status = VALUES(status)"
    );
    $upsertStmt->bind_param("isss", $employeeId, $today, $now, $status);
    
    if (!$upsertStmt->execute()) {
        errorResponse('Error recording check-in: ' . $upsertStmt->error, 500);
    }
    
    // Log the action
    $logStmt = $conn->prepare(
        "INSERT INTO attendance_logs (employee_id, action, details) 
         VALUES (?, 'check_in', JSON_OBJECT('check_in_time', ?))"
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
    
    successResponse($record, 'Check-in recorded successfully', 201);
    
} catch (Exception $e) {
    errorResponse('Error during check-in: ' . $e->getMessage(), 500);
} finally {
    $conn->close();
}
?>
