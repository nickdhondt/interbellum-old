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

    if (!empty($post["username"]) || !empty($post["password"])) {
        $user_data = get_data(array("t.password, t.user_id, t.username, s.description, s.permission_type"), "user", "username", $post["username"], "permission", "permission_type", "permission_type", true);

        $field = array("feedback" => "login");
        $field_legal = true;

        if (empty($user_data) || !password_verify($post["password"], $user_data["password"])) {
            $field_legal = false;
            $field["errors"][] = "Username or password is wrong";
        } else {
            $_SESSION["-int-user_id"] = $user_data["user_id"];

            $field["user_data"]["username"] = $user_data["username"];

            if ($user_data["permission_type"] <= 1) $field["user_data"]["permission"] = $user_data["description"];
        }

        $field["legal"] = $field_legal;

        $data[] = $field;

        $legal = true;
    } else {
        $errors[] = "No data received";
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