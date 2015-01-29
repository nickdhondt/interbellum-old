<?php

// functions.php is required (only for the page title, since the included file "includes/header.php" uses the function html_page_title() which exists in "includes/functions.php"
require_once "includes/functions.php";

// Empty the session superglobal
session_unset();
session_destroy();

// Clearing the remember me cookies
// Note: this method of remembering the user will be replaced with a better simpler system
setcookie("remember_me_hash", "", time()-3600);
setcookie("remember_me_id", "", time()-3600);

// Redirecting the user back to the index page
header ("Refresh: 2; url=\"index.php\"");

// Page header
include "includes/header.php";

?>
<h1>U bent succesvol uitgelogd</h1>
<p>U wordt zo meteen doorgestuurd...</p>
<?php

// Page footer
include "includes/footer.php";

?>