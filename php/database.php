<?php
class Database
{
    public static $database = null;
    private $servername;
    private $username;
    private $password;
    private $conn = null;


    public static function getDatbase($username, $password)
    { //Singleton
        if (self::$database === null) {
            self::$database = new Database("visualizzatore", 123456);

            $query = "SELECT users.id FROM users WHERE users.username = :username AND users.password = :pwd";
            $tmpStatm = self::$database->getStatement($query);
            $tmpStatm->bindParam(':username', $username, PDO::PARAM_STR);
            $tmpStatm->bindParam(':pwd', hash("sha512", $password), PDO::PARAM_STR);
            $res = self::$database->executeQuery($tmpStatm);

            if (count($res) == 0) {
                self::$database = null;
            } else {
                self::$database = new Database($username, $password);
            }

        }

        return self::$database;
    }

    private function __construct($username, $password)
    {
        $this->servername = "84.33.120.138";
        $this->username = $username;
        $this->password = $password;

    }

    function getStatement($sql)
    {
        try {
            $this->openConnection();

            $statement = $this->conn->prepare($sql);

            $this->closeConnection();
            return $statement;

        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    function executeQuery($statement)
    {
        try {
            $this->openConnection();

            $statement->execute();
            $data = $statement->fetchAll();

            $this->closeConnection();
            return $data;

        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    function getBasicQuery($index)
    {
        $toReturn = "";

        switch ($index) {
            case 0: //dipartimenti
                $toReturn = "SELECT dipartimenti.codice, dipartimenti.nome, dipartimenti.sede, impiegati.matricola, impiegati.cognome AS 'cognome responsabile' FROM dipartimenti JOIN impiegati ON impiegati.matricola = dipartimenti.id_direttore";
                break;
            case 1: //impiegati
                $toReturn = "SELECT impiegati.matricola, impiegati.cognome, impiegati.stipendio, dipartimenti.codice, dipartimenti.nome AS 'nome dipartimento' FROM dipartimenti JOIN impiegati ON impiegati.id_dipartimento = dipartimenti.codice";
                break;
            case 2: //progetti
                $toReturn = "SELECT progetti.sigla, progetti.nome, progetti.bilancio, impiegati.matricola, impiegati.cognome AS 'cognome responsabile' FROM progetti JOIN impiegati ON progetti.id_responsabile = impiegati.matricola";
                break;
            case 3: //partecipazioni
                $toReturn = "SELECT impiegati.matricola, impiegati.cognome AS 'cognome impiegato', progetti.sigla, progetti.nome AS 'nome progetto' FROM (partecipazioni JOIN impiegati ON partecipazioni.id_impiegato = impiegati.matricola) JOIN progetti ON partecipazioni.id_progetto = progetti.sigla";
                break;
        }

        return $toReturn;
    }

    function getBasicComboBox($index, $name, $isFirstSpace)
    {
        $outp = "<select name='$name'>";

        if ($isFirstSpace) {
            $outp .= "<option></option>";
        }

        switch ($index) {
            case 0:
                $data = $this->executeQuery($this->getStatement($this->getBasicQuery(0)));
                foreach ($data as $dato) {
                    $outp .= "<option>" . $dato['nome'] . " (" . $dato['codice'] . ")" . "</option>";
                }
                break;
        }



        $outp .= "</select>";

        return $outp;
    }

    function getTable($index, $queryOutp)
    {

        $outp = "<table class=\"table\" >";

        switch ($index) {
            case 0: //dipartimenti
                $outp .= "<thead><tr><th scope=\"col\">Codice</th><th scope=\"col\">Nome</th><th scope=\"col\">Sede</th><th scope=\"col\">Cognome Responsabile</th></tr></thead>";

                $outp .= "<tbody>";
                foreach ($queryOutp as $row) {
                    $outp .= "<tr><td>" . $row['codice'] . "</td><td>" . $row['nome'] . "</td><td>" . $row['sede'] . "</td><td>" . $row['cognome responsabile'] . " (" . $row['matricola'] . ")" . "</td></tr>";
                }
                $outp .= "</tbody>";
                break;
            case 1: //impiegati
                $outp .= "<thead><tr><th scope=\"col\">Matricola</th><th scope=\"col\">Cognome</th><th scope=\"col\">Stipendio</th><th scope=\"col\">Dipartimento</th></tr></thead>";

                $outp .= "<tbody>";
                foreach ($queryOutp as $row) {
                    $outp .= "<tr><td>" . $row['matricola'] . "</td><td>" . $row['cognome'] . "</td><td>" . $row['stipendio'] . "</td><td>" . $row['nome dipartimento'] . " (" . $row['codice'] . ")" . "</td></tr>";
                }
                $outp .= "</tbody>";
                break;
            case 2: //progetti
                $outp .= "<thead><tr><th scope=\"col\">Sigla</th><th scope=\"col\">Nome</th><th>Bilancio</th><th scope=\"col\">Cognome Responsabile</th></tr></thead>";

                $outp .= "<tbody>";
                foreach ($queryOutp as $row) {
                    $outp .= "<tr><td>" . $row['sigla'] . "</td><td>" . $row['nome'] . "</td><td>" . $row['bilancio'] . "</td><td>" . $row['cognome responsabile'] . " (" . $row['matricola'] . ")" . "</td></tr>";
                }
                $outp .= "</tbody>";
                break;
            case 3: //partecipazioni
                $outp .= "<thead><tr><th scope=\"col\">Cognome impiegato</th><th scope=\"col\">Nome progetto</th></tr></thead>";

                $outp .= "<tbody>";
                foreach ($queryOutp as $row) {
                    $outp .= "<tr><td>" . $row['cognome impiegato'] . " (" . $row['matricola'] . ")" . "</td><td>" . $row['nome progetto'] . " (" . $row['sigla'] . ")" . "</td></tr>";
                }
                $outp .= "</tbody>";
                break;
        }

        $outp .= "</table>";

        return $outp;
    }

    public function getBasicTable($index)
    {
        return $this->getTable($index, $this->executeQuery($this->getStatement($this->getBasicQuery($index))));
    }

    public static function getValueInParentheses($stringa)
    {
        $inizio = strpos($stringa, '(');
        $fine = strpos($stringa, ')');

        if ($inizio !== false && $fine !== false && $fine > $inizio) {
            return substr($stringa, $inizio + 1, $fine - $inizio - 1);
        }

        return null;
    }

    private function closeConnection()
    {
        $this->conn = null;
    }

    private function openConnection()
    {
        $this->conn = new PDO("mysql:host=$this->servername;dbname=AziendaImpiegatiProgetti", $this->username, $this->password);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //set the PDO error mode to exception
    }
}