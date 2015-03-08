<?php

require_once "../includes/functions.php";

include "includes/management.php";

include "includes/pageparts/header.php";

?>
        <h1 class="tooltip">Ranglijst</h1>
    <div class="info">
        <img class="info" src="img/info_icon.svg" alt="info" />
    </div>
    <div class="info-hover">
        Deze ranglijst is tijdelijk. De volgorde klopt niet. Deze ranglijst dient momenteel enkel als referentie voor de aanwezige spelers op deze server.
    </div>
<div class="container">
        <?php

        $query = mysqli_query($connection, "SELECT username FROM user ORDER BY username ASC");

        $i = 1;
        while($username = mysqli_fetch_assoc($query)) {
            echo $i.". ".$username["username"] . "<br />";
            $i++;
        }
?>
</div>
    <?php
        include "includes/pageparts/footer.php";

        ?>