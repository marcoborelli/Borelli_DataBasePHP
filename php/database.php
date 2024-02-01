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
            self::$database = new Database($username, $password);
            //echo self::$database->getBasicQuery(0);
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

        $outp = "<table border=\"5\">";

        switch ($index) {
            case 0: //dipartimenti
                $outp .= "<tr><th>Codice</th><th>Nome</th><th>Sede</th><th>Cognome Responsabile</th></tr>";

                foreach ($queryOutp as $row) {
                    $outp .= "<tr><td>" . $row['codice'] . "</td><td>" . $row['nome'] . "</td><td>" . $row['sede'] . "</td><td>" . $row['cognome responsabile'] . " (" . $row['matricola'] . ")" . "</td></tr>";
                }
                break;
            case 1: //impiegati
                $outp .= "<tr><th>Matricola</th><th>Cognome</th><th>Stipendio</th><th>Dipartimento</th></tr>";

                foreach ($queryOutp as $row) {
                    $outp .= "<tr><td>" . $row['matricola'] . "</td><td>" . $row['cognome'] . "</td><td>" . $row['stipendio'] . "</td><td>" . $row['nome dipartimento'] . " (" . $row['codice'] . ")" . "</td></tr>";
                }
                break;
            case 2: //progetti
                $outp .= "<tr><th>Sigla</th><th>Nome</th><th>Bilancio</th><th>Cognome Responsabile</th></tr>";

                foreach ($queryOutp as $row) {
                    $outp .= "<tr><td>" . $row['sigla'] . "</td><td>" . $row['nome'] . "</td><td>" . $row['bilancio'] . "</td><td>" . $row['cognome responsabile'] . " (" . $row['matricola'] . ")" . "</td></tr>";
                }
                break;
            case 3: //partecipazioni
                $outp .= "<tr><th>Cognome impiegato</th><th>Nome progetto</th></tr>";

                foreach ($queryOutp as $row) {
                    $outp .= "<tr><td>" . $row['cognome impiegato'] . " (" . $row['matricola'] . ")" . "</td><td>" . $row['nome progetto'] . " (" . $row['sigla'] . ")" . "</td></tr>";
                }
                break;
        }

        $outp .= "</table>";

        return $outp;
    }

    public function getBasicTable($index)
    {
        return $this->getTable($index, $this->executeQuery($this->getStatement($this->getBasicQuery($index))));
    }

    public static function getValueInParentheses($stringa) {
        $inizio = strpos($stringa, '(');
        $fine = strpos($stringa, ')');
        
        if ($inizio !== false && $fine !== false && $fine > $inizio) {
          return substr($stringa, $inizio + 1, $fine - $inizio - 1);
        }
        
        return null;
      }

    private function closeConnection()
    {
        $conn = null;
    }

    private function openConnection()
    {
        $this->conn = new PDO("mysql:host=$this->servername;dbname=AziendaImpiegatiProgetti", $this->username, $this->password);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //set the PDO error mode to exception
    }
}