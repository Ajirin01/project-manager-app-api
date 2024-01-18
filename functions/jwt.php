<?php
// require 'vendor/autoload.php';
require __DIR__ . '/../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function generateToken($userId) {
    $key = "My-pin<is>Sin2@+cos2@"; // Replace with a secure secret key
    $payload = [
        "user_id" => $userId,
        "exp" => time() + (60 * 60 * 24), // Token expiration time (e.g., 24 hours)
    ];

    return JWT::encode($payload, $key, 'HS256');
}

function verifyToken($token) {
    $key = "My-pin<is>Sin2@+cos2@"; // Replace with a secure secret key

    // Decode the token without passing $headers by reference
    // $decoded = JWT::decode($token, $key, array('HS256'));
    $decoded = JWT::decode($token, new Key($key, 'HS256'));
    
    return $decoded;
}


?>
