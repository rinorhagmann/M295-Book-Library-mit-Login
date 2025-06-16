<?php
$host = 'localhost';
$db = 'books';
$user = 'root'; // Dein MySQL-Benutzername
$pass = 'root';     // Dein MySQL-Passwort
$charset = 'utf8mb4';

if(!$con = mysqli_connect($host, $user, $pass, $db)){
    
    die("Connection failed: ".mysqli_connect_error());
}
?>
