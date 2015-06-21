<?php

function register_user($username, $password, $email, $key) {
    global $conn;

    $options = [
        'cost' => 10,
    ];

    $password_hash = password_hash($password, PASSWORD_BCRYPT, $options);

    $stmt = $conn->prepare("INSERT INTO user (username, password, email) VALUES(?,?,?)");
    $stmt->bind_param("sss", $username, $password_hash, $email);
    $stmt->execute();

    devalue_key($conn->insert_id, $key);

    $result = $stmt->get_result();

    if ($stmt->affected_rows >= 1) {
        return true;
    }

    return false;
}