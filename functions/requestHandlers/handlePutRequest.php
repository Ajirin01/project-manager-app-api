<?php
include_once __DIR__.'/../updateItem.php';

function handlePutRequest($conn) {
    if ($_GET['route'] === 'update-item') {
        $json_data = file_get_contents('php://input');

        // Decode JSON data into an associative array
        $data = json_decode($json_data, true);

        // Check if the required fields are present in the JSON data
        if (!isset($data['project_data'])) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'Invalid request parameters']);
            exit();
        }

        // Extract data from the decoded JSON array
        $projectData = $data['project_data'];


        // Extract user_id and project data from the request
        $userId = $_GET['user_id'];
        $projectId = $projectData['projectId'];

        $payload = [
            'name' => $projectData['name'],
            'description' => $projectData['description'],
            'status' => $projectData['status']
            // Add other fields as needed
        ];

        echo json_encode(updateItem($conn, $userId, $projectId, $payload));
    } else if ($_GET['route'] === 'update-task') {
        // Handle update task logic here
    } else {
        // Handle unsupported routes for PUT requests
        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
    }
}
?>
