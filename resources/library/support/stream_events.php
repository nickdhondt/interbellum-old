<?php

function new_messages($user_id, $time, &$loop_time) {
    global $conn;

    $loop_time = microtime(true);

    $stmt = $conn->query("SELECT message_update FROM user WHERE user_id=" . $user_id);

    if (!$stmt) {
        return $conn->error;
    } else {
        $message_update = $stmt->fetch_assoc();

        if ($message_update["message_update"] > $time) return array(true, "data" => $message_update["message_update"], "time" => $time);
        return array(false, "data" => $message_update["message_update"], "time" => $time);
    }
}