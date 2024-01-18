<?php
    function deleteProject($conn, $user_id, $project_id)
    {
        // Example query to get existing projects for the user
        $query = "SELECT project_data FROM projects WHERE user_id = ?";

        // Prepare the statement
        $statement = $conn->prepare($query);
        if (!$statement) {
            // Handle query preparation error
            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error']);
            return;
        }

        // Bind parameters
        $statement->bind_param("i", $user_id);

        // Execute the query
        $statement->execute();

        // Get result
        $result = $statement->get_result();

        if ($result) {
            $row = $result->fetch_assoc();
            $existing_projects = json_decode($row['project_data'], true);

            // Filter out the project with the specified project_id
            $filtered_projects = array_filter($existing_projects, function ($project) use ($project_id) {
                return $project['id'] !== $project_id;
            });

            // Update the projects in the database
            $updated_projects_data = json_encode(array_values($filtered_projects));

            // Example query to update the projects
            $update_query = "UPDATE projects SET project_data = ? WHERE user_id = ?";

            // Prepare the update statement
            $update_statement = $conn->prepare($update_query);

            if (!$update_statement) {
                // Handle update query preparation error
                http_response_code(500);
                echo json_encode(['error' => 'Internal Server Error']);
                return;
            }

            // Bind parameters for the update statement
            $update_statement->bind_param("si", $updated_projects_data, $user_id);

            // Execute the update query
            $update_success = $update_statement->execute();

            if ($update_success) {
                // Return a success message or any relevant data
                http_response_code(200); // OK
                echo json_encode(['message' => 'Project deleted successfully']);
            } else {
                // Handle update query error
                http_response_code(500);
                echo json_encode(['error' => 'Internal Server Error']);
            }

            // Close the update statement
            $update_statement->close();
        } else {
            // Handle query error
            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error']);
        }

        // Close the statement
        $statement->close();
    }
?>
