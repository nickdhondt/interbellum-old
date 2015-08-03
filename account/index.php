<?php

session_start();

require_once("../resources/config.php");

$user_data = logged_in();

// The user must be logged in
if (!$user_data) header("Location: ../");

?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Interbellum - Account</title>
    <script src="../script/shared.js"></script>
    <meta name=viewport content="width=device-width, initial-scale=1">
    <link id="pagestyle" rel="stylesheet" href="css/screen.css"/>
    <link rel="icon" href="../img/favicon.png" type="image/png">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,400italic,700,700italic' rel='stylesheet' type='text/css'>
    <script src="script/test.js"></script>
</head>
<body>
<h1>Nothing here yet</h1>
<p>God dammit, stop reading! I said, NOTHING HERE YET!</p>
<div id="dark">Change to night mode</div>
<p><small>Hey hey, click <a href="../">here</a> maybe?</small></p>
<div id="jef"><div></div></div>
</body>
</html>