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
    <h1>Staalfabriek</h1>
    <div class="container">
        <h2>Info</h2>
        <p>Staal is een essentieel onderdeel in gebouwen, infrastructuur, werktuigen, schepen, auto’s, machines en wapens. Het is dan ook onmisbaar bij het bouwen van uw wereldrijk. Het is echter geen natuurlijk metaal maar wel een legering van ijzer en koolstof. Wees niet gevreesd, dit fantastische chemisch wonder is binnen uw handbereik. U kan uw hard verdiende grondstoffen verwerken tot staal in de staalfabriek.</p>

<?php
// Inclusion of the village_main file
// In this file, the whole file is generated based on the COMMON variables for the building.
    //The not common variabled are displayed in this file.
include "includes/pageparts/village_main.php";