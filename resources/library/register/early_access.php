<?php

function early_access_key_available($key) {
    global $conn;

    $stmt = $conn->prepare("SELECT COUNT(*) AS early_access_keys FROM early_access_keys WHERE used=0 AND early_access_key=?");
    $stmt->bind_param("s", $key);
    $stmt->execute();

    $result = $stmt->get_result();

    $row = $result->fetch_assoc();

    if ($row["early_access_keys"] >= 1) return true;
    else return false;
}

function devalue_key($new_user_id, $key) {
    global $conn;

    $stmt = $conn->prepare("UPDATE early_access_keys SET used=1, new_user_id=? WHERE early_access_key=?");
    $stmt->bind_param("is", $new_user_id, $key);
    $stmt->execute();

    if ($stmt->affected_rows >= 1) return true;
    return false;
}

function generate_early_access_keys($amount) {
    global $conn;

    $keys = array();
    $formatted_keys = array();
    $insert_keys = array();

    for ($i = 0; $i < $amount; $i++) {
        $random = rand() . rand() . microtime(true);

        $keys[] = substr(md5($random),0 ,16);
    }

    for ($i = 0; $i < count($keys); $i++) {
        $insert_keys[] = "(" . $_SESSION["-int-user_id"] . ", '" . $keys[$i] . "')";
    }

    for ($i = 0; $i < count($keys); $i++) {
        $one = substr($keys[$i], 0, 4);
        $two = substr($keys[$i], 4, 4);
        $three = substr($keys[$i], 8, 4);
        $four = substr($keys[$i], 12, 4);

        $formatted_keys[] = $one . " - " . $two . " - " . $three . " - " . $four;
    }

    $conn->query("INSERT INTO early_access_keys (from_user_id, early_access_key) VALUES " . implode(", ", $insert_keys));

    return $formatted_keys;
}