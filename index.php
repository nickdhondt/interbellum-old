<?php

// function.php is required
require_once "includes/functions.php";

// Declare an empty errors array
$errors = array();

// Check is the "login" button has been clicked
if (!empty($_POST["btn_login"])) {
    // If the user hasn't entered a username, an error will be added to the errors array
    // In the other case the entered username is ut in the username variable
    if (!empty($_POST["txt_username"])) {
        $username = $_POST["txt_username"];
    } else {
        $errors[] = "Vul uw gebruikersnaam in";
    }

    // If the user hasn't entered a password, an error will be added to the errors array
    // In the other case the entered password is ut in the password variable
    if (!empty($_POST["txt_password"])) {
        $password = $_POST["txt_password"];
    } else {
        $errors[] = "Vul uw wachtwoord in";
    }

    // If the script hasn't generated errors, the entered username and password will be checked
    if (empty($errors)) {
        // The username is checked using the user_exists function
        // This function will return the user id or false, depending on the input
        // If the user does not exists, a error message is added the the errors array
        $user_id = user_exists($username);
        if ($user_id === false) {
            $errors[] = "Gebruiker niet gevonden";
        } else {
            // the fields array detemines which fields need to be requested to the database
            // user_data will request these fields an return an array with the data (or an error)
            $fields = array("username", "password");
            $user_data = user_data($user_id, $fields);
            // verifying the entered password using the php password API
            // the hashed password are compared
            if (password_verify($password, $user_data["password"]) === true) {
                // If this check succeeded the user is logged in by putting his user id in a session
                // Note: the is no session_start() function on this page, this funtion is included in the functions.php file (see top of this file)
                $_SESSION["user_id"] = $user_id;
                // "remember me" checkbox
                // Note: this method of remembering the user will be replaced with a better simpler system
                if (isset($_POST["chk_remember"]) && $_POST["chk_remember"] === "remember_check") {
                    $remember_hash = hash("sha512", rand() . $username);
                    if (!empty($_COOKIE["remember_me_id"])) {
                        $session_id = $_COOKIE["remember_me_id"];
                        setcookie("remember_me_hash", $remember_hash, time() + (60 * 60 * 24 * 365));
                        $cookie_fields = array(
                            "remember_hash" => $remember_hash
                        );
                        update_session($session_id, $cookie_fields);
                    } else {
                        $remember_me_id = make_session($user_id, $remember_hash, $_SERVER["HTTP_USER_AGENT"]);
                        setcookie("remember_me_hash", $remember_hash, time() + (60 * 60 * 24 * 365));
                        setcookie("remember_me_id", $remember_me_id, time() + (60 * 60 * 24 *365));
                    }
                }
            } else {
                // If the given password is wrong, a error is thrown
                $errors[] = "Het wachtwoord is fout";
            }
        }
    }
}

// Page header (including html head, etc)
include "includes/header.php";

// Main content
$user_logged_in = user_logged_in();

// Checking if the user is logged in
// The user_logged_in() function also checks if the user has checked the "remember me" checkbox
// Note: this method of remembering the user will be replaced with a better simpler system
if ($user_logged_in === false) {
    // If the user is not logged in, the login form is shown
    // The loginform.php file also deals with possible errors (output_errors() function)
    include "includes/loginform.php";
} else {
    // If the user is logged in the username is determined by his user id
    // The fields array determines which fields need to be requested to the db
    $fields = array("username");
    $user_data = user_data($user_logged_in, $fields);
    echo "<h1>Welkom " . sanitize($user_data["username"]) . "</h1>";

    // The list with options a user has once logged in
    include "includes/loggedin.php";
}

// Page footer
include "includes/footer.php";

?>