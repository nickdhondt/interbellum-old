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

if (!empty($_POST["data"])) {
    // Decode the JSON data
    $post = json_decode($_POST["data"], true);
    if ($user_data) {
        $logged_in = true;

        if (!empty($post["thread"])&& !empty($post["message"])){
            // Todo: use id instead of time to link te stream event to the thread
            $time = microtime(true);

            send_message($post["thread"], $user_data["user_id"], $post["message"], $time);
            $thread_uids = get_uids_thread($post["thread"]);

            $user_ids = array();

            foreach ($thread_uids as $user_id) {
                if ($user_id != $user_data["user_id"]) $user_ids[] = $user_id;
            }

            update_stream_field($user_ids, "message_update", $time);

            $legal = true;
        } else {
            // Set general error
            $errors[] = "Not all data received";
        }
    } else {
        $errors[] = "Not logged in";
    }
} else {
    // Set general error
    $errors[] = "Received wrong data";
}

// Put all data in an array
if ($legal === true) {
    $response = array(
        "legal" => $legal,
        "logged_in" => $logged_in,
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