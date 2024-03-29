<?php
class Database
{
    public static $database = null;
    private $servername;
    private $username;
    private $password;
    private $conn = null;

    private $loggedUsername;


    public static function addNewUser($username, $password) //-1 = utente gia' esiste; 0 = ok
    {
        $pwd = hash("sha512", $password);
        $permessi = 0;

        self::getDatbase();

        $checkIfExist = self::$database->login($username, $password);

        if ($checkIfExist != -1) {
            self::$database = null;
            return -1;
        }

        $query = "INSERT INTO users (username, password, permessi) VALUES (:username, :pwd, :permessi)";
        $stmt = self::$database->getStatement($query);

        $stmt->bindParam(":username", $username, PDO::PARAM_STR);
        $stmt->bindParam(":pwd", $pwd, PDO::PARAM_STR);
        $stmt->bindParam(":permessi", $permessi, PDO::PARAM_INT);

        self::$database->executeQuery($stmt);

        self::$database = null; //dopo il login non mi interessa che il db abbia ancora le credenziali
        return 0;
    }

    public function getPermissionLoggedUser()
    {
        self::getDatbase(); //non so perche' serva (dovrebbe gia' essere istanziato)

        $query = "SELECT * FROM users WHERE users.username = :username";
        $tmpStatm = self::$database->getStatement($query);
        $tmpStatm->bindParam(':username', $this->loggedUsername, PDO::PARAM_STR);

        $res = self::$database->executeQuery($tmpStatm);
        return $res[0]['permessi'];
    }

    public function login($username, $password) //-1 = nome utente sbagliato; 0 = nome utente esistene, pwd sbagliata; 1 = loggato
    {
        $pwd = hash("sha512", $password);

        $query = "SELECT users.id FROM users WHERE users.username = :username";
        $tmpStatm = self::$database->getStatement($query);
        $tmpStatm->bindParam(':username', $username, PDO::PARAM_STR);

        $res = self::$database->executeQuery($tmpStatm);

        if (count($res) == 0) {
            return -1;
        }

        $query = "SELECT users.id FROM users WHERE users.username = :username AND users.password = :pwd";
        $tmpStatm = self::$database->getStatement($query);
        $tmpStatm->bindParam(':username', $username, PDO::PARAM_STR);
        $tmpStatm->bindParam(':pwd', $pwd, PDO::PARAM_STR);
        $res = self::$database->executeQuery($tmpStatm);

        if (count($res) == 0) {
            return 0;
        }

        $this->loggedUsername = $username;
        return 1;
    }

    public static function getDatbase()
    { //Singleton
        if (self::$database === null) {
            self::$database = new Database("programma", 123456);
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

    function getBasicComboBox($index, $name, $isFirstSpace, $selectedData, $isReadOnly)
    {
        $readOnly = "";
        if ($isReadOnly) {
            $readOnly = "disabled";
        }

        $outp = "<select name='$name' onchange='changedComboBox(event)' class='form-control' $readOnly>";

        if ($isFirstSpace) {
            $outp .= "<option></option>";
        }

        switch ($index) {
            case 0:
                $data = $this->executeQuery($this->getStatement($this->getBasicQuery(0)));
                foreach ($data as $dato) {
                    $toIns = $selectedData === $dato['codice'] ? "selected" : "";
                    $outp .= "<option " . $toIns . " value = '" . $dato['codice'] . "'>" . $dato['nome'] . "</option>";
                }
                break;
            case 1:
                $data = $this->executeQuery($this->getStatement($this->getBasicQuery(1)));
                foreach ($data as $dato) {
                    $toIns = $selectedData === $dato['matricola'] ? "selected" : "";

                    $outp .= "<option " . $toIns . " value = '" . $dato['matricola'] . "'>" . $dato['cognome'] . "</option>";
                }
                break;
            case 2:
                $data = $this->executeQuery($this->getStatement($this->getBasicQuery(2)));
                foreach ($data as $dato) {
                    $toIns = $selectedData === $dato['sigla'] ? "selected" : "";

                    $outp .= "<option " . $toIns . " value = '" . $dato['sigla'] . "'>" . $dato['nome'] . "</option>";
                }
                break;
        }



        $outp .= "</select>";

        return $outp;
    }

    function getTable($index, $queryOutp)
    {

        $outp = "<table class=\"table\" >";
        $counter = 0;

        $isReadOnly = ($this->getPermissionLoggedUser() == 0);
        $readOnlyString = $isReadOnly ? "readonly" : ""; //se posso solo visualizzare non posso modificare le textBox
        $hideButtonString = $isReadOnly ? "" : "<td> <input type='submit' class='btn btn-secondary' name='deleteTable' onclick='onDelete(event)' value ='DEL'/></td>"; //se posso solo visualizzare non devo poter vedere il tasto elimina record
        $delCoulum = $isReadOnly ? "" : "<th scope=\"col\">Elimina</th>"; //se posso solo visualizzare non devo poter vedere la colonna "elimina"

        switch ($index) {
            case 0: //dipartimenti
                $outp .= "<thead><tr>$delCoulum<th scope=\"col\">Codice</th><th scope=\"col\">Nome</th><th scope=\"col\">Sede</th><th scope=\"col\">Cognome Responsabile</th></tr></thead>";

                $outp .= "<tbody>";
                foreach ($queryOutp as $row) {
                    $outp .= "<form id='formDip" . $counter . "' action='dipartimenti.php' method='POST'>
                    <tr>
                    $hideButtonString
                    <td> <input type='text' name='codiceTable' class='form-control' onfocus='selected(event)' onblur='deselected(event)' value='" . $row['codice'] . "' $readOnlyString/> <input type='hidden' name='pk' value='" . $row['codice'] . "'/> </td>
                    <td> <input type='text' name='nomeTable' class='form-control' onfocus='selected(event)' onblur='deselected(event)' value='" . $row['nome'] . "' $readOnlyString/> </td>
                    <td> <input type='text' name='sedeTable' class='form-control' onfocus='selected(event)' onblur='deselected(event)' value='" . $row['sede'] . "' $readOnlyString/> </td>
                    <td>" . $this->getBasicComboBox(1, "cbCognomeImpiegatoInDipartimentiTable", false, $row['matricola'], $isReadOnly) . "</td>
                    </tr>
                    </form>";

                    $counter++;
                }
                $outp .= "</tbody>";
                break;
            case 1: //impiegati
                $outp .= "<thead><tr>$delCoulum<th scope=\"col\">Matricola</th><th scope=\"col\">Cognome</th><th scope=\"col\">Stipendio</th><th scope=\"col\">Dipartimento</th></tr></thead>";

                $outp .= "<tbody>";
                foreach ($queryOutp as $row) {
                    $outp .= "<form id='formImp" . $counter . "' action='impiegati.php' method='POST'>
                    <tr>
                    $hideButtonString
                    <td> <input type='number' name='matricolaTable' class='form-control' onfocus='selected(event)' onblur='deselected(event)' value='" . $row['matricola'] . "' $readOnlyString/> <input type='hidden' name='pk' value='" . $row['matricola'] . "'/> </td>
                    <td> <input type='text' name='cognomeTable' class='form-control' onfocus='selected(event)' onblur='deselected(event)' value='" . $row['cognome'] . "' $readOnlyString/> </td>
                    <td> <input type='number' name='stipendioTable' class='form-control' onfocus='selected(event)' onblur='deselected(event)' value='" . $row['stipendio'] . "' $readOnlyString/> </td>
                    <td>" . $this->getBasicComboBox(0, "cbNomeDipartimentoInImpiegatiTable", false, $row['codice'], $isReadOnly) . "</td>
                    </tr>
                    </form>";

                    $counter++;
                }
                $outp .= "</tbody>";
                break;
            case 2: //progetti
                $outp .= "<thead><tr>$delCoulum<th scope=\"col\">Sigla</th><th scope=\"col\">Nome</th><th>Bilancio</th><th scope=\"col\">Cognome Responsabile</th></tr></thead>";

                $outp .= "<tbody>";
                foreach ($queryOutp as $row) {
                    $outp .= "<form id='formProg" . $counter . "' action='progetti.php' method='POST'>
                    <tr>
                    $hideButtonString
                    <td> <input type='text' name='siglaTable' class='form-control' onfocus='selected(event)' onblur='deselected(event)' value='" . $row['sigla'] . "' $readOnlyString/> <input type='hidden' name='pk' value='" . $row['sigla'] . "'/> </td>
                    <td> <input type='text' name='nomeTable' class='form-control' onfocus='selected(event)' onblur='deselected(event)' value='" . $row['nome'] . "' $readOnlyString/> </td>
                    <td> <input type='number' name='bilancioTable' class='form-control' onfocus='selected(event)' onblur='deselected(event)' value='" . $row['bilancio'] . "' $readOnlyString/> </td>
                    <td>" . $this->getBasicComboBox(1, "cbCognomeResponsabileInProgettiTable", false, $row['matricola'], $isReadOnly) . "</td>
                    </tr>
                    </form>";

                    $counter++;
                }
                $outp .= "</tbody>";
                break;
            case 3: //partecipazioni
                $outp .= "<thead><tr>$delCoulum<th scope=\"col\">Cognome impiegato</th><th scope=\"col\">Nome progetto</th></tr></thead>";

                $outp .= "<tbody>";
                foreach ($queryOutp as $row) {
                    $outp .= "<form id='formPart" . $counter . "' action='partecipazioni.php' method='POST'>
                    <tr>
                    $hideButtonString
                    <td>" . $this->getBasicComboBox(1, "cbCognomeImpiegatoInPartecipazioniTable", false, $row['matricola'], $isReadOnly) . " <input type='hidden' name='pk1' value='" . $row['matricola'] . "'/> </td>
                    <td>" . $this->getBasicComboBox(2, "cbNomeProgettoInPartecipazioniTable", false, $row['sigla'], $isReadOnly) . " <input type='hidden' name='pk2' value='" . $row['sigla'] . "'/> </td>
                    </tr>
                    </form>";

                    $counter++;
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