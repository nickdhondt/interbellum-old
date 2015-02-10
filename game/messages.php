<?php

// functions.php is required
// Note: this file is not in the /game/includes directory, but in the /includes directory
require_once "../includes/functions.php";

// This file checks a number of things (is the user logged in?, etc.) and fetches basic information (resources in the current city, etc.)
include "includes/management.php";

// Getting the thread "breadcrumbs". Every user that participates in a conversation, get a breadcrumb (see db -> thr_recipient and thread)
// 1 thread is make, and for each user, a "breadcrumb" is made
$threads = get_thread_breadcrumbs($user_id);
// Seconds since jan 1 1970
$time = time();

// This array will hold the ids of the thread the user is a recipient in/the user has a breadcrumb from
$thr_ids = array();

// Loop through all the breadcrumbs and place the thread id in the array
foreach($threads as $thread_id) {
    $thr_ids[] = $thread_id["thr_id"];
}

include "includes/pageparts/header.php";

?>
    <h1>Inbox</h1>
    <div class="button">
        <a href="newm.php">
            <div>
                Nieuw bericht
            </div>
        </a>
    </div>
    <div class="container">
<?php

if (!empty($threads)) {
    // Get all the threadnames and ids
    $thread_data = mass_get_thread_data($thr_ids);

    // Looping through all the threads (breadcrumbs) a user participates in
    foreach ($threads as $thread) {
        $last_message_ids = array();

        // Initially a thread is set as read, unless prover read
        $unread = "";
        $unread_tooltip = "";
        // If the status is 0, meaning the message is unread, all important variables will be set to their unread variant
        if ($thread["status"] == 0) {
            $unread = " class=unread";
            $unread_tooltip = "Ongelezen bericht";
        }

        // Calculate how long the last message has been sent by subtracting the time it has been send of the current time
        $seconds_since = $time - $thread["last_mod"];

        // Well loop through the array with the data of all the threads
        $thr_name = "";
        foreach($thread_data as $find_thr_name) {
            // This is a foreach inside a foreach
            // If we find the id of the element we are looping through (first foreach) and it matches the id of the second foreach element,
            // The thread name is put in the $thr=_name variable and printed later
            if ($find_thr_name["id"] === $thread["thr_id"]) {
                $thr_name = $find_thr_name["thr_name"];
            }
        }

        // Get the last message of the thread link we are constructing
        $last_message =  get_last_message($thread["thr_id"]);

        // Get the username of the user who has sent the last message in the thread we are constructing
        $fields = array("username");
        $last_sender = user_data($last_message["user_id"], $fields);

        // Format and sanitize the last message
        // Preventing XSS, etc. and substr to 75 characters
        $last_message_formatted = sanitize(format_message($last_message["body"], "PREVIEW"));
        // Determine if there are more than 2 correspondents, if there are, this is a group messaeg
        $recipients = thread_recipients($thread["thr_id"]);
        $group_message = "singleconversation";
        if (count($recipients) > 2) {
            $group_message = "groupconversation";
        }

        // Now it's just a matter of putting all the pieces together
        ?>
        <div class="messagecontainer" title="<?php echo $unread_tooltip ?>">
            <div class="<?php echo $group_message ?>">
                <a href="viewm.php?thread=<?php echo $thread["thr_id"]; ?>&page=0#bottom">
                    <div class="conversation">
                        <section>
                            <strong<?php echo $unread ?>><?php if($group_message === "groupconversation") { ?><img class="info" src="img/group_icon.svg" alt="Ongelezen bericht" /> <?php } if(!empty($unread)) { ?><img class="info" src="img/newmessage_icon.svg" alt="Ongelezen bericht" /> <?php } echo $thr_name ?></strong><?php echo " - " . format_elapsed_seconds($seconds_since); ?>
                        </section>
                        <section class="preview">
                            <em><?php echo $last_sender["username"] ?></em><?php echo ": " . $last_message_formatted; ?>

                        </section>
                    </div>
                </a>
            </div>
        </div>
        <?php
    } ?>
    <?php
} else {
    ?>
        <em>Je hebt geen berichten</em>
    <?php
}

?>
    </div>
    <?php

include "includes/pageparts/footer.php";

?>