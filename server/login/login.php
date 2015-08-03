<?php

session_start();

require_once("../../resources/config.php");

// Script will return JSON string
header("Content-Type: application/json");

// Parameters
$legal = false;
$response = array();
$data = array();
$errors = array();

if (!empty($_POST["data"])) {
    // Decode the JSON data
    $post = json_decode($_POST["data"], true);

    if (!empty($post["username"]) || !empty($post["password"])) {
        // Get user data
        $user_data = get_data(array("t.password, t.user_id, t.username, s.description, s.permission_type"), "user", "username", $post["username"], "permission", "permission_type", "permission_type", true);

        $field = array("feedback" => "login");
        $field_legal = true;

        // Check if user exists and password is right
        if (empty($user_data) || !password_verify($post["password"], $user_data["password"])) {
            $field_legal = false;
            $field["errors"][] = "Username or password is wrong";
        } else {
            // Set session and determine permission level
            $_SESSION["-int-user_id"] = $user_data["user_id"];

            $field["user_data"]["username"] = $user_data["username"];

            if ($user_data["permission_type"] <= 1) $field["user_data"]["permission"] = $user_data["description"];
        }

        $field["legal"] = $field_legal;

        $data[] = $field;

        $legal = true;
    } else {
        // Set general errors
        $errors[] = "No data received";
    }
} else {
    // Set general errors
    $errors[] = "Received wrong data";
}

// Put it all in an array
if ($legal === true) {
    $response = array(
        "legal" => $legal,
        "feedback" => $data
    );
} else {
    $response = array(
        "legal" => $legal,
        "errors" => $errors
    );
}

// Decode and send
echo json_encode($response);