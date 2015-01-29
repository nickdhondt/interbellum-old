<?php
/*
include "../../includes/functions.php";
$session_user_id = $_SESSION["user_id"];

if (!empty($_GET["user_id"])) {
    $user_id = $_GET["user_id"];
    if ($user_id === $session_user_id) {
        $initial_messages = unread_messages($user_id);
        $unread_messages = $initial_messages;

        $loops = 0;

        while ($initial_messages === $unread_messages) {
            usleep(950000);
            $unread_messages = unread_messages($user_id);
            if ($loops >= 10) {
                break;
            }
            $loops++;
        }

        if ($unread_messages >= "1") {
            echo " (" . $unread_messages . ")";
        }
    }
} else {
    echo "error";
}

*/

// Temporary
include "../../includes/functions.php";
$session_user_id = $_SESSION["user_id"];

if (!empty($_GET["user_id"])) {
    $user_id = $_GET["user_id"];
    if ($user_id === $session_user_id) {
        $unread_messages = unread_messages($user_id);
        if ($unread_messages >= "1" && $unread_messages <= "9") {
            echo $unread_messages;
        } elseif ($unread_messages > 9) {
            echo "9+";
        }
    }
} else {
    echo "error";
}
