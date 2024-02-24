<html>
<?php
require_once('database.php');
session_start();

if (!isset($_SESSION["DATABASE"])) {
    header("location:../html/login.html");
    exit();
} else if (isset($_POST['matricolaInImpiegatiIns'])) { //se ho aggiunto un nuovo record
    $db = clone $_SESSION["DATABASE"];

    $matricolaImp = $_POST['matricolaInImpiegatiIns'];
    $cognomeImp = $_POST['cognomeInImpiegatiIns'];
    $stipendioImp = $_POST['stipendioInImpiegatiIns'];
    $nomeDipImp = $_POST['cbNomeDipartimentoInImpiegatiIns'];

    $query = "INSERT INTO impiegati (matricola, cognome, stipendio, id_dipartimento) VALUES(:matr, :cognome, :stipendio, :nomeDip)";

    $tmpStatm = $db->getStatement($query);

    $tmpStatm->bindParam(':matr', $matricolaImp, PDO::PARAM_INT);
    $tmpStatm->bindParam(':cognome', $cognomeImp, PDO::PARAM_STR);
    $tmpStatm->bindParam(':stipendio', $stipendioImp, PDO::PARAM_INT);
    $tmpStatm->bindParam(':nomeDip', $nomeDipImp, PDO::PARAM_STR);

    $db->executeQuery($tmpStatm);
} else if (isset($_POST['update'])) {
    $db = clone $_SESSION["DATABASE"];

    $oldPk = $_POST['pk'];
    $matricolaImp = $_POST['matricolaTable'];
    $cognomeImp = $_POST['cognomeTable'];
    $stipendioImp = $_POST['stipendioTable'];
    $idDipImp = $_POST['cbNomeDipartimentoInImpiegatiTable'];

    $query = "UPDATE impiegati SET matricola = :matricola, cognome = :cognome, stipendio = :stipendio, id_dipartimento = :idDip WHERE impiegati.matricola = :oldPk";

    $tmpStatm = $db->getStatement($query);

    $tmpStatm->bindParam(':matricola', $matricolaImp, PDO::PARAM_INT);
    $tmpStatm->bindParam(':oldPk', $oldPk, PDO::PARAM_INT);
    $tmpStatm->bindParam(':cognome', $cognomeImp, PDO::PARAM_STR);
    $tmpStatm->bindParam(':stipendio', $stipendioImp, PDO::PARAM_INT);
    $tmpStatm->bindParam(':idDip', $idDipImp, PDO::PARAM_STR);

    $db->executeQuery($tmpStatm);
} else if (!isset($_POST['update']) && isset($_POST['pk'])) { //sto eliminando il record
    $db = clone $_SESSION["DATABASE"];

    $pk = $_POST['pk'];

    $query = "DELETE FROM impiegati WHERE impiegati.matricola=:matricola";
    $tmpStatm = $db->getStatement($query);
    $tmpStatm->bindParam(':matricola', $pk, PDO::PARAM_INT);
    $db->executeQuery($tmpStatm);
}

$sheetNumber = 1;
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/div.css">
    <title>IMPIEGATI</title>
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
                        <form action="" method="POST" id="formImpiegatiFilter">
                            <div class="form-group">
                                <label for="surnameInImpiegati">Cognome:</label>
								<input type="text" class="form-control" name="surnameInImpiegati" placeholder="Inserisci il cognome dell'impiegato" value=<?php echo (isset($_POST['surnameInImpiegati']) ? $_POST['surnameInImpiegati'] : "") ?>>
								
                                <label for="cbOperatoreStipendioInDipartimenti">Stipendio:</label>
								<div class="form-row">
									<div class="form-group col-md-2">
										<select class="form-control" name="cbOperatoreStipendioInDipartimenti">
											<option value=""></option>
											<option value="<"><</option>
											<option value="<="><=</option>
											<option value="=">=</option>
											<option value=">=">>=</option>
											<option value=">">></option>
										</select>
									</div>
									<div class="form-group col-md-10">
										<input type="text" class="form-control" name="stipendioInDipartimenti" placeholder="Inserisci lo stipendio" value=<?php echo (isset($_POST['stipendioInDipartimenti']) ? $_POST['stipendioInDipartimenti'] : "") ?>>
									</div>
								</div>
								
								<label for="cbNomeDipartimentoInImpiegati">Nome dipartimento:</label>
								<?php
									$db = clone $_SESSION["DATABASE"];
									echo $db->getBasicComboBox(0, "cbNomeDipartimentoInImpiegati", true, "", false)
								?>
								
							</div>
                            <button type="submit" class="btn btn-primary">Filtra</button>
                        </form>
                    </div>
                    <div class="col-md-12" <?php $db = clone $_SESSION["DATABASE"]; if($db->getPermissionLoggedUser()==0) {echo "style='display:none'";}?>>
                        <form action="" method="POST" id="formImpiegatiIns">
                            <div class="form-group">
                                <label for="matricolaInImpiegatiIns">Matricola [PK]:</label>
                                <input type="text" class="form-control" name="matricolaInImpiegatiIns" placeholder="Inserire la matricola dell'impiegato" required>
								
								<label for="cognomeInImpiegatiIns">Cognome:</label>
                                <input type="text" class="form-control" name="cognomeInImpiegatiIns" placeholder="Inserire il cognome dell'impiegato" required>
								
								<label for="stipendioInImpiegatiIns">Stipendio:</label>
                                <input type="text" class="form-control" name="stipendioInImpiegatiIns" placeholder="Inserire lo stipendio dell'impiegato" required>
								
								<label for="cbNomeDipartimentoInImpiegatiIns">Nome dipartimento:</label>
								<?php
									$db = clone $_SESSION["DATABASE"];
									echo $db->getBasicComboBox(0, "cbNomeDipartimentoInImpiegatiIns", false, "", false)
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
				if (isset($_POST['surnameInImpiegati']) && isset($_POST['cbOperatoreStipendioInDipartimenti'])
					&& isset($_POST['stipendioInDipartimenti']) && isset($_POST['cbNomeDipartimentoInImpiegati'])) {

					$cognImp = $_POST['surnameInImpiegati'] . "%";
					$operatore = $_POST['cbOperatoreStipendioInDipartimenti'];
					$stipendio = $_POST['stipendioInDipartimenti'];
					$idDipartimento = $_POST['cbNomeDipartimentoInImpiegati'];

					$query = $db->getBasicQuery($sheetNumber);
					$query .= " WHERE impiegati.cognome LIKE :cognImp";
					if ($operatore != "") {
						$query .= " AND impiegati.stipendio $operatore :stipendio";
					}
					if ($idDipartimento != "") {
						$query .= " AND impiegati.id_dipartimento = :idDipartimento";
					}

					$tmpStatm = $db->getStatement($query);

					$tmpStatm->bindParam(':cognImp', $cognImp, PDO::PARAM_STR);
					if ($operatore != "") {
						$tmpStatm->bindParam(':stipendio', $stipendio, PDO::PARAM_INT);
					}
					if ($idDipartimento != "") {
						$tmpStatm->bindParam(':idDipartimento', $idDipartimento, PDO::PARAM_STR);
					}

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