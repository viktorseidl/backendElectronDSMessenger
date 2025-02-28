<?php

class SetupDb
{
    private $host;
    private $database;
    private $databaseV;
    private $databaseP = null;
    private $username;
    private $password;
    private $pdo;
    private $stmt;


    public function __construct($host, $dbname, $dbnamepflege = null, $user, $password)
    {
        if (!isset($host, $dbname, $user)) {
            die("Error: Missing database configuration parameters.");
        }
        $this->host = $host;
        $this->database = 'master';
        $this->databaseV = trim($dbname);
        $this->databaseP = strlen(trim($dbnamepflege)) > 0 ? trim($dbnamepflege) : null;
        $this->username = trim($user);
        $this->password = trim($password);
    }

    public function checkDBCredentials(): bool|string
    {
        // Try to connect to the database
        try {
            // Set the DSN (Data Source Name) for SQL Server
            $dsn = "sqlsrv:Server={$this->host};Database={$this->database}";

            // Create a PDO instance (this will throw an exception if the connection fails)
            $this->pdo = new PDO($dsn, $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            //Verify if Databases exists
            $vexists = (strlen($this->databaseV) > 0) ? $this->checkIfDatabaseVPExists($this->databaseV) : false;
            $pexists = (strlen($this->databaseP) > 0) ? $this->checkIfDatabaseVPExists($this->databaseP) : false;
            //If Verwaltung Db exists, then create ConfigFile
            if ($vexists) {
                $res = $this->createConfigJson([
                    "host" => $this->host,
                    "master" => $this->database,
                    "databaseVerwaltung" => $this->databaseV,
                    "databasePflege" => $this->databaseP,
                    "username" => $this->username,
                    "password" => $this->password
                ]);
                return $res;
            } else {
                return "NO CONNECTION";
            }
        } catch (PDOException $e) {
            return "NO CONNECTION";
        }
    }

    public function createConfigJson($data): bool|string
    {
        $jsonData = json_encode($data, JSON_PRETTY_PRINT);
        $filePath = __DIR__ . "/../Config/config.json";
        if (file_put_contents($filePath, $jsonData)) {
            return true;
        } else {
            return 'FILE CREATION FAILED';
        }
    }

    public function checkIfDatabaseVPExists($name): bool
    {
        $sql = "SELECT 1 AS DatabaseExists FROM sys.databases WHERE name = :dbname";
        $stm = $this->pdo->prepare($sql);
        $stm->bindParam(':dbname', $name);
        $stm->execute();
        $result = $stm->fetch(PDO::FETCH_ASSOC);
        $stm->closeCursor();
        return !empty($result);
    }
    public function checkIfTableExists($name): bool
    {
        $sql = "SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = '" . $name . "' AND TABLE_SCHEMA = 'dbo' ";
        $stm = $this->pdo->prepare($sql);
        $stm->execute();
        $result = $stm->fetch(PDO::FETCH_ASSOC);
        $stm->closeCursor();
        return !empty($result);
    }
    public function checkIfTableColumnAppTypeExists($name): bool
    {
        $sql = "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '" . $name . "' AND TABLE_SCHEMA = 'dbo' AND COLUMN_NAME = 'apptype' ";
        $stm = $this->pdo->prepare($sql);
        $stm->execute();
        $result = $stm->fetch(PDO::FETCH_ASSOC);
        $stm->closeCursor();
        return !empty($result);
    }
    public function checkOrCreateTables(): mixed
    {
        $dsn = "sqlsrv:Server={$this->host};Database={$this->databaseV}";
        $this->pdo = new PDO($dsn, $this->username, $this->password);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Create a PDO instance (this will throw an exception if the connection fails)

        $tables = ['Pinnwand', 'EMail', 'EMail_Anhang'];
        $needed = [];
        $executed = [];
        foreach ($tables as $value) {
            switch ($value) {
                case 'Pinnwand':
                    if ($this->checkIfTableExists($value) == false) {
                        array_push($needed, $value . 'Table');
                        $sql = " 
                                    CREATE TABLE [" . $this->databaseV . "].[dbo].[Pinnwand] (
                                    [ID] INT IDENTITY(1,1) PRIMARY KEY,
                                    [postedid] BIGINT UNIQUE NOT NULL,
                                    [anwender] VARCHAR(100) NOT NULL,
                                    [nachricht] TEXT NULL,
                                    [wichtig] INT DEFAULT 0 NOT NULL,
                                    [xkoordinate] INT DEFAULT 50 NOT NULL,
                                    [ykoordinate] INT DEFAULT 50 NOT NULL,
                                    [geloescht] INT DEFAULT NULL,
                                    [geloeschtdatum] DATETIME DEFAULT NULL,
                                    [HexColumn] VARCHAR(15) DEFAULT '#fef08aFF' NOT NULL
                                )";
                        $stm = $this->pdo->prepare($sql);
                        $result = $stm->execute();
                        $stm->closeCursor();
                        $result ? array_push($executed, $value . 'Table') : '';
                    }
                    break;
                case 'EMail':
                    if ($this->checkIfTableExists($value) == false) {
                        array_push($needed, $value . 'Table');
                        $sql = " 
                                    CREATE TABLE [" . $this->databaseV . "].[dbo].[EMail] (
                                    [ID] INT IDENTITY(1,1) PRIMARY KEY,
                                    [Datum] DATETIME NOT NULL,
                                    [Grund_ID] INT NOT NULL,
                                    [Betreff] VARCHAR(50) NULL,
                                    [Nachricht] [ntext] NULL,
                                    [Sender] VARCHAR(50) NOT NULL,
                                    [Empfänger] VARCHAR(50) NOT NULL,
                                    [Erledigt] BIT NULL,
                                    [Wichtig] VARCHAR(1) DEFAULT '0' NOT NULL,
                                    [Anhang] INT DEFAULT 0 NOT NULL,
                                    [gelöscht] INT DEFAULT NULL,
                                    [gelöschtDatum] smalldatetime DEFAULT NULL,
                                    [gelöschtUser] VARCHAR(50) DEFAULT NULL,
                                    [apptype] VARCHAR(255) DEFAULT NULL
                                )";
                        $stm = $this->pdo->prepare($sql);
                        $result = $stm->execute();
                        $stm->closeCursor();
                        $result ? array_push($executed, $value . 'Table') : '';
                    }
                    if (!$this->checkIfTableColumnAppTypeExists($value)) {
                        array_push($needed, $value . 'Column');
                        $sql = " 
                                    ALTER TABLE [" . $this->databaseV . "].[dbo].[EMail]
                                    ADD [apptype] VARCHAR(255) DEFAULT NULL;
                                )";
                        $stm = $this->pdo->prepare($sql);
                        $result = $stm->execute();
                        $stm->closeCursor();
                        $result ? array_push($executed, $value . 'Table') : '';
                    }
                    break;
                case 'EMail_Anhang':
                    if ($this->checkIfTableExists($value) == false) {
                        array_push($needed, $value . 'Table');
                        $sql = " 
                            CREATE TABLE [" . $this->databaseV . "].[dbo].[EMail_Anhang] (
                            [ID] INT NOT NULL,
                            [Pos] INT NOT NULL,
                            [Mail] TEXT NOT NULL,
                            [Name] VARCHAR(100) NOT NULL, 
                            PRIMARY KEY (`ID`, `Pos`)
                        )";
                        $stm = $this->pdo->prepare($sql);
                        $result = $stm->execute();
                        $stm->closeCursor();
                        $result ? array_push($executed, $value . 'Table') : '';
                    }
                    break;
            }
        }
        if (count($needed) == count($executed)) {
            return ["res" => true, "ergebnis" => $executed];
        } else {
            return ["res" => false, "fehlt" => array_diff($needed, $executed), "soll" => $needed];
        }

    }
}

?>