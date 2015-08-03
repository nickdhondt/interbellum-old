<?php

// register a user
function register_user($username, $password, $email, $key) {
    global $conn;

    $options = [
        'cost' => 10,
    ];

    // Hash the password
    $password_hash = password_hash($password, PASSWORD_BCRYPT, $options);

    // Perform a mysql query
    // Insert the user data
    $stmt = $conn->prepare("INSERT INTO user (username, password, email) VALUES(?,?,?)");
    $stmt->bind_param("sss", $username, $password_hash, $email);
    $stmt->execute();

    $result = $stmt->get_result();

    // If the registration was successful, devalue the key
    if ($stmt->affected_rows >= 1) {
        devalue_key($conn->insert_id, $key);
        return true;
    }

    return false;
}