<?php

require_once "../includes/functions.php";

include "includes/management.php";

$errors = array();

if (isset($_POST["btn_send"])) {
    if (!empty($_POST["txt_recipient"]) && !empty($_POST["txt_body"]) && !empty($_POST["txt_thread"])) {
        $recipient = explode(",", $_POST["txt_recipient"]);
        $body = $_POST["txt_body"];
        $thread = $_POST["txt_thread"];

        if (strlen($body) > 1000) {
            $errors[] = "Uw bericht is te lang (max 1000)";
        }

        if (strlen($thread) > 64) {
            $errors[] = "Het onderwerp is te lang";
        }

        $wrongusers = array();
        foreach ($recipient as $potential_recipient) {
            $recipient_user_id = user_exists(trim($potential_recipient));
            if ($recipient_user_id === false) {
                $wrongusers[] = $potential_recipient;
            }
        }

        if (!empty($wrongusers)) {
            if (count($wrongusers) === 1) {
                $errors[] = "De gebruiker " . $wrongusers[0] . " bstaat niet";
            } else {
                $allwrongusers = implode(", ", $wrongusers);
                $errors[] = "De gebruikers " . $allwrongusers . " bestaan niet";
            }
        }

        $new_thread = false;
        $new_thread_breadcrumb = false;
        $new_message = false;
        if (empty($errors)) {
            $new_thread = make_thread($thread);
            $new_message = make_message($new_thread, $user_id, $body);
            $new_thread_breadcrumb = make_thread_breadcrumbs($new_thread, $user_id, 1);
            foreach ($recipient as $actual_recipient) {
                $actual_recipient_id = user_exists(trim($actual_recipient));
                if ($actual_recipient_id !== $user_id) {
                    make_thread_breadcrumbs($new_thread, $actual_recipient_id);
                }
            }
        }

        if ($new_thread !== false && $new_thread_breadcrumb !== false && $new_message !== false) {
            header("Location: viewm.php?thread=" . $new_thread);
        }
    } else {
        $errors[] = "U moet een gebruiker, onderwerp en een bericht invullen.";
    }
}

include "includes/pageparts/header.php";

?>
<h1>Nieuw bericht</h1>
<?php

output_errors($errors);

?>
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
    <ul>
        <li>
            <input type="text" name="txt_recipient" placeholder="Ontvanger(s)" />
            <span class="info">
                <img class="info" src="img/info_icon.svg" alt="info" />
                <div>
                    Plaats een komma tussen meerdere gebruikers. "gebruiker een, gebruiker twee, etc."
                </div>
            </span>
        </li>
        <li>
            <input type="text" name="txt_thread" placeholder="Onderwerp" />
        </li>
        <li>
            <textarea name="txt_body" maxlength="1000" placeholder="Bericht"></textarea>
        </li>
        <li>
            <input type="submit" name="btn_send" value="Verzenden" />
        </li>
    </ul>
</form>
<?php

include "includes/pageparts/footer.php";

?>