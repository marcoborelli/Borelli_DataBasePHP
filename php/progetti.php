<html>
<?php
require_once('database.php');
session_start();

if (!isset($_SESSION["DATABASE"])) {
    header("location:../html/login.html");
    exit();
}

$sheetNumber = 2;
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

                    <label for="siglaInProgetti">Sigla:</label>
                    <input type="text" name="siglaInProgetti" value=<?php echo (isset($_POST['siglaInProgetti']) ? $_POST['siglaInProgetti'] : "") ?>>

                    <br>

                    <label for="nameInProgetti">Nome:</label>
                    <input type="text" name="nameInProgetti" value=<?php echo (isset($_POST['nameInProgetti']) ? $_POST['nameInProgetti'] : "") ?>>

                    <br>

                    <label>Bilancio:</label>
                    <select name="cbOperatoreBilancioInProgetti">
                        <option value=""></option>
                        <option value="<"><</option>
                        <option value="<="><=</option>
                        <option value="=">=</option>
                        <option value=">=">>=</option>
                        <option value=">">></option>
                    </select>

                    <input type="text" name="bilancioInDipartimenti" id="bilancioInDipartimenti" value=<?php echo (isset($_POST['bilancioInDipartimenti']) ? $_POST['bilancioInDipartimenti'] : "") ?>>

                    <br>

                    <label for="surnameRespInDipartimenti">Cognome responsabile:</label>
                    <input type="text" name="surnameRespInDipartimenti" id="surnameRespInDipartimenti" value=<?php echo (isset($_POST['surnameRespInDipartimenti']) ? $_POST['surnameRespInDipartimenti'] : "") ?>>

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
                isset($_POST['siglaInProgetti']) && isset($_POST['nameInProgetti'])
                && isset($_POST['cbOperatoreBilancioInProgetti']) && isset($_POST['bilancioInDipartimenti']) && isset($_POST['surnameRespInDipartimenti'])) {
            
                $siglaProgetto = $_POST['siglaInProgetti'] . "%";
                $nomeProgetto = $_POST['nameInProgetti'] . "%";
                $operatoreInBilancio = $_POST['cbOperatoreBilancioInProgetti'];
                $bilancio = $_POST['bilancioInDipartimenti'];
                $cognomeResp = $_POST['surnameRespInDipartimenti']. "%";
            
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

</body>

</html>