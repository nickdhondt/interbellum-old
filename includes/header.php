<?php

// This functions determines what the <title></title> should be (see below and see html_page_title() in "functions.php")
$title = html_page_title($_SERVER["SCRIPT_FILENAME"]);

?>
<!doctype html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Interbellum is een gratis browsergame waar iedere speler eigenaar is van een stad en die moet verdedigen tegen andere spelers">
    <meta name="keywords" content="browsergame, gratis, multiplayer, mmo">
    <meta name=viewport content="width=device-width, initial-scale=1">
    <title><?php echo $title ?></title>
    <link rel="icon" href="img/favicon.png" type="image/png">
    <link rel="stylesheet" type="text/css" href="css/screen.css">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,400italic,700,700italic' rel='stylesheet' type='text/css'>
</head>
<body>
<header>
    <nav>
        <input type="checkbox" id="show_nav"/>
        <label for="show_nav"></label>
        <div id="show_nav"><a href="#show_nav"><div></div></a></div>
        <div id="hide_nav"><a href="#hide_nav"><div></div></a></div>
        <ul>
            <li><a href="index.php"><div>Home</div></a></li>
            <li><a href="#"><div>Registreer</div></a></li>
            <li><a href="#"><div>Forum</div></a></li>
            <li><a href="hulp/"><div>Hulp</div></a></li>
            <li><a href="#"><div>Blog</div></a></li>
        </ul>
    </nav>
</header>
<main>
    <div id="content">