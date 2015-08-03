<?php

session_start();

require_once("../../resources/config.php");

// Script will return JSON string
header("Content-Type: application/json");

// Parameters
$legal = true;
$response = array();
$data = array();
$field = "";
$errors = array();

if (!empty($_POST["data"])) {
    // Decode the JSON data
    $post = json_decode($_POST["data"], true);

    if (!empty($post["username"])) {
        $field = array("field" => "username");
        $field_legal = true;

        // Check if data exists
        if (data_exists($post["username"], "user", "username")) {
            $field_legal = false;
            $field["errors"][] = "Username already exists";
        }

        $field["legal"] = $field_legal;

        $data[] = $field;

    } elseif (!empty($post["email"])) {

        $field = array("field" => "email");
        $field_legal = true;

        // Check if data exists
        if (data_exists($post["email"], "user", "email")) {
            $field_legal = false;
            $field["errors"][] = "Email address already used";
        }

        $field["legal"] = $field_legal;

        $data[] = $field;
    } else {
        // Set general error
        $errors[] = "No data received";
        $legal = false;
    }
} else {
    // Set general error
    $errors[] = "Received wrong data";
    $legal = false;
}

// Put all data in an array
if ($legal === true) {
    $response = array(
        "legal" => $legal,
        "fields" => $data
    );
} else {
    $response = array(
        "legal" => $legal,
        "errors" => $errors
    );
}

echo json_encode($response);