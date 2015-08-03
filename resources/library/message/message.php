<?php

function send_message($thread_id, $from_id, $message, $time) {
    global $conn;

    $time = round($time, 4);

    $stmt = $conn->prepare("INSERT INTO message (from_id, thread_id, message, time) VALUES (?,?,?,?)");
    $stmt->bind_param("iisd", $from_id, $thread_id, $message, $time);
    $stmt->execute();

    if (!$stmt) {
        return $conn->error;
    } else {
        return true;
    }
}

function get_messages($thread_ids, $microtime) {
    global $conn;

    $where = prepare_where_clause("m.thread_id", $thread_ids);

    $stmt = $conn->query("SELECT m.message, m.thread_id, t.subject FROM message m INNER JOIN thread t ON t.thread_id=m.thread_id WHERE TIME>" . $microtime . " AND (" . $where . ") ORDER BY thread_id, time");

    if(!$stmt) {
        return $conn->error;
    } else {
        $messages = array();

        while($message = $stmt->fetch_assoc()) {
            if (!array_key_exists($message["thread_id"], $messages)) $messages[$message["thread_id"]]["subject"] = $message["subject"];
            $messages[$message["thread_id"]]["messages"][] = $message["message"];
        }

        return $messages;
    }
}

function get_last_messages($thread_ids) {
    global $conn;

    $threads_array = array();

    if (count($thread_ids) > 0) {
        foreach($thread_ids as $thread_id) {
            $threads_array[] = "thread_id=" . $thread_id;
        }

        $where = implode($threads_array, " OR ");

        $stmt = $conn->query("SELECT m.message, m.thread_id FROM message m INNER JOIN (SELECT thread_id, MAX(time) as time FROM message WHERE (" . $where . ") GROUP BY thread_id) s ON s.thread_id=m.thread_id AND m.time=s.time ");

        if (!$stmt) {
            return $conn->error;
        } else {
            $messages = array();

            while($message = $stmt->fetch_assoc()) {
                $messages[] = $message;
            }

            return $messages;
        }
    } else {
        return false;
    }
}