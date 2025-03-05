<?php

require('Database.php');
class Notes
{
    private string $user;
    private string $dbtype;
    private string $dbnameV;
    private string $dbnameP;
    private $pdo;

    public function __construct($dbtype = null, $user = null)
    {
        $this->dbtype = $dbtype;
        $this->user = $user;
        $this->pdo = new Database();
        $configFile = __DIR__ . '/../config/config.json';
        if (file_exists($configFile)) {
            $configData = file_get_contents($configFile);
            $config = json_decode($configData, true);
            $this->dbnameV = $config['databaseVerwaltung'];
            $this->dbnameP = $config['databasePflege'];
        } else {
            die("Error: Database configuration not found.");
        }
    }

    public function getAllMyNotes($user = null, $typ = 0): mixed
    {
        $params = [
            ':user' => $this->user
        ];
        $sql = "";
        if ($typ == 0) { //All active text: "Second Note", x: 200, y: 200,prio:0,hexcolor:'#87ff72FF'
            $sql = "SELECT TOP (1000) CAST(ID AS INT) as id, nachricht as text, CAST(xkoordinate AS INT) as x, CAST(ykoordinate AS INT) as y, CAST(wichtig AS INT) as prio, HexColumn as hexcolor  FROM [" . $this->dbnameV . "].[dbo].[Pinnwand] WHERE geloescht  IS NULL  AND LOWER(anwender) = LOWER(:user) ORDER BY geloeschtdatum DESC;";
        } else { //All leted
            $sql = "SELECT TOP (1000) CAST(ID AS INT) as id, nachricht as text, CAST(xkoordinate AS INT) as x, CAST(ykoordinate AS INT) as y, CAST(wichtig AS INT) as prio, HexColumn as hexcolor, geloeschtdatum as datum FROM [" . $this->dbnameV . "].[dbo].[Pinnwand] WHERE geloescht  IS NOT NULL  AND LOWER(anwender) = LOWER(:user) ORDER BY geloeschtdatum DESC;";
        }
        $result = $this->pdo->query($sql, $params);
        if (!empty($result)) {
            $narr = [];
            foreach ($result as $value) {
                $typ == 0 ? array_push($narr, ["id" => intval($value['id']), "text" => $value['text'], "x" => intval($value['x']), "y" => intval($value['y']), "prio" => intval($value['prio']), "hexcolor" => $value['hexcolor']]) : array_push($narr, ["id" => intval($value['id']), "text" => $value['text'], "x" => intval($value['x']), "y" => intval($value['y']), "prio" => intval($value['prio']), "hexcolor" => $value['hexcolor'], "datum" => $this->convertToGermanDate($value['datum'])]);
            }
            return $narr;
        } else {
            return [];
        }
    }
    public function updateNotePositionOnID($user = null, $id = null, $x = 50, $y = 50): mixed
    {
        $params = [
            ':user' => $this->user,
            ':nid' => $id,
            ':xk' => $x,
            ':yk' => $y,
        ];
        $sql = "UPDATE [" . $this->dbnameV . "].[dbo].[Pinnwand] SET xkoordinate=:xk, ykoordinate=:yk WHERE ID = :nid  AND LOWER(anwender) = LOWER(:user);";
        $result = $this->pdo->execute($sql, $params);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
    public function updateNotePriorityOnID($user = null, $id = null, $prio): mixed
    {
        $params = [
            ':user' => $this->user,
            ':nid' => $id,
            ':pri' => $prio
        ];
        $sql = "UPDATE [" . $this->dbnameV . "].[dbo].[Pinnwand] SET [wichtig]=:pri WHERE ID = :nid AND LOWER(anwender) = LOWER(:user);";
        $result = $this->pdo->execute($sql, $params);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
    public function updateNoteColorOnID($user = null, $id = null, $color = '#e0c4f5FF'): mixed
    {
        $params = [
            ':user' => $this->user,
            ':nid' => $id,
            ':cl' => $color
        ];
        $sql = "UPDATE [" . $this->dbnameV . "].[dbo].[Pinnwand] SET HexColumn=:cl WHERE ID = :nid AND LOWER(anwender) = LOWER(:user);";
        $result = $this->pdo->execute($sql, $params);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
    public function updateNoteTextOnID($user = null, $id = null, $text = ''): mixed
    {
        $params = [
            ':user' => $this->user,
            ':nid' => $id,
            ':txt' => $text
        ];
        $sql = "UPDATE [" . $this->dbnameV . "].[dbo].[Pinnwand] SET nachricht=:txt WHERE ID = :nid AND LOWER(anwender) = LOWER(:user);";
        $result = $this->pdo->execute($sql, $params);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
    public function updateNoteDeleteOnID($user = null, $id = null): mixed
    {
        $params = [
            ':user' => $this->user,
            ':nid' => $id
        ];
        $sql = "UPDATE [" . $this->dbnameV . "].[dbo].[Pinnwand] SET geloescht=1,geloeschtdatum=CURRENT_TIMESTAMP  WHERE ID = :nid AND LOWER(anwender) = LOWER(:user);";
        $result = $this->pdo->execute($sql, $params);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
    public function updateNoteRestoreOnID($user = null, $id = null): mixed
    {
        $params = [
            ':user' => $this->user,
            ':nid' => $id
        ];
        $sql = "UPDATE [" . $this->dbnameV . "].[dbo].[Pinnwand] SET geloescht=NULL,geloeschtdatum=NULL  WHERE ID = :nid AND LOWER(anwender) = LOWER(:user);";
        $result = $this->pdo->execute($sql, $params);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
    public function addNewNote($user = null, $id = null): mixed
    {
        $params = [
            ':user' => $this->user,
            ':nid' => $id
        ];
        $sql = "INSERT INTO [" . $this->dbnameV . "].[dbo].[Pinnwand] (postedid,anwender,nachricht, wichtig,xkoordinate,ykoordinate,HexColumn) VALUES (:nid,:user,'',0,50, 50, '#fef08aFF') ";
        $result = $this->pdo->execute($sql, $params);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
    public function convertToGermanDate($datetimeString)
    {
        // Convert the string to a DateTime object
        $date = new DateTime($datetimeString);

        // Format the date into German format (dd.mm.yyyy HH:MM:SS)
        return $date->format('d.m.Y H:i:s');
    }
} 
?>