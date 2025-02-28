<?php
require('Database.php');
class Messages
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

    public function getAllMessagesIntCount(): mixed
    {
        $params = [
            ':user' => $this->user
        ];
        $sql = "SELECT count(ID) as wert FROM [" . $this->dbnameV . "].[dbo].[EMail] WHERE gelöscht  IS NULL  AND LOWER(Empfänger) = LOWER('" . $this->user . "') AND Erledigt=0;";
        $result = $this->pdo->query($sql, []);
        if (!empty($result)) {
            return $result[0]['wert'];
        } else {
            return [];
        }
    }
    public function getAllMessages(): mixed
    {
        $params = [
            ':user' => $this->user
        ];
        $sql = "SELECT TOP (1000) *,(SELECT name FROM [" . $this->dbnameV . "].[dbo].[Grund] WHERE ID=[" . $this->dbnameV . "].[dbo].[EMail].Grund_ID ) as grundname, DATEDIFF(SECOND, '1970-01-01 00:00:00', Datum) AS created, (SELECT FORMAT(CAST([" . $this->dbnameV . "].[dbo].[EMail].Datum AS DATETIME), 'dd.MM.yy')) as FormattedDate,
        CASE 
        WHEN EXISTS (
            SELECT 1 
            FROM [" . $this->dbnameV . "].[dbo].[BerechtigungAnwender]
            WHERE (Mitarbeiter IS NOT NULL AND Mitarbeiter != '')
              AND Anwender = [" . $this->dbnameV . "].[dbo].[EMail].Sender
        )
        THEN ISNULL((
            SELECT TOP (1) Mitarbeiter 
            FROM [" . $this->dbnameV . "].[dbo].[BerechtigungAnwender]
            WHERE (Mitarbeiter IS NOT NULL AND Mitarbeiter != '')
              AND Anwender = [" . $this->dbnameV . "].[dbo].[EMail].Sender
        ), 'B')
        ELSE UPPER([" . $this->dbnameV . "].[dbo].[EMail].Sender)
    END AS Sendername FROM [" . $this->dbnameV . "].[dbo].[EMail] WHERE gelöscht  IS NULL  AND LOWER(Empfänger) = LOWER(:user) AND Datum <= CURRENT_TIMESTAMP Order by Erledigt Asc;";
        $result = $this->pdo->query($sql, $params);
        if (!empty($result)) {
            return $result;
        } else {
            return [];
        }
    }
    public function groupAndFilterData($inputArray)
    {
        $groupedData = [];

        foreach ($inputArray as $item) {
            // Create a unique key based on the grouping fields
            $key = $item['Datum'] . '|' . $item['Grund_ID'] . '|' . $item['Betreff'] . '|' . $item['Anhang'];

            if (!isset($groupedData[$key])) {
                // Initialize a new object for the group
                $groupedData[$key] = [
                    'ID' => [$item['ID']],
                    'Datum' => $item['Datum'],
                    'Grund_ID' => $item['Grund_ID'],
                    'Anhang' => $item['Anhang'],
                    'Betreff' => $item['Betreff'],
                    'Nachricht' => $item['Nachricht'],
                    'created' => $item['created'],
                    'FormattedDate' => $item['FormattedDate'],
                    'Empfänger' => $item['Empfänger'],
                    'grundname' => $item['grundname'],
                    'Wichtig' => $item['Wichtig'],
                    'Sendername' => [$item['Sendername']],
                    'Erledigt' => [$item['Erledigt']]
                ];
            } else {
                // Add the ID and Empfänger to the existing group
                $groupedData[$key]['ID'][] = $item['ID'];
                $groupedData[$key]['Sendername'][] = $item['Sendername'];
                $groupedData[$key]['Erledigt'][] = $item['Erledigt'];
                $groupedData[$key]['Erledigt'][] = $item['Erledigt'];
            }
        }

        // Reset the keys to make the array numeric
        return array_values($groupedData);
    }
    public function getAllMessagesSend(): mixed
    {
        $params = [
            ':user' => $this->user
        ];
        $sql = "SELECT 
	*,
       (SELECT TOP (1000) name 
        FROM [" . $this->dbnameV . "].[dbo].[Grund] 
        WHERE Grund.ID = E.Grund_ID) AS grundname, 
       DATEDIFF(SECOND, '1970-01-01 00:00:00', Datum) AS created, 
       (SELECT FORMAT(CAST(E.Datum AS DATETIME), 'dd.MM.yy')) AS FormattedDate,
       Empfänger as Sendername
		/*CASE WHEN (SELECT TOP (1) Mitarbeiter FROM [" . $this->dbnameV . "].[dbo].[BerechtigungAnwender] WHERE Anwender = Empfänger) 
		IS NOT NULL AND (SELECT TOP (1) Mitarbeiter FROM [" . $this->dbnameV . "].[dbo].[BerechtigungAnwender] WHERE Anwender = Empfänger) != '' 
		THEN (SELECT TOP (1) Mitarbeiter FROM [" . $this->dbnameV . "].[dbo].[BerechtigungAnwender] WHERE Anwender = Empfänger) ELSE Empfänger END as Sendername*/
        FROM [" . $this->dbnameV . "].[dbo].[EMail] E
        WHERE LOWER(Sender) = LOWER(:user);";
        $result = $this->pdo->query($sql, $params);
        if (!empty($result)) {
            $result = $this->groupAndFilterData($result);
            return $result;
        } else {
            return [];
        }
    }
    public function getMimeTypeFromFilename($filename)
    {
        // Define a mapping of file extensions to MIME types
        $mimeTypes = array(
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'gif' => 'image/gif',
            'pdf' => 'application/pdf',
            'txt' => 'text/plain',
            'html' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'zip' => 'application/zip',
            'mp3' => 'audio/mpeg',
            'mp4' => 'video/mp4',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'xml' => 'application/xml',
            'asc' => 'application/pgp-signature',
            'prf' => 'application/pics-rules',
            'p10' => 'application/pkcs10',
            'p7c' => 'application/pkcs7-mime',
            'p7m' => 'application/pkcs7-mime',
            'csv' => 'text/csv',
            // Add more extensions and MIME types as needed
        );

        // Get the file extension
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        // Return the MIME type based on the file extension
        return isset($mimeTypes[$extension]) ? $mimeTypes[$extension] : 'application/octet-stream';
    }
    public function getGzippedFileSizeFromBase64($base64String)
    {
        // Decode the base64 string to get the gzipped data
        $decodedData = base64_decode($base64String);

        // Get the size of the gzipped data
        $fileSize = strlen($decodedData);  // in bytes

        return $fileSize;
    }
    public function getAllMessagesTrash(): mixed
    {
        $params = [
            ':user' => $this->user
        ];
        $sql = "SELECT TOP (1000) [ID]
      ,[Datum]
      ,[Grund_ID]
      ,[Betreff]
      ,[Nachricht]
      ,[Sender]
      ,[Empfänger]
      ,[Erledigt]
      ,[Wichtig]
      ,[Anhang]
      ,[gelöscht]
      ,[gelöschtDatum]
      ,[gelöschtUser],
	  (SELECT name FROM [" . $this->dbnameV . "].[dbo].[Grund] WHERE ID=EMail.Grund_ID ) as grundname, 
DATEDIFF(SECOND, '1970-01-01 00:00:00', Datum) AS created,
(SELECT FORMAT(CAST([" . $this->dbnameV . "].[dbo].[EMail].Datum AS DATETIME), 'dd.MM.yy')) as FormattedDate,
CASE WHEN (SELECT TOP (1) [Mitarbeiter] FROM [" . $this->dbnameV . "].[dbo].[BerechtigungAnwender] WHERE [Anwender] = [Sender]) 
		IS NOT NULL AND (SELECT TOP (1) [Mitarbeiter] FROM [" . $this->dbnameV . "].[dbo].[BerechtigungAnwender] WHERE  [Anwender] = [Sender]) != '' 
		THEN (SELECT TOP (1) [Mitarbeiter] FROM [" . $this->dbnameV . "].[dbo].[BerechtigungAnwender] WHERE [Anwender] = [Sender]) ELSE [Sender] END as Sendername
  FROM [" . $this->dbnameV . "].[dbo].[EMail] WHERE LOWER([gelöschtUser])=LOWER(:user);";
        $result = $this->pdo->query($sql, $params);
        if (!empty($result)) {
            return $result;
        } else {
            return [];
        }
    }

    public function getAllFiles(): mixed
    {
        $sql = "SELECT DISTINCT 
        EMail.Anhang, 
        EMail_Anhang.ID AS anhangId, 
        EMail_Anhang.Pos AS fileindex,
        EMail_Anhang.Name AS filename 
        FROM [" . $this->dbnameV . "].[dbo].[EMail] 
        LEFT JOIN [" . $this->dbnameV . "].[dbo].[EMail_Anhang] ON EMail.Anhang = EMail_Anhang.ID AND EMail_Anhang.ID > 0 WHERE LOWER(EMail.Sender) = LOWER('" . $this->user . "') OR LOWER(EMail.Empfänger) = LOWER('" . $this->user . "') AND EMail.gelöscht IS NULL AND EMail.Anhang > 0 ";

        $result = $this->pdo->query($sql, []);
        if (is_array($result) && (!empty($result)) && (count($result) > 0)) {
            $newArr = [];
            for ($i = 0; $i < count($result); $i++) {
                $result[$i]['filetype'] = $this->getMimeTypeFromFilename($result[$i]['filename']);
                //$result[$i]['filesizeuncomp'] = $this->getGzippedFileSizeFromBase64($result[$i]['basefile']);
            }
            return $result;
        } else {
            return [];
        }
    }
    public function deleteMessagesArrayOnID($inputArray)
    {
        if (count($inputArray) > 0) {
            $preparedstr = implode(',', $inputArray);
            $params = [
                ':user' => $this->user
            ];
            $sql = "UPDATE [" . $this->dbnameV . "].[dbo].[EMail] 
            SET EMail.gelöscht = 1, 
                EMail.gelöschtUser = :user, 
                EMail.gelöschtDatum = CURRENT_TIMESTAMP
            WHERE EMail.ID IN (" . $preparedstr . ")";
            $result = $this->pdo->execute($sql, $params);
            return ($result > 0) ? true : false;
        } else {
            return false;
        }
    }
    public function deleteMessagesOnID($inputID)
    {
        if (intval($inputID) > 0) {

            $params = [
                ':user' => $this->user,
                ':mid' => $inputID
            ];
            $sql = "UPDATE [" . $this->dbnameV . "].[dbo].[EMail] 
            SET EMail.gelöscht = 1, 
                EMail.gelöschtUser = :user, 
                EMail.gelöschtDatum = CURRENT_TIMESTAMP
            WHERE EMail.ID=:mid";
            $result = $this->pdo->execute($sql, $params);
            return ($result > 0) ? true : false;
        } else {
            return false;
        }
    }
    public function moveBackToInboxMessageOnID($inputID)
    {
        if ($inputID > 0) {
            $params = [
                ':mid' => $inputID
            ];
            $sql = "UPDATE [" . $this->dbnameV . "].[dbo].[EMail] 
            SET EMail.gelöscht = Null, 
                EMail.gelöschtUser = Null, 
                EMail.gelöschtDatum = Null
            WHERE EMail.ID=:mid";
            $result = $this->pdo->execute($sql, $params);
            return ($result > 0) ? true : false;
        } else {
            return false;
        }
    }
    public function markAsReadMessageArray($inputArray, $readunread)
    {
        if (count($inputArray) > 0) {
            $preparedstr = implode(',', $inputArray);
            $params = [
                ':upd' => $readunread
            ];
            $sql = "UPDATE [" . $this->dbnameV . "].[dbo].[EMail] 
            SET EMail.Erledigt = :upd
            WHERE EMail.ID IN (" . $preparedstr . ")";
            $result = $this->pdo->execute($sql, $params);
            return ($result > 0) ? true : false;
        } else {
            return false;
        }
    }
    public function markAsReadMessageOnID($inputID, $readmode)
    {
        $params = [
            ':upd' => $readmode
        ];
        $sql = "UPDATE [" . $this->dbnameV . "].[dbo].[EMail] 
        SET EMail.Erledigt = :upd
        WHERE EMail.ID IN (" . $inputID . ")";
        $result = $this->pdo->execute($sql, $params);
        return ($result > 0) ? true : false;
    }
    public function getAllEmpfaengerandGroupen()
    {
        if ($this->dbnameP != null) {

            $sql = "SELECT DISTINCT [Anwender] ,[Mitarbeiter] ,[Gruppe] FROM [MedicarePflegehsw].[dbo].[BerechtigungAnwender] where (gelöscht is Null OR gelöscht=0) AND (deaktiviert=0 OR deaktiviert is null)";
            //$sql = "WITH RankedRows AS (SELECT p.Anwender,p.Gruppe,COALESCE(NULLIF(p.Mitarbeiter, ''), h.Mitarbeiter) AS Mitarbeiter, ROW_NUMBER() OVER (PARTITION BY p.Anwender, p.Gruppe ORDER BY p.Anwender, p.Gruppe) AS RowNum FROM  [" . $this->dbnameP . "].[dbo].[BerechtigungAnwender] p  LEFT JOIN  [" . $this->dbnameV . "].[dbo].[BerechtigungAnwender] h ON p.Anwender = h.Anwender ) SELECT Distinct Anwender, Gruppe, Mitarbeiter FROM RankedRows";
            $result = $this->pdo->query($sql, []);
            return (count($result) > 0) ? $result : false;
        } else {
            $sql = "SELECT DISTINCT [Anwender] ,[Mitarbeiter] ,[Gruppe] FROM [MedicarePflegehsw].[dbo].[BerechtigungAnwender] where (gelöscht is Null OR gelöscht=0) AND (deaktiviert=0 OR deaktiviert is null)";
            $result = $this->pdo->query($sql, []);
            return (count($result) > 0) ? $result : false;
        }
    }
    public function getAttachmentsOnAttachmentId($id): mixed
    {
        $params = [
            ':aid' => $id
        ];
        $sql = "SELECT * FROM [" . $this->dbnameV . "].[dbo].[EMail_Anhang] WHERE ID = :aid ORDER BY Pos ASC";
        $result = $this->pdo->query($sql, $params);
        if (!empty($result)) {
            for ($i = 0; $i < count($result); $i++) {
                $result[$i]['filetype'] = $this->getMimeTypeFromFilename($result[$i]['Name']);
                $result[$i]['filesizeuncomp'] = $this->getGzippedFileSizeFromBase64($result[$i]['Mail']);
            }
            return $result;
        } else {
            return [];
        }
    }
    public function getFileToSaveOnIdAndIndex($id, $idindex): mixed
    {
        $params = [
            ':aid' => $id,
            ':pid' => $idindex
        ];
        $sql = "SELECT * FROM [" . $this->dbnameV . "].[dbo].[EMail_Anhang] WHERE ID = :aid AND Pos = :pid ";
        $result = $this->pdo->query($sql, $params);
        if (!empty($result)) {
            for ($i = 0; $i < count($result); $i++) {
                $result[$i]['filetype'] = $this->getMimeTypeFromFilename($result[$i]['Name']);
                $result[$i]['filesizeuncomp'] = $this->getGzippedFileSizeFromBase64($result[$i]['Mail']);
            }
            return $result;
        } else {
            return [];
        }
    }

}

?>