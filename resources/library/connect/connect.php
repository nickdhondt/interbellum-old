<?php

$config = array(
    "host" => "localhost",
    "username" => "root",
    "password" => "",
    "dbname" => "interbellum"
);

// Create a connection
$conn = new mysqli($config["host"], $config["username"], $config["password"], $config["dbname"]);

// Abort the script if connecting failed
if (!$conn) {
    die("Connection with database failed: " . $conn->connect_errno);
}

$conn->set_charset("utf8");