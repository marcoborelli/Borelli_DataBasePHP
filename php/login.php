<?php
require_once('database.php');

session_start();
$name = $_POST['user'];
$password = $_POST['password'];

$database = Database::getDatbase();

$res = $database->login($name, $password);

switch ($res) {
    case -1:
        unset($_SESSION["DATABASE"]);
        echo "Nome utente inesistente";
        break;
    case 0:
        unset($_SESSION["DATABASE"]);
        echo "Password errata";
        break;
    case 1:
        $_SESSION["DATABASE"] = $database;
        header("location:dipartimenti.php");
        break;
}