<?php

// functions.php is required
// Note: this file is not in the /game/includes directory, but in the /includes directory
require_once "../includes/functions.php";

// This file checks a number of things (is the user logged in?, etc.) and fetches basic information (resourses in the current city, etc.)
include "includes/management.php";

// Getting the thread "breadcrumbs". Every user that participates in a conversation, get a breakdcrumb (see db -> thr_recipient and thread)
// 1 thread is make, and for each user, a "breadcrumb" is made
$threads = get_thread_breadcrumbs($user_id);
// Seconds since jan 1 1970
$time = time();

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
    // Looping through all the threads (breadcrumbs) a user participates in
    foreach ($threads as $thread) {
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

        // Requesting the thread data, needed for the thread title (which is not included in the breadcrumbs)
        $thread_data = get_thread_data($thread["thr_id"]);
        // Getting the last sent message, to show as little preview
        $last_message =  get_last_message($thread["thr_id"]);
        // Getting the username of the sender of the last message
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
                            <strong<?php echo $unread ?>><?php if($group_message === "groupconversation") { ?><img class="info" src="img/group_icon.svg" alt="Ongelezen bericht" /> <?php } if(!empty($unread)) { ?><img class="info" src="img/newmessage_icon.svg" alt="Ongelezen bericht" /> <?php } echo $thread_data["thr_name"] ?></strong><?php echo " - " . format_elapsed_seconds($seconds_since); ?>
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