<?php

session_start();

require_once("../../resources/config.php");

// Script will return JSON string
header("Content-Type: application/json");

// Parameters
$legal = true;
$logged_in = false;
$response = array();
$data = array();
$field = "";
$errors = array();
$user_data = logged_in();

if (!empty($_GET)) {
    if ($user_data) {
        $logged_in = true;

        if (!empty($_GET["username"])) {
            $username = $_GET["username"];
            $legal = true;

            $data = assist_usernames(trim($username), $user_data["username"]);
        } else {
            //Set general errors
            $errors[] = "Not all data received";
        }
    } else {
        // Set general errors
        $errors[] = "Not logged in";
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
        "logged_in" => $logged_in,
        "feedback" => $data
    );
} else {
    $response = array(
        "legal" => $legal,
        "logged_in" => $logged_in,
        "errors" => $errors
    );
}

echo json_encode($response);