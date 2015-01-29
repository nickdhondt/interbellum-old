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
        $member_data = user_data($member_id, $fields);
        // Add the usernames to an array
        $all_members_array[] = $member_data["username"];
        // If the users id is present in the list of participant who didn't delete this message, the message is not deleted and the deleted variabel is set to false
        if ($member_id === $user_id) {
            $deleted = false;
        }
    }
    // Make a string with all the conversation members, separated by a comma
    $all_members = implode(", ", $all_members_array);

    //
    foreach ($thread_breadcrumbs as $thr_user_id) {
        if ($thr_user_id["user_id"] === $user_id) {
            $authorzation = true;
        }
    }

    if ($authorzation === true && $deleted === false) {
        $fields = array("status" => 1);
        update_breadcrumb($thr_id, $user_id, $fields);
    } else {
        header("Location: messages.php");
        die();
    }

    if (isset($_GET["delete"])) {
        delete_breadcrumb($thr_id, $user_id);
        header("Location: delm.php");
        die();
    }
} else {
    header("Location: messages.php");
    die();
}

if (isset($_POST["btn_reply"])) {
    if (!empty(trim($_POST["txt_reply"]))) {
        $reply = trim($_POST["txt_reply"]);

        if (strlen($reply) > 1000) {
            $errors[] = "Bericht is te lang";
        }

        if (empty($errors)) {
            if ($authorzation !== false) {
                make_message($thr_id, $user_id, $reply);
                $last_mod = time();
                $send_fields = array("status" => 0, "last_mod" => $last_mod);
                $send_own_fields = array("status" => 1, "last_mod" => $last_mod);
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

if (!empty($_GET["thread"]) && $authorzation !== false) {
    if (!empty($_GET["page"])) {
        $page = $_GET["page"];
        if ($page < 0) {
            $page = 0;
        }
    } else {
        $page = 0;
    }
    $thread_data = get_thread_data($thr_id);
    $messages_count = count_all_messages($thr_id);
    $thread_messages = array_reverse(get_message_data($thr_id, $page));
}

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
        if (isset($messages_count)) {
            echo implode(" ", display_pages($page, $messages_count, 15));
        }
        ?>
        </center>
    </div>
    <div class="container">
        <span class="info">
            <img class="info" src="img/group_icon.svg" alt="Gespreksleden"/>
                        <div><?php echo count($all_members_array); ?> gespreksleden: <?php
                echo $all_members;

            ?>
            </div>
            </span>
            <?php

    foreach($thread_messages as $message) {
        $fields = array("username");
        $sender = user_data($message["user_id"], $fields);
        $date_unformatted = date_create($message["senddate"]);
        $date_formmatted = date_format($date_unformatted, "Y/m/d - H:i:s");
        $message_body = format_message(sanitize($message["body"]));

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
    ?>
    </div>
    <div class="container">
        <center>
            <?php
            if (isset($messages_count)) {
                echo implode(" ", display_pages($page, $messages_count, 15));
            }
            ?>
        </center>
    </div>
        <?php
}

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