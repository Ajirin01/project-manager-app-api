<?php
    require_once "jwt.php";

    function register($conn, $full_name, $email, $password){
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Example query to register a new user
        $query = "INSERT INTO users (full_name, password, email) VALUES (?, ?, ?)";

        // Prepare the statement
        $statement = $conn->prepare($query);
        if (!$statement) {
            // Handle query preparation error
            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error']);
            return;
        }

        // Bind parameters
        $statement->bind_param("sss", $full_name, $hashedPassword, $email);

        // Execute the query
        $success = $statement->execute();

        // Check if the query was successful
        if ($success) {
            $userId = $conn->insert_id;
            $token = generateToken($userId, $full_name);

            http_response_code(201); // Created
            echo json_encode(['message' => 'User registered successfully', 'token' => $token]);
        } else {
            // Handle query error
            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error']);
        }

        // Close the statement
        $statement->close();
    }