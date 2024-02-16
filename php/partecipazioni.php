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
    <title>PARTECIPAZIONI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
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
                <form action="" method="POST" id="formPartecipazioni">

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