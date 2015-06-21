<?php

session_start();

require_once("../../resources/config.php");

header("Content-Type: application/json");

$legal = false;
$response = array();
$data = array();
$errors = array();

if (!empty($_POST["data"])) {
    $post = json_decode($_POST["data"], true);

    if (!empty($post["key"])) {
        $field = array("field" => "eak");
        $field_legal = true;

        if (!early_access_key_available($post["key"])) {
            $field_legal = false;
        }

        $field["legal"] = $field_legal;

        $data[] = $field;

        $legal = true;
    } else {
        $errors[] = "No key received";
    }
} else {
    $errors[] = "Received wrong data";
}

if ($legal === true) {
    $response = array(
        "legal" => $legal,
        "eak" => $data
    );
} else {
    $response = array(
        "legal" => $legal,
        "errors" => $errors
    );
}

echo json_encode($response);