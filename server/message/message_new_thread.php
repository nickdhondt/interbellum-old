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

        if (!empty($post["recipients"]) && !empty($post["subject"]) && !empty($post["message"])){
            $time = microtime(true);

            $recipient_ids = get_user_ids($post["recipients"]);

            $other_ids = array();

            foreach($recipient_ids as $recipient_id) {
                $other_ids[] = $recipient_id["user_id"];
            }

            $ids = array($user_data["user_id"]);

            foreach($recipient_ids as $recipient_id) {
                $ids[] = $recipient_id["user_id"];
            }

            $thread_id = new_thread($post["subject"], $ids);

            send_message($thread_id, $user_data["user_id"], $post["message"], $time);
            update_stream_field($other_ids, "message_update", $time);

            $data["success"] = true;
            $data["thread"] = $thread_id;

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