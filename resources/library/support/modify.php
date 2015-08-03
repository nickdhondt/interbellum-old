<?php

function update_data($table, $column, $data, $row, $value) {
    global $conn;
    $prepare_types = "";

    $stmt = $conn->prepare("UPDATE $table SET $column=? WHERE $row=?");
    if (is_int($data)) $prepare_types = "i";
    else if (is_double($data)) $prepare_types = "d";
    else if (is_string($data)) $prepare_types = "s";

    if (is_int($value)) $prepare_types .= "i";
    else if (is_double($value)) $prepare_types .= "d";
    else if (is_string($value)) $prepare_types .= "s";

    $stmt->bind_param($prepare_types, $data, $value);
    $stmt->execute();

    if (!$stmt){
        return $conn->error;
    } else {
        if ($conn->affected_rows >= 1) return true;
        else return false;
    }
}