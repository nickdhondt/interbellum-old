<?php
$clearance = 0;   //The minimum required auth_level in order to access this page. NOTE: a user must be this level or higher.

// functions.php is required
// Note: this file is not in the /game/includes directory, but in the /includes directory
require_once "../includes/functions.php";

// This file checks a number of things (is the user logged in?, etc.) and fetches basic information (resourses in the current city, etc.)
include "includes/management.php";

// Requiring of the village_info.php file
// In this file, the common variables for all buildings are fetched and ready to be used. NOT common variables must be calculated after the inclusion of this file.
require "includes/village_info.php";

// Inclusion of the header file
// In this file, the window title is generated, navigation is made, resources are shown, etc.
include "includes/pageparts/header.php";

?>
    <h1>Refter</h1>
    <div class="container">
        <h2>Info</h2>
        <p>De liefde van de man gaat door de maag. Waarom zou een mens dan niet investeren in zijn refter. Hier komen alle uitgeputte werkmensen genieten van hun welverdiende 15 minuten middagpauze. Toch raar hoe hard je onderdanen willen werken voor je als je hen eten heeft. Vooral de roomsoesjes en de pudding scoort hoog de laatste tijd. Heb je onmiddellijk ook een plaats gevonden om dat vervelende industriële afval in te verwerken.</p>
<?php
// Inclusion of the village_main file
// In this file, the whole file is generated based on the COMMON variables for the building.
    //The not common variabled are displayed in this file.
include "includes/pageparts/village_main.php";