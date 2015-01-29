<?php

// functions.php is required
require "../includes/functions.php";

// If the user is logged in, he will be redirected to initial.php
// In the other case, the user will be redirected the login page
// Note: this is /game/index.php, not /index.php
if (user_logged_in() !== false) {
    header("Location: initial.php");
} else {
    header("Location: ../index.php");
}