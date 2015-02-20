<?php
// functions.php is required
// note: this file is not in the /game/includes directory, but in the /includes directory.
require_once "../includes/functions.php";

//This file checks a number of things (is the user logged in?, etc.) and fetches basic information (resourses in the current city, etc.)
include "includes/management.php";

// Inclusion of the header file
// In this file, the window title is generated, navigation is made, resources are shown, etc.
include "includes/pageparts/header.php";

//Information Gathering
    //Gather the information required for this building
    $level_info = get_building_level_info()["steel_factory"];
    $max_level = $level_info["max_level"];
    $buildings_data = get_buildings_data($city_id, "steel_factory");
    $current_steel_factory_level = $buildings_data["steel_factory"];
    $current_steel_per_hour = calculate_resource_per_hour($level_info["base_gain"], $current_steel_factory_level, $level_info["resource_constant"]);

    //Pre-define values in the if-statement to avoid undeclared variables
    $next_level_cost = array();
    $next_steel_factory_level = 0;
    $next_level_steel_per_hour = 0;

    //Check whether or not the last level has been reached.
    $maxed_out = false;
    if($current_steel_factory_level < $max_level)
    {
        $next_steel_factory_level = $current_steel_factory_level + 1;
        $next_level_steel_per_hour = calculate_resource_per_hour($level_info["base_gain"], $next_steel_factory_level, $level_info["resource_constant"]);
        $next_level_cost = calculate_cost($level_info["base_steel_cost"], $level_info["base_coal_cost"], $level_info["base_wood_cost"], $next_steel_factory_level, $level_info["cost_constant"]);
    } else {
        if($max_level > $current_steel_factory_level) $maxed_out = true;
    }
?>
    <h1>Staalfabriek</h1>
    <div class="container">
        <h2>Info</h2>
        <p>Staal is een essentieel onderdeel in gebouwen, infrastructuur, werktuigen, schepen, auto’s, machines en wapens. Het is dan ook onmisbaar bij het bouwen van uw wereldrijk. Het is echter geen natuurlijk metaal maar wel een legering van ijzer en koolstof. Wees niet gevreesd, dit fantastische chemisch wonder is binnen uw handbereik. U kan uw hard verdiende grondstoffen verwerken tot staal in de staalfabriek.</p>
        <h2>Uitbreidingopties</h2>
        Huidige level: Level <?php echo $current_steel_factory_level ?> <br/>
        <?php if($maxed_out === false) { ?>
        Volgende level: Level <?php echo $next_steel_factory_level ?> <br/> <?php } ?>
        Maximum level: Level <?php echo $max_level ?> <br/>
        <h2>Productiestatistieken</h2>
        productie staal op <em>huidige</em> level :  <?php echo $current_steel_per_hour ?> staal/uur <br/>
        <?php if($maxed_out === false) { ?>
        productie staal op <em>volgende</em> level :  <?php echo $next_level_steel_per_hour ?> staal/uur <br/> <?php } ?>
        <h2>Overhead kosten</h2>
        <?php if($maxed_out === false) { ?>
        Uitbreidingskosten naar Level <?php echo $next_steel_factory_level ?>:
        <ul class="show_resources">
            <li>Staal: <?php echo $next_level_cost["steel"] ?></li>
            <li>Steenkool: <?php echo $next_level_cost["coal"] ?></li>
            <li>Hout: <?php echo $next_level_cost["wood"] ?></li>
        </ul>
        <br/> <?php } ?>

        <!-- These lines are added by the programmer and are references to a possible future extention to the game. THIS IS NOT INCLUDED IN THE DATABASE; -->
        <?php if($maxed_out === false) { ?>
        Vereiste arbeiders op Level <?php echo $current_steel_factory_level ?>:
        <ul class="show_resources">
            <li>Arbeiders: 5</li>
            <li>Bedienden: 1</li>
        </ul>
            <br/>
        Vereiste arbeiders op Level<?php echo $next_steel_factory_level ?>:
        <ul class="show_resources">
            <li>Arbeiders: 10</li>
            <li>Bedienden: 2</li>
        </ul>
        <br/> <?php } ?>
        <!-- These lines are added by the programmer and are references to a possible future extention to the game. THIS IS NOT INCLUDED IN THE DATABASE; -->

    </div>
<?php

// Inclusion of the footer file
include "includes/pageparts/footer.php";
?>