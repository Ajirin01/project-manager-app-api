<?php
    function addSubtaskToItem($conn, $userId, $parentId, $payload) {
        authenticateUser();
        
        $selectProjectsQuery = "SELECT project_data FROM projects WHERE user_id = ?";
        $selectProjectsStmt = $conn->prepare($selectProjectsQuery);
        $selectProjectsStmt->bind_param("i", $userId);
        $selectProjectsStmt->execute();
        $selectProjectsResult = $selectProjectsStmt->get_result();

        if (!$selectProjectsResult) {
            // Handle query error
            return ['error' => 'Failed to retrieve projects'];
        }

        // Fetch the existing projects
        $row = $selectProjectsResult->fetch_assoc();
        $projects = json_decode($row['project_data'], true);

        // Update the projects array
        $updatedProjects = addSubtaskToProject($projects, $parentId, $payload);

        $jsonUpdatedProjects = json_encode($updatedProjects);

        // Example query to update the projects in the database
        $updateProjectsQuery = "UPDATE projects SET project_data = ? WHERE user_id = ?";
        $updateProjectsStmt = $conn->prepare($updateProjectsQuery);
        $updateProjectsStmt->bind_param("si", $jsonUpdatedProjects, $userId);

        if ($updateProjectsStmt->execute()) {
            return $updatedProjects;
        } else {
            // Handle query error
            return ['error' => 'Failed to update projects'];
        }
    }

    function addSubtaskToProject($projects, $parentId, $payload) {
        return array_map(function ($item) use ($parentId, $payload) {
            if ($item['id'] === $parentId) {
                if (!empty($payload['isTask']) && $payload['isTask']) {
                    // If parent is a task, perform deep search
                    $item['subtasks'] = addSubtaskToTask($item['subtasks'], $parentId, $payload);
                } else {
                    // If parent is a project, simply push to subtasks
                    $item['subtasks'][] = $payload;
                }
            } elseif (!empty($item['subtasks'])) {
                // Recursively traverse subtasks
                $item['subtasks'] = addSubtaskToProject($item['subtasks'], $parentId, $payload);
            }
            return $item;
        }, $projects);
    }

    function addSubtaskToTask($subtasks, $parentId, $payload) {
        return array_map(function ($item) use ($parentId, $payload) {
            if ($item['id'] === $parentId) {
                $item['subtasks'][] = [
                    'id' => uniqid(),  // You can use a proper method to generate unique IDs
                    'name' => $payload['formData']['name'],
                    'description' => $payload['formData']['description'],
                    'status' => 'pending',
                    'subtasks' => [],
                    'collapsed' => true,
                ];
            } elseif (!empty($item['subtasks'])) {
                // Recursively traverse subtasks
                $item['subtasks'] = addSubtaskToTask($item['subtasks'], $parentId, $payload);
            }
            return $item;
        }, $subtasks);
    }

?>
