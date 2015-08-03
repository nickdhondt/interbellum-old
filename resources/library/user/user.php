<?php

// Checks if a user is logged in
function logged_in() {
    if (!empty($_SESSION["-int-user_id"])) {
        // If user is logged in, return basic user data
        return get_data(array("t.user_id, t.username, s.description, s.permission_type"), "user", "user_id", $_SESSION["-int-user_id"], "permission", "permission_type", "permission_type", true);
    } else return false;
}

function assist_usernames($username_proposal, $exclude_username) {
    global $conn;

    // Todo: escape wildcard char
    //$username_proposal = $conn->real_escape_string($username_proposal);

    $stmt = $conn->prepare("SELECT username FROM user WHERE username LIKE CONCAT('%', ?, '%') AND username <> '" . $exclude_username . "' ORDER BY username ASC LIMIT 7");
    $stmt->bind_param("s", $username_proposal);
    $stmt->execute();
    $result = $stmt->get_result();

    $usernames = array();

    if ($stmt) {
        while($username = $result->fetch_assoc()) {
            $usernames[] = $username["username"];
        }

        return $usernames;
    } else {
        return $conn->errno;
    }
}

function get_user_ids($usernames) {
    global $conn;

    $errors = false;

    $stmt = $conn->prepare("SELECT user_id, username FROM user WHERE username=?");

    $results = array();

    foreach ($usernames as $username) {
        $stmt->bind_param("s", $username);
        $stmt->execute();

        if ($stmt) {

            $result = $stmt->get_result();
            $results[] = $result->fetch_assoc();
        } else {
            $errors = true;
        }
    }

    if (!$errors) return $results;
    else return $conn->errno;
}

function update_stream_field($user_ids, $field, $time) {
    global $conn;

    $where_parts = array();

    foreach ($user_ids as $user_id) {
        $where_parts[] = "user_id=" . $user_id;
    }

    $where  = implode(" OR ", $where_parts);

    $time = round($time, 4);

    $stmt = $conn->query("UPDATE user SET " . $field . "=" . $time . " WHERE " . $where);

    if (!$stmt) {
        return $conn->error;
    } else {
        return true;
    }
}