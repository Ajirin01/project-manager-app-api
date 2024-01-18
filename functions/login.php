<?php
    require_once "jwt.php";

    function login($conn, $email, $password) {
        // Example query to fetch user data by email
        $query = "SELECT id, full_name, email, password FROM users WHERE email = ?";
        
        // Prepare the statement
        $statement = $conn->prepare($query);
        if (!$statement) {
            http_response_code(500);
            return ['error' => 'Internal Server Error'];
        }

        // Bind parameters
        $statement->bind_param("s", $email);

        // Execute the query
        $statement->execute();

        // Get result
        $result = $statement->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $user_id = $row['id'];
            $full_name = $row['full_name'];
            $email = $row['email'];
            $hashedPassword = $row['password'];

            // Verify the password
            if (password_verify($password, $hashedPassword)) {
                $token = generateToken($user_id);

                http_response_code(200); // OK
                return ['message' => 'Login successful', 'token' => $token, 'user_id' => $user_id, 'full_name' => $full_name, 'email'=> $email];
            } else {
                http_response_code(401); // Unauthorized
                return ['error' => 'Invalid credentials'];
            }
        } else {
            http_response_code(401); // Unauthorized
            return ['error' => 'Invalid credentials'];
        }
    }
?>
