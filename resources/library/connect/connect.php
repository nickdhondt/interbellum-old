<?php

$config = array(
    "host" => "localhost",
    "username" => "root",
    "password" => "",
    "dbname" => "interbellum"
);

$conn = new mysqli($config["host"], $config["username"], $config["password"], $config["dbname"]);

if (!$conn) {
    die("Connection with database failed: " . $conn->connect_errno);
}