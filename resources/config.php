<?php

/**********************************/
/*                                */
/*   Interbellum                  */
/*   General configuration file   */
/*   Version: 2.0 pa              */
/*                                */
/**********************************/

// Constants
$settings = array(
    "map" => array(
        "coordinates_per_tile" => 7,
        "map_size" => 30,
        "map_density" => 1/3,
        "static_area" => 3,
        "dynamic_area" => 2
    )
);

// Set the timezone
date_default_timezone_set("Europe/Brussels");

// Get the required functions
require_once "library/connect/connect.php";
require_once "library/register/register.php";
require_once "library/register/early_access.php";
require_once "library/user/user.php";
require_once "library/support/support.php";
require_once "library/support/retrieve.php";
require_once "library/support/modify.php";
require_once "library/support/stream_events.php";
require_once "library/city/city.php";
require_once "library/message/thread.php";
require_once "library/message/message.php";

// Set error reporting
ini_set("error_reporting", "true");
error_reporting(E_ALL|E_STRICT);