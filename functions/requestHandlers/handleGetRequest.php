<?php
    include_once __DIR__.'/../getProjects.php';
    include_once __DIR__.'/../getProjectById.php';
    
    function handleGetRequest($conn) {
        if ($_GET['route'] === 'projects') {
            $userId = $_GET['user_id']; // Assuming you pass the user_id as a query parameter
            echo getAllUserProject($conn, $userId);

        }else if ($_GET['route'] === 'project') {
            $userId = $_GET['user_id']; // Assuming you pass the user_id as a query parameter
            $projectId = $_GET['project_id']; // Assuming you pass the user_id as a query parameter

            echo getUserProjectById($conn, $userId, $projectId);
            
        }else {
            // Handle unsupported routes for GET requests
            http_response_code(404);
            echo json_encode(['error' => 'Not Found']);
        }
    }
?>  