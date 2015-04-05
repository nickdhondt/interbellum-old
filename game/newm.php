<?php
$clearance = 0;   //The minimum required auth_level in order to access this page. NOTE: a user must be this level or higher.

// functions.php is required
// Note: this file is not in the /game/includes directory, but in the /includes directory
require_once "../includes/functions.php";

// This file checks a number of things (is the user logged in?, etc.) and fetches basic information (resourses in the current city, etc.)
include "includes/management.php";

//Declare the error array
$errors = array();

// The user must click the send button
if (isset($_POST["btn_send"])) {
    // All fields are required, none can be empty
    $body = $_POST["txt_body"];
    $thread = $_POST["txt_thread"];
    if (!empty($_POST["txt_recipient"]) && !empty(trim($_POST["txt_body"])) && !empty($_POST["txt_thread"])) {
        // If the sender wants to send to multiple recipients, the recipients are put in an array
        $recipient = explode(",", $_POST["txt_recipient"]);

        // The body cannot be longer than 1000 characters
        if (strlen($body) > 1000) {
            $errors[] = "Uw bericht is te lang (max 1000)";
        }

        // The thread name can't be longer than 64 characters
        if (strlen($thread) > 64) {
            $errors[] = "Het onderwerp is te lang (max 64)";
        }

        // Maximum 20 recipients allowed
        if (count($recipient) > 20) {
            $errors[] = "Er zijn maximaal 20 ontvangers toegestaan";
        }

        $right_users_id = array();
        $wrongusers = array();  //Empty the array with the wrong users
        $correctusers = array();    //Empty the array with correct users
        // We loop through all the potential recipients and put non existing recipients in an array
        foreach ($recipient as $potential_recipient) {
            $recipient_user_id = user_exists(trim($potential_recipient));
            if ($recipient_user_id === false) {
                $wrongusers[] = $potential_recipient;
            }
            else
            {
                // The legit users id's are also put in an array. But not the sender, only the user recipients
                if ($potential_recipient != $user_id) {
                    $right_users_id[] = $recipient_user_id;
                }
                $correctusers[] = trim($potential_recipient);    //Fill the array with correct users
            }
        }


        // If there are more than one wrong recipient, we want to put the last recipient in a different variable
        // This way we can make a correct sentence (see below)
        if (count($wrongusers) > 1) {
            $upperbound = count($wrongusers) - 1;

            $last_wrong_user = $wrongusers[$upperbound];
            unset($wrongusers[$upperbound]);
        }

        // We add the wrong recipients to the errors array
        // This will be displayed later in the script
        if (!empty($wrongusers)) {
            // We add a different error message based on the amount of wrong recipients. Either 1 or more.
            // If $last_wrong_user is empty, this means there is only one wrong recipient (see above)
            if (empty($last_wrong_user)) {
                $errors[] = "De gebruiker " . $wrongusers[0] . " bestaat niet";
            } else {
                $allwrongusers = implode(", ", $wrongusers);
                $errors[] = "De gebruikers " . $allwrongusers . " en " . $last_wrong_user . " bestaan niet";
            }
        }

        $new_thread = false;
        $new_thread_breadcrumb = false;
        $new_message = false;
        // If there are no errors we make a new thread
        if (empty($errors)) {
            // Making a new thread
            $new_thread = make_thread($thread);
            // Make a new message, linked to the just made thread
            $new_message = make_message($new_thread, $user_id, $body);
            // Make a link to the thread for the sender (a breadcrumb)
            // Note: the last parameter is 1, this means the thread is marked a read. But only for the sender, because at this point we only made a breadcrumb for the sender
            $new_thread_breadcrumb = make_thread_breadcrumbs($new_thread, $user_id, 1);

            // Now we make breadcrumbs for all other users
            // Note: there is no third parameter specified in make_thread_breadcrumbs(). This is means it will automatically be set to 0, making the thread unread
            $new_thread_breadcrumbs_others = mass_make_thread_breadcrumbs($new_thread, $right_users_id);
        }

        // If everything is made, we redirect the user the the conversation
        if ($new_thread !== false && $new_thread_breadcrumb !== false && $new_message !== false) {
            header("Location: viewm.php?thread=" . $new_thread);
        }
    } else {
        $errors[] = "U moet een gebruiker, onderwerp en een bericht invullen.";
    }
}

//Show the form
include "includes/pageparts/header.php";

?>
<h1>Nieuw bericht</h1>
<?php

output_errors($errors);

?>
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
    <ul>
        <li>
            <input type="text" name="txt_recipient" placeholder="Ontvanger(s)" value="<?php
            if(!empty($correctusers)) echo trim(implode(", ", $correctusers));
            ?>" required="" />
            <div class="info">
                <img class="info" src="img/info_icon.svg" alt="info" />
            </div>
            <div class="info-hover">
                Plaats een komma tussen meerdere gebruikers. "Obama, Vladimir, Esteban, etc."
            </div>
        </li>
        <li>
            <input type="text" name="txt_thread" placeholder="Onderwerp" value="<?php
            if(!empty($thread)) echo $thread;
            ?>" required="" />
        </li>
        <li>
            <textarea name="txt_body" maxlength="1000" placeholder="Bericht" required=""><?php if(!empty($body)) echo $body; ?></textarea>
        </li>
        <li>
            <input type="submit" name="btn_send" value="Verzenden" />
        </li>
    </ul>
</form>
<?php

include "includes/pageparts/footer.php";

?>
