<?php
    // authenticateUser();

    function getUserProjectById($conn, $userId, $projectId) {
        $query = "SELECT project_data FROM projects WHERE user_id = ?";
        
        // Prepare the statement
        $statement = $conn->prepare($query);
        if (!$statement) {
            // Handle query preparation error
            http_response_code(500);
            return json_encode(['error' => 'Internal Server Error']);
        }

        // Bind parameters
        $statement->bind_param("i", $userId);

        // Execute the query
        $statement->execute();

        // Get result
        $result = $statement->get_result();

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $projects = json_decode($row['project_data'], true);

                // Search for the project by ID
                $foundProject = findProjectById($projects, $projectId);

                if ($foundProject) {
                    return json_encode($foundProject);
                } else {
                    return json_encode([]);
                }
            }
        } else {
            // Handle query error
            http_response_code(500);
            return json_encode(['error' => 'Internal Server Error']);
        }

        // Close the statement
        $statement->close();
    }

    function findProjectById($projects, $projectId) {
        foreach ($projects as $project) {
            if ($project['id'] === $projectId) {
                return $project;
            }

            // Recursively search in subtasks
            $foundInSubtasks = findProjectById($project['subtasks'], $projectId);
            if ($foundInSubtasks) {
                return $foundInSubtasks;
            }
        }

        return null;
    }
?>
