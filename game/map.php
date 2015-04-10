<?php

require_once "../includes/functions.php";

include "includes/management.php";

include "includes/pageparts/header.php";

if (empty($_GET["x"]) && empty($_GET["y"])) {
    $fields = array("x", "y");
    $current_city_data = get_city_data($_SESSION["city_id"], $fields);

    $x = $current_city_data["x"];
    $y = $current_city_data["y"];
} else {
    $x = $_GET["x"];
    $y = $_GET["y"];
}

?>
    <h1 class="tooltip">Kaart</h1>
    <div class="info">
        <img class="info" src="img/info_icon.svg" alt="info" />
    </div>
    <div class="info-hover">
        Tips: Dubbelklik op een tegel om te centreren. Klik op een tegel om details weer te geven.
    </div>
    <div class="large_container">
        <div id="map">
            <div id="buttons">
                <div id="go_up" onclick="goUp()"></div>
                <div id="go_left" onclick="goLeft()"></div>
                <div id="land"></div>
                <div id="go_right" onclick="goRight()"></div>
                <div id="go_down" onclick="goDown()"></div>
            </div>
            <div id="loader">
                <img id="loadimg" src="img/loading_icon5.gif" alt="Laden..."/>
            </div>
        </div>
        <div id="information">
            <h2>Klik op een stad om de details weer te geven</h2>
        </div>
        <div id="clear"></div>
    </div>
    <script>
        document.addEventListener("onload", getMapData(<?php echo $x . ", " . $y ?>));
    </script>
<?php

include "includes/pageparts/footer.php";

?>