<?php
// functions.php is required
// Note: this file is not in the /game/includes directory, but in the /includes directory
require_once "../includes/functions.php";

include "includes/management.php";


//Build the current page
include "includes/pageparts/header.php";

//Get the full name
$pernicktion = get_full_name_pernicktion(get_auth_level($user_id));
?>

<h1>Pernicktion error</h1>
<p>De opgevraagde pagina kan niet bekeken worden als "<?php echo $pernicktion ?>".</p>

<h2>Ga terug naar:</h2>
<ul>
    <li>
        <a href="city.php">Je stad</a>
    </li>
    <li>
        <a href="messages.php">Je Inbox</a>
    </li>
    <li>
        <a href="../index.php">De Homepagina</a>
    </li>
</ul>

<?php
include "includes/pageparts/footer.php";
?>