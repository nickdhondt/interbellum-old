<?php

session_start();

require_once("../resources/config.php");

$user_data = logged_in();

// The user must be logged in
if (!$user_data) header("Location: ../");

$city_coordinates = focus_city_coordinates($user_data["user_id"]);

?>
<!doctype html>
<html lang="en">
<head>
    <script>
        var settings = settings || {};

        settings = {
            map: {
                centerX: <?php if (!empty($city_coordinates["x"])) echo $city_coordinates["x"]; else echo floor($settings["map"]["map_size"] / 2) ?>,
                centerY: <?php if (!empty($city_coordinates["y"])) echo $city_coordinates["y"]; else echo floor($settings["map"]["map_size"] / 2) ?>,
                size: <?php echo $settings["map"]["map_size"] ?>

            }
        };
    </script>
    <meta charset="UTF-8">
    <title>Interbellum</title>
    <script src="../script/shared.js"></script>
    <script src="script/application.js"></script>
    <script src="script/game.js"></script>
    <meta name=viewport content="width=device-width, initial-scale=1">
    <link rel="icon" href="../img/interbellum_icon_32.png" type="image/png">
    <link rel="stylesheet" type="text/css" href="css/screen.css">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,400italic,700,700italic' rel='stylesheet' type='text/css'>
</head>
<body>
<div id="int_notif"></div>
<div id="time"></div>
<header>
    <nav>
        <div id="hamburger"></div>
        <ul>
            <li>
                <div id="messaging_ref" class="nav_item"></div>
            </li>
            <li>
                <div id="notifications_ref" class="nav_item"></div>
            </li>
            <li>
                <div id="settings_ref" class="nav_item"></div>
            </li>
            <li>
                <div id="fullscreen_ref" class="nav_item"></div>
            </li>
        </ul>
        <div class="cf"></div>
    </nav>
</header>
<main>
    <div id="map">
        <div id="map_container"></div>
    </div>
    <div id="city">
        <h1>City</h1>
    </div>
    <div id="federation">
        <h1>Federation</h1>
    </div>
    <div id="messaging" class="half_page">
        <div>
            <h1>Messaging</h1>
            <div>
                <div></div>
            </div>
        </div>
        <div>
            <div class="button wide_button">
                <div id="new_message_ref">
                    New message
                </div>
            </div>
        </div>
    </div>
    <div id="new_message" class="half_page">
        <div>
            <h1>New message</h1>
            <div>
                <div></div>
            </div>
        </div>
        <div>
            <form>
                <ul>
                    <li id="new_recipient_box">
                        <div>
                            <label for="recipient_new">To:</label>
                            <input type="text" name="txt_recipient_new" id="recipient_new" placeholder="Username"/>
                        </div>
                        <div id="recipient_suggestions" class="type_assistant"></div>
                        <div class="form_notice"></div>
                    </li>
                    <li>
                        <input type="text" name="txt_subject" placeholder="Subject" class="wide_field"/>
                        <div class="form_notice"></div>
                    </li>
                    <li>
                        <textarea name="txt_new_message" placeholder="Message"></textarea>
                        <div class="form_notice"></div>
                    </li>
                    <li>
                        <input type="button" name="btn_new_message" value="Send"/>
                    </li>
                </ul>
            </form>
        </div>
    </div>
    <div id="notifications" class="half_page">
        <div>
            <h1>Notifications</h1>
            <div>
                <div></div>
            </div>
        </div>
        <div>Nothing here yet!</div>
    </div>
    <div id="settings" class="half_page">
        <div>
            <h1>Settings</h1>
            <div>
                <div></div>
            </div>
        </div>
        <div>Nothing here yet!</div>
    </div>
    <div id="chat_bar"></div>
</main>
<footer>
    <nav>
        <ul>
            <li>
                <div id="map_ref">Map</div>
            </li>
            <li>
                <div id="city_ref">City</div>
            </li>
            <li>
                <div id="federation_ref">Fed</div>
            </li>
            <li>
                <div></div>
            </li>
        </ul>
    </nav>
</footer>
<div id="loading">
    <div>
        <div>Loading Interbellum...</div>
        <div class="loader" id="initial_loader" data-progress="0"><div class="progress"></div></div>
    </div>
</div>
<div id="jef"><div></div></div>
</body>
</html>