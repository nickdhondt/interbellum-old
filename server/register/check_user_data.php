<?php

session_start();

require_once("../../resources/config.php");

header("Content-Type: application/json");

$legal = true;
$response = array();
$data = array();
$field = "";
$errors = array();

if (!empty($_POST["data"])) {
    $post = json_decode($_POST["data"], true);

    if (!empty($post["username"])) {
        $field = array("field" => "username");
        $field_legal = true;

        if (data_exists($post["username"], "user", "username")) {
            $field_legal = false;
            $field["errors"][] = "Username already exists";
        }

        $field["legal"] = $field_legal;

        $data[] = $field;

    } elseif (!empty($post["email"])) {

        $field = array("field" => "email");
        $field_legal = true;
        if (data_exists($post["email"], "user", "email")) {
            $field_legal = false;
            $field["errors"][] = "Email address already used";
        }

        $field["legal"] = $field_legal;

        $data[] = $field;
    } else {
        $errors[] = "No data received";
        $legal = false;
    }
} else {
    $errors[] = "Received wrong data";
    $legal = false;
}

if ($legal === true) {
    $response = array(
        "legal" => $legal,
        "fields" => $data
    );
} else {
    $response = array(
        "legal" => $legal,
        "errors" => $errors
    );
}

echo json_encode($response);