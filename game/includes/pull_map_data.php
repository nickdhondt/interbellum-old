<?php

require_once "../../includes/functions.php";

$user_id = user_logged_in();
if ($user_id === false) {
    header("Location: ../index.php");
    die();
}

if (isset($_GET["x"]) && isset($_GET["y"])) {
    $x = $_GET["x"];
    $y = $_GET["y"];
    $map_data = get_map_data($x, $y, 7);

    $citys = array();

    foreach($map_data as $city) {
        if ($city["user_id"] == $user_id) {
            $city["type"] = 0;
        } else {
            $city["type"] = 2;
        }

        $citys[] = $city;
    }

    echo json_encode($citys, JSON_PRETTY_PRINT);
}