<html>
<?php
require_once('database.php');
session_start();

if (!isset($_SESSION["DATABASE"])) {
    header("location:../html/login.html");
    exit();
} else if (isset($_POST['cbCognomeImpiegatoInPartecipazioniIns'])) { //se ho aggiunto un nuovo record
    $db = clone $_SESSION["DATABASE"];

    $cognInPartec = $_POST['cbCognomeImpiegatoInPartecipazioniIns'];
    $nomProgInPartec = $_POST['cbNomeProgettoInPartecipazioniIns'];

    $query = "INSERT INTO partecipazioni (id_impiegato, id_progetto) VALUES(:impieg, :nomeProg)";

    $tmpStatm = $db->getStatement($query);

    $tmpStatm->bindParam(':impieg', $cognInPartec, PDO::PARAM_INT);
    $tmpStatm->bindParam(':nomeProg', $nomProgInPartec, PDO::PARAM_STR);

    $db->executeQuery($tmpStatm);
} else if (isset($_POST['update'])) {
    $db = clone $_SESSION["DATABASE"];

    $oldPk1 = $_POST['pk1']; //impiegato
    $oldPk2 = $_POST['pk2']; //progetto
    $impiegPart = $_POST['cbCognomeImpiegatoInPartecipazioniTable'];
    $progPart = $_POST['cbNomeProgettoInPartecipazioniTable'];

    $query = "UPDATE partecipazioni SET id_impiegato = :idImpieg, id_progetto = :idProg WHERE partecipazioni.id_impiegato = :oldPk1 AND partecipazioni.id_progetto = :oldPk2";

    $tmpStatm = $db->getStatement($query);

    $tmpStatm->bindParam(':oldPk1', $oldPk1, PDO::PARAM_INT);
    $tmpStatm->bindParam(':oldPk2', $oldPk2, PDO::PARAM_STR);
    $tmpStatm->bindParam(':idImpieg', $impiegPart, PDO::PARAM_INT);
    $tmpStatm->bindParam(':idProg', $progPart, PDO::PARAM_STR);

    $db->executeQuery($tmpStatm);
} else if (!isset($_POST['update']) && isset($_POST['pk1']) && isset($_POST['pk2'])) { //sto eliminando il record
    $db = clone $_SESSION["DATABASE"];

    $pk1 = $_POST['pk1']; //impiegato
    $pk2 = $_POST['pk2']; //progetto

    $query = "DELETE FROM partecipazioni WHERE partecipazioni.id_impiegato = :pk1 AND partecipazioni.id_progetto = :pk2";
    $tmpStatm = $db->getStatement($query);
    $tmpStatm->bindParam(':pk1', $pk1, PDO::PARAM_INT);
    $tmpStatm->bindParam(':pk2', $pk2, PDO::PARAM_STR);
    $db->executeQuery($tmpStatm);
}

$sheetNumber = 3;
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/div.css">
    <title>PARTECIPAZIONI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../js/script.js"></script>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand">Borelli_DatabasePHP</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="dipartimenti.php">Dipartimenti</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="impiegati.php">Impiegati</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="partecipazioni.php">Partecipazioni</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="progetti.php">Progetti</a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container">
        <div class="dataAndFilter">
            <div class="filter">
                <form action="" method="POST" id="formPartecipazioniFilter">

                    <label for="projNameInPartecipazioni">Nome progetto:</label>
                    <input type="text" name="projNameInPartecipazioni" value=<?php echo (isset($_POST['projNameInPartecipazioni']) ? $_POST['projNameInPartecipazioni'] : "") ?>>

                    <br>

                    <label for="surnInPartecipazioni">Cognome dipendente:</label>
                    <input type="text" name="surnInPartecipazioni" id="surnInPartecipazioni" value=<?php echo (isset($_POST['surnInPartecipazioni']) ? $_POST['surnInPartecipazioni'] : "") ?>>

                    <br>

                    <input type="submit" value="FILTRA">
                </form>
            </div>
            <div class="insData">
                <form action="" method="POST" id="formPartecipazioniIns">

                    <label for="cbCognomeImpiegatoInPartecipazioniIns">Cognome impiegato [PK]:</label>
                    <?php
                        $db = clone $_SESSION["DATABASE"];
                        echo $db->getBasicComboBox(1, "cbCognomeImpiegatoInPartecipazioniIns", false, "")
                    ?>

                    <br>

                    <label for="cbNomeProgettoInPartecipazioniIns">Nome progetto [PK]:</label>
                    <?php
                        $db = clone $_SESSION["DATABASE"];
                        echo $db->getBasicComboBox(2, "cbNomeProgettoInPartecipazioniIns", false, "")
                    ?>

                    <br>

                    <input type="submit" value="INSERISCI">
                </form>
            </div>
        </div>
        <div class="table" id="tabella">
            <?php

            $db = clone $_SESSION["DATABASE"];
            if (isset($_POST['projNameInPartecipazioni']) && isset($_POST['surnInPartecipazioni'])) {

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