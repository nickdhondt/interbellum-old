<?php

session_start();

require_once("../../resources/config.php");

header("Content-Type: application/json");

$legal = false;
$response = array();
$data = array();
$field = "";
$errors = array();
$can_register = true;

if (!empty($_POST["data"])) {
    $post = json_decode($_POST["data"], true);
    if (!empty($post["key"]) || !empty($post["username"]) || !empty($post["password"]) || !empty($post["pass_repeat"]) || !empty($post["email"]) || !empty($post["terms"])){
        $data["registered"] = false;

        // General errors
        $field = array("field" => "general");
        $field_legal = true;

        if (!early_access_key_available($post["key"])) {
            $field_legal = false;
            $field["errors"][] = "Key not valid";
            $can_register = false;
        }

        if (logged_in()) {
            $field_legal = false;
            $field["errors"][] = "You must not be logged in while registering";
            $can_register = false;
        }

        $field["legal"] = $field_legal;

        $data[] = $field;

        // Username errors

        $field = array("field" => "username");
        $field_legal = true;

        if (data_exists($post["username"], "user", "username")) {
            $field_legal = false;
            $field["errors"][] = "Username already in use";
            $can_register = false;
        }

        if (strlen($post["username"]) < 3) {
            $field_legal = false;
            $field["errors"][] = "Minimum length is 3 characters";
            $can_register = false;
        }

        if (strlen($post["username"]) > 16) {
            $field_legal = false;
            $field["errors"][] = "Maximum length is 16 characters";
            $can_register = false;
        }

        if (preg_match("/[^a-zA-Z0-9\-_\.]/", $post["username"])) {
            $field_legal = false;
            $field["errors"][] = "No special characters are allowed";
            $can_register = false;
        }

        $field["legal"] = $field_legal;

        $data[] = $field;

        // Password errors

        $field = array("field" => "password");
        $field_legal = true;

        if (strlen($post["password"]) < 6) {
            $field_legal = false;
            $field["errors"][] = "Your password must at least 6 characters long";
            $can_register = false;
        }

        if (strlen($post["password"]) > 32) {
            $field_legal = false;
            $field["errors"][] = "Your password can't be longer than 32 characters";
            $can_register = false;
        }

        $field["legal"] = $field_legal;

        $data[] = $field;

        // Password repeat errors

        $field = array("field" => "pass_repeat");
        $field_legal = true;

        if ($post["password"] !== $post["pass_repeat"]) {
            $field_legal = false;
            $field["errors"][] = "Your passwords must match";
            $can_register = false;
        }

        $field["legal"] = $field_legal;

        $data[] = $field;

        // Email errors

        $field = array("field" => "email");
        $field_legal = true;

        if (strlen($post["email"]) > 254) {
            $field_legal = false;
            $field["errors"][] = "An email address can't be longer than 254 characters";
            $can_register = false;
        }
        if (!preg_match("/[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+\.[a-zA-Z]{2,4}/", $post["email"])) {
            $field_legal = false;
            $field["errors"][] = "Please enter a valid email address";
            $can_register = false;
        }
        if (data_exists($post["email"], "user", "email")) {
            $field_legal = false;
            $field["errors"][] = "Email address already used";
            $can_register = false;
        }

        $field["legal"] = $field_legal;

        $data[] = $field;

        // Terms errors

        $field = array("field" => "terms");
        $field_legal = true;

        if ($post["terms"] !== true) {
            $field_legal = false;
            $field["errors"][] = "You must agree with the terms and conditions. We own you now!";
            $can_register = false;
        }

        $field["legal"] = $field_legal;

        $data[] = $field;

        /* General: register */

        $field = array("field" => "general");
        $field_legal = true;

        if ($can_register === true) {
            if (!register_user($post["username"], $post["password"], $post["email"], $post["key"])) {
                $field_legal = false;
                $field["errors"][] = "There was a problem: user not registered";
                $can_register = false;
            } else {
                $data["registered"] = true;
            }
        }

        $field["legal"] = $field_legal;

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
        "fields" => $data
    );
} else {
    $response = array(
        "legal" => $legal,
        "errors" => $errors
    );
}

echo json_encode($response);