<?php

function logged_in() {
    if (!empty($_SESSION["-int-user_id"])) {
        return get_data(array("t.username, s.description, s.permission_type"), "user", "user_id", $_SESSION["-int-user_id"], "permission", "permission_type", "permission_type", true);
    } else return false;
}