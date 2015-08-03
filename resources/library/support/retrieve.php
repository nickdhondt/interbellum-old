<?php

// Check if data exists in the db
function data_exists($data, $table, $row) {
    global $conn;

    // Perform a mysql query
    $stmt = $conn->prepare("SELECT COUNT(*) AS data_count FROM " . $table . " WHERE " . $row . "=?");

    $stmt->bind_param("s", $data);
    $stmt->execute();

    $result = $stmt->get_result();

    $row = $result->fetch_assoc();

    // Count the rows, more or equal than 1 return true
    if ($row["data_count"] >= 1) return true;
    else return false;
}

// Get data from the db
function get_data($fields, $table, $row, $data, $second_table = "", $first_col = "", $second_col = "", $join = false) {
    global $conn;

    // Prepare the field to fit in the SQL query
    $select_fields = prepare_fields_for_select($fields);

    // Execute a mysql query
    // With or without an INNER JOIN
    if ($join === true) $stmt = $conn->prepare("SELECT " . $select_fields . " FROM " . $table . " t INNER JOIN " . $second_table . " s ON t." . $first_col . "=s." . $second_col . " WHERE " . $row . "=?");
    else $stmt = $conn->prepare("SELECT " . $select_fields . " FROM " . $table . " WHERE " . $row . "=?");
    $stmt->bind_param("s", $data);
    $stmt->execute();

    $result = $stmt->get_result();

    // Return the data
    if ($result) {
        return $result->fetch_assoc();
    } else {
        return $conn->error;
    }
}

function count_records($data, $table, $row) {
    global $conn;

    // Perform a mysql query
    $stmt = $conn->prepare("SELECT COUNT(*) AS data_count FROM " . $table . " WHERE " . $row . "=?");

    $stmt->bind_param("s", $data);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result) {
        $row = $result->fetch_assoc();

        return $row["data_count"];
    } else {
        return $conn->error;
    }
}