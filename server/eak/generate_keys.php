<?php

session_start();

require_once("../../resources/config.php");

// Script will return JSON string
header("Content-Type: application/json");

// Parameters
$legal = false;
$response = array();
$data = array();
$keys = "";
$errors = array();

if (!empty($_POST["data"])) {
    // Decode the JSON data
    $post = json_decode($_POST["data"], true);
    if (!empty($post["keys"])){
        // Get date and generate keys
        $data["time"] = date("j-n-Y"). " - " . date("H:i:s");
        $keys = generate_early_access_keys($post["keys"]);

        $field = array("feedback" => "keys");
        $field_legal = true;

        $field["legal"] = $field_legal;
        $field["keys"] = $keys;

        $data[] = $field;

        $legal = true;
    } else {
        // Set general error
        $errors[] = "Not all data received";
    }
} else {
    // Set general error
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