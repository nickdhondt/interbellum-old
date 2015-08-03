<?php

session_start();

require_once("../../resources/config.php");

// Script will return JSON string
header("Content-Type: application/json");

// Parameters
$legal = true;
$logged_in = false;
$response = array();
$data = array();
$errors = array();
$user_data = logged_in();

if ($user_data) {
    $logged_in = true;

    $threads = get_threads($user_data["user_id"], array());

    if (count($threads) > 0) {
        $thread_ids = array();

        foreach($threads as $thread_data) {
            $thread_ids[] = $thread_data["thread_id"];
        }

        $messages = get_last_messages($thread_ids);

        foreach($messages as $message) {
            if (strlen($message["message"]) > 150) $threads[$message["thread_id"]]["last_message"] = substr($message["message"], 0, 150) . " ...";
            else $threads[$message["thread_id"]]["last_message"] = $message["message"];

            $data["inbox_threads"] = $threads;
        }
    } else {
        $data["inbox_threads"] = false;
    }
} else {
    $errors[] = "Not logged in";
}

// Put all data in an array
if ($legal === true) {
    $response = array(
        "legal" => $legal,
        "logged_in" => $logged_in,
        "errors" => $errors,
        "feedback" => $data
    );
} else {
    $response = array(
        "legal" => $legal,
        "logged_in" => $logged_in,
        "errors" => $errors
    );
}

echo json_encode($response);