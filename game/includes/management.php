<?php

// Check if the user is logged in
// If not, redirect to login page
$user_id = user_logged_in();
if ($user_id === false) {
    header("Location: ../index.php");
    die();
}

// There needs to be a city set as "active", this is done in "initial.php"
// User will be redirected to the login page, if there is no city set as active
// The city in the session variable is considered active
// Note: the user is not logged out, but only redirected to the login page. The user will see this pages logged in.
if (!empty($_SESSION["city_id"])) {
    $city_id = $_SESSION["city_id"];

    // This function updates the city (resources, etc...)
    manage_single_city($city_id);

    // Gathering data that is needed to display the basic information (resources, etc.)
    $city_fields = array("user_id", "name", "steel", "wood", "coal");
    $city_data = get_city_data($city_id, $city_fields);
    if ($city_data["user_id"] == $user_id) {
        // Gathering basic information about the buildings in the active city
        // Needed to display city overview, headquarters, etc.
        $fields_buildings = array("headquarters", "steel_factory", "coal_mine", "woodchopper", "storage", "kitchen");
        $buildings_data = get_buildings_data($city_id, $fields_buildings);
        $building_info = get_building_game_info();
        $building_level_info = get_building_level_info();
    } else {
        // If the active city is not owned by the user, the user will be redirected to the login page
        header("Location: ../index.php");
        die();
    }
} else {
    header("Location: ../index.php");
    die();
}

?>