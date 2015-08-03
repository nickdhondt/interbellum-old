<?php

session_start();

require_once("../../resources/config.php");

// Script will return JSON string
header("Content-Type: application/json");

$user_id = $_SESSION["-int-user_id"];

session_write_close();

if (isset($_GET["fast"])) $fast = boolval($_GET["fast"]);
else $fast = false;

$script_beginning = microtime(true);
if (empty($_GET["time"])) $now = $script_beginning;
else $now = $_GET["time"];
$messages = array();
$break = false;
$debug = array();

ini_set('max_execution_time', 90);

$first_loop = true;

while ($script_beginning >= (microtime(true) - 60) && $break === false) {
    // Event logic here

    $past = $now;
    $new_messages = new_messages($user_id, $past, $now);

    $debug["time"][] = array($past, $now);

    $debug["loop check"][] = $new_messages;
    if ($new_messages[0]) {
        $debug["true"][] = $past;
        $thread_ids = get_own_threads($user_id);
        $messages = get_messages($thread_ids, $past);
        $debug["messages"][] = $messages;
        $break = true;
    }

    if ($fast === true) break;

    if ($first_loop === true) {
        $first_loop = false;
    } elseif ($break === false) {
        usleep(1500000);
    }
}


$time = array(microtime(true), date("F d - H:i:s"), "messages" => $messages, "last_poll" => $now, "debug" => $debug);

echo json_encode($time);