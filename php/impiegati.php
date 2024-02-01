<html>
<?php
require_once('database.php');
session_start();

if (!isset($_SESSION["DATABASE"])) {
    header("location:../html/login.html");
    exit();
}

$sheetNumber = 1;
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
                <form action="" method="POST" id="formImpiegati">

                    <label for="surnameInImpiegati">Cognome:</label>
                    <input type="text" name="surnameInImpiegati" value=<?php echo (isset($_POST['surnameInImpiegati']) ? $_POST['surnameInImpiegati'] : "") ?>>

                    <br>

                    <label>Stipendio:</label>
                    <select name="cbOperatoreStipendioInDipartimenti">
                        <?php 
                        $comboBoxValue = isSet($_SESSION['cbOperatoreStipendioInDipartimenti']) ? $_SESSION['cbOperatoreStipendioInDipartimenti'] : "";
                        ?>

                        <option value="" <?php if ($comboBoxValue == "") echo ""; ?>></option>
                        <option value="<" <?php if ($comboBoxValue == "<") echo "<"; ?>><</option>
                        <option value="<=" <?php if ($comboBoxValue == "<=") echo "<="; ?>><=</option>
                        <option value="=" <?php if ($comboBoxValue == "=") echo "="; ?>>=</option>
                        <option value=">=" <?php if ($comboBoxValue == ">=") echo ">="; ?>>>=</option>
                        <option value=">" <?php if ($comboBoxValue == ">") echo ">"; ?>>></option>
                    </select>

                    <input type="text" name="stipendioInDipartimenti" id="stipendioInDipartimenti" value=<?php echo (isset($_POST['stipendioInDipartimenti']) ? $_POST['stipendioInDipartimenti'] : "") ?>>
                    
                    <br>

                    <label>Nome dipartimento:</label>
                    <?php
                        $db = clone $_SESSION["DATABASE"];
                        echo $db->getBasicComboBox(0, "cbNomeDipartimentoInDipartimenti", true)
                    ?>

                    <br>

                    <input type="submit" value="filterInDipartimenti">
                </form>
            </div>
            <div class="insData">

            </div>
        </div>
        <div class="table" id="tabella">
            <?php

            $db = clone $_SESSION["DATABASE"];
            if (
                isset($_POST['surnameInImpiegati']) && isset($_POST['cbOperatoreStipendioInDipartimenti'])
                && isset($_POST['stipendioInDipartimenti']) && isset($_POST['cbNomeDipartimentoInDipartimenti'])) {
            
                $cognImp = $_POST['surnameInImpiegati'] . "%";
                $operatore = $_POST['cbOperatoreStipendioInDipartimenti'];
                $stipendio = $_POST['stipendioInDipartimenti'];
                $idDipartimento = Database::getValueInParentheses($_POST['cbNomeDipartimentoInDipartimenti']);

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

</body>

</html>