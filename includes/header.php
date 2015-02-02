<?php

// This functions determines what the <title></title> should be (see below and see html_page_title() in "functions.php")
$title = html_page_title($_SERVER["SCRIPT_FILENAME"]);

?>
<!doctype html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Interbellum is een gratis browsergame waar iedere speler eigenaar is van een stad en die moet verdedigen tegen andere spelers">
    <meta name="keywords" content="browsergame, Tribal Wars, gratis, multiplayer, mmo">
    <title><?php echo $title ?></title>
    <link rel="stylesheet" type="text/css" href="css/screen.css">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,400italic,700,700italic' rel='stylesheet' type='text/css'>
</head>
<body>
<header></header>
<main>
