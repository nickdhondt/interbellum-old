<?php

// function.php is required
require_once "includes/functions.php";

// Declare an empty errors array
$errors = array();

// For some reason the cookie -int-username is set to "root" if $username is not set to "". Like WTF? Why?
$username = "";

//Smartform Functionality
    //1. Get, if present, the saved cookie data.
    if(!empty($_COOKIE["-int-username"]))
    {
        $username = sanitize($_COOKIE["-int-username"]);
    }
    if(!empty($_COOKIE["-int-remember_me"]))
    {
        $remember_me_checkstate = sanitize($_COOKIE["-int-remember_me"]);
    }

// Check is the "login" button has been clicked
if (!empty($_POST["btn_login"])) {
    // If the user hasn't entered a username, an error will be added to the errors array
    // In the other case the entered username is ut in the username variable
    if (!empty($_POST["txt_username"])) {
        $username = $_POST["txt_username"];
    } else {
        $errors[] = "Vul uw gebruikersnaam in";
        $username = "";
    }

    // If the user hasn't entered a password, an error will be added to the errors array
    // In the other case the entered password is ut in the password variable
    if (!empty($_POST["txt_password"])) {
        $password = $_POST["txt_password"];
    } else {
        $errors[] = "Vul uw wachtwoord in";
        $password = "";
    }

    //Get the checkstate of the "remember_me" checkbox
    if (!empty($_POST["chk_remember"]) && $_POST["chk_remember"] === "remember_check")
    {
        $remember_me_checkstate = "checked";
    }
    else
    {
        $remember_me_checkstate = "";
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
            // the fields array determines which fields need to be requested to the database
            // user_data will request these fields an return an array with the data (or an error)
            $fields = array("username", "password");
            $user_data = user_data($user_id, $fields);
            // verifying the entered password using the php password API
            // the hashed password are compared
            if (password_verify($password, $user_data["password"]) === true) {
                //
                setcookie("-int-username", "" , time() - 3600);
                setcookie("-int-remember_me", "" , time() - 3600);


                // If this check succeeded the user is logged in by putting his user id in a session
                // Note: there is no session_start() function on this page, this function is included in the functions.php file (see top of this file)
                $_SESSION["user_id"] = $user_id;
                // If the "remember me" checkbox is ticked the following script is executed
                if ($remember_me_checkstate == "checked")
                {
                    // Take the first three characters from the username and convert it to the integer form
                    $username_part = intval(substr($username, 0, 3));
                    // Make a sha512 hash of this converted part of the username concatenated to the password
                    $remember_hash = hash("sha512", $username_part . $password);

                    // Set two cookies containing the user id and the hash for a year
                    setcookie("-int-remember_my_name", $user_id, time() + (60 * 60 * 24 * 365));
                    setcookie("-int-remember_me_hash", $remember_hash, time() + (60 * 60 * 24 * 365));

                    $remember_fields = array(
                        "remember_hash" => $remember_hash
                    );

                    // Now put this hash in the database. user_logged_in() will use this value to check if the user has chosen to remember the credentials
                    update_user($user_id, $remember_fields);
                }

                // Empty the variables
                $username = "";
                $remember_me_checkstate = "";
            } else {
                // If the given password is wrong, a error is thrown
                $errors[] = "Het wachtwoord is fout";
            }
        }
    }
}

//Smartform functionality
    //2. Enter the given values in a coockie.
    //The values used by the smartform functionality have passed the if-structures on top of this page. Therefore, they don't need additional safety checks.
    //
    if(!empty($username))   //If an username was given, remember it.
    {
        setcookie("-int-username", $username);
    }
    else
    {
        setcookie("-int-username", "", time() - 3600);  //In case the user left this blank, the previous value ought to be deleted.
    }

    if(!empty($remember_me_checkstate))    //If the remember_me checkbox was checked, remember it.
    {
        setcookie("-int-remember_me", $remember_me_checkstate);
    }
    else
    {
        setcookie("-int-remember_me", "", time() - 3600);    //In case the user didn't check this, the previous value ought to be deleted.
    }

// Page header (including html head, etc)
include "includes/header.php";

?>
<aside>
<?php

// Main content
$user_logged_in = user_logged_in();

// Checking if the user is logged in
// The user_logged_in() function also checks if the user has checked the "remember me" checkbox
// Note: this method of remembering the user will be replaced with a better simpler system
if ($user_logged_in ===  false) {
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

?>
    </aside>
    <div id="container">
        <h1>Interbellum</h1>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed orci tortor, bibendum a ultricies et, venenatis non lorem. Nulla facilisi. Vivamus imperdiet neque ut facilisis mattis. Suspendisse placerat gravida velit, eget mollis lorem consequat ac. Cras fermentum, arcu quis vestibulum ultrices, tortor urna commodo turpis, sit amet faucibus velit urna vitae libero. In sit amet fermentum leo, eu mollis libero. Fusce non purus faucibus, efficitur lectus eu, pellentesque velit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
        <div id="register_area">
            <div id="register_button">
                <a href="#">
                    <div>Registreer</div>
                </a>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
        <?php

// Page footer
include "includes/footer.php";

?>