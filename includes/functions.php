<?php

/*
 * List of all the functions in functions.php (not completed yet)
 * Please add a newly made function to this list
 *
 * get_buildings_game_info()
 * get_buildings_level_info()
 * calculate_speed_improvement()
 * calculate_population_storage()
 * calculate_cost()
 * output_errors()
 * user_exists()
 * user_data()
 * update_user()
 * user_logged_in()
 * get_session_data()
 * get_session_hash()
 * make_thread()
 * make_message()
 * make_thread_breadcrumbs()
 * mass_make_thread_breadcrumbs()
 * get_thread_breadcrumbs()
 * get_thread_data()
 * get_message_data()
 * make_link_from_url()
 * format_message()
 * get_user_id_from_breadcrumbs§)
 * sanitize()
 * update_breadcrumb()
 * prepare_fields()
 * unread_messages()
 * get_last_message()
 * format_elapsed_seconds()
 * thread_recipients()
 * make_session()
 * update_session()
 * delete_breadcrumb()
 * count_all_messages()
 * display_pages()
 * count_citys()
 * create_city()
 * coordinates_exist()
 * create_building()
 * get_citys()
 * get_city_data()
 * get_buildings_data()
 * update_resources()
 * calculate_resource_per_hour()
 * calculate_storage_capacity()
 * get_resource_task_timings()
 * update_city()
 * delete_completed_tasks()
 * legal_building()
 * buildings_next_level()
 * create_task()
 * get_task_time()
 * calculate_building_time()
 * upgrade_buildings()
 * update_building()
 * get_future_tasks()
 * manage_single_city()
 * html_page_title()
 * mass_user_data()
 * prepare_fields_select()
 * count_all_users()
 * mass_update_breadcrumbs()
 * mass_get_thread_data()
 *
 */

// This file is included at the top of each page. The microtime at the beginning of the script is saved in a variabele
// In the footer the execution time is calculated, based on this variable
$page_cal_start = microtime(true);

session_start();

// Connection to the database
include "database/connect.php";

// Setting the timezeone to GMT+1
date_default_timezone_set("Europe/Brussels");

/* Gamedata */

// This function will return "gamedata" needed to create hyperlinks, etc.
// The first string inside the second array is supposed to be the text on which to click in the city.php page
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
            // This formula need 'base_resource_cost' and 'cost_constant' (see calculate_cost())
            "base_steel_cost" => 42,
            "base_coal_cost" => 45,
            "base_wood_cost" => 31,
            // base_time determines how long it takes to upgrade/build a building
            // This is calculated using a formula
            // The formula needs 'base_time' and 'time_constant' (see calculate_building_time())
            "base_time" => 45,
            // This constant determines how quickly a building makes resources
            // This is calculate using a formula, which needs 'base_gain' and 'resource_constant' to calculate the resources per hour (see calculate_resource_per_hour())
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
// The returned double is between 0 and 1, so by multiplying the building time (in seconds) with this coefficient you get a smeller building time
function calculate_speed_improvement($level, $constant, $current_building_seconds) {
    return (pow($constant, $level) * $current_building_seconds);
}

// Calculates the total population that is allowed in a city based on the level
function calculate_population_storage($base, $level, $constant) {
    return ($level + 4 * $level + 4 * $level + 4) + $base / $constant;
}

// Calculates the costs of all resources and returns an array with the calculated values
function calculate_cost($steel, $coal, $wood, $level, $cost_constant) {
    $cost = array();
    $cost["steel"] = round(($level * $level * $level) * $cost_constant) + $steel;
    $cost["coal"] = round(($level * $level * $level) * $cost_constant) + $coal;
    $cost["wood"] = round(($level * $level * $level) * $cost_constant) + $wood;

    return $cost;
}

// Returns error messages from a received array in html list (<ul><li>...) format if the array isn't empty
function output_errors($errors) {
    // Check if the array contains elements
    if (!empty($errors)) {
        echo "<ul>";
        // Loop through the elements and puts them in a list
        foreach($errors as $error) {
            echo "<li>" . $error . "</li>";
        }
        echo "</ul>";
    }
}

/* User */

// Checks if a user exists in the database
// Returns false, or the user id
function user_exists($username) {
    // The connection to the db needs to be accessible inside the function
    global $connection;

    // Try to select the id based on a given username
    $sql = mysqli_query($connection, "SELECT id FROM user WHERE username='$username'");

    if (!$sql) {
        // If the query failed, error information will be returned
        return mysqli_error($connection);
    } else {
        if (mysqli_num_rows($sql) === 1) {
            // If there exactly 1 user, his id will be returned
            // Fetch the id and return the value
            $user_id = mysqli_fetch_assoc($sql);
            return $user_id["id"];
        } else {
            // If there are more or less than 1 username false is returned
            return false;
        }
    }
}

// Selects fields base on the given fields array
function user_data($user_id, $fields) {
    global $connection;

    // We can't select an array, the array needs to be converted to a string understandable for SQL
    $sql_fields = prepare_fields_select($fields);

    // Execute the mysql query
    // Select all the specified fields for a user
    $sql = mysqli_query($connection, "SELECT $sql_fields FROM user WHERE id='$user_id'");

    // If the query failed, an error is returned
    // If it succeeded the requested fields are returned
    if (!$sql) {
        return mysqli_error($connection);
    } else {
        return mysqli_fetch_assoc($sql);
    }
}

// Update/change fields in the user table
function update_user($user_id, $fields) {
    global $connection;

    // Making a string from the $fields array
    $values = prepare_fields($fields);

    // Execute the query, update the specified fields for a user
    // If the query succeeded true is returned
    // If the query failed an error is returned
    if (mysqli_query($connection, "UPDATE user SET $values WHERE id=$user_id")) {
        return true;
    } else {
        return mysqli_error($connection);
    }
}

// Check if a user is logged in
function user_logged_in () {
    // If there is a user id in the session superglobal, the id is returned
    // If there isn't a user id, false is returned
    if (!empty($_SESSION["user_id"])) {
        return $_SESSION["user_id"];
    } elseif (!empty($_COOKIE["-int-remember_my_name"]) && !empty($_COOKIE["-int-remember_me_hash"])) {
        // If the remember me cookies are set. Their values are saved in a variable
        $cookie_user_id = $_COOKIE["-int-remember_my_name"];
        $cookie_hash = $_COOKIE["-int-remember_me_hash"];

        // Get the saved hash from the db
        $remember_fields = array("remember_hash");
        $user_hash_data = user_data($cookie_user_id, $remember_fields);

        // If the hash in the cookie and the hash in the db are equal, The user id is saved in session
        if ($user_hash_data["remember_hash"] == $cookie_hash) {
            $_SESSION["user_id"] = $cookie_user_id;
            // return the user id
            return $_SESSION["user_id"];
        } else {
            return false;
        }
    } else {
        return false;
    }
}

/* Session */

// Will be deleted in favour of a better "remember me" system
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

// Will be deleted in favour of a better "remember me" system
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

// The messaging system works like this:
// There is 1 thread, multiple "breadcrumbs" and multiple messages
// breadcrumbs are linked to a thread, each user has a breadcrumb that holds information like the status (read/unread), time of last message, user id and thread id
// There are multiple breadcrumbs with the same thread id. Each member of a conversation has his own breadcrumb
// Messages are linked to a thread, not to the breadcrumbs
// The inbox of a user displays the breadcrumbs a user owns

// Makes a new thread
function make_thread($thread) {
    global $connection;

    // If the query succeeds the id of the just made thread is returned
    // If it fails false is returned
    if (mysqli_query($connection, "INSERT INTO thread (thr_name) VALUES ('$thread')")) {
        return mysqli_insert_id($connection);
    } else {
        return false;
    }
}

// Make a new message
function make_message($thr_id, $user_id, $body) {
    global $connection;

    // Escape special characters in a string to use in SQL statement
    // eg. "\", "'", etc.
    $body = mysqli_real_escape_string($connection, $body);
    //Format the current date
    $datetime = date("Y-m-d H:i:s");

    // Insert the sender, thread id, formatted date and safe message body
    // The the query fails, an error is returned
    // If it succeeds, true is returned
    if (mysqli_query($connection, "INSERT INTO message (thr_id, user_id, senddate, body) VALUES ('$thr_id', '$user_id', '$datetime', '$body')")) {
        return true;
    } else {
        return mysqli_error($connection);
    }
}

// Make a "breadcrumb"
function make_thread_breadcrumbs($thr_id, $user_id, $status=0) {
    global $connection;

    // Set the current time
    $time = time();

    // Insert the user id date, etc. in the breadcrumbs table (thr_recipient)
    // Return true or false if the query succeeds of fails
    if (mysqli_query($connection, "INSERT INTO thr_recipient (thr_id, user_id, last_mod, status) VALUES ('$thr_id', '$user_id', $time, $status)")) {
        return true;
    } else {
        return false;
    }
}

// Makes multiple breadcrumbs
function mass_make_thread_breadcrumbs($thr_id, $users, $status = 0) {
    global $connection;

    // Set the current time
    $time = time();

    // Will contain an array with the value rows that will be inserted
    $insert_values_array = array();

    // Make a row for each user id and put it in the array
    foreach ($users as $user_id) {
        $insert_values_array[] = "('" . $thr_id . "', '" . $user_id . "', '" . $time . "', '" . $status . "')";
    }

    // Convert to a comma separated string
    $insert_values = implode(", ", $insert_values_array);

    // Insert the rows in the db
    $sql_insert_breadcrumbs = mysqli_query($connection, "INSERT INTO thr_recipient (thr_id, user_id, last_mod, status) VALUES $insert_values");

    // Return false if the query failed and true if it succeeded
    if (!$sql_insert_breadcrumbs) {
        return false;
    } else {
        return true;
    }
}

// Select a users threads based on the breadcrumbs
function get_thread_breadcrumbs($user_id) {
    global $connection;

    // This array will be filled with the users threads
    $all_threads = array();

    // Select all the threads the user has read or are unread (not deleted threads, these have status 2)
    $sql = mysqli_query($connection, "SELECT thr_id, status, last_mod FROM thr_recipient WHERE user_id='$user_id' AND (status=0 OR status=1) ORDER BY last_mod DESC");

    // If the query failed an error is returned
    // If is succeeded the data is returned
    if (!$sql) {
        return mysqli_error($connection);
    } else {
        // Loop through all the selected rows and put them in an associative array
        while ($thread_id = mysqli_fetch_assoc($sql)) {
            $all_threads[] = array (
                "thr_id" => $thread_id["thr_id"],
                "status" => $thread_id["status"],
                "last_mod" => $thread_id["last_mod"]);
        }

        // Return the array
        return $all_threads;
    }
}

// Select thread data (currently only the thread name)
function get_thread_data($thr_id) {
    global $connection;

    // SQL query to select the thread name for a thread id
    $sql = mysqli_query($connection, "SELECT thr_name FROM thread WHERE id='$thr_id'");

    // If the query fails an error is returned
    // If it succeeds an array containing the requested data is returned
    if (!$sql) {
        return mysqli_error($connection);
    } else  {
        return mysqli_fetch_assoc($sql);
    }
}

// Fetch the message of a thread
// Maximum 15 messages are returned -> only 1 page of messages
function get_message_data($thread_id, $page) {
    global $connection;

    // Calculate where to start selecting messages based on the page the user is viewing
    // Page 0 starts at message 0 page 4 at message 60
    $start = $page * 15;

    // This array will be filled withe the selected messages
    $all_messages_data = array();

    // The query selects the send date, user id en message body for all message in a page
    // Starting at message $start and ending 15 messages later
    $sql = mysqli_query($connection, "SELECT user_id, senddate, body FROM message WHERE thr_id='$thread_id' ORDER BY senddate DESC LIMIT $start,15") or die(mysqli_error($connection));

    // Return an error or return the messages in an array
    if (!$sql) {
        return mysqli_error($connection);
    } else {
        while($message_data = mysqli_fetch_assoc($sql)) {
            $all_messages_data[] = $message_data;
        }
        return $all_messages_data;
    }
}

// Detect a url in text and make a hyperlink
function make_link_from_url($text){

    // Look for these anomalies
    $reg_ex_url = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,10}(\/\S*|:\S*)?/";

    // Search for the pattern and store the matches in an array ($matches)
    preg_match_all ($reg_ex_url, $text, $matches);

    $used_patterns = array();

    // Loop through the matches
    foreach ($matches[0] as $pattern){
        if (!array_key_exists ($pattern, $used_patterns)){
            $used_patterns[$pattern] = true;

            // Replace the pattern (url) by a hyperlink
            $text = str_replace ($pattern, "<strong><a href=\"" . $pattern . "\" target=\"_blank\" rel=\"nofollow\">" . $pattern . "</a></strong>", $text);
        }
    }
    // Return the text with hyperlinks
    return $text;
}

// This function formats text and can be used for multiple purposes
// By default it does a "complete" format
function format_message($body, $mode = "COMPLETE") {
    // Preview mode. This is used to display the little preview of a conversation in the inbox
    if ($mode === "PREVIEW") {
        // If the body is longer than 75 characters, it is cut to 75 characters and an ellipsis (...) is added to the end
        if (strlen($body) > 75) {
            // Return the formatted string
            return substr($body, 0, 75) . "...";
        } else {
            // If the body is shorter than 75 characters it is returned unmodified
            return $body;
        }
    } elseif ($mode === "COMPLETE") {
        // Complete mode. Used to display complete messages in a conversation
        // Replace \n\r (new line carriage return) by a <br /> tag, essentially making the enters display in html
        // Also detect urls and replace them by hyperlinks
        return nl2br(make_link_from_url($body));
    } else {
        // If the mode is not recognized just the <br /> tags are added
        return nl2br($body);
    }
}

// Returns all the id of the users that are participating in a conversation (even if the user has deleted a conversation)
function get_user_id_from_breadcrumbs($thr_id) {
    global $connection;

    // Id's will be put in this array
    $all_authorized_ids = array();

    // The users who are "authorized", who have a breadcrumb with specified thread is are selected
    $sql = mysqli_query($connection, "SELECT user_id, status FROM thr_recipient WHERE thr_id='$thr_id'");

    // Return an error or return the user id's
    if (!$sql) {
        return mysqli_error($connection);
    } else {
        // Loop through the selected rows and put them in an array
        while ($user_id = mysqli_fetch_assoc($sql)) {
            $all_authorized_ids[] = $user_id;
        }

        // return the array
        return $all_authorized_ids;
    }
}

// Make a string safe, prevent XSS
function sanitize ($text) {
    return htmlspecialchars($text, ENT_QUOTES);
}

// Update a breadcrumb
function update_breadcrumb($thr_id, $user_id, $fields) {
    global $connection;

    // Convert the fields array to a string with field and value
    $values = prepare_fields($fields);

    // Return true if the query succeeds, or return an erro
    if (mysqli_query($connection, "UPDATE thr_recipient SET $values WHERE user_id=$user_id && thr_id=$thr_id")) {
        return true;
    } else {
        return mysqli_error($connection);
    }
}

// Converts an array to a string containing the field to update and the updated value
function prepare_fields ($fields) {
    $single_values = array();

    // Loop through the array and split it in the key and the value
    // The key represents the field and the value is the new value which will be put into the db
    foreach ($fields as $field => $value) {
        // Add the field and value to an array
        // exemple: (username='new username')
        // If the value is a string, put single quotes around the value
        if (gettype($value) === "integer" || gettype($field) === "double") {
            $single_values[] = $field . "=" . $value;
        } else {
            $single_values[] = $field . "='" . $value . "'";
        }
    }

    // Return the array as a string
    return implode (", ", $single_values);
}

// Counts the unread messages
function unread_messages ($user_id) {
    // Get the users breadcrumbs
    $thr_breadcrumbs = get_thread_breadcrumbs($user_id);
    // Unread messages default set to 0
    $unread_messages = "0";

    // Loop through the breadcrumbs if a breadcrumb with status 0 (=unread) is found, $unread_messages in increased by 1
    foreach ($thr_breadcrumbs as $thr_breadcrumb) {
        if ($thr_breadcrumb["status"] === "0") {
            $unread_messages++;
        }
    }

    // If there are more than 9 unread messages, 9+ will be returned
    // If there are less, the number of unread messages will be returned
    if ($unread_messages <= "9") {
        return $unread_messages;
    } else {
        return "9+";
    }
}

// Select the last message in a thread
function get_last_message ($thr_id) {
    global $connection;

    // Select the last message in a thread
    // Limit makes the query select only one message
    $sql = mysqli_query($connection, "SELECT user_id, body FROM message WHERE thr_id=$thr_id ORDER BY senddate DESC LIMIT 1");

    // Return an error or return the message and it's sender (user id)
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
    } else {
        return "?";
    }
}

function thread_recipients ($thr_id) {
    global $connection;

    $sql = mysqli_query($connection, "SELECT user_id, status FROM thr_recipient WHERE thr_id=$thr_id AND (status=0 OR status=1)");

    if (!$sql) {
        return mysqli_error($connection);
    } else {
        $all_recipients_id = array();
        $loop = 0;
        while($recipient_id = mysqli_fetch_assoc($sql)) {
            $all_recipients_id[$loop]["user_id"] = $recipient_id["user_id"];
            $all_recipients_id[$loop]["status"] = $recipient_id["status"];

            $loop++;
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

// Generates an array with the available pages. Also includes "..." if there is a gap between pages. (eg. 1 ... 7 8 9 10 11 12 13)
function display_pages($page, $items_count, $items_per_page) {
    // Calculate the total amount of pages based on the total amount of elements and the amount of elements on a page
    $pages = ceil($items_count / $items_per_page);

    // This array will hold all the pages and other characters ("...", etc.)
    $output = array();

    // These booleans indicate if there needs to be added a "..." at the end of the beginning of the array with pages
    $at_end = false;
    $at_begin = false;

    // If there are 7 or less pages, this means there are no gaps between pages (1 ... 4 5)
    // So the pages list can be a string of 7 or less pages
    if ($pages <= 7) {
        $start = 0;
        $end = $pages;
    } else {
        // If there are more than 7 pages, this means there is a gap somewhere
        // There are three scenarios:
        // a) 1 2 3 4 5 6 7 ... 15
        // b) 1 ... 3 4 5 6 7 8 9 ... 15
        // c) 1 ... 12 13 14 15
        if ($page <= 3) {
            // Scenario a
            $start = 0;
            $end = 7;
            // Add the "...", but later (se below)
            $at_end = true;
        } elseif ($page > 3 && $page < $pages - 4) {
            // Scenario b
            $start = $page - 3;
            $end = $page + 4;
            // Add the "...", but later (se below)
            $at_begin = true;
            $at_end = true;
        } elseif($page >= $pages - 4) {
            // Scenario c
            $start = $page - 3;
            $end = $pages;
            // Add the "...", but later (se below)
            $at_begin = true;
        } else {
            // Backup scenario
            $start = 0;
            $end = $pages;
        }
    }

    $loop = 0;

    // If there needs to be a "..." at the beginning of the pages list (scenario b and c) the first page is added and then the "..."
    if ($at_begin === true) {
        $output[$loop] = array(
            // If set to true, this indicates that this is a page and should be a hyperlink
            "href" => true,
            "page" => 1
        );
        $loop++;

        $output[$loop] = array(
            // If set to false, this indicates that this is not a page and should not be a hyperlink
            "href" => false,
            "page" => "..."
        );
        $loop++;
    }

    // A loop for the middle part of the pages list
    for ($i = $start; $i < $end; $i++) {
        // If the page is active there is no need fo a hyperlink and the pagenumber is printed bold
        if ($page == $i) {
            $output[$loop] = array(
                "href" => false,
                "page" => "<strong>" . ($i + 1) . "</strong>"
            );
        } else {
            // The other pagenumbers need to be hyperlinks
            $output[$loop] = array(
                "href" => true,
                "page" => $i + 1
            );
        }
        $loop++;
    }

    // If there needs to be a "..." at the end of the pages list (scenario a and b) the "..." is added and then the last page
    if ($at_end === true) {
        $output[$loop] = array(
            "href" => false,
            "page" => "..."
        );
        $loop++;

        $output[$loop] = array(
            "href" => true,
            "page" => $pages
        );
    }

    // Return the array with pages
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
        if(is_array($fields))
        {
            $sql_fields = implode(", ", $fields);
        } else {
            $sql_fields = $fields;
        }
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
                    $steel_per_hour = calculate_resource_per_hour($building_info["steel_factory"]["base_gain"], $steel_factory_level, $building_info["steel_factory"]["resource_constant"]);
                } else {
                    $steel_per_hour = calculate_resource_per_hour($building_info["steel_factory"]["base_gain"], $steel_factory_level, $building_info["steel_factory"]["resource_constant"]);
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
                    $coal_per_hour = calculate_resource_per_hour($building_info["coal_mine"]["base_gain"], $coal_mine_level, $building_info["coal_mine"]["resource_constant"]);
                } else {
                    $coal_per_hour = calculate_resource_per_hour($building_info["coal_mine"]["base_gain"], $coal_mine_level, $building_info["coal_mine"]["resource_constant"]);
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
                    $wood_per_hour = calculate_resource_per_hour($building_info["woodchopper"]["base_gain"], $woodchopper_level, $building_info["woodchopper"]["resource_constant"]);
                } else {
                    $wood_per_hour = calculate_resource_per_hour($building_info["woodchopper"]["base_gain"], $woodchopper_level, $building_info["woodchopper"]["resource_constant"]);
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
                        $steel_per_hour = calculate_resource_per_hour($building_info["steel_factory"]["base_gain"], $steel_factory_level, $building_info["steel_factory"]["resource_constant"]);
                    } else {
                        $steel_per_hour = calculate_resource_per_hour($building_info["steel_factory"]["base_gain"], $steel_factory_level, $building_info["steel_factory"]["resource_constant"]);
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
                        $coal_per_hour = calculate_resource_per_hour($building_info["coal_mine"]["base_gain"], $coal_mine_level, $building_info["coal_mine"]["resource_constant"]);
                    } else {
                        $coal_per_hour = calculate_resource_per_hour($building_info["coal_mine"]["base_gain"], $coal_mine_level, $building_info["coal_mine"]["resource_constant"]);
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
                        $wood_per_hour = calculate_resource_per_hour($building_info["woodchopper"]["base_gain"], $woodchopper_level, $building_info["woodchopper"]["resource_constant"]);
                    } else {
                        $wood_per_hour = calculate_resource_per_hour($building_info["woodchopper"]["base_gain"], $woodchopper_level, $building_info["woodchopper"]["resource_constant"]);
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

function calculate_resource_per_hour($base_gain, $level, $resource_constant) {
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

function html_page_title($filename) {
    // The variable $building_info is declared in "includes/pageparts/header.php"
    // This contains information about the name and filename of the buildings (headquarters = Hoofdkwartier, pagename = bhead[.php])
    // This information is used to determine the page title
    global $building_info;
    global $city_data;
    global $thread_data;

    // Get the filename(relative to the document root) eg. "dir/file.txt". Then isolate the filename ("file.txt") and exclude the .php extension.
    $current_page = basename($filename, ".php");

    // Set a general title, in case the file is not recognised
    $title = "Interbellum";

    $title_suffix = " - Interbellum";

    // The array which contains the titles for each pagename
    $file_to_title = array(
        "index" => "Login" . $title_suffix,
        "logout" => "Uitloggen" . $title_suffix,
        "messages" => "Inbox" . $title_suffix,
        // See get_building_game_info()
        // Since the building pages are named after the data in get_building_game_info(), we can use the same information to generate the page title
        // Example: In get_building_game_info(): bhead. We make a .php file "bhead.php". This function isolates "bhead" from the uri.
        // We can use this isolated part (bhead) to search in get_building_game_info() and get the building name (for bhead: "Hoofdkwartier"
        $building_info["headquarters"][1] => $building_info["headquarters"][0] . $title_suffix,
        "city" => $city_data["name"] . $title_suffix,
        "viewm" => $thread_data["thr_name"] . $title_suffix,
        "newm" => "Nieuw bericht" . $title_suffix,
        "preferences" => "Voorkeuren" . $title_suffix,
        "settings" => "Instellingen" . $title_suffix,
        "delm" => "Succesvol verwijderd" . $title_suffix,
    );

    // If the title array contains a title for the current page, the title is set to the specified title
    if (array_key_exists($current_page, $file_to_title)) {
        $title = $file_to_title[$current_page];
    }

    // Return the title
    return $title;
}

function mass_user_data($user_ids, $fields) {
    global $connection;

    // Prepare the fields we want to select so they are correct mysql syntax
    $sql_fields = prepare_fields_select($fields);

    // Make a string from the array of id's
    $user_ids_for_sql = implode(",", $user_ids);

    // Select the specified fields where the user id is in the range of the given id's the script requests
    $sql_get_data = mysqli_query($connection, "SELECT $sql_fields FROM user WHERE id IN ($user_ids_for_sql)");

    if (!$sql_get_data) {
        // Return error information if the query failed
        return mysqli_error($connection);
    } else {
        // make an array with the requested data and return it
        $data = array();
        while($row = mysqli_fetch_assoc($sql_get_data)) {
            $data[] = $row;
        }
        return $data;
    }
}

function prepare_fields_select($fields) {
    // Prepare the fields we want to select so they are correct mysql syntax
    // If there aren't any fields sent, select all the fields "*"
    if (!empty ($fields)) {
        $sql_fields = implode(", ", $fields);
    } else {
        $sql_fields = "*";
    }

    // Return the string in correct form for mysql
    return $sql_fields;
}

function count_all_users() {
    global $connection;

    // A sql statement that will count all the users
    $sql = mysqli_query($connection, "SELECT COUNT(id) FROM user");

    $all_users = mysqli_fetch_assoc($sql);

    // Return the count
    return $all_users["COUNT(id)"];
}

function mass_update_breadcrumbs($thr_id, $user_ids, $fields) {
    global $connection;

    // We need the users in this format: user_id=1 OR user_id=2 etc.
    $where_clause = prepare_where_clause("user_id", $user_ids);

    // Convert the array with fields to a string the MySQL db will understand
    $values = prepare_fields($fields);

    // Execute the query in thr_recipients
    $sql_update_breadcrumbs = mysqli_query($connection, "UPDATE thr_recipient SET $values WHERE $where_clause AND thr_id=$thr_id");

    // If the query failed, an error is returned
    // If it succeeds, true is returned
    if (!$sql_update_breadcrumbs) {
        return mysqli_error($connection);
    } else {
        return true;
    }
}

function mass_get_thread_data($thr_ids) {
    global $connection;

    // Format the data for the WHERE clause
    $where_clause = prepare_where_clause("id", $thr_ids);

    // Execute the SQL query
    // Select the thread id and thread name for a series of threads (array $thr_ids)
    $sql_thread_data = mysqli_query($connection, "SELECT id, thr_name FROM thread WHERE $where_clause");

    if (!$sql_thread_data) {
        // If the query fails, we return an error message
        return mysqli_error($connection);
    } else {
        // This array will hold the thread data for all threads
        $thr_names = array();

        // Loop through the reaceived data and put in in the array
        while($thr_data = mysqli_fetch_assoc($sql_thread_data)) {
            $thr_names[] = array("id" => $thr_data["id"], "thr_name" => $thr_data["thr_name"]);
        }

        // Return the thread data for all asked threads
        return $thr_names;
    }
}

function prepare_where_clause($field, $values) {
    // We format the data the WHERE clause will understand
    return "(" . $field . "=" . implode(" OR " . $field . "=", $values) . ")";
}