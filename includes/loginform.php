<h1>Login</h1>
<?php

// If the script generated errors, they will be shown to the user using the output_errors() function
output_errors($errors);

?>
<form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post">
    <ul>
        <li>
            <input type="text"  name="txt_username" placeholder="Gebruikersnaam"  />
        </li>
        <li>
            <input type="password" name="txt_password" placeholder="Wachtwoord"  />
        </li>
        <li>
            <input type="checkbox" name="chk_remember" id="remember" value="remember_check" />
            <label for="remember" >Wachtwoord onthouden</label>
        </li>
        <li>
            <input type="submit" name="btn_login" value="Inloggen" />
        </li>
    </ul>
</form>