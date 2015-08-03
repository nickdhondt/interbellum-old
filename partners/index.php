<?php

// Todo: include header
session_start();

require_once("../resources/config.php");

$user_data = logged_in();

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Interbellum is een gratis browsergame waar iedere speler eigenaar is van een stad en die moet verdedigen tegen andere spelers">
    <meta name="keywords" content="browsergame, gratis, multiplayer, mmo">
    <meta name=viewport content="width=device-width, initial-scale=1">
    <title>Partners - Interbellum</title>
    <link rel="icon" href="../img/interbellum_icon_32.png" type="image/png">
    <link rel="stylesheet" type="text/css" href="../css/screen.css">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,400italic,700,700italic' rel='stylesheet' type='text/css'>
    <script src="../script/shared.js"></script>
    <script src="../script/application.js"></script></head>
<body>
<div id="int_notif"></div>
<header>
    <nav>
        <div id="hamburger"></div>
        <ul>
            <li>
                <a href="../index.php">
                    <div>
                        Home
                    </div>
                </a>
            </li>
            <li>
                <a href="#">
                    <div>
                        Blog
                    </div>
                </a>
            </li>
            <li>
                <a href="#">
                    <div>
                        Wiki
                    </div>
                </a>
            </li>
            <li class="accent" id="options_container">
                <?php if ($user_data) { ?>
                    <div>
                    <span class="href" id="options">
                        Welcome, <?php echo $user_data["username"]; ?>
                    </span>
                    </div>
                    <div id="options_panel">
                        <ul>
                            <li>
                                <a href="../game"><div>Play</div></a>
                            </li>
                            <li>
                                <a href="../account" target="_blank"><div>Account</div></a>
                            </li>
                            <?php
                            if ($user_data["permission_type"] <= 1) {
                                ?>
                                <li>
                                    <a href="../admin" target="_blank"><div><?php echo $user_data["description"] ?></div></a>
                                </li>
                            <?php
                            }
                            ?>
                            <li>
                                <div id="signout">Sign out</div>
                            </li>
                        </ul>
                    </div>
                <?php } else { ?>
                    <div>
                    <span class="href" id="login">
                        Sign in
                    </span>
                    </div>
                <?php } ?></li>
        </ul>
        <div class="cf"></div>
    </nav>
</header>
<main>
    <div class="accent_region">
        <div>
            <h1>Partners:</h1>
            <ul>
                <li><a href="http://estebandenis.ddns.net">Wheatley HomeSite <small>(http://estebandenis.ddns.net/)</small></a></li>
            </ul>
        </div>
    </div>
</main>
<footer>
    &copy; Goldenratio Interactive &mdash; [<a href="../about/">About</a> &ndash; <a href="#">Goldenratio</a> &ndash; <a href="../partners/">Partners</a>]
</footer>
<div id="shade">
    <div id="signin_popup">
        <h1>Sign in</h1>
        <div id="signin_notice"></div>
        <form>
            <ul>
                <li>
                    <input type="text" placeholder="Username" name="txt_l_username"/>
                </li>
                <li>
                    <input type="password" placeholder="Password" name="txt_l_password"/>
                </li>
                <li>
                    <input type="checkbox" id="remember" name="chk_l_remember"/>
                    <label for="remember">Remember me</label>
                </li>
                <li>
                    <input type="button" value="Sign in" id="btn_login"/>
                </li>
            </ul>
        </form>
    </div>
</div>
<div id="jef"><div></div></div>
</body>
</html>