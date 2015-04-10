<?php

$clearance = 0;   //The minimum required auth_level in order to access this page. NOTE: a user must be this level or higher.

require_once "../includes/functions.php";

include "includes/management.php";

$errors = array();

if (isset($_GET["change_pwd"])) {
    $errors[] = "Wijzig uw wachtwoord aub";
}

if (!empty($_POST["btn_change"])) {
    if (!empty($_POST["current_password"]) && !empty($_POST["new_password"]) && !empty($_POST["password_repeat"])) {
        $current_password = $_POST["current_password"];
        $new_password = $_POST["new_password"];
        $password_repeat = $_POST["password_repeat"];

        // Get basic user data
        $fields = array("password");
        $user_data = user_data($user_id, $fields);

        if (password_verify($current_password, $user_data["password"]) === true) {
            if (strlen($new_password) < 6) {
                $errors[] = "Het nieuwe wachtwoord moet ten minste 6 karakters lang zijn";
            }
            if (contains_letters_and_number($new_password) === false) {
                $errors[] = "Het nieuwe wachtwoord moet letters en nummers bevatten";
            }
            if ($new_password !== $password_repeat) {
                $errors[] = "Niewe wachtwoord en wachtwoord herhalen moeten gelijk zijn";
            }
            if ($new_password === $current_password) {
                $errors[] = "Het nieuwe wachtwoord mag niet gelijk zijn aan het oude wachtwoord";
            }
        } else {
            $errors[] = "Het huidige wachtwoord is fout";
        }
    } else {
        $errors[] = "Alle velden moeten ingevuld worden";
    }

    if (empty($errors)) {
        $options = [
            'cost' => 10,
        ];

        $hash = password_hash($new_password, PASSWORD_BCRYPT, $options);

        $update_fields = array(
            "password" => $hash,
            "last_pwd_change" => time()
        );

        update_user($user_id, $update_fields);

        header("Location: pwdch.php");
    }
}

include "includes/pageparts/header.php";

?>
    <h1>Instellingen</h1>
    <div class="container">
        <h2>Wachtwoord wijzigen</h2>
        <?php
        output_errors($errors);
        ?>
        <form action="settings.php" method="post" class="invisible">
            <ul>
                <li>
                    <input type="password" placeholder="Huidige wachtwoord" name="current_password"/>
                </li>
                <li>
                    <input type="password" placeholder="Nieuwe wachtwoord" name="new_password"/>
                </li>
                <li>
                    <input type="password" placeholder="Wachtwoord herhalen" name="password_repeat"/>
                </li>
                <li>
                    <input type="submit" value="Wijzigen" name="btn_change"/>
                </li>
            </ul>
        </form>
    </div>
<?php

include "includes/pageparts/footer.php";

?>