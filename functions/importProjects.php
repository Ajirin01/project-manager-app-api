<?php
    function importProjects($conn, $user_id, $projects_data)
    {
        authenticateUser();
        // Example query to get existing projects for the user
        $select_query = "SELECT project_data FROM projects WHERE user_id = ?";

        // Prepare the select statement
        $select_statement = $conn->prepare($select_query);
        if (!$select_statement) {
            // Handle select query preparation error
            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error']);
            return;
        }

        // Bind parameters for the select statement
        $select_statement->bind_param("i", $user_id);

        // Execute the select query
        $select_statement->execute();

        // Get result
        $select_result = $select_statement->get_result();

        if ($select_result) {
            $row = $select_result->fetch_assoc();

            if ($select_result->num_rows == 0) {
                // No existing projects, create a new array
                $existing_projects = [];
            } else {
                // Decode existing projects data
                $existing_projects = json_decode($row['project_data'], true);
            }

            // Iterate through frontend submitted projects
            foreach ($projects_data as $frontend_project) {
                // Check if the project exists in the existing projects array
                $existing_project_index = array_search($frontend_project['id'], array_column($existing_projects, 'id'));

                if ($existing_project_index !== false) {
                    // Update existing project by merging with frontend data
                    $existing_projects[$existing_project_index] = array_merge($existing_projects[$existing_project_index], $frontend_project);
                } else {
                    // Add new project
                    array_push($existing_projects, $frontend_project);
                }
            }

            // Encode the updated projects array
            $updated_projects_data = json_encode($existing_projects);

            // Prepare the update statement
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
                // Return the updated projects object
                http_response_code(201); // Created or Updated
                echo json_encode(['message' => 'Projects updated successfully', 'projects' => $existing_projects]);
            } else {
                // Handle update query error
                http_response_code(500);
                echo json_encode(['error' => 'Internal Server Error']);
            }

            // Close the update statement
            $update_statement->close();
        } else {
            // Handle select query error
            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error']);
        }

        // Close the select statement
        $select_statement->close();
    }



?>
