<?php

// Prepare fields for SQL SELECT
function prepare_fields_for_select($fields) {
    return implode(", ", $fields);
}

function prepare_where_clause($field, $values) {
    $where_parts = array();

    foreach ($values as $value) {
        $where_parts[] = $field . "=" . $value;
    }

    $where  = implode(" OR ", $where_parts);

    return $where;
}