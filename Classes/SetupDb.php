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
        $this->databaseV = $dbname;
        $this->databaseP = $dbnamepflege ? $dbnamepflege : null;
        $this->username = $user;
        $this->password = $password;
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
            $vexists = (strlen($this->databaseV) > 0) ? $this->checkIfDatabaseVPExists($this->databaseV) : false;
            $pexists = (strlen($this->databaseP) > 0) ? $this->checkIfDatabaseVPExists($this->databaseP) : false;
            if ($vexists) {
                if ((strlen($this->databaseP) > 0) && (!$pexists)) {
                    return "NO CONNECTION";
                }
                if (
                    $this->createConfigJson([
                        "host" => $this->host,
                        "master" => $this->database,
                        "databaseVerwaltung" => $this->databaseV,
                        "databasePflege" => $this->databaseP,
                        "username" => $this->username,
                        "password" => $this->password
                    ]) == true
                ) {
                    return true;
                } else {
                    return "FILE CREATION FAILED";
                }
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
        return !empty($result);
    }
}

?>