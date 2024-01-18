<?php
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }

    require __DIR__ . '/../functions/jwt.php';
    
    function authenticateUser() {
        $headers = apache_request_headers();
        $token = isset($headers['Authorization']) ? $headers['Authorization'] : null;
    
        if (!$token && isset($_SERVER['HTTP_AUTHORIZATION'])) {
            // Retry with HTTP_ prefix (e.g., on some Apache servers)
            $token = $_SERVER['HTTP_AUTHORIZATION'];
        }
    
        if (!$token) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized - Missing Token']);
            exit;
        }
    
        // Remove "Bearer " prefix if present
        $token = str_replace('Bearer ', '', $token);
    
        try {
            $decoded = verifyToken($token);
            // You can perform additional checks here based on the decoded user information
            return $decoded;
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized - Invalid Token']);
            exit;
        }
    }