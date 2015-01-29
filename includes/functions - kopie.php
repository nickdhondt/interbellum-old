<?php

// See the API .pdf for more detailed information

session_start();
include "database/connect.php";

// Setting the timezeone to GMT+1
date_default_timezone_set("Europe/Brussels");

/* Gamedata */

// This function will return gamedata needed to create hyperlinks
// The first string inside the second array is supposed to be the tekst on which to click
// The second string is the name of the page (e.g. bhead.php)
function get_building_game_info() {
    return array(
        "headquarters" => array("Hoofdkwartier", "bhead"),
        "steel_factory" => array("Staalfabriek", "bsteel"),
        "coal_mine" => array("Kolenmijn", "bcoal"),
        "woodchopper" => array("Houthakker", "bwood"),
        "storage" => array("Warenhuis", "bstorage"),
        "kitchen" => array("Refter", "bkitchen")
    );
}

// This function returns data that determines the characteristics of a building
function get_building_level_info() {
    return array(
        "steel_factory" => array(
            // Maximum level a building can reach
            "max_level" => 20,
            // A constant which determines the cost of the building
            // The cost is calculated using a formula
            // This formula need 'base_recourse_cost' and 'cost_constant' (see calculate_cost())
            "base_steel_cost" => 42,
            "base_coal_cost" => 45,
            "base_wood_cost" => 31,
            // base_time determines how long it takes to upgrade/build a building
            // This is calculated using a formula
            // The formula needs 'base_time' and 'time_constant' (see calculate_building_time())
            "base_time" => 45,
            // This constant determines how quickly a building makes recourses
            // This is calculate using a formula, which needs 'base_gain' and 'recourse_constant' to calculate the recourses per hour (see calculate_recourse_per_hour())
            "base_gain" => 20,
            "time_constant" => 9,
            "cost_constant" => 2,
            "resource_constant" => 6
        ),
        "coal_mine" => array(
            "max_level" => 20,
            "base_steel_cost" => 45,
            "base_coal_cost" => 41,
            "base_wood_cost" => 32,
            "base_time" => 42,
            "base_gain" => 20,
            "time_constant" => 8.5,
            "cost_constant" => 2,
            "resource_constant" => 6
        ),
        "woodchopper" => array(
            "max_level" => 20,
            "base_steel_cost" => 45,
            "base_coal_cost" => 42,
            "base_wood_cost" => 31,
            "base_time" => 42,
            "base_gain" => 20,
            "time_constant" => 8.5,
            "cost_constant" => 1.8,
            "resource_constant" => 6
        ),
        "storage" => array(
            "max_level" => 20,
            "base_steel_cost" => 39,
            "base_coal_cost" => 37,
            "base_wood_cost" => 45,
            "base_time" => 36,
            // This constant is needed to calculate the storage capacity
            // This calculation is based on a formula which requires 'base_capacity' and 'capacity_constant'. (see calculate_storage_capacity())
            "base_capacity" => 20,
            "time_constant" => 6,
            "cost_constant" => 1.9,
            "capacity_constant" => 50
        ),
        "kitchen" => array(
            "max_level" => 25,
            "base_steel_cost" => 34,
            "base_coal_cost" => 39,
            "base_wood_cost" => 29,
            "base_time" => 34,
            // This constant is needed to calculate the population capacity
            // This calculation is based on a formula which requires 'base_capacity' and 'capacity_constant'. (see calculate_population_storage())
            "base_capacity" => 120,
            "time_constant" => 6,
            "cost_constant" => 1.8,
            "capacity_constant" => 3
        ),
        "headquarters" => array(
            "max_level" => 25,
            "base_steel_cost" => 42,
            "base_coal_cost" => 40,
            "base_wood_cost" => 50,
            "base_time" => 50,
            "cost_constant" => 2.5,
            "time_constant" => 7,
            // This constant determines how big the improvement in building speed is compared to the previous level
            // This is calculated based on the 'building_speed_constant'. See calculate_speed_improvement())
            "building_speed_constant" => 0.97
        )
    );
}

// Calculates the building speed improvement when upgrading buildings
// The returned double is between 0 and 1, so by multiplying the building time (in seconds) with this coefficient you get a shorter building time
function calculate_speed_improvement($level, $constant, $current_building_seconds) {
    return (pow($constant, $level) * $current_building_seconds);
}

// Calculates the total population that is allowed in a city
function calculate_population_storage($base, $level, $constant) {
    return ($level + 4 * $level + 4 * $level + 4) + $base / $constant;
}

// Calculates the costs of all recourses and returns an array with the calculated values
function calculate_cost($steel, $coal, $wood, $level, $cost_constant) {
    $cost = array();
    $cost["steel"] = round(($level * $level * $level) * $cost_constant) + $steel;
    $cost["coal"] = round(($level * $level * $level) * $cost_constant) + $coal;
    $cost["wood"] = round(($level * $level * $level) * $cost_constant) + $wood;

    return $cost;
}

function output_errors($errors) {
    if (!empty($errors)) {
        echo "<ul>";
        foreach($errors as $error) {
            echo "<li>" . $error . "</li>";
        }
        echo "</ul>";
    }
}

/* User */

function user_exists($username) {
    global $connection;
    $sql = mysqli_query($connection, "SELECT id FROM user WHERE username='$username'");

    if (!$sql) {
        return mysqli_error($connection);
    } else {
        if (mysqli_num_rows($sql) >= 1) {
            $user_id = mysqli_fetch_assoc($sql);
            return $user_id["id"];
        } else {
            return false;
        }
    }
}

function user_data($user_id, $fields) {
    global $connection;
    if (!empty ($fields)) {
        $sql_fields = implode(", ", $fields);
    } else {
        $sql_fields = "*";
    }

    $sql = mysqli_query($connection, "SELECT $sql_fields FROM user WHERE id='$user_id'");

    if (!$sql) {
        return mysqli_error($connection);
    } else {
        return mysqli_fetch_assoc($sql);
    }
}

function update_user($user_id, $fields) {
    global $connection;

    $values = prepare_fields($fields);

    if (mysqli_query($connection, "UPDATE user SET $values WHERE id=$user_id")) {
        return true;
    } else {
        return mysqli_error($connection);
    }
}

function user_logged_in () {
    if (!empty($_SESSION["user_id"])) {
        return $_SESSION["user_id"];
    } elseif (!empty($_COOKIE["remember_me_id"]) && !empty($_COOKIE["remember_me_hash"])) {
        $cookie_id = $_COOKIE["remember_me_id"];
        $cookie_hash = $_COOKIE["remember_me_hash"];
        $session_hash = get_session_hash($cookie_id);
        if ($session_hash !== false && $session_hash === $cookie_hash) {
            $session_fields = array("user_id");
            $session_data = get_session_data($cookie_id, $session_fields);
            $_SESSION["user_id"] = $session_data["user_id"];
            return $session_data["user_id"];
        } else {
            return false;
        }
    } else {
        return false;
    }
}

/* Session */

function get_session_data($session_id, $fields) {
    global $connection;

    if (empty($fields)) {
        $sql_fields = "*";
    } else {
        $sql_fields = implode(", ", $fields);
    }

    $sql = mysqli_query($connection, "SELECT $sql_fields FROM session WHERE id=$session_id");

    if (!$sql) {
        return mysqli_error($connection);
    } else {
        return mysqli_fetch_assoc($sql);
    }
}

function get_session_hash($session_id) {
    global $connection;

    $sql = mysqli_query($connection, "SELECT remember_hash FROM session WHERE id=$session_id");

    if (!$sql) {
        return mysqli_error($connection);
    } else {
        if (mysqli_num_rows($sql) >= 1) {
            return true;
        } else {
            return false;
        }
    }
}

/* Thread */

function make_thread($thread) {
    global $connection;
    //$datetime = date ("Y-m-d H:i:s");
    if (mysqli_query($connection, "INSERT INTO thread (thr_name) VALUES ('$thread')")) {
        return mysqli_insert_id($connection);
    } else {
        return false;
    }
}

function make_message($thr_id, $user_id, $body) {
    global $connection;
    $datetime = date("Y-m-d H:i:s");
    if (mysqli_query($connection, "INSERT INTO message (thr_id, user_id, senddate, body) VALUES ('$thr_id', '$user_id', '$datetime', '$body')")) {
        return true;
    } else {
        return mysqli_error($connection);
    }
}

function make_thread_breadcrumbs($thr_id, $user_id, $status=0) {
    global $connection;
    $time = time();
    if (mysqli_query($connection, "INSERT INTO thr_recipient (thr_id, user_id, last_mod, status) VALUES ('$thr_id', '$user_id', $time, $status)")) {
        echo mysqli_error($connection);
        return true;
    } else {
        return false;
    }
}

function get_thread_breadcrumbs($user_id) {
    global $connection;
    $all_threads = array();

$sql = mysqli_query($connection, "SELECT thr_id, status, last_mod FROM thr_recipient WHERE user_id='$user_id' AND (status=0 OR status=1) ORDER BY last_mod DESC") or die(mysqli_error($connection));
    while ($thread_id = mysqli_fetch_assoc($sql)) {
        $all_threads[] = array (
            "thr_id" => $thread_id["thr_id"],
            "status" => $thread_id["status"],
            "last_mod" => $thread_id["last_mod"]);
    }

    return $all_threads;
}

function get_thread_data($thr_id) {
    global $connection;
    $sql = mysqli_query($connection, "SELECT thr_name FROM thread WHERE id='$thr_id'") or die(mysqli_error($connection));
    return mysqli_fetch_assoc($sql);
}

function get_message_data($thread_id, $page) {
    global $connection;

    $start = $page * 15;

    $all_messages_data = array();
    $sql = mysqli_query($connection, "SELECT user_id, senddate, body FROM message WHERE thr_id='$thread_id' ORDER BY senddate DESC LIMIT $start,15") or die(mysqli_error($connection));

    while($message_data = mysqli_fetch_assoc($sql)) {
        $all_messages_data[] = $message_data;
    }
    return $all_messages_data;
}

function make_link_from_url($text){

    // Look for these anomalies
    $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,10}(\/\S*|:\S*)?/";

    // Store the matches
    preg_match_all ($reg_exUrl, $text, $matches);

    $usedPatterns = array();

    foreach ($matches[0] as $pattern){

        if (!array_key_exists ($pattern, $usedPatterns)){

            $usedPatterns[$pattern]=true;
            $text = str_replace ($pattern, "<strong><a href=\"" . $pattern . "\" target=\"_blank\" rel=\"nofollow\">" . $pattern . "</a></strong>", $text);

        }
    }

    return $text;

}

function format_message($body, $state = "COMPLETE") {
    $body_formatted = nl2br(make_link_from_url($body));
    $body_formatted_simple = $body;
    if ($state === "PREVIEW") {
        if (strlen($body) > 75) {
            return substr($body_formatted_simple, 0, 100) . "...";
        } else {
            return $body;
        }
    } elseif ($state === "COMPLETE") {
        return $body_formatted;
    }
}

function get_user_id_from_breadcrumbs($thr_id) {
    global $connection;

    $all_authorized_ids = array();
    $sql = mysqli_query($connection, "SELECT user_id FROM thr_recipient WHERE thr_id='$thr_id'") or die(mysqli_error($connection));

    while ($user_id = mysqli_fetch_assoc($sql)) {
        $all_authorized_ids[] = $user_id;
    }

    return $all_authorized_ids;
}

function sanitize ($text) {
    return htmlspecialchars($text, ENT_QUOTES);
}

function update_breadcrumb($thr_id, $user_id, $fields) {
    global $connection;

    $values = prepare_fields($fields);

    if (mysqli_query($connection, "UPDATE thr_recipient SET $values WHERE user_id=$user_id && thr_id=$thr_id")) {
        return true;
    } else {
        return mysqli_error($connection);
    }
}

function prepare_fields ($fields) {
    $single_values = array();

    foreach ($fields as $column => $value) {
        if (gettype($value) === "integer" || gettype($column) === "double") {
            $single_values[] = $column . "=" . $value;
        } else {
            $single_values[] = $column . "='" . $value . "'";
        }
    }

    return implode (", ", $single_values);
}

function unread_messages ($user_id) {
    $thr_breadcrumbs = get_thread_breadcrumbs($user_id);
    $unread_messages = "0";

    foreach ($thr_breadcrumbs as $thr_breadcrumb) {
        if ($thr_breadcrumb["status"] === "0") {
            $unread_messages++;
        }
    }
    count ($thr_breadcrumbs);

    if ($unread_messages <= "9") {
        return $unread_messages;
    } else {
        return "9+";
    }
}

function get_last_message ($thr_id) {
    global $connection;

    $sql = mysqli_query($connection, "SELECT user_id, body FROM message WHERE thr_id=$thr_id ORDER BY senddate DESC LIMIT 1");

    if (!$sql) {
        return mysqli_error($connection);
    } else {
        return mysqli_fetch_assoc($sql);
    }
}

function format_elapsed_seconds ($seconds) {
    if ($seconds <= 3540 && $seconds >= 0) {
        return ceil($seconds/60) . "m";
    } elseif ($seconds > 3540 && $seconds <= 84599) {
        return round($seconds/3600) . "h";
    } elseif ($seconds > 84599 && $seconds <= 2678399) {
        return round($seconds/86400) . "d";
    } elseif ($seconds > 2678399) {
        return "+31d";
    }
}

function thread_recipients ($thr_id) {
    global $connection;

    $sql = mysqli_query($connection, "SELECT user_id FROM thr_recipient WHERE thr_id=$thr_id AND (status=0 OR status=1)");

    if (!$sql) {
        return mysqli_error($connection);
    } else {
        $all_recipients_id = array();
        while($recipient_id = mysqli_fetch_assoc($sql)) {
            $all_recipients_id[] = $recipient_id["user_id"];
        }
        return $all_recipients_id;
    }
}

function make_session($user_id, $remember_hash, $user_agent) {
    global $connection;

    if (mysqli_query($connection, "INSERT INTO session (user_id, remember_hash, user_agent) VALUES ($user_id, '$remember_hash', '$user_agent')")) {
        return mysqli_insert_id($connection);
    } else {
        return mysqli_error($connection);
    }

}

function update_session($session_id, $fields) {
    global $connection;

    $values = prepare_fields($fields);

    if (mysqli_query($connection, "UPDATE session SET $values WHERE id=$session_id")) {
        return true;
    } else {
        return mysqli_error($connection);
    }

}

function delete_breadcrumb($thr_id, $user_id) {
    global $connection;

    $active_members = count(thread_recipients($thr_id));

    if ($active_members < 2) {
        if(mysqli_query($connection, "DELETE FROM thr_recipient WHERE thr_id=$thr_id")) {
            return true;
        } else {
            return mysqli_error($connection);
        }
    } else {
        $fields = array("status" => 2);
        update_breadcrumb($thr_id, $user_id, $fields);
        return true;
    }
}

function count_all_messages($thr_id) {
    global $connection;

    $sql = mysqli_query($connection, "SELECT COUNT(id) FROM message WHERE thr_id=$thr_id");

    if (!$sql) {
        return mysqli_error($connection);
    } else {
        $count_messages_array = mysqli_fetch_assoc($sql);
        return $count_messages_array["COUNT(id)"];
    }
}

function display_pages($page, $items_count, $items_per_page) {
    $pages = ceil($items_count / $items_per_page);
    $output = array();

    for ($i = 0; $i < $pages; $i++) {
            $output[] = $i + 1;
    }
    return $output;
}

function count_citys($user_id) {
    global $connection;

    $sql = mysqli_query($connection, "SELECT COUNT(id) FROM city WHERE user_id=$user_id");

    if (!$sql) {
        return mysqli_error($connection);
    } else {
        $count_array = mysqli_fetch_assoc($sql);
        return $count_array["COUNT(id)"];
    }
}

function create_city($user_id, $city_name) {
    global $connection;

    $x = rand(0, 20);
    $y = rand(0, 20);

    while (coordinates_exist($x, $y) != false) {
        $x = rand(0, 20);
        $y = rand(0, 20);
    }

    if (mysqli_query($connection, "INSERT INTO city (user_id, x, y, steel, wood, coal, name) VALUES ($user_id, $x, $y, 530.0, 530.0, 530.0, '$city_name')")) {
        return mysqli_insert_id($connection);
    } else {
        return mysqli_error($connection);
    }
}

function coordinates_exist($x, $y) {
    global $connection;

    $sql = mysqli_query($connection, "SELECT id FROM city WHERE x=$x AND y=$y LIMIT 1");

    $city_id = mysqli_fetch_assoc($sql);

    if (!empty($city_id)) {
        return $city_id["id"];
    } else {
        return false;
    }
}

function create_building($user_id, $city_id) {
    global $connection;

    if (mysqli_query($connection, "INSERT INTO building (user_id, city_id, headquarters, steel_factory, coal_mine, woodchopper, storage, kitchen) VALUES ($user_id, $city_id, 1, 0, 0 ,0 ,1 ,1)")) {
        return true;
    } else {
        return mysqli_error($connection);
    }
}

function get_citys($user_id, $fields) {
    global $connection;

    if (!empty ($fields)) {
        $sql_fields = implode(", ", $fields);
    } else {
        $sql_fields = "*";
    }

    $sql = mysqli_query($connection, "SELECT $sql_fields FROM city WHERE user_id=$user_id");

    if (!$sql) {
        return mysqli_error($connection);
    } else {
        return mysqli_fetch_assoc($sql);
    }
}

function get_city_data($city_id, $fields) {
    global $connection;

    if (!empty ($fields)) {
        $sql_fields = implode(", ", $fields);
    } else {
        $sql_fields = "*";
    }

    $sql = mysqli_query($connection, "SELECT $sql_fields FROM city WHERE id=$city_id");

    if (!$sql) {
        return mysqli_error($connection);
    } else {
        return mysqli_fetch_assoc($sql);
    }
}

function get_buildings_data($city_id, $fields) {
    global $connection;

    if (!empty ($fields)) {
        $sql_fields = implode(", ", $fields);
    } else {
        $sql_fields = "*";
    }

    $sql = mysqli_query($connection, "SELECT $sql_fields FROM building WHERE city_id=$city_id");

    if (!$sql) {
        return mysqli_error($connection);
    } else {
        return mysqli_fetch_assoc($sql);
    }
}

function update_resources($city_id) {
    global $connection;

    $city_fields  = array("update_time", "steel", "coal", "wood");
    $building_fields = array("steel_factory", "coal_mine", "woodchopper", "storage");

    $city_data = get_city_data($city_id, $city_fields);
    $task_data = get_resource_task_timings($city_id, false);
    $building_info = get_building_level_info();
    $buildings_data = get_buildings_data($city_id, $building_fields);

    $steel_per_hour_array = array();
    $coal_per_hour_array = array();
    $wood_per_hour_array = array();
    $steel_per_hour = 0;
    $coal_per_hour = 0;
    $wood_per_hour = 0;

    $deserved_steel = $city_data["steel"];
    $deserved_coal = $city_data["coal"];
    $deserved_wood = $city_data["wood"];

    $storage_capacity = round(calculate_storage_capacity($building_info["storage"]["base_capacity"], $buildings_data["storage"], $building_info["storage"]["capacity_constant"]));

    if (!empty($task_data["storage"])) {
        $storage_upgrades = count($task_data["storage"]);
    } else {
        $storage_upgrades = 0;
    }

    if ($storage_upgrades === 0) {
        if ($deserved_steel <= $storage_capacity) {
            if (!empty($task_data["steel_factory"])) {
                $steel_factory_upgrades = count($task_data["steel_factory"]);
            } else {
                $steel_factory_upgrades = 0;
            }

            if ($steel_factory_upgrades === 0) {
                $steel_factory_level = $buildings_data["steel_factory"];
                $no_action_interval = time() - $city_data["update_time"];
                if ($steel_factory_level > 0) {
                    $steel_per_hour = calculate_recourse_per_hour($building_info["steel_factory"]["base_gain"], $steel_factory_level, $building_info["steel_factory"]["resource_constant"]);
                } else {
                    $steel_per_hour = calculate_recourse_per_hour($building_info["steel_factory"]["base_gain"], $steel_factory_level, $building_info["steel_factory"]["resource_constant"]);
                }
                $deserved_steel += ($steel_per_hour / 3600) * $no_action_interval;
            } elseif ($steel_factory_upgrades >= 1) {
                foreach($task_data["steel_factory"] as $steel_task_data) {
                    $steel_per_hour_array[] = $building_info["steel_factory"]["base_gain"] * $steel_task_data["level"] * ($building_info["steel_factory"]["resource_constant"] * $building_info["steel_factory"]["resource_constant"]);
                }
                $i = 0;
                foreach ($steel_per_hour_array as $steel_per_hour) {
                    if ($i === 0) {
                        $deserved_steel_array[] = ($steel_per_hour / 3600) * ($task_data["steel_factory"][$i]["update_time"] - $city_data["update_time"]);
                    } else {
                        $deserved_steel_array[] = ($steel_per_hour / 3600) * ($task_data["steel_factory"][$i]["update_time"] - $task_data["steel_factory"][$i-1]["update_time"]);
                    }
                    $i++;
                }
                $deserved_steel_array[] = ($steel_per_hour / 3600) * (time() - $task_data["steel_factory"][$i-1]["update_time"]);
                $deserved_steel = 0;
                foreach($deserved_steel_array as $deserved_steel_part) {
                    $deserved_steel += $deserved_steel_part;
                }
            }
        }

        if ($deserved_coal <= $storage_capacity) {
            if (!empty($task_data["coal_mine"])) {
                $coal_mine_upgrades = count($task_data["coal_mine"]);
            } else {
                $coal_mine_upgrades = 0;
            }

            if ($coal_mine_upgrades === 0) {
                $coal_mine_level = $buildings_data["coal_mine"];
                $no_action_interval = time() - $city_data["update_time"];
                if ($coal_mine_level > 0) {
                    $coal_per_hour = calculate_recourse_per_hour($building_info["coal_mine"]["base_gain"], $coal_mine_level, $building_info["coal_mine"]["resource_constant"]);
                } else {
                    $coal_per_hour = calculate_recourse_per_hour($building_info["coal_mine"]["base_gain"], $coal_mine_level, $building_info["coal_mine"]["resource_constant"]);
                }
                $deserved_coal += ($coal_per_hour / 3600) * $no_action_interval;
            } elseif ($coal_mine_upgrades >= 1) {
                foreach ($task_data["coal_mine"] as $coal_task_data) {
                    $coal_per_hour_array[] = $building_info["coal_mine"]["base_gain"] * $coal_task_data["level"] * ($building_info["coal_mine"]["resource_constant"] * $building_info["coal_mine"]["resource_constant"]);
                }
                $i = 0;
                foreach ($coal_per_hour_array as $coal_per_hour) {
                    if ($i === 0) {
                        $deserved_coal_array[] = ($coal_per_hour / 3600) * ($task_data["coal_mine"][$i]["update_time"] - $city_data["update_time"]);
                    } else {
                        $deserved_coal_array[] = ($coal_per_hour / 3600) * ($task_data["coal_mine"][$i]["update_time"] - $task_data["coal_mine"][$i - 1]["update_time"]);
                    }
                    $i++;
                }
                $deserved_coal_array[] = ($coal_per_hour / 3600) * (time() - $task_data["coal_mine"][$i - 1]["update_time"]);
                $deserved_coal = 0;
                foreach ($deserved_coal_array as $deserved_coal_part) {
                    $deserved_coal += $deserved_coal_part;
                }
            }
        }

        if ($deserved_wood <= $storage_capacity) {

            if (!empty($task_data["woodchopper"])) {
                $woodchopper_upgrades = count($task_data["woodchopper"]);
            } else {
                $woodchopper_upgrades = 0;
            }

            if ($woodchopper_upgrades === 0) {
                $woodchopper_level = $buildings_data["woodchopper"];
                $no_action_interval = time() - $city_data["update_time"];
                if ($woodchopper_level > 0) {
                    $wood_per_hour = calculate_recourse_per_hour($building_info["woodchopper"]["base_gain"], $woodchopper_level, $building_info["woodchopper"]["resource_constant"]);
                } else {
                    $wood_per_hour = calculate_recourse_per_hour($building_info["woodchopper"]["base_gain"], $woodchopper_level, $building_info["woodchopper"]["resource_constant"]);
                }
                $deserved_wood += ($wood_per_hour / 3600) * $no_action_interval;
            } elseif ($woodchopper_upgrades >= 1) {
                foreach ($task_data["woodchopper"] as $wood_task_data) {
                    $wood_per_hour_array[] = $building_info["woodchopper"]["base_gain"] * $wood_task_data["level"] * ($building_info["woodchopper"]["resource_constant"] * $building_info["woodchopper"]["resource_constant"]);
                }
                $i = 0;
                foreach ($wood_per_hour_array as $wood_per_hour) {
                    if ($i === 0) {
                        $deserved_wood_array[] = ($wood_per_hour / 3600) * ($task_data["woodchopper"][$i]["update_time"] - $city_data["update_time"]);
                    } else {
                        $deserved_wood_array[] = ($wood_per_hour / 3600) * ($task_data["woodchopper"][$i]["update_time"] - $task_data["woodchopper"][$i - 1]["update_time"]);
                    }
                    $i++;
                }
                $deserved_wood_array[] = ($wood_per_hour / 3600) * (time() - $task_data["woodchopper"][$i - 1]["update_time"]);
                $deserved_wood = 0;
                foreach ($deserved_wood_array as $deserved_wood_part) {
                    $deserved_wood += $deserved_wood_part;
                }
            }
        }
    } else {
        foreach ($task_data["storage"] as $storage_data) {
            if ($deserved_steel <= $storage_capacity) {
                if (!empty($task_data["steel_factory"])) {
                    $steel_factory_upgrades = count($task_data["steel_factory"]);
                } else {
                    $steel_factory_upgrades = 0;
                }

                if ($steel_factory_upgrades === 0) {
                    $steel_factory_level = $buildings_data["steel_factory"];
                    $no_action_interval = time() - $storage_data["time"];
                    if ($steel_factory_level > 0) {
                        $steel_per_hour = calculate_recourse_per_hour($building_info["steel_factory"]["base_gain"], $steel_factory_level, $building_info["steel_factory"]["resource_constant"]);
                    } else {
                        $steel_per_hour = calculate_recourse_per_hour($building_info["steel_factory"]["base_gain"], $steel_factory_level, $building_info["steel_factory"]["resource_constant"]);
                    }
                    $deserved_steel += ($steel_per_hour / 3600) * $no_action_interval;
                } elseif ($steel_factory_upgrades >= 1) {
                    foreach($task_data["steel_factory"] as $steel_task_data) {
                        $steel_per_hour_array[] = $building_info["steel_factory"]["base_gain"] * $steel_task_data["level"] * ($building_info["steel_factory"]["resource_constant"] * $building_info["steel_factory"]["resource_constant"]);
                    }
                    $i = 0;
                    foreach ($steel_per_hour_array as $steel_per_hour) {
                        if ($i === 0) {
                            $deserved_steel_array[] = ($steel_per_hour / 3600) * ($task_data["steel_factory"][$i]["update_time"] - $storage_data["time"]);
                        } else {
                            $deserved_steel_array[] = ($steel_per_hour / 3600) * ($task_data["steel_factory"][$i]["update_time"] - $task_data["steel_factory"][$i-1]["update_time"]);
                        }
                        $i++;
                    }
                    $deserved_steel_array[] = ($steel_per_hour / 3600) * (time() - $task_data["steel_factory"][$i-1]["update_time"]);
                    $deserved_steel = 0;
                    foreach($deserved_steel_array as $deserved_steel_part) {
                        $deserved_steel += $deserved_steel_part;
                    }
                }
            }

            if ($deserved_coal <= $storage_capacity) {
                if (!empty($task_data["coal_mine"])) {
                    $coal_mine_upgrades = count($task_data["coal_mine"]);
                } else {
                    $coal_mine_upgrades = 0;
                }

                if ($coal_mine_upgrades === 0) {
                    $coal_mine_level = $buildings_data["coal_mine"];
                    $no_action_interval = time() - $storage_data["time"];
                    if ($coal_mine_level > 0) {
                        $coal_per_hour = calculate_recourse_per_hour($building_info["coal_mine"]["base_gain"], $coal_mine_level, $building_info["coal_mine"]["resource_constant"]);
                    } else {
                        $coal_per_hour = calculate_recourse_per_hour($building_info["coal_mine"]["base_gain"], $coal_mine_level, $building_info["coal_mine"]["resource_constant"]);
                    }
                    $deserved_coal += ($coal_per_hour / 3600) * $no_action_interval;
                } elseif ($coal_mine_upgrades >= 1) {
                    foreach ($task_data["coal_mine"] as $coal_task_data) {
                        $coal_per_hour_array[] = $building_info["coal_mine"]["base_gain"] * $coal_task_data["level"] * ($building_info["coal_mine"]["resource_constant"] * $building_info["coal_mine"]["resource_constant"]);
                    }
                    $i = 0;
                    foreach ($coal_per_hour_array as $coal_per_hour) {
                        if ($i === 0) {
                            $deserved_coal_array[] = ($coal_per_hour / 3600) * ($task_data["coal_mine"][$i]["update_time"] - $storage_data["time"]);
                        } else {
                            $deserved_coal_array[] = ($coal_per_hour / 3600) * ($task_data["coal_mine"][$i]["update_time"] - $task_data["coal_mine"][$i - 1]["update_time"]);
                        }
                        $i++;
                    }
                    $deserved_coal_array[] = ($coal_per_hour / 3600) * (time() - $task_data["coal_mine"][$i - 1]["update_time"]);
                    $deserved_coal = 0;
                    foreach ($deserved_coal_array as $deserved_coal_part) {
                        $deserved_coal += $deserved_coal_part;
                    }
                }
            }

            if ($deserved_wood <= $storage_capacity) {

                if (!empty($task_data["woodchopper"])) {
                    $woodchopper_upgrades = count($task_data["woodchopper"]);
                } else {
                    $woodchopper_upgrades = 0;
                }

                if ($woodchopper_upgrades === 0) {
                    $woodchopper_level = $buildings_data["woodchopper"];
                    $no_action_interval = time() - $storage_data["time"];
                    if ($woodchopper_level > 0) {
                        $wood_per_hour = calculate_recourse_per_hour($building_info["woodchopper"]["base_gain"], $woodchopper_level, $building_info["woodchopper"]["resource_constant"]);
                    } else {
                        $wood_per_hour = calculate_recourse_per_hour($building_info["woodchopper"]["base_gain"], $woodchopper_level, $building_info["woodchopper"]["resource_constant"]);
                    }
                    $deserved_wood += ($wood_per_hour / 3600) * $no_action_interval;
                } elseif ($woodchopper_upgrades >= 1) {
                    foreach ($task_data["woodchopper"] as $wood_task_data) {
                        $wood_per_hour_array[] = $building_info["woodchopper"]["base_gain"] * $wood_task_data["level"] * ($building_info["woodchopper"]["resource_constant"] * $building_info["woodchopper"]["resource_constant"]);
                    }
                    $i = 0;
                    foreach ($wood_per_hour_array as $wood_per_hour) {
                        if ($i === 0) {
                            $deserved_wood_array[] = ($wood_per_hour / 3600) * ($task_data["woodchopper"][$i]["update_time"] - $storage_data["time"]);
                        } else {
                            $deserved_wood_array[] = ($wood_per_hour / 3600) * ($task_data["woodchopper"][$i]["update_time"] - $task_data["woodchopper"][$i - 1]["update_time"]);
                        }
                        $i++;
                    }
                    $deserved_wood_array[] = ($wood_per_hour / 3600) * (time() - $task_data["woodchopper"][$i - 1]["update_time"]);
                    $deserved_wood = 0;
                    foreach ($deserved_wood_array as $deserved_wood_part) {
                        $deserved_wood += $deserved_wood_part;
                    }
                }
            }
        }
    }


    if ($deserved_steel > $storage_capacity) {
        $deserved_steel = $storage_capacity;
    }

    if ($deserved_wood > $storage_capacity) {
        $deserved_wood = $storage_capacity;
    }

    if ($deserved_coal > $storage_capacity) {
        $deserved_coal = $storage_capacity;
    }

    $fields = array(
        "update_time" => time(),
        "steel" => $deserved_steel,
        "coal" => $deserved_coal,
        "wood" => $deserved_wood
    );

    update_city($city_id, $fields);
}

function calculate_recourse_per_hour($base_gain, $level, $resource_constant) {
    if ($level >= 1) {
        $resource_per_hour = (($base_gain * ($level * $level)) + ($base_gain * $base_gain)) / $resource_constant;
    } else {
        $resource_per_hour = ($base_gain / 2);
    }
    return $resource_per_hour;
}

function calculate_storage_capacity($base_capacity, $level, $storage_constant) {
    $capacity = $base_capacity * (($level * $level * $level) +  $storage_constant);
    return $capacity;
}

function get_resource_task_timings($city_id, $select_all = true) {
    global $connection;

    if ($select_all === true) {
        $sql = mysqli_query($connection, "SELECT building, update_time, level FROM task WHERE city_id=$city_id AND (building='steel_factory' OR building='coal_mine' OR building='woodchopper' OR building='storage') ORDER BY time ASC");
    } else {
        $current_time = time();
        $sql = mysqli_query($connection, "SELECT building, update_time, level FROM task WHERE city_id=$city_id AND (building='steel_factory' OR building='coal_mine' OR building='woodchopper' OR building='storage') AND time<$current_time ORDER BY time ASC");
    }
    $timing_data = array();

    if (!$sql) {
        return mysqli_error($connection);
    } else {
        while($db_timing_data = mysqli_fetch_assoc($sql)) {
            if ($db_timing_data["building"] === "steel_factory") {
                $timing_data["steel_factory"][] = $db_timing_data;
            } elseif ($db_timing_data["building"] === "coal_mine") {
                $timing_data["coal_mine"][] = $db_timing_data;
            } elseif ($db_timing_data["building"] === "woodchopper") {
                $timing_data["woodchopper"][] = $db_timing_data;
            } elseif ($db_timing_data["building"] === "storage") {
                $timing_data["storage"][] = $db_timing_data;
            }
        }
    }
    return $timing_data;
}

function update_city($city_id, $fields) {
    global $connection;

    $values = prepare_fields($fields);

    if (mysqli_query($connection, "UPDATE city SET $values WHERE id=$city_id")) {
        return true;
    } else {
        return mysqli_error($connection);
    }
}

function delete_completed_tasks($city_id) {
    global $connection;
    $current_time = time();

   if(mysqli_query($connection, "DELETE FROM task WHERE update_time<=$current_time AND city_id=$city_id")) {
       return true;
   } else {
       return mysqli_error($connection);
   }
}

function legal_building($building) {
    $all_buildings = get_building_game_info();
    if (array_key_exists($building, $all_buildings) === true) {
        return true;
    } else {
        return false;
    }
}

function buildings_next_level($city_id) {
    global $connection;

    $current_time = time();
    $all_buildings = get_building_game_info();

    $buildings_list = array();

    foreach ($all_buildings as $present_building => $data) {
        $buildings_list[] = $present_building;
    }

    $buildings_sql = implode(", ", $buildings_list);

    $sql = mysqli_query($connection, "SELECT building, level FROM task WHERE city_id=$city_id AND update_time>$current_time");
    $sql_buildings = mysqli_query($connection, "SELECT $buildings_sql FROM building WHERE city_id=$city_id");

    $buildings_next_level = array();

    if (!$sql || !$sql_buildings) {
        return mysqli_error($connection);
    } else {
        while($task = mysqli_fetch_assoc($sql)) {
            $buildings_next_level[$task["building"]] = $task["level"] + 1;
        }
        while ($buildings = mysqli_fetch_assoc($sql_buildings)) {
            foreach($buildings as $building => $level) {
                if (!isset($buildings_next_level[$building])) {
                    $buildings_next_level[$building] = $level + 1;
                }
            }
        }
        return $buildings_next_level;
    }
}

function create_task($city_id, $building, $level, $time_constant) {
    global $connection;

    $start_time = get_task_time($city_id);

    if (!empty($start_time)) {
        $building_time = (int)$start_time + calculate_building_time($level, $time_constant);
    } else {
        $building_time = time() + calculate_building_time($level, $time_constant);
    }

    if (mysqli_query($connection, "INSERT INTO task (city_id, building, level, update_time) VALUES ($city_id, '$building', $level, $building_time)")) {
        return true;
    } else {
        return mysqli_error($connection);
    }
}

function get_task_time($city_id) {
    global $connection;

    $sql = mysqli_query($connection, "SELECT update_time FROM task WHERE city_id=$city_id ORDER BY update_time DESC LIMIT 1");

    if (!$sql) {
        return mysqli_error($connection);
    } else {
        $time_data = mysqli_fetch_assoc($sql);
        return $time_data["update_time"];
    }
}

function calculate_building_time($level, $constant) {
    return 30 + (($level * $level * $level) * $constant);
}

function upgrade_buildings($city_id) {
    global $connection;

    $current_time = time();

    $sql = mysqli_query($connection, "SELECT building, level FROM task WHERE city_id=$city_id AND update_time<=$current_time ORDER BY update_time ASC");

    if (!$sql) {
        return mysqli_error($connection);
    } else {
        $fields = array();
        while($completed_task = mysqli_fetch_assoc($sql)) {
            $fields[$completed_task["building"]] = $completed_task["level"];
        }
        if (!empty($fields)) {
            echo update_building($city_id, $fields);
        }
        return true;
    }
}

function update_building($city_id, $fields) {
    global $connection;

    $values = prepare_fields($fields);

    if (mysqli_query($connection, "UPDATE building SET $values WHERE city_id=$city_id")) {
        return true;
    } else {
        return mysqli_error($connection);
    }
}

function get_future_tasks($city_id) {
    global $connection;

    $current_time = time();

    $sql = mysqli_query($connection, "SELECT building, level, update_time FROM task WHERE city_id=$city_id AND update_time>$current_time");

    if (!$sql) {
        return mysqli_error($connection);
    } else {
        $tasks = array();
        while($task = mysqli_fetch_assoc($sql)) {
            $tasks[] = $task;
        }
        return $tasks;
    }
}

function manage_single_city($city_id) {
    update_resources($city_id);
    upgrade_buildings($city_id);
    delete_completed_tasks($city_id);
}