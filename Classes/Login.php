<?php
require('Database.php');
class Login
{

    private string $userSha256;
    private string $passSha256;
    private string $dbtype;
    private string $dbnameV;
    private string $dbnameP;
    private $pdo;
    public function __construct($dbtype = null, $userSha256 = null, $passSha256 = null)
    {
        $this->dbtype = $dbtype;
        $this->userSha256 = $userSha256;
        $this->passSha256 = $passSha256;
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
    public function loginByApplikation(): mixed
    {
        $result = $this->checkCredentialsOnTyp();
        return $result;

    }
    public function loginByExternCall(): mixed
    {
        if ($this->checkIfDatabasePflegeExists()) {
            $result = $this->checkCredentialsOnTyp('pflege');
            if ($result != false) {
                return $result;
            } else {
                $result = $this->checkCredentialsOnTyp('verwaltung');
                if ($result != false) {
                    return $result;
                } else {
                    return false;
                }
            }
        } else {
            $result = $this->checkCredentialsOnTyp('verwaltung');
            if ($result != false) {
                return $result;
            } else {
                return false;
            }
        }
    }
    public function checkCredentialsOnTyp($typ = null): mixed
    {
        $params = [
            ':user' => $this->userSha256,
            ':pass' => $this->passSha256
        ];
        $sql = '';
        if ($typ == null) {
            if ($this->dbtype == "pflege") {
                $sql = "SELECT DISTINCT TOP 1 BA.Anwender as Name, BA.Kennwort, BA.Gruppe, 'P' as usertypeVP, (LTRIM(RTRIM(M.Name2)) + ' ' + LTRIM(RTRIM(M.Name1))) AS Mitarbeitername FROM [" . $this->dbnameP . "].[dbo].[BerechtigungAnwender] as BA LEFT JOIN [" . $this->dbnameV . "].[dbo].Mitarbeiter as M ON BA.MitarbeiterID = M.ID WHERE BA.gelöscht ! = 0 AND M.BeendigungDatum  IS NULL  AND LOWER(BA.Anwender) = LOWER(:user) AND BA.Kennwort = :pass  AND [deaktiviert]=0;";
            } else {
                $sql = "SELECT TOP 1 [Anwender], ([Anwender]) as Mitarbeitername,  NULL as Gruppe, [Kennwort], 'V' as usertypeVP FROM [" . $this->dbnameV . "].[dbo].[BerechtigungAnwender] WHERE [Kennwort] = :pass  and Lower(Anwender) = Lower(:user) AND [deaktiviert]=0;";
            }
        } else {
            if ($typ == "pflege") {
                $sql = "SELECT DISTINCT TOP 1 BA.Anwender as Name, BA.Kennwort, BA.Gruppe, 'P' as usertypeVP, (LTRIM(RTRIM(M.Name2)) + ' ' + LTRIM(RTRIM(M.Name1))) AS Mitarbeitername FROM [" . $this->dbnameP . "].[dbo].[BerechtigungAnwender] as BA LEFT JOIN [" . $this->dbnameV . "].[dbo].Mitarbeiter as M ON BA.MitarbeiterID = M.ID WHERE BA.gelöscht ! = 0 AND M.BeendigungDatum  IS NULL  AND LOWER(BA.Anwender) = LOWER(:user) AND BA.Kennwort = :pass  AND [deaktiviert]=0;";
            } else {
                $sql = "SELECT TOP 1 [Anwender], ([Anwender]) as Mitarbeitername, NULL as Gruppe, [Kennwort], 'V' as usertypeVP FROM [" . $this->dbnameV . "].[dbo].[BerechtigungAnwender] WHERE [Kennwort] = :pass  and Lower(Anwender) = Lower(:user) AND [deaktiviert]=0;";
            }
        }
        return $this->pdo->query($sql, $params);
        if (!empty($result)) {
            return $result;
        } else {
            return "keindatensatz";
        }
    }

    public function checkIfDatabasePflegeExists(): bool
    {
        $sql = "SELECT 1 AS DatabaseExists FROM sys.databases WHERE name = :dbname";

        $params = [
            ':dbname' => 'MedicarePflegehsw'
        ];
        $result = $this->pdo->query($sql, $params);
        if (!empty($result)) {
            return true;
        } else {
            return false;
        }
    }
}

?>