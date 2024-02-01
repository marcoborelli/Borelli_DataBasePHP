<html>
<?php
require_once('database.php');
session_start();

if (!isset($_SESSION["DATABASE"])) {
    header("location:../html/login.html");
    exit();
}
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

                    <label for="cityNameInDipartimenti">Nome città:</label>
                    <input type="text" name="cityNameInDipartimenti"
                        value=<?php echo (isset($_POST['cityNameInDipartimenti']) ? $_POST['cityNameInDipartimenti'] : "") ?>>

                        <br>

                    <label for="surnameRespInDipartimenti">Cognome responsabile:</label>
                    <input type="text" name="surnameRespInDipartimenti" id="surnameRespInDipartimenti"
                        value=<?php echo (isset($_POST['surnameRespInDipartimenti']) ? $_POST['surnameRespInDipartimenti'] : "") ?>>


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
                isset($_POST['cityNameInDipartimenti']) && isset($_POST['surnameRespInDipartimenti'])
                /*&& $_POST['cityNameInDipartimenti'] != "" && $_POST['surnameRespInDipartimenti'] != ""*/) {

                $nomeCitta = $_POST['cityNameInDipartimenti'] . "%";
                $cognResp = $_POST['surnameRespInDipartimenti'] . "%";

                $query = $db->getBasicQuery(0);
                $query .= " WHERE dipartimenti.sede LIKE :nomeCitta AND impiegati.cognome LIKE :cognImp";

                $tmpStatm = $db->getStatement($query);

                $tmpStatm->bindParam(':nomeCitta', $nomeCitta, PDO::PARAM_STR);
                $tmpStatm->bindParam(':cognImp', $cognResp, PDO::PARAM_STR);

                echo $db->getTable(0, $db->executeQuery($tmpStatm));
            } else {
                echo $db->getBasicTable(0);
            }

            ?>
        </div>
    </div>

</body>

</html>