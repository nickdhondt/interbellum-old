<?php

session_start();

require_once("../resources/config.php");

$user_data = logged_in();

if ($user_data["permission_type"] > 1 || !$user_data) header("Location: ../");

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Interbellum - <?php echo $user_data["description"] ?> panel</title>
    <script src="../script/shared.js"></script>
    <script src="script/admin.js"></script>
    <meta name=viewport content="width=device-width, initial-scale=1">
    <link rel="icon" href="../img/favicon.png" type="image/png">
    <link rel="stylesheet" type="text/css" href="css/screen.css">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,400italic,700,700italic' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Source+Code+Pro' rel='stylesheet' type='text/css'>
</head>
<body>
<div id="int_notif"></div>
<div class="content">
    <header>
        <div>
            <h1><?php echo $user_data["description"] ?> panel</h1><div id="page">- Early Access</div>
        </div>
    </header>
    <main>
        <div id="shade"></div>
        <nav>
            <div id="hamburger"></div>
            <ul>
                <li>
                    <div id="early_access_nav">Early Access</div>
                </li>
                <li>
                    <div id="users_nav">Users</div>
                </li>
            </ul>
        </nav>
        <div class="panels">
            <div id="early_access">
                <div class="col_one">
                    <div>
                        <h1>Generate keys</h1>
                        <form>
                            <ul>
                                <li>
                                    <div id="keys_label">
                                        <label for="sld_amount">1</label>
                                    </div>
                                </li>
                                <li>
                                    <input type="range" id="sld_amount" min="1" max="10" value="1"/>
                                </li>
                                <li>
                                    <input type="button" value="Generate" name="btn_generate"/>
                                </li>
                            </ul>
                        </form>
                    </div>
                    <div>
                        <h1>New keys</h1>
                        <div id="message">Please generate keys first</div>
                        <div id="new_keys"></div>
                    </div>
                </div>
                <div class="col_two">
                    <div>
                        <h1>Keys</h1>
                    </div>
                </div>
            </div>
            <div id="users">
                <div class="col_one">
                    <div>
                        <h1>Users</h1>
                    </div>
                </div>
                <div class="col_two">

                </div>
            </div>
        </div>
        <div class="cf"></div>
    </main>
</div>
<div id="jef"><div></div></div>
</body>
</html>