<?php
require_once 'config.php';

// Set CORS headers
setCORSHeaders();

// Get the request URI
$requestUri = $_SERVER['REQUEST_URI'];
$basePath = '/api';

// Remove base path from URI
if (strpos($requestUri, $basePath) === 0) {
    $path = substr($requestUri, strlen($basePath));
} else {
    $path = $requestUri;
}

// Remove query string
$path = explode('?', $path)[0];

// Remove trailing slash
$path = rtrim($path, '/');

// Route mapping
$routes = [
    // Employees
    '/employees/read' => 'api/employees/read.php',
    '/employees/create' => 'api/employees/create.php',
    '/employees/search' => 'api/employees/search.php',
    '/employees/update' => 'api/employees/update.php',
    '/employees/delete' => 'api/employees/delete.php',
    
    // Attendance
    '/attendance/checkin' => 'api/attendance/checkin.php',
    '/attendance/checkout' => 'api/attendance/checkout.php',
    '/attendance/get-history' => 'api/attendance/get-history.php',
    '/attendance/stats' => 'api/attendance/stats.php',
];

// Match route
$routeFound = false;
foreach ($routes as $pattern => $file) {
    if ($path === $pattern) {
        if (file_exists($file)) {
            include $file;
            $routeFound = true;
            break;
        }
    }
}

// If no route found
if (!$routeFound) {
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Endpoint not found',
        'path' => $path
    ]);
}
?>
