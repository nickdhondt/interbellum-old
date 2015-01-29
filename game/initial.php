<?php

// functions.php is required
require_once "../includes/functions.php";

// check if the user is logged in
$user_id = user_logged_in();
if ($user_id === false) {
    // If the user isn't logged in, he will be redirected to the login page
    header("Location: ../index.php");
    // The script terminates
    die();
}

// Counting the city's a user owns
// Based on this number, the script will decide what to do
$city_count = count_citys($user_id);

// If the user own no city's, one will be created
if ($city_count <= 0) {
    // A random cityname is generated based on these parts
    $cityname_parts = array(
        array("man", "lich", "nor", "peter", "pre", "ports", "south", "wake", "new", "glas"),
        array("ting", "chef", "sunder", "wells", "hamp", "edin", "stir", "wolver", "swan"),
        array("ville", "port", "field", "ford", "bridge", "stol", "bury", "chester", "try", "ham")
    );

    // 0 based index
    $count_part_one = count($cityname_parts[0]) - 1;
    $count_part_two = count($cityname_parts[1]) - 1;
    $count_part_three = count($cityname_parts[2]) - 1;

    // Select random name parts and the fisrt character is uppercase
    $cityname = ucfirst($cityname_parts[0][rand(0, $count_part_one)] . $cityname_parts[1][rand(0, $count_part_two)] . $cityname_parts[2][rand(0, $count_part_three)]);

    // Create city, the returned value should be the city id of the new city
    // Returns error information if the action fails
    $city_id = create_city($user_id, $cityname);
    // Create the buildings for the just created city
    // Return true or error information
    create_building($user_id, $city_id);

    // Insert the city id in a session
    // The city is needed to display the city information and to update resources, etc
    // The result is that there must always be a city id in the active session
    // If this is not the case, the user is not allowed in the game (see game files (city.php, messages.php, etc.))
    $_SESSION["city_id"] = $city_id;

    // The user will be redirected to the following page, unless there is another page specified in the querystring (see below)
    $redirect = "city.php";
} else {
    // The city id will be requested to the db
    $fields = array("id");
    $city_id = get_citys($user_id, $fields);
    // If the user owns 1 city, the user will be redirected to that city
    // If the user owns more city's, the user will be redirected to the overview page
    // A city will be set as active city, which means scripts will execute based on that id when the user is redirects (update the resources, etc.)
    if ($city_count == 1) {
        $_SESSION["city_id"] = $city_id["id"];
        $redirect = "city.php";
    } else {
        $_SESSION["city_id"] = $city_id["id"];
        $redirect = "overvc.php";
    }
}

// If the is a key in the querystring (eg. initial.php?settings), the first get key will be converted in a .php page and the user will be redirected there
// This will overwrite the previously initialised redirect variable
if (!empty($_GET)) {
    $get_keys = array_keys($_GET);
    $redirect = $get_keys[0] . ".php";
}

header("Location: " . $redirect);

?>