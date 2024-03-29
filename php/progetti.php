<html>
<?php
require_once('database.php');
session_start();

if (!isset($_SESSION["DATABASE"])) {
    header("location:../html/login.html");
    exit();
} else if (isset($_POST['siglaInProgettiIns'])) { //se ho aggiunto un nuovo record
    $db = clone $_SESSION["DATABASE"];

    $siglaProg = $_POST['siglaInProgettiIns'];
    $nomeProg = $_POST['nomeInProgettiIns'];
    $bilancioProg = $_POST['bilancioInProgettiIns'];
    $cognRespProg = $_POST['cbCognomeResponsabileInProgettiIns'];

    $query = "INSERT INTO progetti (sigla, nome, bilancio, id_responsabile) VALUES(:sigla, :nome, :bilancio, :cognomeResp)";

    $tmpStatm = $db->getStatement($query);

    $tmpStatm->bindParam(':sigla', $siglaProg, PDO::PARAM_STR);
    $tmpStatm->bindParam(':nome', $nomeProg, PDO::PARAM_STR);
    $tmpStatm->bindParam(':bilancio', $bilancioProg, PDO::PARAM_INT);
    $tmpStatm->bindParam(':cognomeResp', $cognRespProg, PDO::PARAM_INT);

    $db->executeQuery($tmpStatm);
} else if (isset($_POST['update'])) {
    $db = clone $_SESSION["DATABASE"];

    $oldPk = $_POST['pk'];
    $siglaProg = $_POST['siglaTable'];
    $nomeProg = $_POST['nomeTable'];
    $bilancioProg = $_POST['bilancioTable'];
    $cognRespProg = $_POST['cbCognomeResponsabileInProgettiTable'];

    $query = "UPDATE progetti SET sigla = :sigla, nome = :nome, bilancio = :bilancio, id_responsabile = :cognResp WHERE progetti.sigla = :oldPk";

    $tmpStatm = $db->getStatement($query);

    $tmpStatm->bindParam(':sigla', $siglaProg, PDO::PARAM_STR);
    $tmpStatm->bindParam(':oldPk', $oldPk, PDO::PARAM_STR);
    $tmpStatm->bindParam(':nome', $nomeProg, PDO::PARAM_STR);
    $tmpStatm->bindParam(':bilancio', $bilancioProg, PDO::PARAM_INT);
    $tmpStatm->bindParam(':cognResp', $cognRespProg, PDO::PARAM_INT);

    $db->executeQuery($tmpStatm);
} else if (!isset($_POST['update']) && isset($_POST['pk'])) { //sto eliminando il record
    $db = clone $_SESSION["DATABASE"];

    $pk = $_POST['pk'];

    $query = "DELETE FROM progetti WHERE progetti.sigla=:sigla";
    $tmpStatm = $db->getStatement($query);
    $tmpStatm->bindParam(':sigla', $pk, PDO::PARAM_STR);
    $db->executeQuery($tmpStatm);
}

$sheetNumber = 2;
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PROGETTI</title>
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
                        <form action="" method="POST" id="formProgettiFilter">
                            <div class="form-group">
                                <label for="siglaInProgetti">Sigla:</label>
								<input type="text" class="form-control" name="siglaInProgetti" placeholder="Inserisci la sigla del progetto" value=<?php echo (isset($_POST['siglaInProgetti']) ? $_POST['siglaInProgetti'] : "") ?>>
                                
								<label for="nameInProgetti">Nome:</label>
								<input type="text" class="form-control" name="nameInProgetti" placeholder="Inserisci il nome del progetto" value=<?php echo (isset($_POST['nameInProgetti']) ? $_POST['nameInProgetti'] : "") ?>>
								
                                <label for="cbOperatoreBilancioInProgetti">Bilancio:</label>
								<div class="form-row">
									<div class="form-group col-md-2">
										<select class="form-control" name="cbOperatoreBilancioInProgetti">
											<option value=""></option>
											<option value="<"><</option>
											<option value="<="><=</option>
											<option value="=">=</option>
											<option value=">=">>=</option>
											<option value=">">></option>
										</select>
									</div>
									<div class="form-group col-md-10">
										<input type="number" class="form-control" name="bilancioInDipartimenti" placeholder="Inserisci il bilancio" value=<?php echo (isset($_POST['bilancioInDipartimenti']) ? $_POST['bilancioInDipartimenti'] : "") ?>>
									</div>
								</div>
								
								<label for="surnameRespInDipartimenti">Cognome responsabile:</label>
								<input type="text" class="form-control" name="surnameRespInDipartimenti" placeholder="Inserisci il cognome del responsabile" value=<?php echo (isset($_POST['surnameRespInDipartimenti']) ? $_POST['surnameRespInDipartimenti'] : "") ?>>
								
							</div>
                            <button type="submit" class="btn btn-primary">Filtra</button>
                        </form>
                    </div>
                    <div class="col-md-12" <?php $db = clone $_SESSION["DATABASE"]; if($db->getPermissionLoggedUser()==0) {echo "style='display:none'";}?>>
                        <form action="" method="POST" id="formProgettiIns">
                            <div class="form-group">
                                <label for="siglaInProgettiIns">Sigla [PK]:</label>
                                <input type="text" class="form-control" name="siglaInProgettiIns" placeholder="Inserire la sigla del progetto" required>
								
								<label for="nomeInProgettiIns">Nome:</label>
                                <input type="text" class="form-control" name="nomeInProgettiIns" placeholder="Inserire il nome del progetto" required>
								
								<label for="bilancioInProgettiIns">Bilancio:</label>
                                <input type="number" class="form-control" name="bilancioInProgettiIns" placeholder="Inserire il bilancio del progetto" required>
								
								<label for="cbCognomeResponsabileInProgettiIns">Cognome responsabile:</label>
								<?php
									$db = clone $_SESSION["DATABASE"];
									echo $db->getBasicComboBox(1, "cbCognomeResponsabileInProgettiIns", false, "", false)
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
				if (isset($_POST['siglaInProgetti']) && isset($_POST['nameInProgetti'])
					&& isset($_POST['cbOperatoreBilancioInProgetti']) && isset($_POST['bilancioInDipartimenti']) && isset($_POST['surnameRespInDipartimenti'])) {

					$siglaProgetto = $_POST['siglaInProgetti'] . "%";
					$nomeProgetto = $_POST['nameInProgetti'] . "%";
					$operatoreInBilancio = $_POST['cbOperatoreBilancioInProgetti'];
					$bilancio = $_POST['bilancioInDipartimenti'];
					$cognomeResp = $_POST['surnameRespInDipartimenti'] . "%";

					$query = $db->getBasicQuery($sheetNumber);
					$query .= " WHERE progetti.sigla LIKE :siglaProg AND progetti.nome LIKE :nomeProg AND impiegati.cognome LIKE :cognomeResp";
					if ($operatoreInBilancio != "") {
						$query .= " AND progetti.bilancio $operatoreInBilancio :bilancio";
					}

					$tmpStatm = $db->getStatement($query);

					$tmpStatm->bindParam(':siglaProg', $siglaProgetto, PDO::PARAM_STR);
					$tmpStatm->bindParam(':nomeProg', $nomeProgetto, PDO::PARAM_STR);
					$tmpStatm->bindParam(':cognomeResp', $cognomeResp, PDO::PARAM_STR);

					if ($operatoreInBilancio != "") {
						$tmpStatm->bindParam(':bilancio', $bilancio, PDO::PARAM_INT);
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