<?php

// functions.php is required
// Note: this file is not in the /game/includes directory, but in the /includes directory.
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

$storage_capacity = round(calculate_storage_capacity($building_level_info["storage"]["base_capacity"], $buildings_data["storage"], $building_level_info["storage"]["capacity_constant"]));

// Check if a player clicked on a level upgrade
if (isset($_POST["btn_level"])) {
    if (!empty($_POST["upgrade"])) {
        $building_to_upgrade = $_POST["upgrade"];
        // The user must upgrade a building that exists in the game
        // If the user send change/wrong data, it will be rejected
        if (legal_building($building_to_upgrade) === false) {
            $errors[] = "Er is een probleem opgetreden";
        } else {
            // Calculate the cost of the building the user wants to upgrade
            $requested_cost = calculate_cost($level_info[$building_to_upgrade]["base_steel_cost"], $level_info[$building_to_upgrade]["base_coal_cost"], $level_info[$building_to_upgrade]["base_wood_cost"], $next_levels[$building_to_upgrade], $level_info[$building_to_upgrade]["cost_constant"]);

            // The upgrade level should be higher than the current level
            if ($buildings_data[$building_to_upgrade] < $next_levels[$building_to_upgrade]) {
                // Check if upgrade level is lower than the max level.
                // If the upgrade level is not smaller the building has not been completed
                if ($level_info[$building_to_upgrade]["max_level"] < $next_levels[$building_to_upgrade]) {
                    $errors[] = "Het gebouw is al uitgebouwd";
                } elseif ($city_data["steel"] < $requested_cost["steel"] || $city_data["coal"] < $requested_cost["coal"] || $city_data["wood"] < $requested_cost["wood"]) {
                    // Check if the resources are available in the city
                    $errors[] = "Er zijn te weinig grondstoffen";
                }
            } else {
                $errors[] = "Er is een probleem opgetreden";
            }
        }

        if (empty($errors)) {
            // If there are no errors a new task is made
            create_task($city_id, $building_to_upgrade,
                $next_levels[$building_to_upgrade],
                $level_info[$building_to_upgrade]["time_constant"],
                $buildings_data["headquarters"]);
            $cost_for_upgrade = calculate_cost($level_info[$building_to_upgrade]["base_steel_cost"], $level_info[$building_to_upgrade]["base_coal_cost"], $level_info[$building_to_upgrade]["base_wood_cost"], $next_levels[$building_to_upgrade], $level_info[$building_to_upgrade]["cost_constant"]);

            // The cost of resources is subtracted from the available resources
            $resource_fields = array(
                "steel" => $city_data["steel"] - $cost_for_upgrade["steel"],
                "coal" => $city_data["coal"] - $cost_for_upgrade["coal"],
                "wood" => $city_data["wood"] - $cost_for_upgrade["wood"]
            );
            update_city($city_id, $resource_fields);
            // The next level is now one higher since there is a task waiting to complete
            $next_levels[$building_to_upgrade] += 1;
        }
    }
}

// Changes have been made to the city (resource update, etc)
// The new information is pulled from the database
$city_data = get_city_data($city_id, $city_fields);
$uncompleted_tasks = get_future_tasks($city_id);

// Inclusion of the header file
// In this file, the window title is generated, navigation is made, resources are shown, etc.
include "includes/pageparts/header.php";

?>
        <h1>Hoofdkwartier</h1>
            <?php
            // Display errors
            output_errors($errors);
?>
<div class="container">
    <h2>Info</h2>
    <p class="last">
        Het hoofdkwartier is het hart van uw steeds groeiende stad. Hier kunnen nieuwe gebouwen worden opgericht en bestaande gebouwen worden verbeterd.
    </p>
</div>
<?php
            // If there are task running, they should be shown
            if (!empty($uncompleted_tasks)) {
                ?>
                <div class="container">
                    <h2>Huidige bouwopdrachten</h2>
                    <table>
                        <tr>
                            <th>Gebouw</th>
                            <th>Level</th>
                            <th>Bouwtijd</th>
                        </tr>
                <?php
            }
            foreach ($uncompleted_tasks as $task) {
                // Loop through all the running tasks and put them in a table with the time of completion
                $time_to_complete = format_time($task["update_time"] - microtime(true));
                ?>
                <tr>
                        <?php
                        echo "<td>" . $building_info[$task["building"]][0] . "</td>";
                        echo "<td>" . $task["level"] . "</td>";
                        echo "<td>" . $time_to_complete . "</td>";
                        ?>
                </tr>
                    <?php
            }

            if (!empty($uncompleted_tasks)) {
                ?>
                    </table>
                </div>
                <?php
            }
?>
<div class="container">

<?php
            // Loop through all the buildings returned by the buildings_data() function
            // The returned buildings should be the building a player can build, the ones for which the requirements are met already
            foreach ($buildings_data as $building => $level) {
                ?>
                <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>" class="upgrade">
                    <?php
                    // Calculate the cost of an upgrade (needed to display the cost to the user)
                    $costs = calculate_cost($level_info[$building]["base_steel_cost"], $level_info[$building]["base_coal_cost"], $level_info[$building]["base_wood_cost"], $next_levels[$building], $level_info[$building]["cost_constant"]);
                    ?>
                    <div>
                        <ul>
                        <?php
                        // Display the building name and its level in a list
                        echo "<li><strong><a href=\"" . $building_info[$building][1] . ".php\">" . $building_info[$building][0] . "</a></strong> (level " . $level . ")</li>";
                        // If the next building level is smaller or equal to the maximum upgrade level the following information is shown. In the other case the building has been upgraded to the maximum
                        if ($next_levels[$building] <= $level_info[$building]["max_level"]) {
                            ?>
                            <?php
                            // Display the upgrade cost and the building time
                            echo "<li><strong>Kosten:</strong> Staal: " . $costs["steel"] . " - Kolen: " . $costs["coal"] . " - Hout: " . $costs["wood"] . "</li>";
                            echo "<li><strong>Bouwtijd:</strong> " . format_time(calculate_building_time($next_levels[$building], $level_info[$building]["time_constant"], $buildings_data["headquarters"])) . "</li>";
                            // If the storage capacity is smaller than the cost of an upgrade, the warehouse is too small
                            if($storage_capacity < $costs["steel"] || $storage_capacity < $costs["coal"] || $storage_capacity < $costs["wood"]) {
                                echo "<li><span class=\"upgrade_condition\">De capaciteit van je warenhuis is te laag</span></li>";
                                // If the currently available resources are lower than the cost of an upgrade, the time until the resources are available is shown
                            } elseif (ceil($city_data["steel"]) < $costs["steel"] || ceil($city_data["coal"]) < $costs["coal"] || ceil($city_data["wood"]) < $costs["wood"]) {
                                // Calculate the shortages of resources
                                $steel_shortage = $costs["steel"] - $city_data["steel"];
                                $coal_shortage = $costs["coal"] - $city_data["coal"];
                                $wood_shortage = $costs["wood"] - $city_data["wood"];

                                // Calculate the resources per hour for each resource
                                $steel_per_hour = calculate_resource_per_hour($building_level_info["steel_factory"]["base_gain"] , $buildings_data["steel_factory"], $building_level_info["steel_factory"]["resource_constant"]);
                                $coal_per_hour = calculate_resource_per_hour($building_level_info["coal_mine"]["base_gain"] , $buildings_data["coal_mine"], $building_level_info["coal_mine"]["resource_constant"]);
                                $wood_per_hour = calculate_resource_per_hour($building_level_info["woodchopper"]["base_gain"] , $buildings_data["woodchopper"], $building_level_info["woodchopper"]["resource_constant"]);

                                // By dividing the shortage by the production per hour and multiplying with 3600 we get the amount of seconds until the resources are avalable
                                $enough_steel_in_seconds = round(($steel_shortage / $steel_per_hour) * (60 * 60));
                                $enough_coal_in_seconds = round(($coal_shortage / $coal_per_hour) * (60 * 60));
                                $enough_wood_in_seconds = round(($wood_shortage / $wood_per_hour) * (60 * 60));

                                // We take the largest waiting time of the three
                                $biggest_wating_time = max($enough_steel_in_seconds, $enough_coal_in_seconds, $enough_wood_in_seconds);

                                // Display the waiting time, in a formatted form (hh:mm:ss)
                                if ($biggest_wating_time > 0) echo "<li><span class=\"upgrade_condition\">Genoeg grondstoffen in: " . format_time($biggest_wating_time) . "</span></li>";
                            } else {
                                ?>
                                <li>
                                    <input type="hidden" name="upgrade" value="<?php echo $building ?>"/>
                                    <?php
                                    // If the next level of a building is 1, the message says "Bouwen" instead of "Upgraden naar level x"
                                    if ($next_levels[$building] === 1) {
                                        ?>
                                        <input type="submit" value="Bouwen" name="btn_level"/>
                                    <?php
                                    } else {
                                        ?>
                                        <input type="submit" value="Upgraden naar level <?php echo $next_levels[$building]; ?>" name="btn_level"/>
                                        <?php
                                    }
                                        ?>
                                </li>
                            <?php
                            }
                        } else {
                            echo "<li><strong>Volledig uitgebouwd</strong></li>";
                        }
                        ?></ul>
                    </div>
                </form>
            <?php
            }
            ?>
        </div>
        <?php

        // Include the footer
include "includes/pageparts/footer.php";

?>
