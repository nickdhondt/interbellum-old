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
    <h1>Houthakker</h1>
    <div class="container">
        <h2>Info</h2>
        <p>Wat maakt een mens nu gelukkiger dan een 100 jaar oude plant neerhakken? Na de broeikasgassen van de staalfabriek en de roet van de koolmijn maakt het toch zoveel niet meer uit. Met zijn gespierde armen en zijn 10 kilo wegende bijl gaat de houthakker aan de slag om jouw dorp van de fijnste houtjes te voorzien dat hij maar vinden kan. Echte topmensen die houthakkers.</p>
<?php
// Inclusion of the village_main file
// In this file, the whole file is generated based on the COMMON variables for the building.
    //The not common variabled are displayed in this file.
include "includes/pageparts/village_main.php";