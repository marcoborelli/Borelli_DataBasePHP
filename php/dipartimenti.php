<html>
<?php
require_once('database.php');
session_start();

if (!isset($_SESSION["DATABASE"])) {
    header("location:../html/login.html");
    exit();
} else if (isset($_POST['codiceInDipartimentiIns'])) { //ho aggiunto un nuovo record
    $db = clone $_SESSION["DATABASE"];

    $codiceDip = $_POST['codiceInDipartimentiIns'];
    $nomeDip = $_POST['nomeInDipartimenti'];
    $sedeDip = $_POST['sedeInDipartimenti'];
    $cognRespDip = $_POST['cbCognomeResponsabileInDipartimentiIns'];

    $query = "INSERT INTO dipartimenti (codice, nome, sede, id_direttore) VALUES(:cod, :nome, :sede, :idResp)";

    $tmpStatm = $db->getStatement($query);

    $tmpStatm->bindParam(':cod', $codiceDip, PDO::PARAM_STR);
    $tmpStatm->bindParam(':nome', $nomeDip, PDO::PARAM_STR);
    $tmpStatm->bindParam(':sede', $sedeDip, PDO::PARAM_STR);
    $tmpStatm->bindParam(':idResp', $cognRespDip, PDO::PARAM_INT);

    $db->executeQuery($tmpStatm);
} else if (isset($_POST['update'])) {
    $db = clone $_SESSION["DATABASE"];

    $oldPk = $_POST['pk'];
    $codiceDip = $_POST['codiceTable'];
    $nomeDip = $_POST['nomeTable'];
    $sedeDip = $_POST['sedeTable'];
    $cognRespDip = $_POST['cbCognomeImpiegatoInDipartimentiTable'];

    $query = "UPDATE dipartimenti SET codice = :cod, nome = :nome, sede = :sede, id_direttore = :idResp WHERE dipartimenti.codice = :oldPk";

    $tmpStatm = $db->getStatement($query);

    $tmpStatm->bindParam(':cod', $codiceDip, PDO::PARAM_STR);
    $tmpStatm->bindParam(':oldPk', $oldPk, PDO::PARAM_STR);
    $tmpStatm->bindParam(':nome', $nomeDip, PDO::PARAM_STR);
    $tmpStatm->bindParam(':sede', $sedeDip, PDO::PARAM_STR);
    $tmpStatm->bindParam(':idResp', $cognRespDip, PDO::PARAM_INT);

    $db->executeQuery($tmpStatm);
} else if (!isset($_POST['update']) && isset($_POST['pk'])) { //sto eliminando il record
    $db = clone $_SESSION["DATABASE"];

    $pk = $_POST['pk'];

    $query = "DELETE FROM dipartimenti WHERE dipartimenti.codice=:cod";
    $tmpStatm = $db->getStatement($query);
    $tmpStatm->bindParam(':cod', $pk, PDO::PARAM_STR);
    $db->executeQuery($tmpStatm);
}

$sheetNumber = 0;
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DIPARTIMENTI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
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
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <div class="row">
                    <div class="col-md-12">
                        <form action="" method="POST" id="formDipartimentiFilter">
                            <div class="form-group">
                                <label for="cityNameInDipartimenti">Nome città:</label>
								<input type="text" class="form-control" name="cityNameInDipartimenti" placeholder="Inserisci il nome della città" value=<?php echo (isset($_POST['cityNameInDipartimenti']) ? $_POST['cityNameInDipartimenti'] : "") ?>>
                                
								<label for="surnameRespInDipartimenti">Cognome responsabile::</label>
								<input type="text" class="form-control" name="surnameRespInDipartimenti" placeholder="Inserisci il cognome del responsabile" value=<?php echo (isset($_POST['surnameRespInDipartimenti']) ? $_POST['surnameRespInDipartimenti'] : "") ?>>
								
							</div>	
							<button type="submit" class="btn btn-primary">Filtra</button>
                        </form>
                    </div>
                    <div class="col-md-12" <?php $db = clone $_SESSION["DATABASE"]; if($db->getPermissionLoggedUser()==0) {echo "style='display:none'";}?>>
                        <form action="" method="POST" id="formDipartimentiIns">
                            <div class="form-group">
                                <label for="codiceInDipartimentiIns">Codice [PK]:</label>
                                <input type="text" class="form-control" name="codiceInDipartimentiIns" placeholder="Inserire il codice del dipartimento" required>
								
								<label for="nomeInDipartimenti">Nome dipartimento:</label>
                                <input type="text" class="form-control" name="nomeInDipartimenti" placeholder="Inserire il nome del dipartimento" required>
								
								<label for="sedeInDipartimenti">Sede:</label>
                                <input type="text" class="form-control" name="sedeInDipartimenti" placeholder="Inserire la sede del dipartimento" required>
								
								<label for="cbCognomeResponsabileInDipartimentiIns">Cognome responsabile:</label>
								<?php
									$db = clone $_SESSION["DATABASE"];
									echo $db->getBasicComboBox(1, "cbCognomeResponsabileInDipartimentiIns", false, "", false)
								?>
                            </div>
                            <button type="submit" class="btn btn-primary">Inserisci</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <?php

				$db = clone $_SESSION["DATABASE"];
				if (isset($_POST['cityNameInDipartimenti']) && isset($_POST['surnameRespInDipartimenti'])) {

					$nomeCitta = $_POST['cityNameInDipartimenti'] . "%";
					$cognResp = $_POST['surnameRespInDipartimenti'] . "%";

					$query = $db->getBasicQuery($sheetNumber);
					$query .= " WHERE dipartimenti.sede LIKE :nomeCitta AND impiegati.cognome LIKE :cognImp";

					$tmpStatm = $db->getStatement($query);

					$tmpStatm->bindParam(':nomeCitta', $nomeCitta, PDO::PARAM_STR);
					$tmpStatm->bindParam(':cognImp', $cognResp, PDO::PARAM_STR);

					echo $db->getTable($sheetNumber, $db->executeQuery($tmpStatm));
				} else {
					echo $db->getBasicTable($sheetNumber);
				}

				?>
            </div>
        </div>
    </div>
</body>
</html>
