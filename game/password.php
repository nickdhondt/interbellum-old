<?php
$clearance = 3;   //The minimum required auth_level in order to access this page. NOTE: a user must be this level or higher.
require_once "../includes/functions.php";

//This page may only be accessed by a selected group of users.
$auth_level = get_auth_level($user_id);
validate_clearance($auth_level, $clearance);

//Check whether the user has logged in prior to let him change his password.
if (count_all_users() > 0) {
    $user_id = user_logged_in();
    if ($user_id === false) {
        header("Location: ../index.php");
        die();
    }
}


$password = "";
$hash = "";
$errors = array();

if (!empty($_POST)) {
    $password = $_POST["txt_password"];

    $options = [
        'cost' => 10,
    ];

    if (!empty($password)) {
        if (strlen($password) < 6) {
            $errors[] = "Het wachtwoord moet minstens uit 6 karakters bestaan";
        } else {
            $hash = password_hash($_POST["txt_password"], PASSWORD_BCRYPT, $options);
        }
    }
}

?>
<!doctype html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Wachtwoord genereren</title>
    <style>
        .hash {
            border: dashed black;
            width: 650px;
            padding: 10px;
            text-align: center;
            margin-top: 10px;
        }

        body {
            width: 670px;
            margin: 10px auto 0 auto;
        }

        ul {
            margin: 0;
            padding: 0;
            list-style: none;
        }
    </style>
</head>
<body>
<div>
    <form action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post">
        <input type="text" placeholder="Wachtwoord" name="txt_password" value="<?php echo $password ?>" required>
        <input type="submit" value="Genereer">
    </form>
</div>
<div class="hash">
<?php

if (!empty($errors)) {
        ?>
    <ul>
    <?php
        foreach ($errors as $error) {
            ?>
        <li>
    <?php
            echo $error;

    ?>
        </li>
            <?php
        }
    ?>
    </ul>
        <?php
    } elseif (empty($errors) && empty($password)) {
        echo "Vul een wachtwoord in en klik op \"genereer\"";
    } else {
        echo $hash;
    }

    ?>
</div>
</body>
</html>