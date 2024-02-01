<html>
<?php
require_once('database.php');
session_start();

if (!isset($_SESSION["DATABASE"])) {
    header("location:../html/login.html");
    exit();
}

$sheetNumber = 3;
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/div.css">
    <title>HELO</title>
</head>

<body>
    <div class="container">
        <div class="dataAndFilter">
            <div class="filter">
                <form action="" method="POST">

                    <label for="projNameInPartecipazioni">Nome progetto:</label>
                    <input type="text" name="projNameInPartecipazioni"
                        value=<?php echo (isset($_POST['projNameInPartecipazioni']) ? $_POST['projNameInPartecipazioni'] : "") ?>>

                        <br>

                    <label for="surnInPartecipazioni">Cognome dipendente:</label>
                    <input type="text" name="surnInPartecipazioni" id="surnInPartecipazioni" value=<?php echo (isset($_POST['surnInPartecipazioni']) ? $_POST['surnInPartecipazioni'] : "") ?>>

                    <br>

                    <input type="submit" value="filterInDipartimenti">
                    <!--<input type="submit" value="resetAllInDipartimenti" id="resetAllInDipartimenti">-->
                </form>
            </div>
            <div class="insData">
                
            </div>
        </div>
        <div class="table" id="tabella">
            <?php

            $db = clone $_SESSION["DATABASE"];
            if (
                isset($_POST['projNameInPartecipazioni']) && isset($_POST['surnInPartecipazioni'])) {

                $nomeProgetto = $_POST['projNameInPartecipazioni'] . "%";
                $cognomeImpiegato = $_POST['surnInPartecipazioni'] . "%";

                $query = $db->getBasicQuery($sheetNumber);
                $query .= " WHERE progetti.nome LIKE :projName AND impiegati.cognome LIKE :cognImp";

                $tmpStatm = $db->getStatement($query);

                $tmpStatm->bindParam(':projName', $nomeProgetto, PDO::PARAM_STR);
                $tmpStatm->bindParam(':cognImp', $cognomeImpiegato, PDO::PARAM_STR);

                echo $db->getTable($sheetNumber, $db->executeQuery($tmpStatm));
            } else {
                echo $db->getBasicTable($sheetNumber);
            }

            ?>
        </div>
    </div>

</body>

</html>