<?php
require_once '../../config.php';

setCORSHeaders();

$method = getRequestMethod();

if ($method !== 'GET') {
    errorResponse('Method not allowed', 405);
}

$conn = getDBConnection();

try {
    $query = isset($_GET['q']) ? trim($_GET['q']) : '';
    $department = isset($_GET['department']) ? trim($_GET['department']) : '';
    
    if (empty($query) && empty($department)) {
        errorResponse('Search query or department filter required', 400);
    }
    
    $sql = "SELECT * FROM employees WHERE 1=1";
    $params = [];
    $types = "";
    
    if (!empty($query)) {
        $searchTerm = "%$query%";
        $sql .= " AND (name LIKE ? OR email LIKE ? OR phone LIKE ?)";
        $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
        $types .= "sss";
    }
    
    if (!empty($department)) {
        $sql .= " AND department = ?";
        $params[] = $department;
        $types .= "s";
    }
    
    $sql .= " ORDER BY name ASC";
    
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    $employees = [];
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
    
    successResponse($employees, 'Search completed successfully');
    
} catch (Exception $e) {
    errorResponse('Error searching employees: ' . $e->getMessage(), 500);
} finally {
    $conn->close();
}
?>
