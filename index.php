<?php

// Todo: include header
session_start();

require_once("resources/config.php");

$user_data = logged_in();

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Interbellum is een gratis browsergame waar iedere speler eigenaar is van een stad en die moet verdedigen tegen andere spelers">
    <meta name="keywords" content="browsergame, gratis, multiplayer, mmo">
    <meta name=viewport content="width=device-width, initial-scale=1">
    <title>Interbellum - MMO (not yet that massive)</title>
    <link rel="icon" href="img/interbellum_icon_32.png" type="image/png">
    <link rel="stylesheet" type="text/css" href="css/screen.css">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,400italic,700,700italic' rel='stylesheet' type='text/css'>
    <script src="script/shared.js"></script>
    <script src="script/application.js"></script>
    <link rel="manifest" href="manifest.json">
</head>
<body>
<div id="int_notif"></div>
<header>
    <nav>
        <div id="hamburger"></div>
        <ul>
            <li>
                <a href="index.php">
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
                                <a href="game"><div>Play</div></a>
                            </li>
                            <li>
                                <a href="account" target="_blank"><div>Account</div></a>
                            </li>
                            <?php
                            if ($user_data["permission_type"] <= 1) {
                            ?>
                            <li>
                                <a href="admin" target="_blank"><div><?php echo $user_data["description"] ?></div></a>
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
    <div id="lander">
        <div>
            <p>Conquer your world</p>
            <div class="href" id="register">Enter early access</div>
        </div>
    </div>
    <div>
        <div>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam tempor velit sed turpis placerat, ut posuere nisi pharetra. Vestibulum aliquam lorem nec libero ornare, sed tristique felis ullamcorper. Vivamus lobortis mollis orci, eget tristique lectus maximus et. Nunc odio tortor, placerat non felis id, tempor interdum sem. Cras blandit arcu ut congue efficitur. Nam fringilla libero ut nunc aliquam aliquam. Integer fermentum diam molestie faucibus blandit. Integer diam augue, sollicitudin vitae diam id, pretium gravida ipsum. Integer scelerisque dolor sed auctor finibus. Aenean dignissim egestas metus, vel convallis diam. Donec efficitur sed nibh at hendrerit.</p>
            <p>Sed nunc ex, venenatis vel aliquam vel, imperdiet sit amet neque. Curabitur mattis risus lacinia elit tristique viverra. Duis iaculis vestibulum ipsum vitae rutrum. Phasellus vel quam sit amet sem placerat feugiat eget at nisl. Sed pharetra, dolor et venenatis ultricies, velit est rutrum ex, sed ornare quam nibh id quam. Ut at neque vitae metus cursus facilisis in quis massa. Ut sagittis quis metus ut suscipit. Donec gravida a lectus at elementum.</p>
            <p>Aenean porttitor orci et neque commodo tempus. Sed lacinia eget ex ut viverra. Pellentesque quis quam egestas, luctus urna tempor, mollis libero. Sed hendrerit sem vulputate massa ultrices, nec volutpat lectus porttitor. Nullam a augue eleifend massa commodo ornare mollis ac nibh. Maecenas imperdiet accumsan tortor ullamcorper mattis. Vestibulum nec purus felis. Aenean blandit lorem ut libero blandit, id placerat mi laoreet. Etiam eget euismod nibh, at scelerisque tortor. Nunc imperdiet eu lacus eget congue. Donec pharetra id ex nec luctus. Nunc faucibus felis eget erat faucibus, non posuere ante laoreet.</p>
        </div>
    </div>
</main>
<footer>
    &copy; Goldenratio Interactive &mdash; [<a href="about/">About</a> &ndash; <a href="#">Goldenratio</a> &ndash; <a href="partners/">Partners</a>]
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
    <div id="preregister_popup">
        <h1>Early access key</h1>
        <form>
            <div id="key_correct"></div>
            <ul class="vertical">
                <li>
                    <input type="text" placeholder="xxxx" name="txt_eak" maxlength="4"/>
                </li>
                <li>
                    <input type="text" placeholder="xxxx" name="txt_eak" maxlength="4"/>
                </li>
                <li>
                    <input type="text" placeholder="xxxx" name="txt_eak" maxlength="4"/>
                </li>
                <li id="lv">
                    <input type="text" placeholder="xxxx" name="txt_eak" maxlength="4"/>
                </li>
                <li>
                    <input type="button" value="Continue" id="btn_eak"/>
                </li>
            </ul>
        </form>
    </div>
    <div id="register_popup">
        <h1>Register</h1>
        <form>
            <ul id="register_form">
                <li>
                    <input type="text" placeholder="Username" name="txt_username" maxlength="16"/>
                    <div class="form_notice"></div>
                </li>
                <li>
                    <input type="password" placeholder="Password" name="txt_pass" maxlength="32"/>
                    <div class="form_notice"></div>
                </li>
                <li>
                    <input type="password" placeholder="Password repeat" name="txt_repeat" maxlength="32"/>
                    <div class="form_notice"></div>
                </li>
                <li>
                    <input type="text" placeholder="Email" name="txt_email" maxlength="254"/>
                    <div class="form_notice"></div>
                </li>
                <li>
                    <label for="terms">Accept terms & conditions</label>
                    <input type="checkbox" id="terms" name="chk_terms"/>
                    <div class="form_notice"></div>
                </li>
                <li class="two_buttons">
                    <input type="button" value="Back" id="btn_register_back"/>
                    <input type="button" value="Register" id="btn_register"/>
                </li>
            </ul>
        </form>
    </div>
</div>
<div id="jef"><div></div></div>
</body>
</html>