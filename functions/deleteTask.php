<?php
    function deleteSubtask($conn, $userId, $subtaskId) {
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
        $updatedProjects = deleteTaskFromItems($projects, $subtaskId);

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

    function deleteTaskFromItems($subtasks, $subtaskId) {
        return array_filter(array_map(function ($item) use ($subtaskId) {
            if ($item['id'] === $subtaskId) {
                // Exclude the subtask with the given subtaskId
                return null;
            } elseif (!empty($item['subtasks'])) {
                // Recursively traverse subtasks
                $item['subtasks'] = deleteTaskFromItems($item['subtasks'], $subtaskId);
            }
            return $item;
        }, $subtasks));
    }
?>
