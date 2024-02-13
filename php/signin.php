<?php
require_once('database.php');

session_start();
$name = $_POST['user'];
$password = $_POST['password'];

$res = Database::addNewUser($name, $password);

switch ($res) {
    case -1:
        echo "L'utente già esiste";
        break;
    case 0:
        echo "Autenticato";
        header("location: ../html/login.html");
        break;
}