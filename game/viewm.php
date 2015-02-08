<?php

// functions.php is required
// Note: this file is not in the /game/includes directory, but in the /includes directory
require_once "../includes/functions.php";

// This file checks a number of things (is the user logged in?, etc.) and fetches basic information (resourses in the current city, etc.)
include "includes/management.php";

// Empty errors array. Will contain errors, if any
$errors = array();
// The authorization is set to false
// Later in the script, when it is checked if the user is a participant in this conversation, this will be set to true
$authorzation = false;

// Check if there is a thread specified in the querystring
// "thread" is to show a thread, "delete" to delete
if (!empty($_GET["thread"]) || !empty($_GET["delete"])) {
    // If delete is set, the thr_id will always be what is in the querystring for delete
    // thread will be overwritten by delete
    if (isset($_GET["thread"])) {
        $thr_id = $_GET["thread"];
    }
    if (isset($_GET["delete"])) {
        $thr_id = $_GET["delete"];
    }

    // Get all the participants for this conversation
    $thread_breadcrumbs = get_user_id_from_breadcrumbs($thr_id);
    // Get only the id from recipients who did not delete this thread
    $all_members_id = thread_recipients($thr_id);
    $all_members_array = array();

    // Deleted is set to true, it will be false if the user didn't delete the message
    // This is checked later in the script
    $deleted = true;
    foreach ($all_members_id as $member_id) {
        // Get the usernames of the members of this conversation
        // Note: only the members who didn't delete the conversation
        $fields = array("username");
        $member_data = user_data($member_id["user_id"], $fields);
        // Add the usernames to an array
        $all_members_array[] = $member_data["username"];
        // If the users id is present in the list of participant who didn't delete this message, the message is not deleted and the deleted variabel is set to false
        if ($member_id["user_id"] === $user_id) {
            $deleted = false;
        }
    }

    // Make a string with all the conversation members, separated by a comma
    $all_members = implode(", ", $all_members_array);

    // We loop through all the users (including users that deleted the message)
    foreach ($thread_breadcrumbs as $thr_user_id) {
        // If the user id is found in the list of recipients of this message, the user is authorized to open the message
        if ($thr_user_id["user_id"] === $user_id) {
            // Authorization set to true, previously false
            $authorzation = true;
        }
    }

    // If the user is authorized, and has not deleted the massage, the user will noy be redirected to his inbox
    // The status of his breadcrumb (thr_recipient table in database contains all the "breadcrumbs") is set to 1
    // Which means the user has read the message
    if ($authorzation === true && $deleted === false) {
        $fields = array("status" => 1);
        update_breadcrumb($thr_id, $user_id, $fields);
    } else {
        header("Location: messages.php");
        die();
    }

    // Make an empty array that will hold all the id's of users who have opened this thread
    $read_by_id = array();

    // Loop through all the breadcrumbs and determine if the user has opened the thread or not
    foreach($thread_breadcrumbs as $read_by_data) {
        // If the status is 1 or 2, this means the user has opened the thread. It is read or read and deleted
        if ($read_by_data["status"] == 2 || $read_by_data["status"] == 1) {
            // Add the id to the array if it has been read or deleted
            $read_by_id[] = $read_by_data["user_id"];
        }
    }

    if (!in_array($user_id, $read_by_id)) {
        $read_by_id[] = $user_id;
    }

    // If the user has clicked delete ("verwijderen"), he will be redirected to the same file but with "delete" in the querystring
    // The message is deleted and the user is redirected to "delm.php", which shows a message confirming the conversation has been deleted
    if (isset($_GET["delete"])) {
        delete_breadcrumb($thr_id, $user_id);
        header("Location: delm.php");
        die();
    }
} else {
    // If the user does noet visit this page correctly (delete/thread should be in the querystring), he is redirected back to the inbox
    header("Location: messages.php");
    die();
}

// The user must click the reply button to send a reply
if (isset($_POST["btn_reply"])) {
    // There must also be a message, the body of the message can't be empty or just filled with spaces
    // If the user hasn't entered a message body, a error will be sent back
    if (!empty(trim($_POST["txt_reply"]))) {
        $reply = trim($_POST["txt_reply"]);

        // The maximum length of a message is 1000 characters
        if (strlen($reply) > 1000) {
            $errors[] = "Bericht is te lang";
        }

        // If there are no errors and the user is authorized to send the message, the message will be sent
        if (empty($errors)) {
            if ($authorzation !== false) {
                // A new message is inserted into the db
                make_message($thr_id, $user_id, $reply);
                // The "last modified" time is set to now for aal recipients (including the sender)
                // For the user who has sent the message, the status is set to 1, meaning it is read
                // For the other recipients, the status is set to 0, meaning the thread breadcrumb is not read
                $last_mod = time();
                $send_fields = array("status" => 0, "last_mod" => $last_mod);
                $send_own_fields = array("status" => 1, "last_mod" => $last_mod);
                // Looping through all the users (including the ones who deleted the message)
                // If the user id (in the session -> $user_id) is equal to the id found in the breadcrumbs, the breadcrumb is updated and the status is 1 (read by user)
                // In the other case, the user id is not equal, the breadcrumb is also updated, but the status is 0 (not read)
                foreach ($thread_breadcrumbs as $thr_user_id) {
                    if ($thr_user_id["user_id"] !== $user_id) {
                        update_breadcrumb($thr_id, $thr_user_id["user_id"], $send_fields);
                    } else {
                        update_breadcrumb($thr_id, $thr_user_id["user_id"], $send_own_fields);
                    }
                }
            }
        }
    } else {
        $errors[] = "U moet een bericht invullen";
    }
}

// A conversation is divided in parts of 15 messages (= a page)
if (!empty($_GET["thread"]) && $authorzation !== false) {
    // If no page is specified in the querystring, the page is automatically set to 0 (meaning the first page)
    if (!empty($_GET["page"])) {
        $page = $_GET["page"];
        if ($page < 0) {
            $page = 0;
        }
    } else {
        $page = 0;
    }

    // The thread data (thread name only currently) is selected from the database
    $thread_data = get_thread_data($thr_id);
    // All messages in the thread are counted
    $messages_count = count_all_messages($thr_id);
    // All messages in the above specified page are selected (body of message, sender id and the date)
    $thread_messages = array_reverse(get_message_data($thr_id, $page));

    // Make a array with all the user id's (including the users who deleted the conversation)
    // Note: this is an array with all id's (not multidimensional), not an array with arrays in which the id is placed (multidimensional) like "$thread_breadcrumbs"
    $all_user_ids = array();
    foreach ($thread_breadcrumbs as $user_id_recipient) {
        $all_user_ids[] = $user_id_recipient["user_id"];
    }

    // Get all the usernames of all the listed id's we put in the single dimensional array just above
    $fields_need_username = array("id", "username");
    $usernames_recipients = mass_user_data($all_user_ids, $fields_need_username);

    // Make an empty array that will hold the usernames of the users who have opened this thread
    $read_by = array();

    // Loop through all the id who have opened this thread
    foreach ($read_by_id as $need_username_for_read) {
        // Loop through all the recipents and compare the user id with the user id of the user whi has opened the thread
        // If they are equal, the username is added to the $read_by array
        foreach ($usernames_recipients as $username_data) {
            if ($username_data["id"] === $need_username_for_read) {
                $read_by[] = $username_data["username"];
            }
        }
    }
}

// Include the header
include "includes/pageparts/header.php";

?>
    <script>
        function alertDelete () {
            delete_button = document.getElementById("delete");
            if (window.confirm("Wilt u het bericht verwijderen?")) {
                window.location = "viewm.php?delete=<?php echo $thr_id; ?>";
            }
        }
    </script>
    <?php

if ($authorzation === true && !empty($_GET["thread"])) {
    // If the user is authorized, the thread name is shown
    // The messages are shown further in the script
    echo "<h1>" . $thread_data["thr_name"] . "</h1>";
    ?>
    <div class="button">
        <a href="#" onclick="alertDelete()">
            <div>
                Verwijderen
            </div>
        </a>
    </div>
    <div class="container">
        <center>
        <?php

        // This feature is unfinished
        if (isset($messages_count)) {
            echo display_pages($page, $messages_count, 15, $thr_id);
        }
        ?>
        </center>
    </div>
    <div class="container">
        <div class="info">
            <img class="info" src="img/group_icon.svg" alt="Gespreksleden"/>
        </div>
        <div class="info-hover"><?php echo count($all_members_array); ?> gespreksleden: <?php
            echo $all_members;

            ?>
        </div>
        <?php

    // Loop through all the messages in a page
    foreach($thread_messages as $message) {

        // Get the username of the sender of this message in the previously constructed "$usernames_recipients"
        foreach($usernames_recipients as $username) {
            if (in_array($message["user_id"], $username)) {
                $sender["username"] = $username["username"];
                break 1;
            }
        }

        // Format the date and the messaeg body
        // Also sanitize the message body to prevent XSS (htmlentities, etc)
        $date_unformatted = date_create($message["senddate"]);
        $date_formmatted = date_format($date_unformatted, "Y/m/d - H:i:s");
        $message_body = format_message(sanitize($message["body"]));

        // If the message has been sent by the user, it is shown in a different style
        if ($message["user_id"] === $user_id) { ?>
            <div class="ownmessage"><em><?php echo $sender["username"] . " &mdash; " . $date_formmatted ?></em></div>
            <section class="ownmessage"><?php echo $message_body ?></section>
        <?php
        } else {
            ?>
            <div class="othermessage"><em><?php echo $sender["username"] . " &mdash; " . $date_formmatted ?></em></div>
            <section class="othermessage"><?php echo $message_body ?></section>
        <?php
        }
    }

        // Count the users who read this conversation
        $total_read_count_users = count($read_by);
        // The maximum amount of usernames that are printed, the others can be viewed by hovering
        $show_read_users = 6;

        // If the number of users who read the conversation is greater than the max amount that is set above, "..." is added to the end of the string and the string only contains 6 users
        if ($total_read_count_users > $show_read_users) {
            // Make a string off all the users. This will be shown when a user hovers over the div
            $title_read_hint_string = implode(", ", $read_by);

            // Cut the last user off the array (max $show_read_users users)
            $read_by = array_slice($read_by, 0, $show_read_users);
            // Add "..."
            $read_by[] = "...";

            // Implode the array, this will be displayed on the screen.
            $read_by_string = implode(", ", $read_by);
        } else {
            // Implode the complete array, the complete string will also be shown when hovering
            $read_by_string = implode(", ", $read_by);
            $title_read_hint_string = $read_by_string;
        }

        ?>
        <div id="read" title="<?php echo $title_read_hint_string; ?>">
        <?php

        echo "Gezien door: " . $read_by_string;
        ?>
        </div>
        <div id="clear"></div>
    </div>
    <div class="container">
        <center>
            <?php

            // This feature is unfinished
            if (isset($messages_count)) {
                echo display_pages($page, $messages_count, 15, $thr_id, true);
            }
            ?>
        </center>
    </div>
        <?php
}

// Errors are outputted
output_errors($errors);

?>
<form action="<?php echo $_SERVER["REQUEST_URI"] ?>#bottom" method="post">
    <ul>
        <li>
            <textarea name="txt_reply" placeholder="Bericht" maxlength="1000" cols="70" rows="12"></textarea>
        </li>
        <li>
            <input type="submit" name="btn_reply" value="Antwoorden" />
        </li>
    </ul>
</form>
        <div id="bottom"></div>
    <?php

include "includes/pageparts/footer.php";

?>