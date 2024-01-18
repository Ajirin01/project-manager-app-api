<?php
    include_once __DIR__.'/../deleteProject.php';
    include_once __DIR__.'/../deleteTask.php';

    function handleDeleteRequest($conn) {
        if ($_GET['route'] === 'delete-project') {
            // Assuming you pass user_id as a parameter or in the request body
            $project_id = $_GET['project_id'];
            $user_id = $_GET['user_id'];

            // Call the deleteProject function
            deleteProject($conn, $user_id, $project_id);
        }else if ($_GET['route'] === 'delete-task') {
            $task_id = $_GET['task_id'];
            $user_id = $_GET['user_id'];

            // Call the deleteProject function
            echo json_encode(deleteSubtask($conn, $user_id, $task_id));
        } else {
            // Handle unsupported routes for POST requests
            http_response_code(404);
            echo json_encode(['error' => 'Not Found']);
        }
    }
?>