<?php

function new_thread($subject, $ids) {
    global $conn;

    $stmt = $conn->prepare("INSERT INTO thread (subject) VALUES (?)");
    $stmt->bind_param("s", $subject);
    $stmt->execute();

    if ($stmt) {
        $thread_id = $stmt->insert_id;
        make_thread_links($thread_id, $ids);

        return $thread_id;
    } else {
        return $conn->error;
    }
}

function make_thread_links($thread_id, $ids) {
    global $conn;

    $values_array = array();
    $time = time();

    foreach ($ids as $id) {
        $values_array[] = "(" . $thread_id . ", " . $id . ", 0, -1, " . $time . ")";
    }

    $values = implode(", ", $values_array);

    $stmt = $conn->query("INSERT INTO thread_link (thread_id, user_id, status, thread_read, last_change) VALUES " . $values);

    if ($stmt) {
        return true;
    } else {
        return $conn->error;
    }
}

function get_uids_thread($thread_id) {
    global $conn;

    $stmt = $conn->query("SELECT user_id FROM thread_link WHERE thread_id=" . $thread_id);

    if (!$stmt) {
        return $conn->error;
    } else {
        $ids = array();

        while($id = $stmt->fetch_assoc()) {
            $ids[] = $id["user_id"];
        }

        return $ids;
    }
}

function get_own_threads($user_id) {
    global $conn;

    $stmt = $conn->query("SELECT thread_id FROM thread_link WHERE user_id=" . $user_id);

    if (!$stmt) {
        return $conn->error;
    } else {
        $thread_ids = array();

        while($thread_id = $stmt->fetch_assoc()) {
            $thread_ids[] = $thread_id["thread_id"];
        }

        return $thread_ids;
    }
}

function get_threads($user_id, $not, $limit = 0) {
    global $conn;

    $not_where_array = array();

    if (count($not) > 0) {
        foreach($not as $not_thread_id) {
            $not_where_array[] = "l.thread_id!=" . $not_thread_id;
        }

        $not_where = implode($not_where_array, " AND ");
    } else {
        $not_where = "1=1";
    }


    $stmt = $conn->query("SELECT t.thread_id, t.subject FROM thread t INNER JOIN thread_link l ON t.thread_id=l.thread_id WHERE l.user_id=" . $user_id . " AND (" . $not_where . ") LIMIT " . $limit . ",10");

    if ($stmt) {
        $threads = array();

        while($thread = $stmt->fetch_assoc()) {
            $threads[$thread["thread_id"]] = $thread;
        }

        return $threads;
    } else {
        return $conn->error;
    }
}