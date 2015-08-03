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

    if (!empty($post["key"])) {
        $field = array("field" => "eak");
        $field_legal = true;

        // Check if key is available
        if (!early_access_key_available($post["key"])) {
            $field_legal = false;
        }

        $field["legal"] = $field_legal;

        $data[] = $field;

        $legal = true;
    } else {
        // Set general errors
        $errors[] = "No key received";
    }
} else {
    // Set general errors
    $errors[] = "Received wrong data";
}

// Put it all in an array
if ($legal === true) {
    $response = array(
        "legal" => $legal,
        "eak" => $data
    );
} else {
    $response = array(
        "legal" => $legal,
        "errors" => $errors
    );
}

// Decode and send
echo json_encode($response);