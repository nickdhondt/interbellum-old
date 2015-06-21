<?php

$sent_username =  file_get_contents('php://input');

$proposed_usernames = array("legal" => true, "proposed_usernames" => array($sent_username, "test"));

echo json_encode($proposed_usernames);