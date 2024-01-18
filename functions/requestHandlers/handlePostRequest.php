<?php
// include_once __DIR__.'/../addProject.php';
// include_once __DIR__.'/../addTask.php';
// include_once __DIR__.'/../login.php';
// include_once __DIR__.'/../register.php';

function handlePostRequest($conn) {
    if ($_GET['route'] === 'add-project') {
        include_once __DIR__.'/../addProject.php';

        // Assuming you pass user_id as a parameter or in the request body
        $user_id = $_GET['user_id'];

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
        $new_project_data = $data['project_data'];

        addProject($conn, $user_id, $new_project_data);
        
    }else if($_GET['route'] === 'add-task'){
        include_once __DIR__.'/../addTask.php';

        // Assuming you pass user_id as a parameter or in the request body
        $user_id = $_GET['user_id'];

        $json_data = file_get_contents('php://input');

        // Decode JSON data into an associative array
        $data = json_decode($json_data, true);

        // Check if the required fields are present in the JSON data
        if (!isset($data['subTask_data'])) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'Invalid request parameters']);
            exit();
        }

        // Extract data from the decoded JSON array
        $new_subtask_data = $data['subTask_data'];

        $parentId = $new_subtask_data['parentId'];

        $payload = [
            'name' => $new_subtask_data['name'],
            'description' => $new_subtask_data['description'],
            'status' => $new_subtask_data['status'],
            'id'=> $new_subtask_data['id'],
            'collapsed' => true,
            'subtasks' => [],
            // Add other fields as needed
        ];

        echo json_encode(addSubtaskToItem($conn, $user_id, $parentId, $payload));

    }else if($_GET['route'] === 'login'){
        include_once __DIR__.'/../login.php';

        $json_data = file_get_contents('php://input');

        // Decode JSON data into an associative array
        $data = json_decode($json_data, true);

        // Check if the required fields are present in the JSON data
        if (!isset($data['email']) || !isset($data['password'])) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'Invalid request parameters']);
            exit();
        }

        // Extract data from the decoded JSON array
        $email = $data['email'];
        $password = $data['password'];

        $login = login($conn, $email, $password);

        echo json_encode($login);

    }else if($_GET['route'] === 'register'){
        include_once __DIR__.'/../register.php';

        $json_data = file_get_contents('php://input');

        // Decode JSON data into an associative array
        $data = json_decode($json_data, true);

        // Check if the required fields are present in the JSON data
        if (!isset($data['fullName']) || !isset($data['email']) || !isset($data['password'])) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'Invalid request parameters']);
            exit();
        }

        // Extract data from the decoded JSON array
        $full_name = $data['fullName'];
        $email = $data['email'];
        $password = $data['password'];

        $register = register($conn, $full_name, $email, $password);

        echo json_encode($register);
    }else if($_GET['route'] === 'import-projects'){
        include_once __DIR__.'/../importProjects.php';
        
        $json_data = file_get_contents('php://input');

        $user_id = $_GET['user_id'];

        // Decode JSON data into an associative array
        $data = json_decode($json_data, true);

        // Check if the required fields are present in the JSON data
        if (!isset($data['projectsData'])) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'Invalid request parameters']);
            exit();
        }

        $projects_data = $data['projectsData'];

        importProjects($conn, $user_id, $projects_data);
        // echo $data['projectData'];
    }
}

?>