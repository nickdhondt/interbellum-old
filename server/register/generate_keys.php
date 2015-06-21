<?php

session_start();

date_default_timezone_set("Europe/Brussels");

require_once("../../resources/config.php");

header("Content-Type: application/json");

$legal = false;
$response = array();
$data = array();
$keys = "";
$errors = array();

if (!empty($_POST["data"])) {
    $post = json_decode($_POST["data"], true);
    if (!empty($post["keys"])){
        $data["time"] = date("j-n-Y"). " - " . date("H:i:s");
        $keys = generate_early_access_keys($post["keys"]);

        $field = array("feedback" => "keys");
        $field_legal = true;

        $field["legal"] = $field_legal;
        $field["keys"] = $keys;

        $data[] = $field;

        $legal = true;
    } else {
        $errors[] = "Not all data received";
    }
} else {
    $errors[] = "Received wrong data";
}

if ($legal === true) {
    $response = array(
        "legal" => $legal,
        "feedback" => $data
    );
} else {
    $response = array(
        "legal" => $legal,
        "errors" => $errors
    );
}

echo json_encode($response);