<?php

// functions.php is required
// Note: this file is not in the /game/includes directory, but in the /includes directory
require_once "../includes/functions.php";

// This file checks a number of things (is the user logged in?, etc.) and fetches basic information (resourses in the current city, etc.)
include "includes/management.php";

// Empty errors array in which will contain errors the script generates
// These errors are displayed to the user later
$errors = array();

// Getting the information that determines the characteristics of the buildings
$level_info = get_building_level_info();
// Getting the next level that can be upgraded to
$next_levels = buildings_next_level($city_id);
//$population = get_total_population($city_id);

// Check if a player clicked on a level upgrade
if (isset($_POST["btn_level"])) {
    if (!empty($_POST["upgrade"])) {
        $building_to_upgrade = $_POST["upgrade"];
        // The user must upgrade a building that exists in the game
        // If the user send change/wrong data, it will be rejected
        if (legal_building($building_to_upgrade) === false) {
            $errors[] = "Er is een probleem opgetreden";
        } else {
            // Following part is under construction
            if ($buildings_data[$building_to_upgrade] < $next_levels[$building_to_upgrade]) {
                if ($level_info[$building_to_upgrade]["max_level"] < $next_levels[$building_to_upgrade]) {
                    $errors[] = "Het gebouw is al uitgebouwd";
                }
            } else {
                $errors[] = "Er is een probleem opgetreden";
            }
        }

        if (empty($errors)) {
            create_task($city_id, $building_to_upgrade, $next_levels[$building_to_upgrade], $level_info[$building_to_upgrade]["time_constant"]);
            $cost_for_upgrade = calculate_cost($level_info[$building_to_upgrade]["base_steel_cost"], $level_info[$building_to_upgrade]["base_coal_cost"], $level_info[$building_to_upgrade]["base_wood_cost"], $next_levels[$building_to_upgrade], $level_info[$building_to_upgrade]["cost_constant"]);

            $resource_fields = array(
                "steel" => $city_data["steel"] - $cost_for_upgrade["steel"],
                "coal" => $city_data["coal"] - $cost_for_upgrade["coal"],
                "wood" => $city_data["wood"] - $cost_for_upgrade["wood"]
            );
            update_city($city_id, $resource_fields);
            $next_levels[$building_to_upgrade] += 1;
        }
    }
}

$city_data = get_city_data($city_id, $city_fields);
$uncompleted_tasks = get_future_tasks($city_id);

// Inclusion of the header file
// In this file, the window title is generated, navigation is made, resources are shown, etc.
include "includes/pageparts/header.php";

?>
        <h1>Hoofdkwartier</h1>
        <div class="container">
            <?php
            foreach ($uncompleted_tasks as $task) {
                $time_to_complete = gmdate("H:i:s", $task["update_time"] - time());
                echo $building_info[$task["building"]][0] . "(Level " . $task["level"] . ") - " . $time_to_complete . "<br />";
            }
            foreach ($buildings_data as $building => $level) {
                ?>
                <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
                    <?php
                    $costs = calculate_cost($level_info[$building]["base_steel_cost"], $level_info[$building]["base_coal_cost"], $level_info[$building]["base_wood_cost"], $next_levels[$building], $level_info[$building]["cost_constant"]);
                    ?>
                    <div>
                        <?php
                        echo "<a href='" . $building_info[$building][1] . ".php'>" . $building_info[$building][0] . "</a> (level " . $level . ")";
                        if ($level + 1 < $level_info[$building]["max_level"]) {
                            echo "Staal: " . $costs["steel"] . " - Kolen: " . $costs["coal"] . " - Hout: " . $costs["wood"];

                            if (round($city_data["steel"]) < $costs["steel"] || round($city_data["coal"]) < $costs["coal"] || round($city_data["wood"]) < $costs["wood"]) {
                                echo "Er zijn niet genoeg grondstoffen";
                            } else {
                                ?>
                                <input type="hidden" name="upgrade" value="<?php echo $building ?>"/>
                                <input type="submit" value="Level <?php echo $next_levels[$building]; ?>" name="btn_level"/>
                            <?php
                            }
                        } elseif ($level + 1 > $level_info[$building]["max_level"]) {
                            echo "Volledig uitgebouwd";
                        }
                        ?>
                    </div>
                </form>
            <?php
            }
            ?>
        </div>
        <?php

include "includes/pageparts/footer.php";

?>