<?php
require('Database.php');
class Uploader
{
    public $dbnameV;
    public $dbnameP;
    public $pdo;
    public function __construct()
    {
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

    public function compressFiles($File)
    {
        $compressedFilesArray = [];
        foreach ($File['tmp_name'] as $key => $tmpName) {
            $fileName = htmlspecialchars(basename($File['name'][$key]), ENT_QUOTES, 'UTF-8');
            $fileTmp = $File['tmp_name'][$key];

            // Read the file contents
            $fileContents = file_get_contents($fileTmp);

            $compressedData = gzencode($fileContents, 6);
            $originalLength = strlen($fileContents);
            $gZipBuffer = pack('V', $originalLength) . $compressedData;
            array_push($compressedFilesArray, array(base64_encode($gZipBuffer), $fileName));
        }
        return $compressedFilesArray;
    }
    public function getID()
{
    try {
        $sql = "SELECT MAX(ID) AS ID FROM [" . $this->dbnameV . "].[dbo].[EMail_Anhang]";
        $stmt = $this->pdo->query($sql,[]); // Execute the query

        if ($stmt) { 
            return $stmt[0]['ID']; // Return ID or false if NULL
        }
        
        return 1; // If query execution failed
    } catch (PDOException $e) {
        error_log("Database Error in getID(): " . $e->getMessage()); // Log the error
        return false;
    }
}
    public function insertFiles($filename, $fileid, $fileindex, $base64)
    {
        $params = [
            ':ids' => $fileid,
            ':pos' => $fileindex,
            ':bas' => $base64,
            ':nam' => $filename
        ];
        $sql = "INSERT INTO [" . $this->dbnameV . "].[dbo].[EMail_Anhang] (ID,Pos,Mail,[Name]) VALUES (:ids, :pos,:bas,:nam);";
        $result = $this->pdo->execute($sql, $params);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
    public function getlatestIDAndInsertFiles($File)
    {
        $lastID = $this->getID() != false ? $this->getID() : 1;
        
        $c = 0;
        foreach ($File as $key => $value) {
            $fileName = $value[1];
            $base64 = $value[0];
            if ($this->insertFiles($fileName, $lastID + 1, $key, $base64) == true) {
                $c++;
            }
        }
        if ($c == count($File)) {
            return $lastID + 1;
        } else {
            return false;
        }
    }
    public function insertMailsUploader($sender, $empfanger, $prio, $date, $betr, $mess, $IdA)
    {
        $c = 0;
        foreach ($empfanger as $value) {
            $this->insertMail($sender, $value, $prio, $date, $betr, $mess, $IdA) ? $c++ : '';
        }
        if ($c == count($empfanger)) {
            return true;
        } else {
            return false;
        }
    }
    public function insertMail($sender, $empfanger, $prio, $date, $betr, $mess, $IdA)
    {
        $dateTime = new DateTime("@$date");
        $formattedDate = $dateTime->format("Y-m-d H:i:s") . ".000";
        $params = [
            ':sen' => $sender,
            ':rec' => $empfanger,
            ':prio' => $prio,
            ':dat' => $formattedDate,
            ':betr' => $betr,
            ':mes' => $mess,
            ':aid' => $IdA
        ];
        $sql = "INSERT INTO [" . $this->dbnameV . "].[dbo].[EMail] (Datum,Grund_ID,Betreff,Nachricht,Sender,Empfänger,Erledigt,Wichtig,Anhang,gelöscht,gelöschtDatum,gelöschtUser) VALUES (CONVERT(DATETIME, :dat, 121),79,:betr,:mes,:sen,:rec,0,:prio,:aid,NULL,NULL,NULL);";
        $result = $this->pdo->execute($sql, $params);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

}