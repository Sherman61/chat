<?php
$host = 'localhost';
$db   = 'chat_app';
$user = 'root';
$pass = 'A@sherman1234!';

$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_error) {
    die('Connection Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}
?>
 