<?php
require_once('database.php');

session_start();
$name = $_POST['user'];
$password = hash("sha512", $_POST['password']);

if (($name == "programma" && $password == "ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413") 
|| ($name == "visualizzatore" && $password == "12345")) {
    $database = Database::getDatbase($name, $_POST['password']);

    $_SESSION["DATABASE"] = $database;
    //$_SESSION["PRIVILEGI_UTENTE"] = ($name == "programma") ? 1 : 0; //0 = visualizzatore, 1 = editor
    
    header("location:dipartimenti.php");

    exit();
} else {
    unset($_SESSION["PRIVILEGI_UTENTE"]);
    unset($_SESSION["DATABASE"]);
    echo "non autenticato";
}