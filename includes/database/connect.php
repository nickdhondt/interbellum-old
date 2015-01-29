<?php

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$database = "interbellum";

// Connecting to the database using mysqli
// Note: not mysql, but mysqli
$connection = mysqli_connect($servername, $username, $password, $database, 3306);

// Connection must be made, nothing will continue unless there is connection
if (!$connection) {
    die ("verbinding met de database mislukt: " . mysqli_connect_error());
}

?>