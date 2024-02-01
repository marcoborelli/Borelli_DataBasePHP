<?php
require_once('database.php');

session_start();
$name = $_POST['user'];
$password = $_POST['password'];

if (($name == "programma" && $password == "123456") || ($name == "visualizzatore" && $password == "12345")) {
    $database = Database::getDatbase($name, $password);

    $_SESSION["DATABASE"] = $database;
    //$_SESSION["PRIVILEGI_UTENTE"] = ($name == "programma") ? 1 : 0; //0 = visualizzatore, 1 = editor
    
    header("location:dipartimenti.php");

    exit();
} else {
    unset($_SESSION["PRIVILEGI_UTENTE"]);
    unset($_SESSION["DATABASE"]);
    echo "non autenticato";
}