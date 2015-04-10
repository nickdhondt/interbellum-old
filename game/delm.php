<?php
$clearance = 0;   //The minimum required auth_level in order to access this page. NOTE: a user must be this level or higher.

require_once "../includes/functions.php";

include "includes/management.php";

include "includes/pageparts/header.php";

?>
    <h1>Uw bericht werd succesvol verwijderd</h1>
    <a href="messages.php">Ga terug naar je inbox</a>
<?php

include "includes/pageparts/footer.php";

?>