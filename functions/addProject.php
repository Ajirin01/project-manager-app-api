<?php
    // require __DIR__ . '/../middlewares/apiAuth.php';

     // middleware to check if the user is logged in

    function addProject($conn, $user_id, $new_project_data)
    {
        authenticateUser();
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
            

            if ($result->num_rows == 0) {
                // Add the new project
                $existing_projects = json_decode('[]', true);
                array_push($existing_projects, $new_project_data);

                // Update the projects in the database
                $updated_projects_data = json_encode($existing_projects);

                // Example query to update or insert the projects
                $update_query = "INSERT INTO projects (user_id, project_data) VALUES (?, ?)";

                // Prepare the update statement
                $update_statement = $conn->prepare($update_query);

                if (!$update_statement) {
                    // Handle update query preparation error
                    http_response_code(500);
                    echo json_encode(['error' => 'Internal Server Error']);
                    return;
                }
    
                // Bind parameters for the update statement
                $update_statement->bind_param("is", $user_id, $updated_projects_data);
            } else {
                $existing_projects = json_decode($row['project_data'], true);
                // Add the new project
                array_push($existing_projects, $new_project_data);

                // Update the projects in the database
                $updated_projects_data = json_encode($existing_projects);

                // Example query to update or insert the projects
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
            }

            

            // Execute the update query
            $update_success = $update_statement->execute();

            if ($update_success) {
                // Retrieve the entire project record after the update
                $select_query = "SELECT * FROM projects WHERE user_id = ?";

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
                    $updated_row = $select_result->fetch_assoc();

                    // Return the updated project object
                    http_response_code(201); // Created or Updated
                    echo json_encode(['message' => 'Project added or updated successfully', 'project' => $updated_row]);
                } else {
                    // Handle select query error
                    http_response_code(500);
                    echo json_encode(['error' => 'Internal Server Error']);
                }

                // Close the select statement
                $select_statement->close();
            } else {
                // Handle update query error
                http_response_code(500);
                echo json_encode(['error' => 'Internal Server Error']);
            }

            // Close the update statement
            $update_statement->close();
        } else {
            // Handle unsupported routes for POST requests
            http_response_code(404);
            echo json_encode(['error' => 'Not Found']);
        }

        // Close the statement
        $statement->close();
    }
?>
