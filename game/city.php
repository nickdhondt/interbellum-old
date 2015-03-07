<?php

// functions.php is required
// Note: this file is not in the /game/includes directory, but in the /includes directory
require_once "../includes/functions.php";

// This file checks a number of things (is the user logged in?, etc.) and fetches basic information (resourses in the current city, etc.)
include "includes/management.php";

// Inclusion of the header file
// In this file, the window title is generated, navigation is made, resources are shown, etc.
include "includes/pageparts/header.php";

?>
        <h1><?php echo $city_data["name"]; ?></h1>
        <div class="container">
            <table>
                <tr>
                    <th>Gebouw</th>
                    <th>Level</th>
                </tr>
            <?php
                // Loop through the buildings returned by the get_buildings_data() function
                // Display the data in a table showing the level and building name
                foreach ($buildings_data as $building => $level) {
                    ?>
                    <tr>
                    <?php
                    echo "<td><strong><a href='" . $building_info[$building][1] . ".php'>" . $building_info[$building][0] . "</a></strong></td>";
                    echo "<td>" . $level . "</td>"
                    ?>
                    </tr>
                        <?php
                }
            ?>
            </table>
        </div>
    <?php

include "includes/pageparts/footer.php";

?>