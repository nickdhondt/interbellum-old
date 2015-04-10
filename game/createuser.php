<?php
$clearance = 3;   //The minimum required auth_level in order to access this page. NOTE: a user must be this level or higher.

require_once "../includes/functions.php";

include "includes/management.php";

include "includes/pageparts/header.php";

//Functions of this page.
function get_pernicktions()
{
    global $connection;
    $result = array();
    $query = mysqli_query($connection, "SELECT auth_id, pernicktion FROM authentication ORDER BY auth_id ASC");
    while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
        $result[] = $row;
    }

    return $result;
}

function make_user($username, $password, $auth_level)
{
    global $connection;
    $query = mysqli_prepare($connection, 'INSERT INTO user (username, password, auth_level) VALUES(?, ?, ?)');
    echo mysqli_error($connection);
    mysqli_stmt_bind_param($query, 'ssi', $username, $password, $auth_level);
    mysqli_stmt_execute($query);
}


$confirm_error = false;
$username = "";
$post_clearance="";
$success = false;

//Check the Postback
if(isset($_POST["submit"]))
{
    //Check if everything is filled in.
    if(!empty($_POST["username"]) && !empty($_POST["password"]) && !empty($_POST["confirm_password"]) && (!empty($_POST["clearance"]) || $_POST["clearance"] === "0"))
    {
        //Check if the password-check is correct.
        if($_POST["password"] !== $_POST["confirm_password"]){
            echo "error";
            $confirm_error = true;
        }

        //Process the order.
        if($confirm_error !== true){
            // Hash the password
            $options = [
                'cost' => 10,
            ];

            $hash = password_hash($_POST["password"], PASSWORD_BCRYPT, $options);

            make_user($_POST["username"], $hash, $_POST["clearance"]);
            $success = true;
        }

    } else {
        //Check what is has been filled in.
        if(isset($_POST["username"])){
            $username = $_POST["username"];
        }
        if(isset($_POST["password"])){
            $password = $_POST["password"];
        }
        if(isset($_POST["confirm_password"])){
            $confirm_password = $_POST["confirm_password"];
        }
        if(isset($_POST["clearance"])){
            $post_clearance = $_POST["clearance"];
        }
        if((isset($password)) && (isset($confirm_password))){
            if($password !== $confirm_password){
                $confirm_error = true;
            }
        }
    }
}

//Get the option List
$options = get_pernicktions();

?>
    <h1>Gebruiker aanmaken</h1>
<?php if ($success === true) echo "<h2>Gebruiker toegevoegd!</h2>"; ?>
    <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
            <ul>
                <li><input type="text" name="username" id="username" value="<?php if(isset($username)) echo $username; ?>" placeholder="Gebruikersnaam"/></li>
                <li><input type="password" name="password" id="password" value="<?php if(isset($password)) echo $password; ?>" placeholder="Wachtwoord"/></li>
                <li><input type="password" name="confirm_password" id="confirm_password" placeholder="Wachtworod herhalen"/>
                    <?php if((isset($confirm_error)) && $confirm_error) echo "Uw ingave komt niet overeen met het opgegeven passwoord." ?></li>
                <li><label for="clearance">Level of Clearance:</label>
                    <select name="clearance" id="clearance">
                        <option disabled>Select the Clearance:</option>
                        <?php
                        foreach($options as $option)
                        {
                            $pernicktion = $option["pernicktion"];
                            $auth_id = $option["auth_id"];
                            ?><option value="<?php echo $auth_id ?>" <?php if(isset($post_clearance)){if($post_clearance === $auth_id) echo " selected";} ?>>
                            <?php echo $pernicktion; ?></option><br/><?php
                        }
                        ?>
                    </select></li>
                <li>            <input type="submit" name="submit" id="submit" value="Maak gebruiker"/>
                </li>
            </ul>
    </form>
<?php

include "includes/pageparts/footer.php";

?>
