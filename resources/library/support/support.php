<?php

function prepare_fields_for_select($fields) {
    return implode(", ", $fields);
}