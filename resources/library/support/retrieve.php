<?php

function data_exists($data, $table, $row) {
    global $conn;

    $stmt = $conn->prepare("SELECT COUNT(*) AS data_count FROM " . $table . " WHERE " . $row . "=?");

    $stmt->bind_param("s", $data);
    $stmt->execute();

    $result = $stmt->get_result();

    $row = $result->fetch_assoc();

    if ($row["data_count"] >= 1) return true;
    else return false;
}

function get_data($fields, $table, $row, $data, $second_table = "", $first_col = "", $second_col = "", $join = false) {
    global $conn;

    $select_fields = prepare_fields_for_select($fields);

    if ($join === true) $stmt = $conn->prepare("SELECT " . $select_fields . " FROM " . $table . " t INNER JOIN " . $second_table . " s ON t." . $first_col . "=s." . $second_col . " WHERE " . $row . "=?");
    else $stmt = $conn->prepare("SELECT " . $select_fields . " FROM " . $table . " WHERE " . $row . "=?");
    $stmt->bind_param("s", $data);
    $stmt->execute();

    $result = $stmt->get_result();

    $data = array();

    if ($result) {
        return $result->fetch_assoc();
    } else {
        return $conn->error;
    }
}