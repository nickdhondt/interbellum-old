<?php

$clearance = 0;   //The minimum required auth_level in order to access this page. NOTE: a user must be this level or higher.

require_once "../includes/functions.php";

include "includes/management.php";

include "includes/pageparts/header.php";

?>
    <h1>Uw wachtwoord werd succesvol gewijzigd</h1>
    <a href="settings.php">Ga terug naar de instellingen</a>
<?php

include "includes/pageparts/footer.php";

?>