<?php

require_once "../includes/functions.php";

include "includes/management.php";

include "includes/pageparts/header.php";

?>
        <h1>Ranglijst</h1>
        <?php

        $query = mysqli_query($connection, "SELECT `username` FROM user");

        $i = 1;
        while($username = mysqli_fetch_assoc($query)) {
            echo $i.". ".$username["username"] . "<br />";
            $i++;
        }

        include "includes/pageparts/footer.php";

        ?>