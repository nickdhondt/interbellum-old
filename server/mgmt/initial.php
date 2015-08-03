<?php

session_start();

require_once("../../resources/config.php");

// Script will return JSON string
header("Content-Type: application/json");

// Parameters
$legal = false;
$logged_in = false;
$response = array();
$data = array();
$errors = array();
$user_id = $_SESSION["-int-user_id"];

if (logged_in()) {
    $logged_in = true;
    $legal = true;

    if (!data_exists($user_id, "city", "user_id")) {
        if (get_data(array("first_city"), "user", "user_id", $user_id)["first_city"] === 1) {
            generate_new_city($user_id, $settings["map"]["map_density"], $settings["map"]["map_size"], $settings["map"]["static_area"], $settings["map"]["dynamic_area"]);

            $city_coordinates = focus_city_coordinates($user_id);

            $data = array("settings" => array("map" => array("x" => $city_coordinates["x"], "y" => $city_coordinates["y"])));
        } else {
            // Todo: what to do after user has been conquered?
        }
    }
} else {
    // Set general errors
    $errors[] = "Not logged in";
}

// Put it all in an array
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

// Decode and send
echo json_encode($response);