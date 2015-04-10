<?php
require_once "../includes/database/connect.php";
$confirm_error = false;
$username = "";
$clearance="";
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
            $clearance = $_POST["clearance"];
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
<!doctype html>
<html lang="nl-be">
<head>
    <meta charset="UTF-8">
    <title>Create User Page</title>
    <link rel="icon" href="../img/favicon.png" type="image/png">
    <link rel="stylesheet" type="text/css" href="css/game.css">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,400italic,700,700italic' rel='stylesheet' type='text/css'/>
</head>
<body>
<main>
    <?php if ($success === true) echo "Gebruiker toegevoegd!"; ?>
    <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
        <div>
            <h1>Create a User</h1>
        </div>
        <div>
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" value="<?php if(isset($username)) echo $username; ?>"/><br/>
        </div>
        <div>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" value="<?php if(isset($password)) echo $password; ?>"/><br/>
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" name="confirm_password" id="confirm_password"/>
            <?php if((isset($confirm_error)) && $confirm_error) echo "Uw ingave komt niet overeen met het opgegeven passwoord." ?><br/>
        </div>
        <div>
            <label for="clearance">Level of Clearance:</label>
            <select name="clearance" id="clearance">
                <option disabled>Select the Clearance:</option>
                <?php
                foreach($options as $option)
                {
                    $pernicktion = $option["pernicktion"];
                    $auth_id = $option["auth_id"];
                    ?><option value="<?php echo $auth_id ?>" <?php if(isset($clearance)){if($clearance === $auth_id) echo " selected";} ?>>
                        <?php echo $pernicktion; ?></option><br/><?php
                    }
                ?>
            </select><br/>
        </div><br/>
        <div>
            <input type="submit" name="submit" id="submit"/>
        </div>
    </form>
</main>
</body>
</html>

<?php
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
mysqli_close($connection);