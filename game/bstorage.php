<?php

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
    <h1>Warenhuis</h1>
    <div class="container">
        <h2>Info</h2>
        <p>Drie verdiepingen hoog. Moet wel genoeg zijn. In de namiddagzon sta je na te genieten van de vers gebouwde opslagplaats. In je hoofd maak je al aantekeningen hoe je die tamme rotboeren harder kan laten werken om dit praaltje van een bouwwerk in een oogwenk vol te krijgen. En door de volle opslagplaats kon je natuurlijk meer belansting heffen. Nee, dat slaat inderdaad helemaal nergens op, maar die boeren zijn toch te dom om zich dat te realiseren.</p>
<?php
// Inclusion of the village_main file
// In this file, the whole file is generated based on the COMMON variables for the building.
    //The not common variabled are displayed in this file.
include "includes/pageparts/village_main.php";