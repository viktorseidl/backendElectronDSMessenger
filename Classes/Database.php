<?php

class Database
{
    private $host;
    private $database;
    private $databasepflege;
    private $username;
    private $password;
    private $pdo;
    private $stmt;
    private $typ;

    // Constructor: Reads credentials from config.json and sets up database connection
    public function __construct()
    {
        // Load database credentials from the config file
        $configFile = __DIR__ . '/../config/config.json'; // Adjust path to your config.json file

        if (!file_exists($configFile)) {
            die("Error: Database configuration file not found.");
        }

        $configData = file_get_contents($configFile);
        $config = json_decode($configData, true);

        if (!$config) {
            die("Error: Invalid database configuration file.");
        }

        // Ensure all required fields are present
        if (!isset($config['host'], $config['databaseVerwaltung'], $config['databasePflege'], $config['username'], $config['password'])) {
            die("Error: Missing database configuration parameters.");
        }

        $this->host = $config['host'];
        $this->database = $config['master'];
        $this->username = $config['username'];
        $this->password = $config['password'];

        // Try to connect to the database
        try {
            // Set the DSN (Data Source Name) for SQL Server
            $dsn = "sqlsrv:Server={$this->host};Database={$this->database}";

            // Create a PDO instance (this will throw an exception if the connection fails)
            $this->pdo = new PDO($dsn, $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            // If the connection fails, display an error message
            die("Database connection failed: " . $e->getMessage());
        }
    }
    public function getPdo()
    {
        return $this->pdo;
    }
    // Method to execute a SELECT query with placeholders and return the result
    public function query($sql, $params = [])
    {
        try {
            // Prepare the query 
            $this->stmt = $this->pdo->prepare($sql);
            // Bind parameters to the placeholders
            foreach ($params as $key => &$value) {
                $this->stmt->bindParam($key, $value);
            }
            $this->stmt->execute();

            // Return the result (fetch all results for SELECT queries)
            return $this->stmt->fetchAll();

        } catch (PDOException $e) {
            // If the query fails, return an error message
            return $e;
        }
    }

    // Method to execute an INSERT, UPDATE, or DELETE query without expecting a result
    public function execute($sql, $params = [])
    {
        try {
            // Prepare the query
            $this->stmt = $this->pdo->prepare($sql);

            // Bind parameters to the placeholders
            foreach ($params as $key => &$value) {
                $this->stmt->bindParam($key, $value);
            }

            // Execute the query
            $this->stmt->execute();

            // Return the number of affected rows
            return $this->stmt->rowCount();
        } catch (PDOException $e) {
            // If the query fails, return an error message
            die("Query failed: " . $e->getMessage());
        }
    }

    // Method to get the last inserted ID (for INSERT queries)
    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }

    // Method to close the connection
    public function close()
    {
        $this->pdo = null;
    }
}

$db = new Database();
?>