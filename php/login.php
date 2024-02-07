<?php
require_once('database.php');

session_start();
$name = $_POST['user'];
$password = $_POST['password'];

$database = Database::getDatbase($name, $password);

if ($database === null) {
    //unset($_SESSION["PRIVILEGI_UTENTE"]);
    unset($_SESSION["DATABASE"]);
    echo "non autenticato";
} else {
    $_SESSION["DATABASE"] = $database;
    header("location:dipartimenti.php");
    exit();
}