<?php
// Database configuration
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'attendance_system');
define('DB_PORT', getenv('DB_PORT') ?: 3306);

// API Configuration
define('API_VERSION', '1.0');
define('API_DEBUG', getenv('API_DEBUG') ?: false);

// CORS Configuration
define('ALLOWED_ORIGINS', ['http://localhost:3000', 'http://localhost:8000', 'http://127.0.0.1:3000']);

// Database connection function
function getDBConnection() {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
        
        if ($conn->connect_error) {
            throw new Exception("Database connection failed: " . $conn->connect_error);
        }
        
        $conn->set_charset("utf8mb4");
        return $conn;
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Database connection error',
            'error' => API_DEBUG ? $e->getMessage() : ''
        ]);
        exit;
    }
}

// Response helper functions
function successResponse($data, $message = "Operation successful", $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode([
        'success' => true,
        'data' => $data,
        'message' => $message
    ]);
    exit;
}

function errorResponse($message, $statusCode = 400, $data = null) {
    http_response_code($statusCode);
    echo json_encode([
        'success' => false,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

// CORS headers
function setCORSHeaders() {
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    
    if (in_array($origin, ALLOWED_ORIGINS)) {
        header("Access-Control-Allow-Origin: $origin");
    }
    
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Accept");
    header("Access-Control-Max-Age: 3600");
    header("Content-Type: application/json");
    
    // Handle preflight requests
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }
}

// Request method helper
function getRequestMethod() {
    return $_SERVER['REQUEST_METHOD'];
}

// Get request body JSON
function getRequestBody() {
    $input = file_get_contents("php://input");
    return json_decode($input, true) ?? [];
}
?>
