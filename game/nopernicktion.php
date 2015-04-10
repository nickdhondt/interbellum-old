<?php
// functions.php is required
// Note: this file is not in the /game/includes directory, but in the /includes directory
require_once "../includes/functions.php";

//Get The user ID.
$user_id = user_logged_in();
if($user_id === false) die;

//Build the current page
include "includes/pageparts/header.php";

//Get the full name
$pernicktion = get_full_name_pernicktion(get_auth_level($user_id));
?>

<h1>Pernicktion Error</h1>
<p>This page cannot be entered by you as <?php echo $pernicktion ?>.</p>

<h2>Return To The Game</h2>
<ul>
    <li>
        <a href="index.php">Your City</a>
    </li>
    <li>
        <a href="messages.php">Your Messages</a>
    </li>
    <li>
        <a href="../index.php">The Homepage</a>
    </li>
</ul>

<?php
include "includes/pageparts/footer.php";
?>