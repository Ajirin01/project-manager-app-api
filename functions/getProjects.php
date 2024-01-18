<?php
    authenticateUser();

    function getAllUserProject($conn, $userId){
        $query = "SELECT * FROM projects WHERE user_id = ?";
            
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
                $projects = [];
                while ($row = $result->fetch_assoc()) {
                    $projects[] = json_decode($row['project_data'], true);
                }
                if(count($projects) > 0){
                    return json_encode($projects[0]);
                }else{
                    return json_encode([]);
                }
            } else {
                // Handle query error
                http_response_code(500);
                return json_encode(['error' => 'Internal Server Error']);
            }

            // Close the statement
            $statement->close();
    }