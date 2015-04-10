<?php
require_once "../includes/database/connect.php";
$confirm_error = false;
$username = ""; $clearance="";

//Check the Postback
if(isset($_POST["submit"]))
{
    //Check if everything is filled in.
    if((isset($_POST["username"])) && (isset($_POST["password"])) && (isset($_POST["confirm_password"])) && (isset($_POST["clearance"])))
    {
        //Check if the password-check is correct.
        if($_POST["password"] !== $_POST["confirm_password"]){
            $confirm_error = true;
        }

        //Process the order.
        if($confirm_error !== true){
            //SET HERE THE PARSE CODE
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
            <?php if((isset($confirm_error)) && $confirm_error) echo "Uw ingave komt niet overeen met het opgegeven paswoord." ?><br/>
        </div>
        <div>
            <label for="clearance">Level of Clearance:</label>
            <select name="clearance" id="clearance">
                <option selected disabled>Select the Clearance:</option>
                <?php
                foreach($options as $option)
                {
                    $pernicktion = $option["pernicktion"];
                    ?><option <?php if(isset($clearance)){if($clearance === $pernicktion) echo " selected";} ?>><?php echo $pernicktion; ?></option><?php
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
        $query = $connection->query("SELECT pernicktion FROM authentication ORDER BY auth_id ASC");
        while($row = $query->fetch_array(MYSQLI_ASSOC)){
            $result[] = $row;
        }
        return $result;
    }

    function make_user()
    {
        echo "gelukt";
    }
