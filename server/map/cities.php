<?php

$rand =  rand(0, 99);

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
$horizontal = 1;
$vertical = 1;
$user_id = $_SESSION["-int-user_id"];

if (!empty($_GET )) {
    if (logged_in()){
        $logged_in = true;

        if (isset($_GET["x"]) || isset($_GET["y"])) {
            $legal = true;
            $x = $_GET["x"];
            $y = $_GET["y"];
            if (isset($_GET["h"])) $horizontal = $_GET["h"];
            if (isset($_GET["v"])) $vertical = $_GET["v"];

            $x_min = $x - floor((($horizontal * $settings["map"]["coordinates_per_tile"]) - 1) / 2);
            $y_min = $y - floor((($vertical * $settings["map"]["coordinates_per_tile"]) - 1) / 2);
            $x_max = $x + floor(($horizontal * $settings["map"]["coordinates_per_tile"]) / 2);
            $y_max = $y + floor(($vertical * $settings["map"]["coordinates_per_tile"]) / 2);

            $cities = get_cities($x_min, $x_max, $y_min, $y_max);

            $offset = floor($settings["map"]["coordinates_per_tile"] / 2);

            for ($ei = 0; $ei <  $vertical; $ei++){
                for ($ieks = 0; $ieks < $horizontal; $ieks++) {
                    $tile = array();

                    $x_ray = $x_min + ($ieks * $settings["map"]["coordinates_per_tile"]) + floor($settings["map"]["coordinates_per_tile"] / 2);
                    $yankee = $y_min + ($ei * $settings["map"]["coordinates_per_tile"]) + floor($settings["map"]["coordinates_per_tile"] / 2);

                    foreach($cities as $city) {
                        if ($city["x"] >= $x_ray - $offset && $city["x"] <= $x_ray + $offset && $city["y"] >= $yankee - $offset && $city["y"] <= $yankee + $offset) {
                            $type = 6;
                            if ($user_id === intval($city["user_id"])) $type = 1;
                            $tile[] = array("x" => intval($city["x"]), "y" => intval($city["y"]), "city" => $city["city_name"], "points" => $city["points"], "owner" => $city["username"], "type" => $type);
                        }
                    }

                    $tile["x"] = $x_ray;
                    $tile["y"] = $yankee;
                    $data[] = $tile;
                }
            }
        } else {
            //Set general errors
            $errors[] = "Not all data received";
        }
    } else {
        // Set general errors
        $errors[] = "Not logged in";
    }
} else {
    // Set general errors
    $errors[] = "Received wrong data";
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
//print_r($data);
// Decode and send
echo json_encode($response);