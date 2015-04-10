<?php

// This functions determines what the <title></title> should be (see below and see html_page_title() in "functions.php")
$title = html_page_title($_SERVER["SCRIPT_FILENAME"]);

?>
<!doctype html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title><?php echo $title ?></title>
    <link rel="icon" href="../img/favicon.png" type="image/png">
    <link rel="stylesheet" type="text/css" href="css/game.css">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,400italic,700,700italic' rel='stylesheet' type='text/css'>
    <script src="js/game.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
</head>
<body>
<?php

// Easter egg
$rand = rand(0, 1000);
$rand2 = rand(0, 5000);

if ($rand === 0 && $rand2 !== 0) {
    ?>
    <audio autoplay>
        <source src="http://www.w3schools.com/html/horse.ogg" type="audio/ogg">
        <source src="http://www.w3schools.com/html/horse.mp3" type="audio/mpeg">
    </audio>
<?php
}

if($rand2 === 0) {
    // Fuck ie and safari (and chrome)
    ?>
    <audio autoplay>
        <source src="http://upload.wikimedia.org/wikipedia/en/3/32/Ain%27t_It_Funny_%28Jennifer_Lopez_song_-_sample%29.ogg" type="audio/ogg">
    </audio>
<?php
}

?>
<div id="content">
    <?php

    // Get the number of unread messages
    $unread_messages = unread_messages($user_id);

    // A number greater than 9 is replaced by 9+
    if ($unread_messages < 1) {
        $unread_messages = "";
    } elseif ($unread_messages > 9) {
        $unread_messages = "9+";
    }

    ?>
    <script>

        var previousUnreadCount = null;

        function httpGetUnreadMessagesCount() {
            var httpRequest = new XMLHttpRequest();

            httpRequest.onreadystatechange = function () {
                if (httpRequest.readyState == 4 && httpRequest.status == 200) {
                    var response = httpRequest.responseText;
                    var unreadBox = document.getElementById("unread");

                    if (unreadBox == null && response > 0) {
                        var unreadDiv = document.createElement("div");
                        unreadDiv.id = "unread";
                        var unreadMessages = document.createTextNode(response);
                        unreadDiv.appendChild(unreadMessages);
                        document.getElementById("inbox").appendChild(unreadDiv);
                    } else if (unreadBox != null) {
                        unreadBox.innerHTML = response;
                    }

                    //console.log(response);
                    //console.log(previousUnreadCount);
                    if (previousUnreadCount != response && previousUnreadCount != null) {
                        if (Notification.permission === "granted") {
                            var notification = new Notification("Nieuwe NickMail", {
                                body: "Ga naar je inbox om het te lezen"
                            });
                            notification.onclick = function() {window.location = "messages.php"};
                        } else if (Notification.permission = "denied") {
                            Notification.requestPermission(function permission(){
                                if (Notification.permission === "granted") {
                                    var notification = new Notification("Nieuwe NickMail", {
                                        body: "Ga naar je inbox om het te lezen"
                                    });
                                    notification.onclick = function() {window.location = "messages.php"};
                                }
                            });
                        }
                    }
                    previousUnreadCount = response;
                }
            };

            httpRequest.open("get", "includes/pull.php?user_id=<?php echo $_SESSION["user_id"]; ?>");
            httpRequest.send();
        }

        setInterval(httpGetUnreadMessagesCount, "10000");

    </script>
    <nav>
        <ul>
            <li class="icon">
                <a href="messages.php" id="inbox">
                    <img src="img/inbox_icon.svg" alt="Inbox"/>
                    <?php
                    // If there are unread messages a div is added containing the number of unread messages
                        if ($unread_messages > 0) {
                            ?>
                    <div id="unread">
                        <?php
                            echo $unread_messages;
                            ?>
                    </div>
                        <?php
                        }

                        ?>
                </a>
            </li>
            <li>
                <a href="#">Profiel</a>
            </li>
            <li>
                <a href="basic_ranking.php">Ranglijst</a>
            </li>
            <li>
                <a href="map.php">Kaart</a>
            </li>
            <li>
                <a href="settings.php">Instellingen</a>
                <ul>
                    <li>
                        <a href="preferences.php">Voorkeuren</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="../logout.php">Uitloggen</a>
            </li>
        </ul>
    </nav>
    <div id="citycontext">
        <ul>
            <li class="href">
                <a href="city.php"><?php echo $city_data["name"]; ?></a>
            </li>
            <li>
                <strong>Staal:</strong> <?php echo ceil($city_data["steel"]) ?>
            </li>
            <li>
                <strong>Steenkool:</strong> <?php echo ceil($city_data["coal"]) ?>
            </li>
            <li>
                <strong>Hout:</strong> <?php echo ceil($city_data["wood"]) ?>
            </li>
        </ul>
    </div>
    <main>