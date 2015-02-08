#Setup
A short guide on how to install and get started with interbellum.
##What you need
* XAMPP (Apache, PHP 5.5, MySQL and phpMyAdmin)
* You can install another package or Apache, PHP, MySQL and phpMyAdmin (or alternative) individually.

##Deploying interbellum
1. Make sure you have a correctly installed server:
  * PHP 5.5
  * MySQL
  * Apache
  * phpMyAdmin
2. Copy the files to the root of your server (htdocs for XAMPP).
3. Using a webbrowser, go to `localhost/phpmyadmin` and add a new database `interbellum`.
4. Copy the contents of `db_structure.sql` to the textarea under the SQL tab and click start (make sure you are in the interbellum db).
  * You should now see multiple tables in the interbellum db.
5. Go the the table `user` to insert a new user. Enter a username, do not enter an id. The password needs to be generated (see step 6).
6. Using a browser, go to `/game/password.php` and enter a password. Copy the generated code and paste in the password field of the new user you are adding.
7. You should be able to log in using the password and username you put in the user tabel.

**Notes:**
* `/game/password.php` is only accessible when a user is logged in (similar the most `/game` files). Only initially, when there aren't any users in the databse, this file can be opened when there is no one logged in.
* If you try to log in and your password is incorrect. Make sure that you have not copied any spaces or returns to the password field when inserting a new user.
