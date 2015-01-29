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
            <?php
            // Under construction
                foreach ($buildings_data as $building => $level) {
                    echo "<a href='" . $building_info[$building][1] . ".php'>" . $building_info[$building][0] . "</a> (level " . $level . ")<br />";
                }
            ?>
        </div>
        <div class="smallcontainer">

        </div>
    <?php

include "includes/pageparts/footer.php";

?>