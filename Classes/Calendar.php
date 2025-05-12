<?php
require('Database.php');

class Calendar{
    public $conn;
    public $dbtype;
    public $user;
    public $requestDate;
    public $viewType; 
    private string $dbnameV;
    private string $dbnameP;
    public function __construct($dbtype, $user, $requestDate, $viewType){ 
        $this ->conn=new Database();
        $this->user=$user;
        $this->requestDate=$requestDate;
        $this->viewType=$viewType;
        $this->dbtype=$dbtype;  
        $configFile = __DIR__ . '/../config/config.json';
        if (file_exists($configFile)) {
            $configData = file_get_contents($configFile);
            $config = json_decode($configData, true);
            $this->dbnameV = $config['databaseVerwaltung'];
            $this->dbnameP = $config['databasePflege'];
        }
    }
    public function _getErgebniserfassung($qtype=null)//Done
    {
        $year=null;
        $dateObj = strtotime($this->requestDate);
        $monat=date('Y-m-d', intval($dateObj)); 
       ( $year==null)?
$query ="WITH Kalender AS (SELECT 
        ID AS kid,
        CASE 
            WHEN TRY_CAST(BackColor AS INT) IS NULL THEN '#ff5eac' 
            ELSE '#' + RIGHT('000000' + CONVERT(VARCHAR(6), FORMAT(CAST(BackColor AS INT), 'X')), 6) 
        END AS katBackColor
    FROM [".$this->dbnameV."].[dbo].KalenderKategorien 
    WHERE Kategorie = 'Ergebniserfassung'
) SELECT DISTINCT 
    H.id AS id,
    null AS wohnbereich,
    Z.Haus as haus,
    K.kid, 
    K.katBackColor,
	CASE 
        WHEN H.Stichtagaktuell IS NOT NULL 
        THEN FORMAT(CONVERT(DATE, H.Stichtagaktuell, 104), 'dd.MM.yyyy')  
        ELSE NULL 
    END AS Dates,
    '#000000' AS VordergrundFarbe,
	CASE 
        WHEN H.Stichtagaktuell IS NOT NULL 
        THEN FORMAT(CONVERT(DATE, H.Stichtagaktuell, 104), 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Ende,
    H.Haus AS Bewohner 
FROM [".$this->dbnameV."].[dbo].[Häuser] H
LEFT JOIN [".$this->dbnameV."].[dbo].Zimmer Z ON H.ID = Z.Haus
LEFT JOIN [".$this->dbnameV."].[dbo].Bewohner B ON Z.ID = B.Zimmer
CROSS JOIN Kalender K
WHERE   
H.Stichtagaktuell IS NOT NULL AND
CONVERT(DATE, H.Stichtagaktuell, 104) = CAST('".$monat."' AS DATE) and  
B.Abgangsdatum is null AND 
B.BewohnerNr < 70000 ORDER BY Dates ASC   
    "
        :
        $query = "WITH Kalender AS (SELECT 
        ID AS kid,
        CASE 
            WHEN TRY_CAST(BackColor AS INT) IS NULL THEN '#ff5eac' 
            ELSE '#' + RIGHT('000000' + CONVERT(VARCHAR(6), FORMAT(CAST(BackColor AS INT), 'X')), 6) 
        END AS katBackColor
    FROM [".$this->dbnameV."].[dbo].KalenderKategorien 
    WHERE Kategorie = 'Ergebniserfassung'
) SELECT DISTINCT 
    H.id AS id,
    null AS wohnbereich,
    Z.Haus as haus,
    K.kid, 
    K.katBackColor,
	CASE 
        WHEN H.Stichtagaktuell IS NOT NULL 
        THEN FORMAT(CONVERT(DATE, H.Stichtagaktuell, 104), 'dd.MM.yyyy')  
        ELSE NULL 
    END AS Dates,
    '#000000' AS VordergrundFarbe,
	CASE 
        WHEN H.Stichtagaktuell IS NOT NULL 
        THEN FORMAT(CONVERT(DATE, H.Stichtagaktuell, 104), 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Ende,
    H.Haus AS Bewohner 
FROM [".$this->dbnameV."].[dbo].[Häuser] H
LEFT JOIN [".$this->dbnameV."].[dbo].Zimmer Z ON H.ID = Z.Haus
LEFT JOIN [".$this->dbnameV."].[dbo].Bewohner B ON Z.ID = B.Zimmer
CROSS JOIN Kalender K
WHERE   
H.Stichtagaktuell IS NOT NULL AND
CONVERT(DATE, H.Stichtagaktuell, 104) = CAST('".$monat."' AS DATE) and  
B.Abgangsdatum is null AND 
B.BewohnerNr < 70000 ORDER BY Dates ASC  
    ";
    $result = $this->conn->query($query, []);
     
        if ($result!=false&&count($result)>0) { 
            $narr=[];
            foreach($result as $row){ 
                        $row['id']='EVALKONTRA-'.$row['id'].$row['kid'];
                        $row['titel']='Ergebniserfassung';
                        $row['realtimestart']='00:00';
                        $row['realtimeend']='23:59'; 
                        $row['ColorHex']= '#8708c7'; 
                        $row['katBackColor']= '#8708c7';  
                        $row['datum']=explode('.',$row['Ende'])[2].'-'.explode('.',$row['Ende'])[1].'-'.explode('.',$row['Ende'])[0]; 
                        $row['realtimestartDate']=$row['Ende'];
                        $row['realtimeendDate']=$row['Ende'];
                        $row['isNoteAttached']=NULL;
                        $row['time']=intval(0);
                        $row['duration']=intval(24*4);
                        $row['ersteller']= strtoupper('XXXXXX'); 
                        $row['isAlarm']=false;
                        $row['isAlarmStamp']=NULL;
                        $row['isEditable']=false;
                        $row['eventTyp']=$row['kid'];
                        $row['isPublic']=true;
                        $row['isprivate']=false; 
                        $row['VerwaltungPflege']=NULL; 
                        $row['kategorie']=$row['kid']; 
                        $row['katForeColor']='#000000';   
                        $row['katBezeichnung']='Ergebniserfassung'; 
                        $row['fgdfgfd']=strtotime($row['Ende']); 
                        unset($row['id']); 
                        unset($row['kid']);
                        unset($row['Dates']);
                        unset($row['VordergrundFarbe']); 
                        unset($row['Ende']); 
                        array_push($narr,$row); 
            }
            return $narr; 
        }else{
            return [];
        } 
    }
    public function _getEvaluierungKontraktur($qtype=null) //Done
    {
        $year=null;
        $dateObj = strtotime($this->requestDate);
        $monat=date('Y-m-d', intval($dateObj)); 
       ( $year==null)?
$query ="WITH Kalender AS (SELECT 
        ID AS kid,
        CASE 
            WHEN TRY_CAST(BackColor AS INT) IS NULL THEN '#ff5eac' 
            ELSE '#' + RIGHT('000000' + CONVERT(VARCHAR(6), FORMAT(CAST(BackColor AS INT), 'X')), 6) 
        END AS katBackColor
    FROM [".$this->dbnameV."].[dbo].KalenderKategorien 
    WHERE Kategorie = 'Evaluierung Kontraktur'
) SELECT DISTINCT 
    B.BewohnerNr AS id,
    Z.Station AS wohnbereich,
    Z.Haus as haus,
    K.kid, 
    K.katBackColor,
	CASE 
        WHEN PB.[KontrakturDatum] IS NOT NULL 
        THEN FORMAT(PB.[KontrakturDatum], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Dates,
    '#000000' AS VordergrundFarbe,
	CASE 
        WHEN PB.[KontrakturDatum] IS NOT NULL 
        THEN FORMAT(PB.[KontrakturDatum], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Ende,
    B.vorname + ' ' + B.Name AS Bewohner 
FROM [".$this->dbnameP."].[dbo].Bewohner PB
LEFT JOIN [".$this->dbnameV."].[dbo].Bewohner B ON PB.BewohnerNr = B.BewohnerNr 
LEFT JOIN [".$this->dbnameV."].[dbo].Zimmer Z ON B.Zimmer = Z.ZimmerNummer
CROSS JOIN Kalender K
WHERE  
PB.[KontrakturDatum] Is Not Null and     
PB.[KontrakturDatum] = CAST('".$monat."' AS DATE) and  
B.Abgangsdatum is null AND 
B.BewohnerNr < 70000 ORDER BY Dates ASC  
    "
        :
        $query = "WITH Kalender AS (SELECT 
        ID AS kid,
        CASE 
            WHEN TRY_CAST(BackColor AS INT) IS NULL THEN '#ff5eac' 
            ELSE '#' + RIGHT('000000' + CONVERT(VARCHAR(6), FORMAT(CAST(BackColor AS INT), 'X')), 6) 
        END AS katBackColor
    FROM [".$this->dbnameV."].[dbo].KalenderKategorien 
    WHERE Kategorie = 'Evaluierung Kontraktur'
)
SELECT DISTINCT 
    B.BewohnerNr AS id,
    Z.Station AS wohnbereich,
    Z.Haus as haus,
    K.kid, 
    K.katBackColor,
	CASE 
        WHEN PB.[KontrakturDatum] IS NOT NULL 
        THEN FORMAT(PB.[KontrakturDatum], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Dates,
    '#000000' AS VordergrundFarbe,
	CASE 
        WHEN PB.[KontrakturDatum] IS NOT NULL 
        THEN FORMAT(PB.[KontrakturDatum], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Ende,
    B.vorname + ' ' + B.Name AS Bewohner 
FROM [".$this->dbnameP."].[dbo].Bewohner PB
LEFT JOIN [".$this->dbnameV."].[dbo].Bewohner B ON PB.BewohnerNr = B.BewohnerNr 
LEFT JOIN [".$this->dbnameV."].[dbo].Zimmer Z ON B.Zimmer = Z.ZimmerNummer
CROSS JOIN Kalender K
WHERE  
PB.[KontrakturDatum] Is Not Null and     
PB.[KontrakturDatum] = CAST('".$monat."' AS DATE) and  
B.Abgangsdatum is null AND 
B.BewohnerNr < 70000 ORDER BY Dates ASC
    ";
    $result = $this->conn->query($query, []);
     
        if ($result!=false&&count($result)>0) { 
            $narr=[];
            foreach($result as $row){ 
                        $row['id']='EVALKONTRA-'.$row['id'].$row['kid'];
                        $row['titel']='Evaluierung Kontraktur';
                        $row['realtimestart']='00:00';
                        $row['realtimeend']='23:59'; 
                        $row['ColorHex']= '#9af5d1'; 
                        $row['katBackColor']= '#9af5d1';  
                        $row['datum']=explode('.',$row['Ende'])[2].'-'.explode('.',$row['Ende'])[1].'-'.explode('.',$row['Ende'])[0]; 
                        $row['realtimestartDate']=$row['Ende'];
                        $row['realtimeendDate']=$row['Ende'];
                        $row['isNoteAttached']=NULL;
                        $row['time']=intval(0);
                        $row['duration']=intval(24*4);
                        $row['ersteller']= strtoupper('XXXXXX'); 
                        $row['isAlarm']=false;
                        $row['isAlarmStamp']=NULL;
                        $row['isEditable']=false;
                        $row['eventTyp']=$row['kid'];
                        $row['isPublic']=true;
                        $row['isprivate']=false; 
                        $row['VerwaltungPflege']=NULL; 
                        $row['kategorie']=$row['kid']; 
                        $row['katForeColor']='#000000';   
                        $row['katBezeichnung']='Evaluierung Kontraktur'; 
                        $row['fgdfgfd']=strtotime($row['Ende']); 
                        unset($row['id']); 
                        unset($row['kid']);
                        unset($row['Dates']);
                        unset($row['VordergrundFarbe']); 
                        unset($row['Ende']); 
                        array_push($narr,$row); 
            }
            return $narr; 
        }else{
            return [];
        } 
    }
    public function _getSicherheitskontrollen($qtype=null) //Done
    {
        $year=null;
        $dateObj = strtotime($this->requestDate);
        $monat=date('Y-m-d', intval($dateObj)); 
       ( $year==null)?
$query ="WITH Kalender AS (SELECT 
ID AS kid,
CASE 
    WHEN TRY_CAST(BackColor AS INT) IS NULL THEN '#ff5eac' 
    ELSE '#' + RIGHT('000000' + CONVERT(VARCHAR(6), FORMAT(CAST(BackColor AS INT), 'X')), 6) 
END AS katBackColor
FROM [".$this->dbnameV."].[dbo].KalenderKategorien 
WHERE Kategorie = 'Sicherheitstechnische Kontrolle'
)
SELECT DISTINCT 
    B.BewohnerNr AS id,
    Z.Station AS wohnbereich,
    Z.Haus as haus,
    K.kid, 
    K.katBackColor,
	CASE 
        WHEN H.[Frist] IS NOT NULL 
        THEN FORMAT(H.[Frist], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Dates,
    '#000000' AS VordergrundFarbe,
	CASE 
        WHEN H.[Frist] IS NOT NULL 
        THEN FORMAT(H.[Frist], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Ende,
    B.vorname + ' ' + B.Name AS Bewohner 
FROM [".$this->dbnameP."].[dbo].Bewohner PB
LEFT JOIN [".$this->dbnameV."].[dbo].Bewohner B ON B.BewohnerNr = PB.BewohnerNr 
LEFT JOIN [".$this->dbnameV."].[dbo].Zimmer Z ON B.Zimmer = Z.ZimmerNummer 
LEFT JOIN [".$this->dbnameP."].[dbo].BewohnerHilfsmittel H ON PB.BewohnerNr = H.BewohnerNr 
CROSS JOIN Kalender K
WHERE 
H.[nicht aktuell] = 0 and 
H.aktuell = 1 and
H.[Frist] Is Not Null and     
H.[Frist] = CAST('".$monat."' AS DATE) and  
B.Abgangsdatum is null AND 
B.BewohnerNr < 70000 ORDER BY Dates ASC
    "
        :
        $query = "WITH Kalender AS (SELECT 
        ID AS kid,
        CASE 
            WHEN TRY_CAST(BackColor AS INT) IS NULL THEN '#ff5eac' 
            ELSE '#' + RIGHT('000000' + CONVERT(VARCHAR(6), FORMAT(CAST(BackColor AS INT), 'X')), 6) 
        END AS katBackColor
    FROM [".$this->dbnameV."].[dbo].KalenderKategorien 
    WHERE Kategorie = 'Sicherheitstechnische Kontrolle'
)
SELECT DISTINCT 
    B.BewohnerNr AS id,
    Z.Station AS wohnbereich,
    Z.Haus as haus,
    K.kid, 
    K.katBackColor,
	CASE 
        WHEN H.[Frist] IS NOT NULL 
        THEN FORMAT(H.[Frist], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Dates,
    '#000000' AS VordergrundFarbe,
	CASE 
        WHEN H.[Frist] IS NOT NULL 
        THEN FORMAT(H.[Frist], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Ende,
    B.vorname + ' ' + B.Name AS Bewohner 
FROM [".$this->dbnameP."].[dbo].Bewohner PB
LEFT JOIN [".$this->dbnameV."].[dbo].Bewohner B ON B.BewohnerNr = PB.BewohnerNr 
LEFT JOIN [".$this->dbnameV."].[dbo].Zimmer Z ON B.Zimmer = Z.ZimmerNummer 
LEFT JOIN [".$this->dbnameP."].[dbo].BewohnerHilfsmittel H ON PB.BewohnerNr = H.BewohnerNr 
CROSS JOIN Kalender K
WHERE 
H.[nicht aktuell] = 0 and 
H.aktuell = 1 and
H.[Frist] Is Not Null and     
H.[Frist] = CAST('".$monat."' AS DATE) and  
B.Abgangsdatum is null AND 
B.BewohnerNr < 70000 ORDER BY Dates ASC
    ";
    $result = $this->conn->query($query, []);
     
        if ($result!=false&&count($result)>0) { 
            $narr=[];
            foreach($result as $row){ 
                        $row['id']='SECKNTR-'.$row['id'].$row['kid'];
                        $row['titel']='Sicherheitstechnische Kontrolle';
                        $row['realtimestart']='00:00';
                        $row['realtimeend']='23:59'; 
                        $row['ColorHex']= '#fa1702'; 
                        $row['katBackColor']= '#fa1702';  
                        $row['datum']=explode('.',$row['Ende'])[2].'-'.explode('.',$row['Ende'])[1].'-'.explode('.',$row['Ende'])[0]; 
                        $row['realtimestartDate']=$row['Ende'];
                        $row['realtimeendDate']=$row['Ende'];
                        $row['isNoteAttached']=NULL;
                        $row['time']=intval(0);
                        $row['duration']=intval(24*4);
                        $row['ersteller']= strtoupper('XXXXXX'); 
                        $row['isAlarm']=false;
                        $row['isAlarmStamp']=NULL;
                        $row['isEditable']=false;
                        $row['eventTyp']=$row['kid'];
                        $row['isPublic']=true;
                        $row['isprivate']=false; 
                        $row['VerwaltungPflege']=NULL; 
                        $row['kategorie']=$row['kid']; 
                        $row['katForeColor']='#000000';   
                        $row['katBezeichnung']='Sicherheitstechnische Kontrolle'; 
                        $row['fgdfgfd']=strtotime($row['Ende']); 
                        unset($row['id']); 
                        unset($row['kid']);
                        unset($row['Dates']);
                        unset($row['VordergrundFarbe']); 
                        unset($row['Ende']); 
                        array_push($narr,$row); 
            }
            return $narr; 
        }else{
            return [];
        } 
    }
    public function _getDekubitusprophylaxe($qtype=null) //Done
    {
        $year=null;
        $dateObj = strtotime($this->requestDate);
        $monat=date('Y-m-d', intval($dateObj)); 
       ( $year==null)?
        $query ="WITH Kalender AS (SELECT 
        ID AS kid,
        CASE 
            WHEN TRY_CAST(BackColor AS INT) IS NULL THEN '#ff5eac' 
            ELSE '#' + RIGHT('000000' + CONVERT(VARCHAR(6), FORMAT(CAST(BackColor AS INT), 'X')), 6) 
        END AS katBackColor
    FROM [".$this->dbnameV."].[dbo].KalenderKategorien 
    WHERE Kategorie = 'Dekubitusprophylaxemaßnahmen'
)
SELECT DISTINCT 
    B.BewohnerNr AS id,
    Z.Station AS wohnbereich,
    Z.Haus as haus,
    K.kid, 
    K.katBackColor,
	CASE 
        WHEN PB.[DekubitusProphylaxeDatum] IS NOT NULL 
        THEN FORMAT(PB.[DekubitusProphylaxeDatum], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Dates,
    '#000000' AS VordergrundFarbe,
	CASE 
        WHEN PB.[DekubitusProphylaxeDatum] IS NOT NULL 
        THEN FORMAT(PB.[DekubitusProphylaxeDatum], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Ende,
    B.vorname + ' ' + B.Name AS Bewohner 
FROM [".$this->dbnameP."].[dbo].Bewohner PB
LEFT JOIN [".$this->dbnameV."].[dbo].Bewohner B ON PB.BewohnerNr = B.BewohnerNr 
LEFT JOIN [".$this->dbnameV."].[dbo].Zimmer Z ON B.Zimmer = Z.ZimmerNummer
CROSS JOIN Kalender K
WHERE  
PB.[DekubitusProphylaxeDatum] Is Not Null and     
PB.[DekubitusProphylaxeDatum] = CAST('".$monat."' AS DATE) and  
B.Abgangsdatum is null AND 
B.BewohnerNr < 70000 ORDER BY Dates ASC
    "
        :
        $query = "WITH Kalender AS (SELECT 
        ID AS kid,
        CASE 
            WHEN TRY_CAST(BackColor AS INT) IS NULL THEN '#ff5eac' 
            ELSE '#' + RIGHT('000000' + CONVERT(VARCHAR(6), FORMAT(CAST(BackColor AS INT), 'X')), 6) 
        END AS katBackColor
    FROM [".$this->dbnameV."].[dbo].KalenderKategorien 
    WHERE Kategorie = 'Dekubitusprophylaxemaßnahmen'
)
SELECT DISTINCT 
    B.BewohnerNr AS id,
    Z.Station AS wohnbereich,
    Z.Haus as haus,
    K.kid, 
    K.katBackColor,
	CASE 
        WHEN PB.[DekubitusProphylaxeDatum] IS NOT NULL 
        THEN FORMAT(PB.[DekubitusProphylaxeDatum], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Dates,
    '#000000' AS VordergrundFarbe,
	CASE 
        WHEN PB.[DekubitusProphylaxeDatum] IS NOT NULL 
        THEN FORMAT(PB.[DekubitusProphylaxeDatum], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Ende,
    B.vorname + ' ' + B.Name AS Bewohner 
FROM [".$this->dbnameP."].[dbo].Bewohner PB
LEFT JOIN [".$this->dbnameV."].[dbo].Bewohner B ON PB.BewohnerNr = B.BewohnerNr 
LEFT JOIN [".$this->dbnameV."].[dbo].Zimmer Z ON B.Zimmer = Z.ZimmerNummer
CROSS JOIN Kalender K
WHERE  
PB.[DekubitusProphylaxeDatum] Is Not Null and     
PB.[DekubitusProphylaxeDatum] = CAST('".$monat."' AS DATE) and  
B.Abgangsdatum is null AND 
B.BewohnerNr < 70000 ORDER BY Dates ASC
    ";
    $result = $this->conn->query($query, []);
     
        if ($result!=false&&count($result)>0) { 
            $narr=[];
            foreach($result as $row){ 
                        $row['id']='DKPLAXMAS-'.$row['id'].$row['kid'];
                        $row['titel']='Dekubitusprophylaxemaßnahmen';
                        $row['realtimestart']='00:00';
                        $row['realtimeend']='23:59'; 
                        $row['ColorHex']= '#ddf542'; 
                        $row['katBackColor']= '#ddf542';  
                        $row['datum']=explode('.',$row['Ende'])[2].'-'.explode('.',$row['Ende'])[1].'-'.explode('.',$row['Ende'])[0]; 
                        $row['realtimestartDate']=$row['Ende'];
                        $row['realtimeendDate']=$row['Ende'];
                        $row['isNoteAttached']=NULL;
                        $row['time']=intval(0);
                        $row['duration']=intval(24*4);
                        $row['ersteller']= strtoupper('XXXXXX'); 
                        $row['isAlarm']=false;
                        $row['isAlarmStamp']=NULL;
                        $row['isEditable']=false;
                        $row['eventTyp']=$row['kid'];
                        $row['isPublic']=true;
                        $row['isprivate']=false; 
                        $row['VerwaltungPflege']=NULL; 
                        $row['kategorie']=$row['kid']; 
                        $row['katForeColor']='#000000';   
                        $row['katBezeichnung']='Dekubitusprophylaxemaßnahmen'; 
                        $row['fgdfgfd']=strtotime($row['Ende']); 
                        unset($row['id']); 
                        unset($row['kid']);
                        unset($row['Dates']);
                        unset($row['VordergrundFarbe']); 
                        unset($row['Ende']); 
                        array_push($narr,$row); 
            }
            return $narr; 
        }else{
            return [];
        } 
    }
    public function _getNortonskala($qtype=null) //Done
    {
        $year=null;
        $dateObj = strtotime($this->requestDate);
        $monat=date('Y-m-d', intval($dateObj)); 
       ( $year==null)?
        $query ="WITH Kalender AS (SELECT 
        ID AS kid,
        CASE 
            WHEN TRY_CAST(BackColor AS INT) IS NULL THEN '#ff5eac' 
            ELSE '#' + RIGHT('000000' + CONVERT(VARCHAR(6), FORMAT(CAST(BackColor AS INT), 'X')), 6) 
        END AS katBackColor
    FROM [".$this->dbnameV."].[dbo].KalenderKategorien 
    WHERE Kategorie = 'Nortonskala'
)
SELECT DISTINCT 
    B.BewohnerNr AS id,
    Z.Station AS wohnbereich,
    Z.Haus as haus,
    K.kid, 
    K.katBackColor,
	CASE 
        WHEN PB.[BradenDatum] IS NOT NULL 
        THEN FORMAT(PB.[BradenDatum], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Dates,
    '#000000' AS VordergrundFarbe,
	CASE 
        WHEN PB.[BradenDatum] IS NOT NULL 
        THEN FORMAT(PB.[BradenDatum], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Ende,
    B.vorname + ' ' + B.Name AS Bewohner 
FROM [".$this->dbnameP."].[dbo].Bewohner PB
LEFT JOIN [".$this->dbnameV."].[dbo].Bewohner B ON PB.BewohnerNr = B.BewohnerNr 
LEFT JOIN [".$this->dbnameV."].[dbo].Zimmer Z ON B.Zimmer = Z.ZimmerNummer
CROSS JOIN Kalender K
WHERE  
PB.[BradenDatum] Is Not Null and     
PB.[BradenDatum] = CAST('".$monat."' AS DATE) and  
B.Abgangsdatum is null AND 
B.BewohnerNr < 70000 ORDER BY Dates ASC
    "
        :
        $query = "WITH Kalender AS (SELECT 
        ID AS kid,
        CASE 
            WHEN TRY_CAST(BackColor AS INT) IS NULL THEN '#ff5eac' 
            ELSE '#' + RIGHT('000000' + CONVERT(VARCHAR(6), FORMAT(CAST(BackColor AS INT), 'X')), 6) 
        END AS katBackColor
    FROM [".$this->dbnameV."].[dbo].KalenderKategorien 
    WHERE Kategorie = 'Nortonskala'
)
SELECT DISTINCT 
    B.BewohnerNr AS id,
    Z.Station AS wohnbereich,
    Z.Haus as haus,
    K.kid, 
    K.katBackColor,
	CASE 
        WHEN PB.[BradenDatum] IS NOT NULL 
        THEN FORMAT(PB.[BradenDatum], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Dates,
    '#000000' AS VordergrundFarbe,
	CASE 
        WHEN PB.[BradenDatum] IS NOT NULL 
        THEN FORMAT(PB.[BradenDatum], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Ende,
    B.vorname + ' ' + B.Name AS Bewohner 
FROM [".$this->dbnameP."].[dbo].Bewohner PB
LEFT JOIN [".$this->dbnameV."].[dbo].Bewohner B ON PB.BewohnerNr = B.BewohnerNr 
LEFT JOIN [".$this->dbnameV."].[dbo].Zimmer Z ON B.Zimmer = Z.ZimmerNummer
CROSS JOIN Kalender K
WHERE  
PB.[BradenDatum] Is Not Null and     
PB.[BradenDatum] = CAST('".$monat."' AS DATE) and  
B.Abgangsdatum is null AND 
B.BewohnerNr < 70000 ORDER BY Dates ASC
    ";
    $result = $this->conn->query($query, []);
     
        if ($result!=false&&count($result)>0) { 
            $narr=[];
            foreach($result as $row){ 
                        $row['id']='NRTSKL-'.$row['id'].$row['kid'];
                        $row['titel']='Nortonskala';
                        $row['realtimestart']='00:00';
                        $row['realtimeend']='23:59'; 
                        $row['ColorHex']= '#b08799'; 
                        $row['katBackColor']= '#b08799';  
                        $row['datum']=explode('.',$row['Ende'])[2].'-'.explode('.',$row['Ende'])[1].'-'.explode('.',$row['Ende'])[0]; 
                        $row['realtimestartDate']=$row['Ende'];
                        $row['realtimeendDate']=$row['Ende'];
                        $row['isNoteAttached']=NULL;
                        $row['time']=intval(0);
                        $row['duration']=intval(24*4);
                        $row['ersteller']= strtoupper('XXXXXX'); 
                        $row['isAlarm']=false;
                        $row['isAlarmStamp']=NULL;
                        $row['isEditable']=false;
                        $row['eventTyp']=$row['kid'];
                        $row['isPublic']=true;
                        $row['isprivate']=false; 
                        $row['VerwaltungPflege']=NULL; 
                        $row['kategorie']=$row['kid']; 
                        $row['katForeColor']='#000000';   
                        $row['katBezeichnung']='Nortonskala'; 
                        $row['fgdfgfd']=strtotime($row['Ende']); 
                        unset($row['id']); 
                        unset($row['kid']);
                        unset($row['Dates']);
                        unset($row['VordergrundFarbe']); 
                        unset($row['Ende']); 
                        array_push($narr,$row); 
            }
            return $narr; 
        }else{
            return [];
        } 
    }
    public function _getBradenskala($qtype=null) //Done
    {
        $year=null;
        $dateObj = strtotime($this->requestDate);
        $monat=date('Y-m-d', intval($dateObj)); 
       ( $year==null)?
        $query ="WITH Kalender AS (SELECT 
        ID AS kid,
        CASE 
            WHEN TRY_CAST(BackColor AS INT) IS NULL THEN '#ff5eac' 
            ELSE '#' + RIGHT('000000' + CONVERT(VARCHAR(6), FORMAT(CAST(BackColor AS INT), 'X')), 6) 
        END AS katBackColor
    FROM [".$this->dbnameV."].[dbo].KalenderKategorien 
    WHERE Kategorie = 'Bradenskala'
)
SELECT DISTINCT 
    B.BewohnerNr AS id,
    Z.Station AS wohnbereich,
    Z.Haus as haus,
    K.kid, 
    K.katBackColor,
	CASE 
        WHEN PB.[BradenDatum] IS NOT NULL 
        THEN FORMAT(PB.[BradenDatum], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Dates,
    '#000000' AS VordergrundFarbe,
	CASE 
        WHEN PB.[BradenDatum] IS NOT NULL 
        THEN FORMAT(PB.[BradenDatum], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Ende,
    B.vorname + ' ' + B.Name AS Bewohner 
FROM [".$this->dbnameP."].[dbo].Bewohner PB
LEFT JOIN [".$this->dbnameV."].[dbo].Bewohner B ON PB.BewohnerNr = B.BewohnerNr 
LEFT JOIN [".$this->dbnameV."].[dbo].Zimmer Z ON B.Zimmer = Z.ZimmerNummer
CROSS JOIN Kalender K
WHERE  
PB.[BradenDatum] Is Not Null and     
PB.[BradenDatum] = CAST('".$monat."' AS DATE) and  
B.Abgangsdatum is null AND 
B.BewohnerNr < 70000 ORDER BY Dates ASC
    "
        :
        $query = "WITH Kalender AS (SELECT 
        ID AS kid,
        CASE 
            WHEN TRY_CAST(BackColor AS INT) IS NULL THEN '#ff5eac' 
            ELSE '#' + RIGHT('000000' + CONVERT(VARCHAR(6), FORMAT(CAST(BackColor AS INT), 'X')), 6) 
        END AS katBackColor
    FROM [".$this->dbnameV."].[dbo].KalenderKategorien 
    WHERE Kategorie = 'Bradenskala'
)
SELECT DISTINCT 
    B.BewohnerNr AS id,
    Z.Station AS wohnbereich,
    Z.Haus as haus,
    K.kid, 
    K.katBackColor,
	CASE 
        WHEN PB.[BradenDatum] IS NOT NULL 
        THEN FORMAT(PB.[BradenDatum], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Dates,
    '#000000' AS VordergrundFarbe,
	CASE 
        WHEN PB.[BradenDatum] IS NOT NULL 
        THEN FORMAT(PB.[BradenDatum], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Ende,
    B.vorname + ' ' + B.Name AS Bewohner 
FROM [".$this->dbnameP."].[dbo].Bewohner PB
LEFT JOIN [".$this->dbnameV."].[dbo].Bewohner B ON PB.BewohnerNr = B.BewohnerNr 
LEFT JOIN [".$this->dbnameV."].[dbo].Zimmer Z ON B.Zimmer = Z.ZimmerNummer
CROSS JOIN Kalender K
WHERE  
PB.[BradenDatum] Is Not Null and     
PB.[BradenDatum] = CAST('".$monat."' AS DATE) and  
B.Abgangsdatum is null AND 
B.BewohnerNr < 70000 ORDER BY Dates ASC
    ";
    $result = $this->conn->query($query, []);
     
        if ($result!=false&&count($result)>0) { 
            $narr=[];
            foreach($result as $row){ 
                        $row['id']='BDSKL-'.$row['id'].$row['kid'];
                        $row['titel']='Bradenskala';
                        $row['realtimestart']='00:00';
                        $row['realtimeend']='23:59'; 
                        $row['ColorHex']= '#d1a56f'; 
                        $row['katBackColor']= '#d1a56f';  
                        $row['datum']=explode('.',$row['Ende'])[2].'-'.explode('.',$row['Ende'])[1].'-'.explode('.',$row['Ende'])[0]; 
                        $row['realtimestartDate']=$row['Ende'];
                        $row['realtimeendDate']=$row['Ende'];
                        $row['isNoteAttached']=NULL;
                        $row['time']=intval(0);
                        $row['duration']=intval(24*4);
                        $row['ersteller']= strtoupper('XXXXXX'); 
                        $row['isAlarm']=false;
                        $row['isAlarmStamp']=NULL;
                        $row['isEditable']=false;
                        $row['eventTyp']=$row['kid'];
                        $row['isPublic']=true;
                        $row['isprivate']=false; 
                        $row['VerwaltungPflege']=NULL; 
                        $row['kategorie']=$row['kid']; 
                        $row['katForeColor']='#000000';   
                        $row['katBezeichnung']='Bradenskala'; 
                        $row['fgdfgfd']=strtotime($row['Ende']); 
                        unset($row['id']); 
                        unset($row['kid']);
                        unset($row['Dates']);
                        unset($row['VordergrundFarbe']); 
                        unset($row['Ende']); 
                        array_push($narr,$row); 
            }
            return $narr; 
        }else{
            return [];
        } 
    }
    public function _getEvalBetreuung($qtype=null) //Done
    {
        $year=null;
        $dateObj = strtotime($this->requestDate);
        $monat=date('Y-m-d', intval($dateObj)); 
       ( $year==null)?
        $query ="WITH Kalender AS (SELECT 
        ID AS kid,
        CASE 
            WHEN TRY_CAST(BackColor AS INT) IS NULL THEN '#ff5eac' 
            ELSE '#' + RIGHT('000000' + CONVERT(VARCHAR(6), FORMAT(CAST(BackColor AS INT), 'X')), 6) 
        END AS katBackColor
    FROM [".$this->dbnameV."].[dbo].KalenderKategorien 
    WHERE Kategorie = 'Evaluierung Betreuung'
)
SELECT DISTINCT 
    B.BewohnerNr AS id,
    Z.Station AS wohnbereich,
    Z.Haus as haus,
    K.kid, 
    K.katBackColor,
	CASE 
        WHEN PB.[EvaluierungBetreuung] IS NOT NULL 
        THEN FORMAT(PB.[EvaluierungBetreuung], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Dates,
    '#000000' AS VordergrundFarbe,
	CASE 
        WHEN PB.[EvaluierungBetreuung] IS NOT NULL 
        THEN FORMAT(PB.[EvaluierungBetreuung], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Ende,
    B.vorname + ' ' + B.Name AS Bewohner 
FROM [".$this->dbnameP."].[dbo].Bewohner PB
LEFT JOIN [".$this->dbnameV."].[dbo].Bewohner B ON PB.BewohnerNr = B.BewohnerNr 
LEFT JOIN [".$this->dbnameV."].[dbo].Zimmer Z ON B.Zimmer = Z.ZimmerNummer
CROSS JOIN Kalender K
WHERE  
PB.[EvaluierungBetreuung] Is Not Null and     
PB.[EvaluierungBetreuung] = CAST('".$monat."' AS DATE) and  
B.Abgangsdatum is null AND 
B.BewohnerNr < 70000 ORDER BY Dates ASC
    "
        :
        $query = "WITH Kalender AS (SELECT 
        ID AS kid,
        CASE 
            WHEN TRY_CAST(BackColor AS INT) IS NULL THEN '#ff5eac' 
            ELSE '#' + RIGHT('000000' + CONVERT(VARCHAR(6), FORMAT(CAST(BackColor AS INT), 'X')), 6) 
        END AS katBackColor
    FROM [".$this->dbnameV."].[dbo].KalenderKategorien 
    WHERE Kategorie = 'Evaluierung Betreuung'
)
SELECT DISTINCT 
    B.BewohnerNr AS id,
    Z.Station AS wohnbereich,
    Z.Haus as haus,
    K.kid, 
    K.katBackColor,
	CASE 
        WHEN PB.[EvaluierungBetreuung] IS NOT NULL 
        THEN FORMAT(PB.[EvaluierungBetreuung], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Dates,
    '#000000' AS VordergrundFarbe,
	CASE 
        WHEN PB.[EvaluierungBetreuung] IS NOT NULL 
        THEN FORMAT(PB.[EvaluierungBetreuung], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Ende,
    B.vorname + ' ' + B.Name AS Bewohner 
FROM [".$this->dbnameP."].[dbo].Bewohner PB
LEFT JOIN [".$this->dbnameV."].[dbo].Bewohner B ON PB.BewohnerNr = B.BewohnerNr 
LEFT JOIN [".$this->dbnameV."].[dbo].Zimmer Z ON B.Zimmer = Z.ZimmerNummer
CROSS JOIN Kalender K
WHERE  
PB.[EvaluierungBetreuung] Is Not Null and     
PB.[EvaluierungBetreuung] = CAST('".$monat."' AS DATE) and  
B.Abgangsdatum is null AND 
B.BewohnerNr < 70000 ORDER BY Dates ASC
    ";
    $result = $this->conn->query($query, []);
     
        if ($result!=false&&count($result)>0) { 
            $narr=[];
            foreach($result as $row){ 
                        $row['id']='EVALBET-'.$row['id'].$row['kid'];
                        $row['titel']='Evaluierung Betreuung';
                        $row['realtimestart']='00:00';
                        $row['realtimeend']='23:59'; 
                        $row['ColorHex']= '#afb394'; 
                        $row['katBackColor']= '#afb394';  
                        $row['datum']=explode('.',$row['Ende'])[2].'-'.explode('.',$row['Ende'])[1].'-'.explode('.',$row['Ende'])[0]; 
                        $row['realtimestartDate']=$row['Ende'];
                        $row['realtimeendDate']=$row['Ende'];
                        $row['isNoteAttached']=NULL;
                        $row['time']=intval(0);
                        $row['duration']=intval(24*4);
                        $row['ersteller']= strtoupper('XXXXXX'); 
                        $row['isAlarm']=false;
                        $row['isAlarmStamp']=NULL;
                        $row['isEditable']=false;
                        $row['eventTyp']=$row['kid'];
                        $row['isPublic']=true;
                        $row['isprivate']=false; 
                        $row['VerwaltungPflege']=NULL; 
                        $row['kategorie']=$row['kid']; 
                        $row['katForeColor']='#000000';   
                        $row['katBezeichnung']='Evaluierung Betreuung'; 
                        $row['fgdfgfd']=strtotime($row['Ende']); 
                        unset($row['id']); 
                        unset($row['kid']);
                        unset($row['Dates']);
                        unset($row['VordergrundFarbe']); 
                        unset($row['Ende']); 
                        array_push($narr,$row); 
            }
            return $narr; 
        }else{
            return [];
        } 
    }
    public function _getWundvermessung($qtype=null) //Done
    {
        $year=null;
        $dateObj = strtotime($this->requestDate);
        $monat=date('Y-m-d', intval($dateObj));
       ( $year==null)?
        $query ="WITH Kalender AS (SELECT
        ID AS kid,
        CASE
            WHEN TRY_CAST(BackColor AS INT) IS NULL THEN '#ff5eac'
            ELSE '#' + RIGHT('000000' + CONVERT(VARCHAR(6), FORMAT(CAST(BackColor AS INT), 'X')), 6)
        END AS katBackColor
    FROM [".$this->dbnameV."].[dbo].KalenderKategorien
    WHERE Kategorie = 'Wundvermessung'
)
SELECT DISTINCT
    B.BewohnerNr AS id,
    Z.Station AS wohnbereich,
    Z.Haus as haus,
    K.kid,
    K.katBackColor,
    CASE
        WHEN PB.[Wundvermessung] IS NOT NULL
        THEN FORMAT(PB.[Wundvermessung], 'dd.MM.yyyy')
        ELSE NULL
    END AS Dates,
    '#000000' AS VordergrundFarbe,
    CASE
        WHEN PB.[Wundvermessung] IS NOT NULL
        THEN FORMAT(PB.[Wundvermessung], 'dd.MM.yyyy')
        ELSE NULL
    END AS Ende,
    B.vorname + ' ' + B.Name AS Bewohner
FROM [".$this->dbnameP."].[dbo].Bewohner PB
LEFT JOIN [".$this->dbnameV."].[dbo].Bewohner B ON PB.BewohnerNr = B.BewohnerNr
LEFT JOIN [".$this->dbnameV."].[dbo].Zimmer Z ON B.Zimmer = Z.ZimmerNummer
CROSS JOIN Kalender K
WHERE  
PB.[Wundvermessung] Is Not Null and    
PB.[Wundvermessung] = CAST('".$monat."' AS DATE) and  
B.Abgangsdatum is null AND
B.BewohnerNr < 70000 ORDER BY Dates ASC
    "
        :
        $query = "WITH Kalender AS (SELECT
        ID AS kid,
        CASE
            WHEN TRY_CAST(BackColor AS INT) IS NULL THEN '#ff5eac'
            ELSE '#' + RIGHT('000000' + CONVERT(VARCHAR(6), FORMAT(CAST(BackColor AS INT), 'X')), 6)
        END AS katBackColor
    FROM [".$this->dbnameV."].[dbo].KalenderKategorien
    WHERE Kategorie = 'Wundvermessung'
)
SELECT DISTINCT
    B.BewohnerNr AS id,
    Z.Station AS wohnbereich,
    Z.Haus as haus,
    K.kid,
    K.katBackColor,
    CASE
        WHEN PB.[Wundvermessung] IS NOT NULL
        THEN FORMAT(PB.[Wundvermessung], 'dd.MM.yyyy')
        ELSE NULL
    END AS Dates,
    '#000000' AS VordergrundFarbe,
    CASE
        WHEN PB.[Wundvermessung] IS NOT NULL
        THEN FORMAT(PB.[Wundvermessung], 'dd.MM.yyyy')
        ELSE NULL
    END AS Ende,
    B.vorname + ' ' + B.Name AS Bewohner
FROM [".$this->dbnameP."].[dbo].Bewohner PB
LEFT JOIN [".$this->dbnameV."].[dbo].Bewohner B ON PB.BewohnerNr = B.BewohnerNr
LEFT JOIN [".$this->dbnameV."].[dbo].Zimmer Z ON B.Zimmer = Z.ZimmerNummer
CROSS JOIN Kalender K
WHERE  
PB.[Wundvermessung] Is Not Null and    
PB.[Wundvermessung] = CAST('".$monat."' AS DATE) and  
B.Abgangsdatum is null AND
B.BewohnerNr < 70000 ORDER BY Dates ASC
    ";
    $result = $this->conn->query($query, []);
     
        if ($result!=false&&count($result)>0) {
            $narr=[];
            foreach($result as $row){
                        $row['id']='WUDVER-'.$row['id'].$row['kid'];
                        $row['titel']='Wundvermessung';
                        $row['realtimestart']='00:00';
                        $row['realtimeend']='23:59';
                        $row['ColorHex']= '#c4a643';
                        $row['katBackColor']= '#c4a643';  
                        $row['datum']=explode('.',$row['Ende'])[2].'-'.explode('.',$row['Ende'])[1].'-'.explode('.',$row['Ende'])[0];
                        $row['realtimestartDate']=$row['Ende'];
                        $row['realtimeendDate']=$row['Ende'];
                        $row['isNoteAttached']=NULL;
                        $row['time']=intval(0);
                        $row['duration']=intval(24*4);
                        $row['ersteller']= strtoupper('XXXXXX');
                        $row['isAlarm']=false;
                        $row['isAlarmStamp']=NULL;
                        $row['isEditable']=false;
                        $row['eventTyp']=$row['kid'];
                        $row['isPublic']=true;
                        $row['isprivate']=false; 
                        $row['VerwaltungPflege']=NULL;
                        $row['kategorie']=$row['kid'];
                        $row['katForeColor']='#000000';  
                        $row['katBezeichnung']='Wundvermessung';
                        $row['fgdfgfd']=strtotime($row['Ende']);
                        unset($row['id']);
                        unset($row['kid']);
                        unset($row['Dates']);
                        unset($row['VordergrundFarbe']);
                        unset($row['Ende']);
                        array_push($narr,$row);
            }
            return $narr;
        }else{
            return [];
        }
    }
    public function _getWundauswertung($qtype=null) //Done
    {
        $year=null;
        $dateObj = strtotime($this->requestDate);
        $monat=date('Y-m-d', intval($dateObj)); 
       ( $year==null)?
        $query ="WITH Kalender AS (SELECT 
        ID AS kid,
        CASE 
            WHEN TRY_CAST(BackColor AS INT) IS NULL THEN '#ff5eac' 
            ELSE '#' + RIGHT('000000' + CONVERT(VARCHAR(6), FORMAT(CAST(BackColor AS INT), 'X')), 6) 
        END AS katBackColor
    FROM [".$this->dbnameV."].[dbo].KalenderKategorien 
    WHERE Kategorie = 'Wundauswertung'
)
SELECT DISTINCT 
    B.BewohnerNr AS id,
    Z.Station AS wohnbereich,
    Z.Haus as haus,
    K.kid, 
    K.katBackColor,
	CASE 
        WHEN PB.[Wundauswertung] IS NOT NULL 
        THEN FORMAT(PB.[Wundauswertung], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Dates,
    '#000000' AS VordergrundFarbe,
	CASE 
        WHEN PB.[Wundauswertung] IS NOT NULL 
        THEN FORMAT(PB.[Wundauswertung], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Ende,
    B.vorname + ' ' + B.Name AS Bewohner 
FROM [".$this->dbnameP."].[dbo].Bewohner PB
LEFT JOIN [".$this->dbnameV."].[dbo].Bewohner B ON PB.BewohnerNr = B.BewohnerNr 
LEFT JOIN [".$this->dbnameV."].[dbo].Zimmer Z ON B.Zimmer = Z.ZimmerNummer
CROSS JOIN Kalender K
WHERE  
PB.[Wundauswertung] Is Not Null and     
PB.[Wundauswertung] = CAST('".$monat."' AS DATE) and  
B.Abgangsdatum is null AND 
B.BewohnerNr < 70000 ORDER BY Dates ASC
    "
        :
        $query = "WITH Kalender AS (SELECT 
        ID AS kid,
        CASE 
            WHEN TRY_CAST(BackColor AS INT) IS NULL THEN '#ff5eac' 
            ELSE '#' + RIGHT('000000' + CONVERT(VARCHAR(6), FORMAT(CAST(BackColor AS INT), 'X')), 6) 
        END AS katBackColor
    FROM [".$this->dbnameV."].[dbo].KalenderKategorien 
    WHERE Kategorie = 'Wundauswertung'
)
SELECT DISTINCT 
    B.BewohnerNr AS id,
    Z.Station AS wohnbereich,
    Z.Haus as haus,
    K.kid, 
    K.katBackColor,
	CASE 
        WHEN PB.[Wundauswertung] IS NOT NULL 
        THEN FORMAT(PB.[Wundauswertung], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Dates,
    '#000000' AS VordergrundFarbe,
	CASE 
        WHEN PB.[Wundauswertung] IS NOT NULL 
        THEN FORMAT(PB.[Wundauswertung], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Ende,
    B.vorname + ' ' + B.Name AS Bewohner 
FROM [".$this->dbnameP."].[dbo].Bewohner PB
LEFT JOIN [".$this->dbnameV."].[dbo].Bewohner B ON PB.BewohnerNr = B.BewohnerNr 
LEFT JOIN [".$this->dbnameV."].[dbo].Zimmer Z ON B.Zimmer = Z.ZimmerNummer
CROSS JOIN Kalender K
WHERE  
PB.[Wundauswertung] Is Not Null and     
PB.[Wundauswertung] = CAST('".$monat."' AS DATE) and  
B.Abgangsdatum is null AND 
B.BewohnerNr < 70000 ORDER BY Dates ASC
    ";
    $result = $this->conn->query($query, []);
     
        if ($result!=false&&count($result)>0) { 
            $narr=[];
            foreach($result as $row){ 
                        $row['id']='WUAUS-'.$row['id'].$row['kid'];
                        $row['titel']='Wundauswertung';
                        $row['realtimestart']='00:00';
                        $row['realtimeend']='23:59'; 
                        $row['ColorHex']= '#a86f5b'; 
                        $row['katBackColor']= '#a86f5b';  
                        $row['datum']=explode('.',$row['Ende'])[2].'-'.explode('.',$row['Ende'])[1].'-'.explode('.',$row['Ende'])[0]; 
                        $row['realtimestartDate']=$row['Ende'];
                        $row['realtimeendDate']=$row['Ende'];
                        $row['isNoteAttached']=NULL;
                        $row['time']=intval(0);
                        $row['duration']=intval(24*4);
                        $row['ersteller']= strtoupper('XXXXXX'); 
                        $row['isAlarm']=false;
                        $row['isAlarmStamp']=NULL;
                        $row['isEditable']=false;
                        $row['eventTyp']=$row['kid'];
                        $row['isPublic']=true;
                        $row['isprivate']=false; 
                        $row['VerwaltungPflege']=NULL; 
                        $row['kategorie']=$row['kid']; 
                        $row['katForeColor']='#000000';   
                        $row['katBezeichnung']='Wundauswertung'; 
                        $row['fgdfgfd']=strtotime($row['Ende']); 
                        unset($row['id']); 
                        unset($row['kid']);
                        unset($row['Dates']);
                        unset($row['VordergrundFarbe']); 
                        unset($row['Ende']); 
                        array_push($narr,$row); 
            }
            return $narr; 
        }else{
            return [];
        } 
    }
    public function _getEvaluierung($qtype=null) //Done
    {
        $year=null;
        $dateObj = strtotime($this->requestDate);
        $monat=date('Y-m-d', intval($dateObj)); 
       ( $year==null)?
        $query ="WITH Kalender AS (SELECT 
        ID AS kid,
        CASE 
            WHEN TRY_CAST(BackColor AS INT) IS NULL THEN '#ff5eac' 
            ELSE '#' + RIGHT('000000' + CONVERT(VARCHAR(6), FORMAT(CAST(BackColor AS INT), 'X')), 6) 
        END AS katBackColor
    FROM [".$this->dbnameV."].[dbo].KalenderKategorien 
    WHERE Kategorie = 'Evaluierung'
)
SELECT DISTINCT 
    B.BewohnerNr AS id,
    Z.Station AS wohnbereich,
    Z.Haus as haus,
    K.kid, 
    K.katBackColor,
	CASE 
        WHEN PB.[Nächste Evaluierung] IS NOT NULL 
        THEN FORMAT(PB.[Nächste Evaluierung], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Dates,
    '#000000' AS VordergrundFarbe,
	CASE 
        WHEN PB.[Nächste Evaluierung] IS NOT NULL 
        THEN FORMAT(PB.[Nächste Evaluierung], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Ende,
    B.vorname + ' ' + B.Name AS Bewohner 
FROM [".$this->dbnameP."].[dbo].Bewohner PB
LEFT JOIN [".$this->dbnameV."].[dbo].Bewohner B ON PB.BewohnerNr = B.BewohnerNr 
LEFT JOIN [".$this->dbnameV."].[dbo].Zimmer Z ON B.Zimmer = Z.ZimmerNummer
CROSS JOIN Kalender K
WHERE  
PB.[Nächste Evaluierung] Is Not Null and     
PB.[Nächste Evaluierung] = CAST('".$monat."' AS DATE) and  
B.Abgangsdatum is null AND 
B.BewohnerNr < 70000 ORDER BY Dates ASC
    "
        :
        $query = "WITH Kalender AS (SELECT 
        ID AS kid,
        CASE 
            WHEN TRY_CAST(BackColor AS INT) IS NULL THEN '#ff5eac' 
            ELSE '#' + RIGHT('000000' + CONVERT(VARCHAR(6), FORMAT(CAST(BackColor AS INT), 'X')), 6) 
        END AS katBackColor
    FROM [".$this->dbnameV."].[dbo].KalenderKategorien 
    WHERE Kategorie = 'Evaluierung'
)
SELECT DISTINCT 
    B.BewohnerNr AS id,
    Z.Station AS wohnbereich,
    Z.Haus as haus,
    K.kid, 
    K.katBackColor,
	CASE 
        WHEN PB.[Nächste Evaluierung] IS NOT NULL 
        THEN FORMAT(PB.[Nächste Evaluierung], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Dates,
    '#000000' AS VordergrundFarbe,
	CASE 
        WHEN PB.[Nächste Evaluierung] IS NOT NULL 
        THEN FORMAT(PB.[Nächste Evaluierung], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Ende,
    B.vorname + ' ' + B.Name AS Bewohner 
FROM [".$this->dbnameP."].[dbo].Bewohner PB
LEFT JOIN [".$this->dbnameV."].[dbo].Bewohner B ON PB.BewohnerNr = B.BewohnerNr 
LEFT JOIN [".$this->dbnameV."].[dbo].Zimmer Z ON B.Zimmer = Z.ZimmerNummer
CROSS JOIN Kalender K
WHERE  
PB.[Nächste Evaluierung] Is Not Null and     
PB.[Nächste Evaluierung] = CAST('".$monat."' AS DATE) and  
B.Abgangsdatum is null AND 
B.BewohnerNr < 70000 ORDER BY Dates ASC
    ";
    $result = $this->conn->query($query, []);
     
        if ($result!=false&&count($result)>0) { 
            $narr=[];
            foreach($result as $row){ 
                        $row['id']='EVAL-'.$row['id'].$row['kid'];
                        $row['titel']='Evaluierung';
                        $row['realtimestart']='00:00';
                        $row['realtimeend']='23:59'; 
                        $row['ColorHex']= '#b598b0'; 
                        $row['katBackColor']= '#b598b0';  
                        $row['datum']=explode('.',$row['Ende'])[2].'-'.explode('.',$row['Ende'])[1].'-'.explode('.',$row['Ende'])[0]; 
                        $row['realtimestartDate']=$row['Ende'];
                        $row['realtimeendDate']=$row['Ende'];
                        $row['isNoteAttached']=NULL;
                        $row['time']=intval(0);
                        $row['duration']=intval(24*4);
                        $row['ersteller']= strtoupper('XXXXXX'); 
                        $row['isAlarm']=false;
                        $row['isAlarmStamp']=NULL;
                        $row['isEditable']=false;
                        $row['eventTyp']=$row['kid'];
                        $row['isPublic']=true;
                        $row['isprivate']=false; 
                        $row['VerwaltungPflege']=NULL; 
                        $row['kategorie']=$row['kid']; 
                        $row['katForeColor']='#000000';   
                        $row['katBezeichnung']='Evaluierung'; 
                        $row['fgdfgfd']=strtotime($row['Ende']); 
                        unset($row['id']); 
                        unset($row['kid']);
                        unset($row['Dates']);
                        unset($row['VordergrundFarbe']); 
                        unset($row['Ende']); 
                        array_push($narr,$row); 
            }
            return $narr; 
        }else{
            return [];
        } 
    }
    public function _getPflegeVisite($qtype=null)//Done
    {
        $year=null;
        $dateObj = strtotime($this->requestDate);
        $monat=date('Y-m-d', intval($dateObj)); 
       ( $year==null)?
        $query ="WITH Kalender AS (SELECT 
        ID AS kid,
        CASE 
            WHEN TRY_CAST(BackColor AS INT) IS NULL THEN '#ff5eac' 
            ELSE '#' + RIGHT('000000' + CONVERT(VARCHAR(6), FORMAT(CAST(BackColor AS INT), 'X')), 6) 
        END AS katBackColor
    FROM [".$this->dbnameV."].[dbo].KalenderKategorien 
    WHERE Kategorie = 'Pflegevisite'
)
SELECT DISTINCT 
    B.BewohnerNr AS id,
    Z.Station AS wohnbereich,
    Z.Haus as haus,
    K.kid, 
    K.katBackColor,
	CASE 
        WHEN PB.[Nächste Pflegevisite] IS NOT NULL 
        THEN FORMAT(PB.[Nächste Pflegevisite], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Dates,
    '#000000' AS VordergrundFarbe,
	CASE 
        WHEN PB.[Nächste Pflegevisite] IS NOT NULL 
        THEN FORMAT(PB.[Nächste Pflegevisite], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Ende,
    B.vorname + ' ' + B.Name AS Bewohner 
FROM [".$this->dbnameP."].[dbo].Bewohner PB
LEFT JOIN [".$this->dbnameV."].[dbo].Bewohner B ON PB.BewohnerNr = B.BewohnerNr 
LEFT JOIN [".$this->dbnameV."].[dbo].Zimmer Z ON B.Zimmer = Z.ZimmerNummer
CROSS JOIN Kalender K
WHERE  
PB.[Nächste Pflegevisite] Is Not Null and     
PB.[Nächste Pflegevisite] = CAST('".$monat."' AS DATE) and  
B.Abgangsdatum is null AND 
B.BewohnerNr < 70000 ORDER BY Dates ASC
    "
        :
        $query = "WITH Kalender AS (SELECT 
        ID AS kid,
        CASE 
            WHEN TRY_CAST(BackColor AS INT) IS NULL THEN '#ff5eac' 
            ELSE '#' + RIGHT('000000' + CONVERT(VARCHAR(6), FORMAT(CAST(BackColor AS INT), 'X')), 6) 
        END AS katBackColor
    FROM [".$this->dbnameV."].[dbo].KalenderKategorien 
    WHERE Kategorie = 'Pflegevisite'
)
SELECT DISTINCT 
    B.BewohnerNr AS id,
    Z.Station AS wohnbereich,
    Z.Haus as haus,
    K.kid, 
    K.katBackColor,
	CASE 
        WHEN PB.[Nächste Pflegevisite] IS NOT NULL 
        THEN FORMAT(PB.[Nächste Pflegevisite], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Dates,
    '#000000' AS VordergrundFarbe,
	CASE 
        WHEN PB.[Nächste Pflegevisite] IS NOT NULL 
        THEN FORMAT(PB.[Nächste Pflegevisite], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Ende,
    B.vorname + ' ' + B.Name AS Bewohner 
FROM [".$this->dbnameP."].[dbo].Bewohner PB
LEFT JOIN [".$this->dbnameV."].[dbo].Bewohner B ON PB.BewohnerNr = B.BewohnerNr 
LEFT JOIN [".$this->dbnameV."].[dbo].Zimmer Z ON B.Zimmer = Z.ZimmerNummer
CROSS JOIN Kalender K
WHERE  
PB.[Nächste Pflegevisite] Is Not Null and     
PB.[Nächste Pflegevisite] = CAST('".$monat."' AS DATE) and  
B.Abgangsdatum is null AND 
B.BewohnerNr < 70000 ORDER BY Dates ASC
    ";
    $result = $this->conn->query($query, []);
     
        if ($result!=false&&count($result)>0) { 
            $narr=[];
            foreach($result as $row){ 
                        $row['id']='PFV-'.$row['id'].$row['kid'];
                        $row['titel']='Pflegevisite';
                        $row['realtimestart']='00:00';
                        $row['realtimeend']='23:59'; 
                        $row['ColorHex']= '#58e3f5'; 
                        $row['katBackColor']= '#58e3f5';  
                        $row['datum']=explode('.',$row['Ende'])[2].'-'.explode('.',$row['Ende'])[1].'-'.explode('.',$row['Ende'])[0]; 
                        $row['realtimestartDate']=$row['Ende'];
                        $row['realtimeendDate']=$row['Ende'];
                        $row['isNoteAttached']=NULL;
                        $row['time']=intval(0);
                        $row['duration']=intval(24*4);
                        $row['ersteller']= strtoupper('XXXXXX'); 
                        $row['isAlarm']=false;
                        $row['isAlarmStamp']=NULL;
                        $row['isEditable']=false;
                        $row['eventTyp']=$row['kid'];
                        $row['isPublic']=true;
                        $row['isprivate']=false; 
                        $row['VerwaltungPflege']=NULL; 
                        $row['kategorie']=$row['kid']; 
                        $row['katForeColor']='#000000';   
                        $row['katBezeichnung']='Pflegevisite'; 
                        $row['fgdfgfd']=strtotime($row['Ende']); 
                        unset($row['id']); 
                        unset($row['kid']);
                        unset($row['Dates']);
                        unset($row['VordergrundFarbe']); 
                        unset($row['Ende']); 
                        array_push($narr,$row); 
            }
            return $narr; 
        }else{
            return [];
        } 
    }
    public function _getSchwerbehindertausweis($qtype=null) //Done
    {
        $year=null;
        $dateObj = strtotime($this->requestDate);
        $monat=date('Y-m-d', intval($dateObj)); 
       ( $year==null)?
        $query ="WITH Kalender AS (SELECT 
        ID AS kid,
        CASE 
            WHEN TRY_CAST(BackColor AS INT) IS NULL THEN '#ff5eac' 
            ELSE '#' + RIGHT('000000' + CONVERT(VARCHAR(6), FORMAT(CAST(BackColor AS INT), 'X')), 6) 
        END AS katBackColor
    FROM [".$this->dbnameV."].[dbo].KalenderKategorien 
    WHERE Kategorie = 'Schwerbehindertenausweis'
)
SELECT DISTINCT 
    B.BewohnerNr AS id,
    Z.Station AS wohnbereich,
    Z.Haus as haus,
    K.kid, 
    K.katBackColor,
	CASE 
        WHEN B.[Schwerbehindert gültig bis] IS NOT NULL 
        THEN FORMAT(B.[Schwerbehindert gültig bis], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Dates,
    '#000000' AS VordergrundFarbe,
	CASE 
        WHEN B.[Schwerbehindert gültig bis] IS NOT NULL 
        THEN FORMAT(B.[Schwerbehindert gültig bis], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Ende,
    B.vorname + ' ' + B.Name AS Bewohner 
FROM [".$this->dbnameV."].[dbo].Bewohner B
LEFT JOIN [".$this->dbnameV."].[dbo].Zimmer Z ON B.Zimmer = Z.ZimmerNummer
CROSS JOIN Kalender K
WHERE  
B.[Schwerbehindert gültig bis] Is Not Null and     
B.[Schwerbehindert gültig bis] = CAST('".$monat."' AS DATE) and  
B.schwerbehindert =1 AND
B.Abgangsdatum is null AND 
B.BewohnerNr < 70000 ORDER BY Dates ASC
    "
        :
        $query = "WITH Kalender AS (SELECT 
        ID AS kid,
        CASE 
            WHEN TRY_CAST(BackColor AS INT) IS NULL THEN '#ff5eac' 
            ELSE '#' + RIGHT('000000' + CONVERT(VARCHAR(6), FORMAT(CAST(BackColor AS INT), 'X')), 6) 
        END AS katBackColor
    FROM [".$this->dbnameV."].[dbo].KalenderKategorien 
    WHERE Kategorie = 'Schwerbehindertenausweis'
)
SELECT DISTINCT 
    B.BewohnerNr AS id,
    Z.Station AS wohnbereich,
    Z.Haus as haus,
    K.kid, 
    K.katBackColor,
	CASE 
        WHEN B.[Schwerbehindert gültig bis] IS NOT NULL 
        THEN FORMAT(B.[Schwerbehindert gültig bis], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Dates,
    '#000000' AS VordergrundFarbe,
	CASE 
        WHEN B.[Schwerbehindert gültig bis] IS NOT NULL 
        THEN FORMAT(B.[Schwerbehindert gültig bis], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Ende,
    B.vorname + ' ' + B.Name AS Bewohner 
FROM [".$this->dbnameV."].[dbo].Bewohner B
LEFT JOIN [".$this->dbnameV."].[dbo].Zimmer Z ON B.Zimmer = Z.ZimmerNummer
CROSS JOIN Kalender K
WHERE  
B.[Schwerbehindert gültig bis] Is Not Null and     
B.[Schwerbehindert gültig bis] = CAST('".$monat."' AS DATE) and  
B.schwerbehindert =1 AND
B.Abgangsdatum is null AND 
B.BewohnerNr < 70000 ORDER BY Dates ASC
    ";
    $result = $this->conn->query($query, []);
     
        if ($result!=false&&count($result)>0) { 
            $narr=[];
            foreach($result as $row){ 
                        $row['id']='SCHWAUS-'.$row['id'].$row['kid'];
                        $row['titel']='Schwerbehindertausweis';
                        $row['realtimestart']='00:00';
                        $row['realtimeend']='23:59'; 
                        $row['ColorHex']= '#0ecf9f'; 
                        $row['katBackColor']= '#0ecf9f';  
                        $row['datum']=explode('.',$row['Ende'])[2].'-'.explode('.',$row['Ende'])[1].'-'.explode('.',$row['Ende'])[0]; 
                        $row['realtimestartDate']=$row['Ende'];
                        $row['realtimeendDate']=$row['Ende'];
                        $row['isNoteAttached']=NULL;
                        $row['time']=intval(0);
                        $row['duration']=intval(24*4);
                        $row['ersteller']= strtoupper('XXXXXX'); 
                        $row['isAlarm']=false;
                        $row['isAlarmStamp']=NULL;
                        $row['isEditable']=false;
                        $row['eventTyp']=$row['kid'];
                        $row['isPublic']=true;
                        $row['isprivate']=false; 
                        $row['VerwaltungPflege']=NULL; 
                        $row['kategorie']=$row['kid']; 
                        $row['katForeColor']='#000000';   
                        $row['katBezeichnung']='Schwerbehindertausweis'; 
                        $row['fgdfgfd']=strtotime($row['Ende']); 
                        unset($row['id']); 
                        unset($row['kid']);
                        unset($row['Dates']);
                        unset($row['VordergrundFarbe']); 
                        unset($row['Ende']); 
                        array_push($narr,$row); 
            }
            return $narr; 
        }else{
            return [];
        } 
    } 
    public function _gettabellenwohngeld($qtype=null) //Done
    {
        $year=null;
        $dateObj = strtotime($this->requestDate);
        $monat=date('Y-m-d', intval($dateObj)); 
       ( $year==null)?
        $query ="WITH Kalender AS (SELECT 
        ID AS kid,
        CASE 
            WHEN TRY_CAST(BackColor AS INT) IS NULL THEN '#ff5eac' 
            ELSE '#' + RIGHT('000000' + CONVERT(VARCHAR(6), FORMAT(CAST(BackColor AS INT), 'X')), 6) 
        END AS katBackColor
    FROM [".$this->dbnameV."].[dbo].KalenderKategorien 
    WHERE Kategorie = 'Tabellenwohngeld'
)
SELECT DISTINCT 
    B.BewohnerNr AS id,
    Z.Station AS wohnbereich,
    Z.Haus as haus,
    K.kid, 
    K.katBackColor,
	CASE 
        WHEN B.[Tabellenwohngeld genehmigt] IS NOT NULL 
        THEN FORMAT(B.[Tabellenwohngeld genehmigt], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Dates,
    '#000000' AS VordergrundFarbe,
	CASE 
        WHEN B.[Tabellenwohngeld genehmigt] IS NOT NULL 
        THEN FORMAT(B.[Tabellenwohngeld genehmigt], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Ende,
    B.vorname + ' ' + B.Name AS Bewohner 
FROM [".$this->dbnameV."].[dbo].Bewohner B
LEFT JOIN [".$this->dbnameV."].[dbo].Zimmer Z ON B.Zimmer = Z.ZimmerNummer
CROSS JOIN Kalender K
WHERE  
B.[Tabellenwohngeld genehmigt] Is Not Null and     
B.[Tabellenwohngeld genehmigt] = CAST('".$monat."' AS DATE) and 
B.Tabellenwohngeld <> 0 and 
B.Tabellenwohngeld is not null AND
B.Abgangsdatum is null AND 
B.BewohnerNr < 70000 ORDER BY Dates ASC
    "
        :
        $query = "WITH Kalender AS (SELECT 
        ID AS kid,
        CASE 
            WHEN TRY_CAST(BackColor AS INT) IS NULL THEN '#ff5eac' 
            ELSE '#' + RIGHT('000000' + CONVERT(VARCHAR(6), FORMAT(CAST(BackColor AS INT), 'X')), 6) 
        END AS katBackColor
    FROM [".$this->dbnameV."].[dbo].KalenderKategorien 
    WHERE Kategorie = 'Tabellenwohngeld'
)
SELECT DISTINCT 
    B.BewohnerNr AS id,
    Z.Station AS wohnbereich,
    Z.Haus as haus,
    K.kid, 
    K.katBackColor,
	CASE 
        WHEN B.[Tabellenwohngeld genehmigt] IS NOT NULL 
        THEN FORMAT(B.[Tabellenwohngeld genehmigt], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Dates,
    '#000000' AS VordergrundFarbe,
	CASE 
        WHEN B.[Tabellenwohngeld genehmigt] IS NOT NULL 
        THEN FORMAT(B.[Tabellenwohngeld genehmigt], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Ende,
    B.vorname + ' ' + B.Name AS Bewohner 
FROM [".$this->dbnameV."].[dbo].Bewohner B
LEFT JOIN [".$this->dbnameV."].[dbo].Zimmer Z ON B.Zimmer = Z.ZimmerNummer
CROSS JOIN Kalender K
WHERE  
B.[Tabellenwohngeld genehmigt] Is Not Null and     
B.[Tabellenwohngeld genehmigt] = CAST('".$monat."' AS DATE) and 
B.Tabellenwohngeld <> 0 and 
B.Tabellenwohngeld is not null AND
B.Abgangsdatum is null AND 
B.BewohnerNr < 70000 ORDER BY Dates ASC
    ";
    $result = $this->conn->query($query, []);
     
        if ($result!=false&&count($result)>0) { 
            $narr=[];
            foreach($result as $row){ 
                        $row['id']='TBW-'.$row['id'].$row['kid'];
                        $row['titel']='Tabellenwohngeld';
                        $row['realtimestart']='00:00';
                        $row['realtimeend']='23:59'; 
                        $row['ColorHex']= '#a89e5b'; 
                        $row['katBackColor']= '#a89e5b';  
                        $row['datum']=explode('.',$row['Ende'])[2].'-'.explode('.',$row['Ende'])[1].'-'.explode('.',$row['Ende'])[0]; 
                        $row['realtimestartDate']=$row['Ende'];
                        $row['realtimeendDate']=$row['Ende'];
                        $row['isNoteAttached']=NULL;
                        $row['time']=intval(0);
                        $row['duration']=intval(24*4);
                        $row['ersteller']= strtoupper('XXXXXX'); 
                        $row['isAlarm']=false;
                        $row['isAlarmStamp']=NULL;
                        $row['isEditable']=false;
                        $row['eventTyp']=$row['kid'];
                        $row['isPublic']=true;
                        $row['isprivate']=false; 
                        $row['VerwaltungPflege']=NULL; 
                        $row['kategorie']=$row['kid']; 
                        $row['katForeColor']='#000000';   
                        $row['katBezeichnung']='Tabellenwohngeld'; 
                        $row['fgdfgfd']=strtotime($row['Ende']); 
                        unset($row['id']); 
                        unset($row['kid']);
                        unset($row['Dates']);
                        unset($row['VordergrundFarbe']); 
                        unset($row['Ende']); 
                        array_push($narr,$row); 
            }
            return $narr; 
        }else{
            return [];
        }
    
         
    } 
    public function _getpflegewohngeld($qtype=null) //Done
    {
        $year=null;
        $dateObj = strtotime($this->requestDate);
        $monat=date('Y-m-d', intval($dateObj)); 
       ( $year==null)?
        $query ="WITH Kalender AS (SELECT 
        ID AS kid,
        CASE 
            WHEN TRY_CAST(BackColor AS INT) IS NULL THEN '#ff5eac' 
            ELSE '#' + RIGHT('000000' + CONVERT(VARCHAR(6), FORMAT(CAST(BackColor AS INT), 'X')), 6) 
        END AS katBackColor
    FROM [".$this->dbnameV."].[dbo].KalenderKategorien 
    WHERE Kategorie = 'Pflegewohngeld'
)
SELECT DISTINCT 
    B.BewohnerNr AS id,
    Z.Station AS wohnbereich,
    Z.Haus as haus,
    K.kid, 
    K.katBackColor,
	CASE 
        WHEN B.[Pflegewohngeld genehmigt] IS NOT NULL 
        THEN FORMAT(B.[Pflegewohngeld genehmigt], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Dates,
    '#000000' AS VordergrundFarbe,
	CASE 
        WHEN B.[Pflegewohngeld genehmigt] IS NOT NULL 
        THEN FORMAT(B.[Pflegewohngeld genehmigt], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Ende,
    B.vorname + ' ' + B.Name AS Bewohner 
FROM [".$this->dbnameV."].[dbo].Bewohner B
LEFT JOIN [".$this->dbnameV."].[dbo].Zimmer Z ON B.Zimmer = Z.ZimmerNummer
CROSS JOIN Kalender K
WHERE  
B.[Pflegewohngeld genehmigt] Is Not Null and     
B.[Pflegewohngeld genehmigt] = CAST('".$monat."' AS DATE) and 
B.Pflegewohngeld <> 0 and 
B.Abgangsdatum is null AND 
B.BewohnerNr < 70000 ORDER BY Dates ASC
    "
        :
        $query = "WITH Kalender AS (SELECT 
        ID AS kid,
        CASE 
            WHEN TRY_CAST(BackColor AS INT) IS NULL THEN '#ff5eac' 
            ELSE '#' + RIGHT('000000' + CONVERT(VARCHAR(6), FORMAT(CAST(BackColor AS INT), 'X')), 6) 
        END AS katBackColor
    FROM [".$this->dbnameV."].[dbo].KalenderKategorien 
    WHERE Kategorie = 'Pflegewohngeld'
)
SELECT DISTINCT 
    B.BewohnerNr AS id,
    Z.Station AS wohnbereich,
    Z.Haus as haus,
    K.kid, 
    K.katBackColor,
	CASE 
        WHEN B.[Pflegewohngeld genehmigt] IS NOT NULL 
        THEN FORMAT(B.[Pflegewohngeld genehmigt], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Dates,
    '#000000' AS VordergrundFarbe,
	CASE 
        WHEN B.[Pflegewohngeld genehmigt] IS NOT NULL 
        THEN FORMAT(B.[Pflegewohngeld genehmigt], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Ende,
    B.vorname + ' ' + B.Name AS Bewohner 
FROM [".$this->dbnameV."].[dbo].Bewohner B
LEFT JOIN [".$this->dbnameV."].[dbo].Zimmer Z ON B.Zimmer = Z.ZimmerNummer
CROSS JOIN Kalender K
WHERE   
B.[Pflegewohngeld genehmigt] Is Not Null and     
B.[Pflegewohngeld genehmigt] = CAST('".$monat."' AS DATE) and 
B.Pflegewohngeld <> 0 and 
B.Abgangsdatum is null AND 
B.BewohnerNr < 70000 ORDER BY Dates ASC
    ";
    $result = $this->conn->query($query, []);
     
        if ($result!=false&&count($result)>0) { 
            $narr=[];
            foreach($result as $row){ 
                        $row['id']='PFL-'.$row['id'].$row['kid'];
                        $row['titel']='Pflegewohngeld';
                        $row['realtimestart']='00:00';
                        $row['realtimeend']='23:59'; 
                        $row['ColorHex']= '#3ea86e'; 
                        $row['katBackColor']= '#3ea86e';  
                        $row['datum']=explode('.',$row['Ende'])[2].'-'.explode('.',$row['Ende'])[1].'-'.explode('.',$row['Ende'])[0]; 
                        $row['realtimestartDate']=$row['Ende'];
                        $row['realtimeendDate']=$row['Ende'];
                        $row['isNoteAttached']=NULL;
                        $row['time']=intval(0);
                        $row['duration']=intval(24*4);
                        $row['ersteller']= strtoupper('XXXXXX'); 
                        $row['isAlarm']=false;
                        $row['isAlarmStamp']=NULL;
                        $row['isEditable']=false;
                        $row['eventTyp']=$row['kid'];
                        $row['isPublic']=true;
                        $row['isprivate']=false; 
                        $row['VerwaltungPflege']=NULL; 
                        $row['kategorie']=$row['kid']; 
                        $row['katForeColor']='#000000';   
                        $row['katBezeichnung']='Pflegewohngeld'; 
                        $row['fgdfgfd']=strtotime($row['Ende']); 
                        unset($row['id']); 
                        unset($row['kid']);
                        unset($row['Dates']);
                        unset($row['VordergrundFarbe']); 
                        unset($row['Ende']); 
                        array_push($narr,$row); 
            }
            return $narr; 
        }else{
            return [];
        }
    
         
    } 
    public function _getPersonalAusweis($qtype=null) //Done
    {
        $year=null;
        $dateObj = strtotime($this->requestDate);
        $monat=date('Y-m-d', intval($dateObj)); 
       ( $year==null)?
        $query ="WITH Kalender AS (
    SELECT 
        ID AS kid,
        CASE 
            WHEN TRY_CAST(BackColor AS INT) IS NULL THEN '#ff5eac' 
            ELSE '#' + RIGHT('000000' + CONVERT(VARCHAR(6), FORMAT(CAST(BackColor AS INT), 'X')), 6) 
        END AS katBackColor
    FROM [".$this->dbnameV."].[dbo].KalenderKategorien 
    WHERE Kategorie = 'Personalausweis'
)
SELECT DISTINCT 
    B.BewohnerNr AS id,
    Z.Station AS wohnbereich,
    Z.Haus as haus,
    K.kid,
    CASE 
        WHEN B.[Personalausweis gültig bis] IS NOT NULL 
        THEN FORMAT(B.[Personalausweis gültig bis], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Dates,
    K.katBackColor,
    '#000000' AS VordergrundFarbe,
    B.vorname + ' ' + B.Name AS Bewohner,
    FORMAT(B.[Personalausweis gültig bis], 'dd.MM.yyyy') AS Ende
FROM [".$this->dbnameV."].[dbo].Bewohner B
LEFT JOIN [".$this->dbnameV."].[dbo].Zimmer Z ON B.Zimmer = Z.ID
CROSS JOIN Kalender K
WHERE 
    B.[Personalausweis gültig bis] IS NOT NULL  AND B.[Personalausweis gültig bis] = CAST('".$monat."' AS DATE) AND B.Abgangsdatum is null AND B.BewohnerNr < 70000 ORDER BY Dates ASC
    "
        :
        $query = "WITH Kalender AS (
    SELECT 
        ID AS kid,
        CASE 
            WHEN TRY_CAST(BackColor AS INT) IS NULL THEN '#ff5eac' 
            ELSE '#' + RIGHT('000000' + CONVERT(VARCHAR(6), FORMAT(CAST(BackColor AS INT), 'X')), 6) 
        END AS katBackColor
    FROM [".$this->dbnameV."].[dbo].KalenderKategorien 
    WHERE Kategorie = 'Personalausweis'
)
SELECT DISTINCT 
    B.BewohnerNr AS id,
    Z.Station AS wohnbereich,
    Z.Haus as haus,
    K.kid,
    CASE 
        WHEN B.[Personalausweis gültig bis] IS NOT NULL 
        THEN FORMAT(B.[Personalausweis gültig bis], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Dates,
    K.katBackColor,
    '#000000' AS VordergrundFarbe,
    B.vorname + ' ' + B.Name AS Bewohner,
    FORMAT(B.[Personalausweis gültig bis], 'dd.MM.yyyy') AS Ende
FROM [".$this->dbnameV."].[dbo].Bewohner B
LEFT JOIN [".$this->dbnameV."].[dbo].Zimmer Z ON B.Zimmer = Z.ZimmerNummer
CROSS JOIN Kalender K
WHERE 
    B.[Personalausweis gültig bis] IS NOT NULL  AND B.[Personalausweis gültig bis] = CAST('".$monat."' AS DATE) AND B.Abgangsdatum is null AND B.BewohnerNr < 70000 ORDER BY Dates ASC
    ";
    $result = $this->conn->query($query, []);
     
        if ($result!=false&&is_array($result)&&count($result)>0) { 
            $narr=[];
            foreach($result as $row){ 
                        $row['id']='GEZ-'.$row['id'].$row['kid'];
                        $row['titel']='Bew. Personalausweis';
                        $row['realtimestart']='00:00';
                        $row['realtimeend']='23:59'; 
                        $row['ColorHex']= '#6e7fc4'; 
                        $row['katBackColor']= '#6e7fc4';  
                        $row['datum']=explode('.',$row['Ende'])[2].'-'.explode('.',$row['Ende'])[1].'-'.explode('.',$row['Ende'])[0]; 
                        $row['realtimestartDate']=$row['Ende'];
                        $row['realtimeendDate']=$row['Ende'];
                        $row['isNoteAttached']=NULL;
                        $row['time']=intval(0);
                        $row['duration']=intval(24*4);
                        $row['ersteller']= strtoupper('XXXXXX'); 
                        $row['isAlarm']=false;
                        $row['isAlarmStamp']=NULL;
                        $row['isEditable']=false;
                        $row['eventTyp']=$row['kid'];
                        $row['isPublic']=true;
                        $row['isprivate']=false; 
                        $row['VerwaltungPflege']=NULL; 
                        $row['kategorie']=$row['kid']; 
                        $row['katForeColor']='#000000';   
                        $row['katBezeichnung']='Personalausweis'; 
                        $row['fgdfgfd']=strtotime($row['Ende']); 
                        unset($row['id']); 
                        unset($row['kid']);
                        unset($row['Dates']);
                        unset($row['VordergrundFarbe']); 
                        unset($row['Ende']); 
                        array_push($narr,$row); 
            }
            return $narr; 
        }else{
            return [];
        }
    
         
    } 
    public function _getBewohnerGEZ($qtype=null) //Done
    {
        $year=null;
        $dateObj = strtotime($this->requestDate);
        $monat=date('Y-m-d', intval($dateObj)); 
         
         
       ( $year==null)?
        $query ="WITH Kalender AS (
    SELECT 
        ID AS kid,
        CASE 
            WHEN TRY_CAST(BackColor AS INT) IS NULL THEN '#ff5eac' 
            ELSE '#' + RIGHT('000000' + CONVERT(VARCHAR(6), FORMAT(CAST(BackColor AS INT), 'X')), 6) 
        END AS katBackColor
    FROM [".$this->dbnameV."].[dbo].KalenderKategorien 
    WHERE Kategorie = 'GEZ-Befreiung'
)
SELECT DISTINCT 
    B.BewohnerNr AS id, 
    NULL AS wohnbereich,
    Z.Haus as haus,
    K.kid,
    CASE 
        WHEN B.[GEZ gültig bis] IS NOT NULL AND B.[GEZ befreit] = 1 
        THEN FORMAT(B.[GEZ gültig bis], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Dates,
    K.katBackColor,
    '#000000' AS VordergrundFarbe,
    B.vorname + ' ' + B.Name AS Bewohner,
    FORMAT(B.[GEZ gültig bis], 'dd.MM.yyyy') AS Ende
FROM [".$this->dbnameV."].[dbo].Bewohner B
LEFT JOIN [".$this->dbnameV."].[dbo].Zimmer Z ON B.Zimmer = Z.ID
CROSS JOIN Kalender K
WHERE 
    B.[GEZ befreit] = 1 AND B.[GEZ gültig bis] IS NOT NULL  AND B.[GEZ gültig bis] = CAST('".$monat."' AS DATE) AND B.Abgangsdatum is null AND B.BewohnerNr < 70000 ORDER BY Dates ASC
    "
        :
        $query = "WITH Kalender AS (
    SELECT 
        ID AS kid,
        CASE 
            WHEN TRY_CAST(BackColor AS INT) IS NULL THEN '#ff5eac' 
            ELSE '#' + RIGHT('000000' + CONVERT(VARCHAR(6), FORMAT(CAST(BackColor AS INT), 'X')), 6) 
        END AS katBackColor
    FROM [".$this->dbnameV."].[dbo].KalenderKategorien 
    WHERE Kategorie = 'GEZ-Befreiung'
)
SELECT DISTINCT 
    B.BewohnerNr AS id, 
    NULL AS wohnbereich,
    Z.Haus as haus,
    K.kid,
    CASE 
        WHEN B.[GEZ gültig bis] IS NOT NULL AND B.[GEZ befreit] = 1 
        THEN FORMAT(B.[GEZ gültig bis], 'dd.MM.yyyy') 
        ELSE NULL 
    END AS Dates,
    K.katBackColor,
    '#000000' AS VordergrundFarbe,
    B.vorname + ' ' + B.Name AS Bewohner,
    FORMAT(B.[GEZ gültig bis], 'dd.MM.yyyy') AS Ende
FROM [".$this->dbnameV."].[dbo].Bewohner B
LEFT JOIN [".$this->dbnameV."].[dbo].Zimmer Z ON B.Zimmer = Z.ZimmerNummer
CROSS JOIN Kalender K
WHERE 
    B.[GEZ befreit] = 1 AND B.[GEZ gültig bis] IS NOT NULL  AND B.[GEZ gültig bis] =  CAST('".$monat."' AS DATE)  AND B.Abgangsdatum is null AND B.BewohnerNr < 70000 ORDER BY Dates ASC
    ";
    $result = $this->conn->query($query, []);
     
          
        if ($result!=false&&is_array($result)&&count($result)>0) {
            $narr=[];
            foreach($result as $row){ 
                        $row['id']='GEZ-'.$row['id'].$row['kid'];
                        $row['titel']='BewohnerGEZ';
                        $row['realtimestart']='00:00';
                        $row['realtimeend']='23:59'; 
                        $row['ColorHex']= '#7d9dd4'; 
                        $row['katBackColor']= '#7d9dd4';  
                        $row['datum']=explode('.',$row['Ende'])[2].'-'.explode('.',$row['Ende'])[1].'-'.explode('.',$row['Ende'])[0]; 
                        $row['realtimestartDate']=$row['Ende'];
                        $row['realtimeendDate']=$row['Ende'];
                        $row['isNoteAttached']=NULL;
                        $row['time']=intval(0);
                        $row['duration']=intval(24*4);
                        $row['ersteller']= strtoupper('XXXXXX'); 
                        $row['isAlarm']=false;
                        $row['isAlarmStamp']=NULL;
                        $row['isEditable']=false;
                        $row['eventTyp']=$row['kid'];
                        $row['isPublic']=true;
                        $row['isprivate']=false; 
                        $row['VerwaltungPflege']=NULL; 
                        $row['kategorie']=$row['kid']; 
                        $row['katForeColor']='#000000';   
                        $row['katBezeichnung']='BewohnerGEZ'; 
                        $row['fgdfgfd']=strtotime($row['Ende']); 
                        unset($row['id']); 
                        unset($row['kid']);
                        unset($row['Dates']);
                        unset($row['VordergrundFarbe']); 
                        unset($row['Ende']); 
                        array_push($narr,$row); 
            }
            return $narr; 
        }else{
            return [];
        } 
    } 
    public function _getBewohnerGenehmigungen($qtype=null) //Done
    { 
        $year=null;
       ( $year==null)?
        $query = "SELECT 
gid as GID,
ID as BGID,
BewohnerNr,
(select top 1 Bezeichnung from [".$this->dbnameV."].[dbo].Genehmigung where Genehmigung.id = GID ) as Genehmigung, 
(select top 1 Bezeichnung from [".$this->dbnameV."].[dbo].Genehmigung where Genehmigung.id = BEW.GID ) as Bezeichnung, 
(select top 1  vorname + ' ' + Name from [".$this->dbnameV."].[dbo].Bewohner where Bewohner.BewohnerNr = BEW.BewohnerNr) as Bewohner ,
NULL as wohnbereich, 
NULL as haus, 
 FORMAT(Datum, 'dd.MM.yyyy') as datestart, 
 FORMAT(Datum, 'dd.MM.yyyy') as abDatum,
 CASE 
WHEN Bemerkung!='' AND Bemerkung is not Null 
THEN Bemerkung 
ELSE NULL 
END AS Bemerkung, 
(SELECT TOP(1) id as eid FROM  [".$this->dbnameV."].[dbo].KalenderKategorien WHERE Kategorie='Genehmigung' and (gelöschtPflege is null or gelöschtPflege = 0)) as eid
from [".$this->dbnameV."].[dbo].BewohnerGenehmigung   BEW
where 
(select top 1 Bezeichnung from [".$this->dbnameV."].[dbo].Genehmigung where Genehmigung.id = BEW.GID ) is not null and
( select top 1 Abgangsdatum from [".$this->dbnameV."].[dbo].bewohner where bewohner.BewohnerNr = BEW.BewohnerNr order by Abgangsdatum ) is null  and  
FORMAT(Datum, 'dd.MM.yyyy') = '".$this->requestDate."'  and Datum is not Null "
        :
        $query = "SELECT 
gid as GID,
ID as BGID,
BewohnerNr,
(select top 1 Bezeichnung from [".$this->dbnameV."].[dbo].Genehmigung where Genehmigung.id = GID ) as Genehmigung, 
(select top 1 Bezeichnung from [".$this->dbnameV."].[dbo].Genehmigung where Genehmigung.id = BEW.GID ) as Bezeichnung, 
(select top 1  vorname + ' ' + Name from [".$this->dbnameV."].[dbo].Bewohner where Bewohner.BewohnerNr = BEW.BewohnerNr) as Bewohner ,
NULL as wohnbereich, 
NULL as haus, 
 FORMAT(Datum, 'dd.MM.yyyy') as datestart, 
 FORMAT(Datum, 'dd.MM.yyyy') as abDatum,
 CASE 
WHEN Bemerkung!='' AND Bemerkung is not Null 
THEN Bemerkung 
ELSE NULL 
END AS Bemerkung, 
(SELECT TOP(1) id as eid FROM  [".$this->dbnameV."].[dbo].KalenderKategorien WHERE Kategorie='Genehmigung' and (gelöschtPflege is null or gelöschtPflege = 0)) as eid
from [".$this->dbnameV."].[dbo].BewohnerGenehmigung   BEW
where 
(select top 1 Bezeichnung from [".$this->dbnameV."].[dbo].Genehmigung where Genehmigung.id = BEW.GID ) is not null and
( select top 1 Abgangsdatum from [".$this->dbnameV."].[dbo].bewohner where bewohner.BewohnerNr = BEW.BewohnerNr order by Abgangsdatum ) is null  and  
FORMAT(Datum, 'dd.MM.yyyy') = '".$this->requestDate."'  and Datum is not Null ";
    
        $result = $this->conn->query($query, []);
          
        if (is_array($result)&&count($result)>0) {
            $narr=[];
            foreach($result as $row){
                $row['id']='BG-'.$row['BGID'].$row['GID'];
                $row['titel']=$row['Genehmigung'];
                $row['realtimestart']='00:00';
                $row['realtimeend']='23:59'; 
                $row['ColorHex']='#AABBFF'; 
                $row['datum']=explode('.',$row['datestart'])[2].'-'.explode('.',$row['datestart'])[1].'-'.explode('.',$row['datestart'])[0]; 
                $row['realtimestartDate']=$row['abDatum'];
                $row['realtimeendDate']=$row['datestart'];
                $row['isNoteAttached']=$row['Bemerkung']!=null?$row['Bemerkung']:null;
                $row['time']=intval(0);
                $row['duration']=intval(24*4);
                $row['ersteller']= strtoupper('XXXXXX'); 
                $row['isAlarm']=false;
                $row['isAlarmStamp']=NULL;
                $row['isEditable']=false;
                $row['eventTyp']=$row['eid'];
                $row['isPublic']=true;
                $row['isprivate']=false; 
                $row['VerwaltungPflege']=NULL; 
                $row['kategorie']=$row['eid'];
                $row['katBackColor']='#AABBFF';
                $row['katForeColor']='#000000'; 
                $row['katBezeichnung']='BewohnerGenehmigung'; 
                unset($row['BewohnerNr']); 
                unset($row['datestart']);
                unset($row['BGID']);
                unset($row['GID']);
                unset($row['moBewohnernat']);
                unset($row['eid']);
                unset($row['abDatum']); 
                array_push($narr,$row);
            }
            return $narr; 
        }else{
            return [];
        }
    
         
    } 
    public function _getMitarbeiterGeburtstage($qtype=null)//Done
    {
        $year=null;
         $dateformat=explode('.',$this->requestDate)[0].'.'.explode('.',$this->requestDate)[1]; 
       ( $year==null)?
        $query = "SELECT Name1 AS Name,Name2 AS Vorname, ID as id, MONTH(Geburtsdatum) AS monat, DAY(Geburtsdatum) AS tag, (SELECT TOP(1) CASE 
        WHEN TRY_CAST(BackColor AS INT) IS NULL THEN '#33B1FF'
        ELSE '#' + RIGHT('000000' + CONVERT(VARCHAR(6), FORMAT(CAST(BackColor AS INT), 'X')), 6) 
    END AS katBackColor FROM  [".$this->dbnameV."].dbo.KalenderKategorien WHERE Kategorie='Geburtstage der Mitarbeiter' and (gelöschtPflege is null or gelöschtPflege = 0)) as katBackColor, (SELECT TOP(1) id as eid FROM  [".$this->dbnameV."].dbo.KalenderKategorien WHERE Bezeichnung='Mitarbeiter-Geb.' and (gelöschtPflege is null or gelöschtPflege = 0)) as eid FROM [".$this->dbnameV."].dbo.Mitarbeiter
    WHERE (gelöscht IS NULL OR gelöscht=0) AND BeendigungDatum IS NULL  AND FORMAT(Geburtsdatum, 'dd.MM')='".$dateformat."' "
        :
        $query = "SELECT Name1 AS Name,Name2 AS Vorname, ID as id, MONTH(Geburtsdatum) AS monat, DAY(Geburtsdatum) AS tag, (SELECT TOP(1) CASE 
        WHEN TRY_CAST(BackColor AS INT) IS NULL THEN '#33B1FF'
        ELSE '#' + RIGHT('000000' + CONVERT(VARCHAR(6), FORMAT(CAST(BackColor AS INT), 'X')), 6) 
    END AS katBackColor FROM  [".$this->dbnameV."].dbo.KalenderKategorien WHERE Kategorie='Geburtstage der Mitarbeiter' and (gelöschtPflege is null or gelöschtPflege = 0)) as katBackColor, (SELECT TOP(1) id as eid FROM  [".$this->dbnameV."].dbo.KalenderKategorien WHERE Bezeichnung='Mitarbeiter-Geb.' and (gelöschtPflege is null or gelöschtPflege = 0)) as eid FROM [".$this->dbnameV."].dbo.Mitarbeiter
    WHERE (gelöscht IS NULL OR gelöscht=0) AND BeendigungDatum IS NULL ";
    
        $result = $this->conn->query($query, []);
          
        if (is_array($result)&&count($result)>0) {
            $narr=[];
            foreach($result as $row){
                $row['id']='M-'.$row['id'];
                $row['titel']=$row['Name'].', '.$row['Vorname'];
                $row['realtimestart']='00:00';
                $row['realtimeend']='23:59'; 
                $row['ColorHex']='#26b7f0'; 
                $row['datum']=explode('.',$this->requestDate)[2].'-'.$row['monat'].'-'.$row['tag']; 
                $row['realtimestartDate']=$row['tag'].'.'.$row['monat'].'.'.explode('.',$this->requestDate)[2];
                $row['realtimeendDate']=$row['tag'].'.'.$row['monat'].'.'.explode('.',$this->requestDate)[2];
                $row['isNoteAttached']=NULL;
                $row['time']=intval(0);
                $row['duration']=intval(24*4);
                $row['ersteller']= strtoupper('XXXXXX'); 
                $row['isAlarm']=false;
                $row['isAlarmStamp']=NULL;
                $row['isEditable']=false;
                $row['eventTyp']=$row['eid'];
                $row['isPublic']=true;
                $row['isprivate']=false; 
                $row['VerwaltungPflege']=NULL;
                $row['haus']=NULL;
                $row['wohnbereich']=NULL;
                $row['kategorie']=$row['eid'];
                $row['katBackColor']='#26b7f0';
                $row['katForeColor']='#000000';
                $row['katBezeichnung']='Geburtstag'; 
                unset($row['Name']);
                unset($row['Vorname']);
                unset($row['monat']);
                unset($row['eid']);
                unset($row['tag']);
                array_push($narr,$row);
            }
            return $narr;
        }else{
            return [];
        }
    
         
    } 
    public function _getHomes(){//Done
        if($this->dbtype=="pflege"){
    $query = "SELECT  z.Station, z.Haus,h.Haus AS Hausname    
    FROM [".$this->dbnameV."].dbo.Zimmer z
    INNER JOIN [".$this->dbnameV."].dbo.Häuser h ON h.id = z.haus
    INNER JOIN [".$this->dbnameP."].dbo.BerechtigungHäuser bh 
    ON bh.Haus = z.Haus AND bh.Station = z.Station
    WHERE bh.Name ='".$this->user."'
    GROUP BY z.Station,z.Haus, h.Haus";
    }else{
        $query = "SELECT Zimmer.Station, Zimmer.Haus, Häuser.Haus AS Hausname FROM [".$this->dbnameV."].dbo.Zimmer INNER JOIN [".$this->dbnameV."].dbo.Häuser ON Häuser.id = Zimmer.haus WHERE ( Zimmer.deaktiviert = 0 or Zimmer.deaktiviert is Null ) AND Zimmer.Station is not null  GROUP BY Zimmer.Haus, Zimmer.Station, Häuser.Haus;";
    }
    $result = $this->conn->query($query, []);
          
        if (is_array($result)&&count($result)>0) {
            $stationen=[];
            $haus=[];
             
            foreach($result as $row){
               array_push($stationen,$row['Station']); 
               array_push($haus,$row['Haus']);
            }
            return [ implode(', ', $stationen), implode(', ', $haus)];
        }else{
            return []; 
        } 
    }
    public function _getBewohnerGeburtstage($qtype=null)//Done
    {     
        $year=null;
        $dateformat=explode('.',$this->requestDate)[0].'.'.explode('.',$this->requestDate)[1];  
        $query = "SELECT DISTINCT B.BewohnerNr,B.Name,B.Vorname, Z.Haus as haus,Z.Station as wohnbereich, FORMAT(B.Geburtsdatum, 'dd.MM') as datum, 
        (SELECT TOP(1) id as eid FROM  [".$this->dbnameV."].dbo.KalenderKategorien WHERE Kategorie='Geburtstage der Bewohner' and (gelöschtPflege is null or gelöschtPflege = 0)) as eid
        FROM [".$this->dbnameV."].dbo.Bewohner B JOIN [".$this->dbnameV."].dbo.Zimmer Z ON B.Zimmer=Z.ID
        WHERE AbgangsDatum IS NULL AND B.Geburtsdatum is not NULL  AND FORMAT(B.Geburtsdatum, 'dd.MM')='".$dateformat."'";
         
        $result = $this->conn->query($query, []);
         
        if (is_array($result)&&count($result)>0) {
            $narr=[];
            foreach($result as $row){
                $row['id']='B-'.$row['BewohnerNr'];
                $row['titel']=$row['Name'].', '.$row['Vorname'];
                $row['realtimestart']='00:00';
                $row['realtimeend']='23:59'; 
                $row['ColorHex']='#faa161'; 
                $row['datum']=explode('.',$this->requestDate)[2].'-'.explode('.',$row['datum'])[1].'-'.explode('.',$row['datum'])[0]; 
                $row['realtimestartDate']=$row['datum'].'.'.explode('.',$this->requestDate)[2];
                $row['realtimeendDate']=$row['datum'].'.'.explode('.',$this->requestDate)[2];
                $row['isNoteAttached']=NULL;
                $row['time']=intval(0);
                $row['duration']=intval(24*4);
                $row['ersteller']= strtoupper('XXXXXX'); 
                $row['isAlarm']=false;
                $row['isAlarmStamp']=NULL;
                $row['isEditable']=false;
                $row['eventTyp']=$row['eid'];
                $row['isPublic']=true;
                $row['isprivate']=false; 
                $row['VerwaltungPflege']=NULL;  
                $row['kategorie']=$row['eid'];
                $row['katBackColor']='#faa161';
                $row['katForeColor']='#000000';
                $row['katBezeichnung']='BewohnerGeburtstag'; 
                unset($row['datum']);
                unset($row['Name']);
                unset($row['Vorname']);
                unset($row['BewohnerNr']); 
                array_push($narr,$row);
            }
            return $narr;
        }else{
            return[];
        }      
    } 
    public function checkWohnBereiche(): mixed
    { 
        $params =[];
        $sql = "";
        if($this->dbtype=="pflege"){
            $params = [
                ':aw' => $this->user
        ];
    $sql = "SELECT  
    z.Station,     
    z.Haus,
    h.Haus AS Hausname          
    FROM [".$this->dbnameV."].dbo.Zimmer z
    INNER JOIN [".$this->dbnameV."].dbo.Häuser h 
    ON 
    h.id = CAST(z.haus as INT)
    JOIN [".$this->dbnameP."].dbo.BerechtigungHäuser bh 
    ON bh.Haus  = CAST(z.Haus as varchar) COLLATE SQL_Latin1_General_CP1_CI_AS
    AND bh.Station = z.Station COLLATE SQL_Latin1_General_CP1_CI_AS
    WHERE bh.Name COLLATE SQL_Latin1_General_CP1_CI_AS = :aw
    GROUP BY z.Station, z.Haus, h.Haus 
    ";
        }else{
            $sql = "SELECT Zimmer.Station, Zimmer.Haus, Häuser.Haus AS Hausname FROM [".$this->dbnameV."].dbo.Zimmer INNER JOIN [".$this->dbnameV."].dbo.Häuser ON Häuser.id = Zimmer.haus WHERE ( Zimmer.deaktiviert = 0 or Zimmer.deaktiviert is Null ) AND Zimmer.Station is not null  GROUP BY Zimmer.Haus, Zimmer.Station, Häuser.Haus;";
        }  
        $result = $this->conn->query($sql, $params);
        if (!empty($result)) {
            return $result;
        } else {
            return [];
        }
    }
    /**
     * HELPER FUNCTIONS 
     * - extractDistinctStationsAndHaeuser (gibt alle Stationen und Häuser zurück worauf der User berechtigt ist)
     * - getUntilRRuleString (Gibt den entsprechenden EndDatumZeitString zurück für die RRule)
     * - isSameDay (Prüft ob das Datum innerhalb des Abruf Datums liegt bei $qType=='day')
     * - calculateEventTimeStAndEd  (Gibt die Start und Ende Zeit an hand von startzeit und Dauer zurück)
     * - getMinutesDifferenceRR  (Gibt die Duration für Tagesansicht zurück)
     * - isDateExcluded  (Gibt die Duration für Tagesansicht zurück)
     * - getallExceptions  (Gibt die Duration für Tagesansicht zurück)
     * - addColonToOffsets  (Formatiert byday)
     */
    public function addColonToOffsets(array $arr): array
    {
        return array_map(
            static function (string $item): string {
                // -?\d+  → optional minus + 1‒n digits
                // [A-Z]+ → 1‒n letters (FR, MO, TU …)
                return preg_replace('/^(-?\d+)([A-Z]+)$/i', '$1:$2', $item);
            },
            $arr
        );
    }
    public function extractDistinctStationsAndHaeuser($dataArr) {
        $stations = [];
        $haeuser = [];
    
        foreach ($dataArr as $item) {
            if (isset($item['Station'])) {
                $stations[] = $item['Station'];
            }
            if (isset($item['Haus'])) {
                $haeuser[] = $item['Haus'];
            }
        }
    
        // Remove duplicates using array_unique and reindex
        $stations = array_values(array_unique($stations));
        $haeuser = array_values(array_unique($haeuser));
    
        return [
            'stations' => $stations,
            'haeuser' => $haeuser
        ];
    }
    public function getUntilRRuleString(DateTime $date, string $mode): string {
        $utcDate = clone $date;
        $utcDate->setTimezone(new DateTimeZone('UTC'));
    
        switch (strtolower($mode)) {
            case 'day':
                $utcDate->setTime(23, 59, 59);
                break;
            case 'week':
                $utcDate->modify('sunday this week')->setTime(23, 59, 59);
                break;
            case 'month':
                $utcDate->modify('last day of this month')->setTime(23, 59, 59);
                break;
            case 'year':
                $utcDate->setDate((int)$utcDate->format('Y'), 12, 31)->setTime(23, 59, 59);
                break;
            default:
                throw new InvalidArgumentException("Invalid mode: $mode");
        }
    
        return $utcDate->format('Ymd\THis\Z');
    }
    public function isSameDay(DateTime $date1, DateTime $date2): bool {
        return $date1->format('Y-m-d') === $date2->format('Y-m-d');
    }
    public function calculateEventTimeStAndEd(int $durationMinutes, DateTime $startTime): array {
        $start = clone $startTime;
    
        // Calculate tentative end time
        $end = clone $startTime;
        $end->modify("+$durationMinutes minutes");
      
        return [
            'start' => $start->format('H:i'),
            'end' => $end->format('H:i'),
            'startobj' => $start,
            'endobj' => $end,
        ];
    }
    public function getMinutesDifferenceRR($startTime, $endTime) { 
        $start = clone $startTime;
    $end = clone $endTime;

    // Define 24:00 as a DateTime object on the same day
    $midnight = clone $start;
    $midnight->setTime(23, 59); // Use 23:59 to represent end of day safely

    // If end time goes beyond midnight, cap it
    if ($end > $midnight) {
        $end = clone $midnight;
    }

    $diff = $start->diff($end);
    $sum = $diff->h * 60 + $diff->i;
    $endsum = intval($sum / 15);

    return $endsum;
    }
    public function getallExceptions($qtype=null) { 
        $date = DateTime::createFromFormat('d.m.Y', $this->requestDate, new DateTimeZone('Europe/Berlin'));
        $startdate=$date->format('d.m.Y');
        $query = "SELECT rrevent_id as id,excluded_date  FROM  [".$this->dbnameV."].dbo.rrevent_exceptions E WHERE E.excluded_date='".$startdate."' ORDER BY E.rrevent_id DESC ";
        $result = $this->conn->query($query, []);  
        return $result;
    }
    public function isDateExcluded(array $excludedDates, string $id, DATETIME $date): bool {
        foreach ($excludedDates as $item) {
            if ($item['id'] === $id && $item['excluded_date'] === $date->format('Y-m-d')) {
                return true;
            }
        }
        return false;
    }
    public function _getRRuleEvents($qtype=null)//Done
    {     
        $Exceptions=$this->getallExceptions($qtype); 
        $narr=[]; //Rückgabe-Array;
        $bereiche=$this->checkWohnBereiche();
        $bereicheArrays=$this->extractDistinctStationsAndHaeuser($bereiche);
        $stationenNames='';
        $haeuserNames='';
        if(count($bereicheArrays['stations'])>0){
            $stationenNames="'" . implode("','",$bereicheArrays['stations']). "'";
        }
        if(count($bereicheArrays['haeuser'])>0){
            $haeuserNames="'" . implode("','",$bereicheArrays['haeuser']). "'";  
        }
        $dbpart='V';
        if($this->dbtype=='pflege'){
        $dbpart='P';
        }   
        $query = "SELECT R.*, (CASE WHEN kategorie is not null THEN (SELECT TOP(1) K.Kategorie FROM [MedicareHirtenbach].dbo.KalenderKategorien K WHERE CAST(R.kategorie AS INT)=K.ID) ELSE NULL END) as kname FROM  [".$this->dbnameV."].dbo.rrevents R WHERE R.starttime<='".$this->requestDate."'  AND (R.until is null or R.until>='".$this->requestDate."')  AND ((R.anwender='HAE') or (R.anwender!='HAE' AND systempart='PUB'))  AND ([location] in(".$haeuserNames.") or [location] is NULL)  AND ([floor] in(".$stationenNames.") or [floor] is NULL) ORDER BY  CAST(R.byhour AS INT) DESC, R.duration DESC  "; 
        $result = $this->conn->query($query, []);  
        /**
         @value $date erzeuge DateTimeObject aus Abruf-Datum für die Berechnungen
         */
        $date = DateTime::createFromFormat('d.m.Y', $this->requestDate, new DateTimeZone('Europe/Berlin'));
        /**
         @value $qtype Sicherstellen das $qtype nicht NULL ist für korrekte Berechnungen
         */
        $qtype==null?'day':$qtype;

        
        if (is_array($result)&&count($result)>0) { 
            foreach($result as $row){
                /**
                 Fixiere festes Start- und Ende-Muster für die Berechnungen (Optimierung der Rechenleistung) 
                @value DTStart (immer Datum des Abrufdatum als Start "day"=tag, "week"=Montag der Woche, "month"= 1. des Monats, "year"= 1. Januar)
                @value festesende (richtet sich nach UNTIL wenn TotalCount == NULL)
                */
                $DTStart=DateTime::createFromFormat('Y-m-d H:i:s.u', $row['starttime'], new DateTimeZone('Europe/Berlin'));
                if($qtype=='day'){
                    $DTStart=explode('T',$this->getUntilRRuleString($DTStart, $qtype))[0].'T000000';
                }else if($qtype=='week'){
                    $monday = clone $DTStart;
                    $monday->modify('monday this week');
                    $DTStart=$DTStart->format('Ymd').'T000000';
                }else if($qtype=='month'){
                    $DTStart=$DTStart->format('Ym').'01T000000';
                }else if($qtype=='year'){
                    $DTStart=$DTStart->format('Y').'0101T000000';                    
                }else{
                    $DTStart=explode('T',$DTStart)[0].'T000000';                    
                } 
                $festesende='';
                if($row['until']==NULL AND $row['totalcount']==NULL){
                    $festesende=';UNTIL='.$this->getUntilRRuleString($date, $qtype);
                }else if($row['until']!=NULL AND $row['totalcount']==NULL){
                    $untdate = DateTime::createFromFormat('Y-m-d H:i:s.u', $row['until'], new DateTimeZone('Europe/Berlin'));
                    $festesende=';UNTIL='.$this->getUntilRRuleString($untdate, $qtype);
                }else if($row['until']==NULL AND $row['totalcount']!=NULL){
                    $festesende=';COUNT='.$row['totalcount'];
                }else{
                    $festesende=';UNTIL='.$this->getUntilRRuleString($date, $qtype);
                }  
                 $rrule = new RRule\RRule("DTSTART;TZID=Europe/Berlin:".$DTStart."\nRRULE:".$row['rrulestring'].$festesende.""); 
                 $timings=$this->calculateEventTimeStAndEd(50,DateTime::createFromFormat('Y-m-d H:i:s.u', $row['starttime'], new DateTimeZone('Europe/Berlin')));
                foreach ($rrule as $key=>$occurrence) { 
                    if($this->isSameDay($occurrence,$date)&&$this->isDateExcluded($Exceptions,$row['id'],$occurrence)==false){
                        /**
                         Erstelle das Object für die übergabe
                         */
                        array_push($narr,//$row['id'].' - '.$occurrence->format('D d M Y')
                        array(
                            "id"=>'RRule-'.$key.'-'.$row['id'],
                            "titel"=>$row['kname']!=NULL?(trim($row['kname'])):'Privat',
                            "endetyp"=>(($row['until']==null&&$row['totalcount']==null)?'NODATE':($row['totalcount']!=NULL?'REPEAT':'DATE')),
                            "endetypdate"=>$row['until'],
                            "endetyprepeats"=>intval($row['totalcount']),
                            "repeatfrequenz"=> $row['rfrequency'],
                            "repeatmuster"=> intval($row['intervalnumber']),
                            "bymontharray"=> $row['bymonth']!=NULL?(count(explode(',',$row['bymonth']))>0?array_map('intval',explode(',',$row['bymonth'])):[intval($row['bymonth'])]):NULL,
                            "bymonthdayarray"=> $row['bymonthday']!=NULL?(count(explode(',',$row['bymonthday']))>0?array_map('intval',explode(',',$row['bymonthday'])):[intval($row['bymonthday'])]):NULL,
                            "jahrmuster"=>(($row['bymonthday']!=NULL&&$row['bymonth']!=NULL)?'DATUM':(($row['byday']!=NULL&&$row['bymonth']!=NULL)?'WOCHENTAGMONAT':(($row['byyearday']!=NULL)?'YEARDAY':'WEEKNUMBER'))),
                            "bydayarray"=>
                            ($row['byday']!=NULL&&preg_match('/[a-zA-Z]/', $row['byday']) === 1)?
                            (
                                count(explode(',',$row['byday']))>0?
                                $this->addColonToOffsets(explode(',',$row['byday'])):
                                $this->addColonToOffsets([$row['byday']])
                            ):(
                                count(explode(',',$row['byday']))>0?
                                explode(',',$row['byday']):
                                [$row['byday']]
                            ),
                            "byyeardayarray"=>$row['byyearday']!=NULL?(count(explode(',',$row['byyearday']))>0?(array_map('intval',explode(',',$row['byyearday']))):[intval($row['byyearday'])]):NULL,
                            "kategorieid"=>'serien',
                            "kategorie"=>(string)$row['kategorie']!=NULL?intval($row['kategorie']):0,
                            "katBezeichnung"=>'rrule', 
                            "ColorHex"=>'#515152',
                            "betreff"=>strlen(trim($row['betreff']))>0?$row['betreff']:'keine Angaben',
                            "zeitraum"=>intval($row['duration']),
                            "von"=>$timings['start'],
                            "bis"=>$timings['end'] ,
                            "boxColor"=>$row['hexcolor'] ,
                            "VerwaltungPflege"=>$row['systempart'] ,
                            "duration"=>$this->getMinutesDifferenceRR($timings['startobj'],$timings['endobj']) ,
                            "datum"=>$timings['startobj']->format('Y-m-d') ,
                            "ersteller"=>$row['anwender'] ,
                            "eventTyp"=>null ,
                            "haus"=>$row['location'] ,
                            "wohnbereich"=>$row['floor'] ,
                            "isAlarm"=>$row['alertrule']==null?false:true ,
                            "isAlarmStamp"=>$row['alertrule']==null?null:$row['alertrule'] ,
                            "isEditable"=>$row['systempart']=='ME'?true:false ,
                            "isNoteAttached"=>$row['isnote']==null?null:$row['isnote'] ,
                            "isPublic"=>$row['systempart']=='ME'?true:false ,
                            "isprivate"=>$row['systempart']=='ME'?true:false ,
                            "katBackColor"=>$row['hexcolor'] ,
                            "realtimeend"=>$timings['end'] ,
                            "realtimeendDate"=>$timings['endobj']->format('d.m.Y'), 
                            "realtimestart"=>$timings['start'] ,
                            "realtimestartDate"=>$timings['startobj']->format('d.m.Y'),
                            "time"=>intval($timings['startobj']->format('H')) 
                        ) 
                        ); 
                    }
                } 
            }
            return $narr;
        }else{
            return[];
        }     
         
    } 
    public function getAllEvents($qtype=null):mixed { 
        //GET ALL SERIEN TERMINE
        $RRulesMy=$this->_getRRuleEvents($qtype);         
        //GET ALL GEBURTSTAGE BEWOHNER 
        $GeburtstageBewohner=$this->_getBewohnerGeburtstage($qtype);
        //GET ALL GEBURTSTAGE MITARBEITER          
        $GeburtstageMitarbeiter=$this->_getMitarbeiterGeburtstage($qtype);
        //GET ALL GENEHMIGUNGEN BEWOHNER          
        $BewohnerGenehmigungen=$this->_getBewohnerGenehmigungen($qtype);      
        //GET ALL GENEHMIGUNGEN BEWOHNER          
        $BewohnerGEZ=$this->_getBewohnerGEZ($qtype);      
        //GET ALL PERSONALAUSWEIS BEWOHNER          
        $BewohnerPersAusweis=$this->_getPersonalAusweis($qtype);   
        //GET ALL PERSONALAUSWEIS BEWOHNER          
        $BewohnerPflegewohngeld=$this->_getpflegewohngeld($qtype);   
        //GET ALL TABELLENWOHNGELD BEWOHNER          
        $BewohnerTabellenwohngeld=$this->_gettabellenwohngeld($qtype);   
        //GET ALL SCHWERBEHINDERTENAUSWEIS BEWOHNER          
        $BewohnerSchwerbehindertausweis=$this->_getSchwerbehindertausweis($qtype);   
        //GET ALL PFLEGEVISITE BEWOHNER          
        $BewohnerPflegeVisite=$this->_getPflegeVisite($qtype);   
        //GET ALL EVALUIERUNG BEWOHNER          
        $BewohnerEvaluierung=$this->_getEvaluierung($qtype);   
        //GET ALL WUNDAUSWERTUNG BEWOHNER          
        $BewohnerWundauswertung=$this->_getWundauswertung($qtype);   
        //GET ALL WUNDVERMESSUNG BEWOHNER          
        $BewohnerWundvermessung=$this->_getWundvermessung($qtype);   
        //GET ALL EVALUIERUNG BETREUUNG BEWOHNER          
        $BewohnerEvalBetreuung=$this->_getEvalBetreuung($qtype);   
        //GET ALL BRADENSKALA BEWOHNER          
        $BewohnerBradenskala=$this->_getBradenskala($qtype);   
        //GET ALL NORTONSKALA BEWOHNER          
        $BewohnerNortonskala=$this->_getNortonskala($qtype);   
        //GET ALL DEKUBITUSPROPHYLAXE BEWOHNER          
        $BewohnerDekubitusprophylaxe=$this->_getDekubitusprophylaxe($qtype);   
        //GET ALL SICHERHEITSKONTROLLEN BEWOHNER          
        $BewohnerSicherheitskontrollen=$this->_getSicherheitskontrollen($qtype);   
        //GET ALL EVALUIERUNG KONTRACTUR BEWOHNER          
        $BewohnerEvaluierungKontraktur=$this->_getEvaluierungKontraktur($qtype);   
        //GET ALL ERGEBNISERFASSUNG BEWOHNER          
        $BewohnerErgebniserfassung=$this->_getErgebniserfassung($qtype);   
        //$BewohnerPersAusweis=[];
    $query= "SELECT TOP(1000) K.ID AS id,COALESCE( 
        NULLIF(KK.Bezeichnung COLLATE SQL_Latin1_General_CP1_CI_AS, ''), 
		NULLIF(K.Betreff COLLATE SQL_Latin1_General_CP1_CI_AS, ''),
        NULLIF(CAST(Notiz AS NVARCHAR(MAX)) COLLATE SQL_Latin1_General_CP1_CI_AS, '')
    ) AS titel, 
    K.Beginnzeit as realtimestart, 
    K.Endezeit as realtimeend, 
    '#' + RIGHT('000000' + CONVERT(VARCHAR(6), FORMAT(K.Farbe, 'X')), 6) AS ColorHex, 
    FORMAT(K.[Beginn], 'yyyy-MM-dd') AS datum,
    FORMAT(K.[Beginn], 'dd.MM.yyyy') AS realtimestartDate,
    FORMAT(K.[Ende], 'dd.MM.yyyy') AS realtimeendDate, 
    NULLIF(CAST(Notiz AS NVARCHAR(MAX)), '') AS isNoteAttached,
    CAST(LEFT(K.Beginnzeit, 2) AS INT) AS time, 
    K.[Hdz] as ersteller, 
    CASE  
        WHEN K.Endezeit = '00:00' THEN 4  -- Special case when end time is 00:00
        ELSE DATEDIFF(MINUTE, 
            DATEADD(SECOND, DATEDIFF(SECOND, '00:00:00', K.Beginnzeit), CAST(K.Beginn AS DATETIME)),
            DATEADD(SECOND, DATEDIFF(SECOND, '00:00:00', K.Endezeit), CAST(K.Ende AS DATETIME))
        ) / 15 
    END AS duration, 
    CASE 
        WHEN K.[User] ='".$this->user."' AND K.[Hdz] ='".$this->user."' THEN 'TRUE' 
        ELSE 'FALSE' 
    END AS isEditable, 
    CASE 
        WHEN K.[Erinnerung] is NULL THEN 'FALSE' 
        ELSE 'TRUE' 
    END AS isAlarm,
    CASE 
        WHEN K.[Erinnerung] is not NULL THEN FORMAT(K.[Erinnerung], 'dd.MM.yyyy HH:mm') 
        ELSE NULL 
    END AS isAlarmStamp,  
	CASE 
        WHEN (K.Kategorie!=0) THEN K.Kategorie 
        ELSE NULL 
    END AS eventTyp, 
	K.Part AS VerwaltungPflege,
	K.Haus AS haus,
	K.Wohnbereich AS wohnbereich,
    CASE 
        WHEN (K.Kategorie!=0) THEN K.Kategorie 
        ELSE NULL 
    END AS kategorie,
	'Termin' AS katBezeichnung, CASE 
        WHEN TRY_CAST(KK.BackColor AS INT) IS NULL THEN NULL
        ELSE '#' + RIGHT('000000' + CONVERT(VARCHAR(6), FORMAT(CAST(KK.BackColor AS INT), 'X')), 6) 
    END AS katBackColor,CASE 
        WHEN TRY_CAST(KK.ForeColor AS INT) IS NULL THEN NULL
        ELSE '#' + RIGHT('000000' + CONVERT(VARCHAR(6), FORMAT(CAST(KK.ForeColor AS INT), 'X')), 6) 
    END AS katForeColor FROM [".$this->dbnameV."].dbo.Kalender K LEFT JOIN [".$this->dbnameV."].dbo.KalenderKategorien KK ON K.Kategorie=KK.ID 
    WHERE (K.[User]='".$this->user."' OR K.[Hdz]='".$this->user."') AND  FORMAT(K.[Beginn], 'dd.MM.yyyy')='".$this->requestDate."'
    ORDER BY K.Beginn ASC, K.Beginnzeit ASC";  
        $result = $this->conn->query($query, []);
        if (count($result)>0) {  
            $narr=[];
            foreach($result as $row){
                $row['isAlarm']=($row['isAlarm']&&($row['isAlarm']=='TRUE'))?true:false;
                $row['isEditable']=($row['isEditable']&&($row['isEditable']=='TRUE'))?true:false;
                $row['isPublic']=($row['isEditable']&&($row['isEditable']=='TRUE'))?true:false;
                $row['isprivate']=($row['isEditable']&&($row['isEditable']=='TRUE'))?true:false; 
                $row['duration']=$this->getMinutesDifference($row['realtimestart'],$row['realtimeend'])/15;
                $row['time']=intval($row['time']);
                $row['id']=intval($row['id']); 
                $row['ersteller']= strtoupper($row['ersteller']); 
                if($row['kategorie']!=null&&$row['kategorie']==21||$row['kategorie']!=null&&$row['kategorie']==22){
                    $row['time']=intval(0);
                    $row['duration']=intval(24*4);
                    $row['realtimestart']='00:00';
                    $row['realtimeend']='23:59';
                }
                 
                array_push($narr,$row);
            }
            $narr=array_merge($RRulesMy,$GeburtstageMitarbeiter,$GeburtstageBewohner,$BewohnerGenehmigungen,$BewohnerGEZ,$BewohnerPersAusweis,$BewohnerPflegewohngeld,$BewohnerTabellenwohngeld,$BewohnerSchwerbehindertausweis,$BewohnerPflegeVisite,$BewohnerEvaluierung,$BewohnerWundauswertung,$BewohnerWundvermessung,$BewohnerEvalBetreuung,$BewohnerBradenskala,$BewohnerNortonskala, $BewohnerDekubitusprophylaxe,$BewohnerSicherheitskontrollen,$BewohnerEvaluierungKontraktur,$BewohnerErgebniserfassung, $narr);
            return $narr;
        } else {
            return array_merge($RRulesMy,$GeburtstageMitarbeiter,$GeburtstageBewohner,$BewohnerGenehmigungen,$BewohnerGEZ,$BewohnerPersAusweis,$BewohnerPflegewohngeld,$BewohnerTabellenwohngeld,$BewohnerSchwerbehindertausweis,$BewohnerPflegeVisite,$BewohnerEvaluierung,$BewohnerWundauswertung,$BewohnerWundvermessung,$BewohnerEvalBetreuung,$BewohnerBradenskala,$BewohnerNortonskala, $BewohnerDekubitusprophylaxe,$BewohnerSicherheitskontrollen,$BewohnerEvaluierungKontraktur,$BewohnerErgebniserfassung);
        }
    }
    public function getKategorien(): mixed {
        $query="Select *,'#' + RIGHT('000000' + CONVERT(VARCHAR(6), FORMAT(CAST(BackColor AS INT), 'X')), 6) AS ColorHex from [".$this->dbnameV."].dbo.KalenderKategorien";
        $result = $this->conn->query($query, []);
        if (!empty($result)){ 
            $narr=[];
            foreach($result as $row){
                 
                array_push($narr,
            array(
                "ID"=>$row['ID'],
                "typ"=>$row['Suchbegriff'],
                "kategoriename"=>$row['Kategorie'],
                "bezeichnung"=>$row['Kategorie'],
                "colorhex"=>$row['ColorHex'],
                "vdeleted"=>$row['gelöscht']==1?true:false,
                "vstart"=>$row['StartPflege']==1?true:false,
                "pdeleted"=>$row['gelöschtPflege']==1?true:false,
                "pstart"=>$row['StartPflege']==1?true:false
            ));
            }
            return $narr;
        }else{
            return [];
        }
        
    }
    public function updateMovementStampViewDaily($newStartStamp,$newEndStamp,$id) { 
        $query="UPDATE [".$this->dbnameV."].dbo.Kalender SET Beginnzeit='".date('H:i',$newStartStamp)."', Endezeit='".date('H:i',$newEndStamp)."', Beginn=DATEADD(SECOND, ".$newStartStamp.", '1970-01-01'), Ende=DATEADD(SECOND, ".$newEndStamp.", '1970-01-01') ,changed=CURRENT_TIMESTAMP WHERE ID=".$id." ";
        if($this->conn->query($query, [])){
            return true;
        }else{
            return false;
        }
    }
    public function getMinutesDifference($startTime, $endTime) {
        $start = DateTime::createFromFormat('H:i', $startTime);
        $end = DateTime::createFromFormat('H:i', $endTime);
    
        $diff = $start->diff($end);
        return $diff->h * 60 + $diff->i;
    }
    public function deleteEventStampOnViewDaily($id,$type) { 
        if($type=='standard'){
            $query="DELETE FROM [".$this->dbnameV."].dbo.Kalender WHERE ID=".$id." ";
        }else{
            $id=explode('-',$id)[2];
            $query="DELETE FROM [".$this->dbnameV."].dbo.rrevents WHERE id=".$id."; DELETE FROM [".$this->dbnameV."].dbo.rrevent_exceptions WHERE rrevent_id=".$id."; ";
        }
        if($this->conn->query($query, [])){
            return true;
        }else{
            return false;
        }
    }
    public function generateRandomHexColor() {
        return sprintf("#%06X", mt_rand(0, 0xFFFFFF));
    }
    public function decimalToHexColor($decimal): string {
        // Ensure the decimal is within the valid range for colors
        if ($decimal < 0 || $decimal > 16777215) {
            return "Invalid color value. Must be between 0 and 16777215.";
        }
        // Convert the decimal to a 6-character hex value
        $hex = str_pad(dechex($decimal), 6, "0", STR_PAD_LEFT);
        // Add the # to create a proper hex color code
        return "#" . strtoupper($hex);
    } 
    public function hexColorToDecimal($hexColor): float|int|string {
        // Remove # if present
        $hexColor = ltrim(strtoupper($hexColor), '#');
        
        // Validate hex color format
        if (!preg_match('/^[0-9A-F]{6}$/', $hexColor)) {
            return false;
        }
        
        // Convert hex to decimal
        return hexdec($hexColor);
    }
    public function getDurationIndicator($startTime=null, $endTime=null, $type=null):int {
        //Only allow with full params
        if($startTime==null&&$endTime==null&&$type==null) return 0;
        // Convert to timestamps
        $startTimestamp = strtotime($startTime);
        $endTimestamp = strtotime($endTime);
        
        // Calculate total minutes
        $totalMinutes = ($endTimestamp - $startTimestamp) / 60;
        
        // Handle different types
        switch ($type) {
            case 'd':
            case 'w':
                return (int) ($totalMinutes / 15);
            case 'm':
                return (int) ($totalMinutes / 1440);
            case 'y':
                return 0;
            case 'l':
                return (int) $totalMinutes;
            default:
                return 0; // Invalid type
        }
    }
    public function getHausIdOnName($name):int{
        $params=array(":N"=>$name);
        $query="SELECT TOP(1) ID FROM [".$this->dbnameV."].dbo.[Häuser] WHERE Haus=:N ";
        $result = $this->conn->query($query, $params);
        if (is_numeric($result[0]['ID'])){ 
            return $result[0]['ID'];
        }else{
            return 0;
        }
    }
    public function getKategorieIdOnName($name):int{  
        $params=array(":N"=>$name);
        $query="SELECT TOP(1) ID FROM [".$this->dbnameV."].dbo.KalenderKategorien WHERE Kategorie=:N ";
        $result = $this->conn->query($query, $params);
        if (!empty($result)){ 
            return $result[0]['ID'];
        }else{
            return 0;
        }
    }
    public function convertGermanWeekdaysToEnglish(array $germanDays): array {
        $dayMap = [
            'MO' => 'MO', // Montag
            'DI' => 'TU', // Dienstag
            'MI' => 'WE', // Mittwoch
            'DO' => 'TH', // Donnerstag
            'FR' => 'FR', // Freitag
            'SA' => 'SA', // Samstag
            'SO' => 'SU'  // Sonntag
        ];
    
        $englishDays = [];
    
        foreach ($germanDays as $day) {
            $upperDay = strtoupper($day);
            if (isset($dayMap[$upperDay])) {
                $englishDays[] = $dayMap[$upperDay];
            }
        }
    
        return $englishDays;
    }
    public function convertNumberedDaysToEnglishString(array $input): string {
        $dayMap = [
            'MO' => 'MO',
            'DI' => 'TU',
            'MI' => 'WE',
            'DO' => 'TH',
            'FR' => 'FR',
            'SA' => 'SA',
            'SO' => 'SU'
        ];
    
        $output = [];
    
        foreach ($input as $item) {
            [$number, $day] = explode(':', $item);
            $day = strtoupper(trim($day));
            if (isset($dayMap[$day])) {
                $output[] = $number . $dayMap[$day];
            }
        }
    
        return implode(',', $output);
    }
    public function insertNewRRuleEvent(
        $Anwender,
        $AnwenderTyp,
        $terminBetreff,
        $terminKategorie,
        $terminSichtbarkeit,
        $terminWohnbereich,
        $terminBemerkung,
        $terminErinnerungSwitch,
        $terminErinnerungDatum,
        $RRuleFrequenz,
        $rruleTerminDauer,
        $rruleTerminStartDatumZeit,
        $rruleTerminEndeType,
        $rruleTerminEndeTypeDatum,
        $rruleTerminEndeTypeWiederholungen,
        $rruleTerminJahresMuster,
        $rruleTerminMonatMuster,
        $rruleTerminWiederholungsMuster,
        $rruleTerminJaehrlichJahresMusterDatum_MonateArray,
        $rruleTerminJaehrlichJahresMusterDatum_TageArray,
        $rruleTerminJaehrlichJahresMusterJahrestag_TageArray,
        $rruleTerminJaehrlichJahresMusterJahrestag_WochenTageArray,
        $rruleTerminJaehrlichJahresMusterJahrestag_WochennummerArray,
        $terminFarbe
    ):mixed { 
        $errorcount=[];
        $startdate=$rruleTerminStartDatumZeit!=null?new DateTime($rruleTerminStartDatumZeit):array_push($errorcount,"Startdatum/Uhrzeit: Bitte geben Sie ein gültiges Startdatum an.");
        $startdate->setTimezone(new DateTimeZone('Europe/Berlin'));
        $startdatum=$rruleTerminStartDatumZeit!=null?$startdate->format('d.m.Y'):null; //'09.05.2025 16:00:00'
        $startzeit=$rruleTerminStartDatumZeit!=null?$startdate->format('H:i:s'):null;
        $dbstartstamp=$rruleTerminStartDatumZeit!=null?$startdatum.' '.$startzeit:date('d.m.Y H:i:s',time());
        if($startdate&&$rruleTerminStartDatumZeit!=null&&$startdate<new DateTime("now",new DateTimeZone('Europe/Berlin'))){
            array_push($errorcount,"Startdatum/Uhrzeit: Bitte geben Sie ein gültiges Startdatum an.");
        }
        if($terminKategorie=='Privater Eintrag'){
            $terminKategorieID=0;
        }else{
            $terminKategorieID=$this->getKategorieIdOnName($terminKategorie)==0?0:$this->getKategorieIdOnName($terminKategorie);
            $terminKategorieID==0?array_push($errorcount,"Kategorie: Die ausgewählte Kategorie konnte nicht gefunden werden."):'';
        }    
        $Anwender=strlen(trim($Anwender))>0?strtoupper(trim($Anwender)):array_push($errorcount,"Rechte: Sie besitzen keine Rechte Einträge in den Kalender zu setzen."); 
        $terminNote=NULL;
        if(strlen(trim($terminBemerkung))>0&&$terminBemerkung!=null){
            $terminNote=$terminBemerkung;
        }
        if(strlen(trim($terminSichtbarkeit))==0||$terminSichtbarkeit==null){
            array_push($errorcount,"Sichtbarkeit: Bitte wählen Sie aus welche Stufe verwendet werden soll.");
        }
        $isErinnerung=NULL;
        if($terminErinnerungSwitch==true){
        $erinnerungdate=$terminErinnerungDatum!=null?new DateTime($terminErinnerungDatum):array_push($errorcount,"Erinnerungdatum/Uhrzeit: Bitte geben Sie ein gültiges Erinnerungsdatum an.");
        $erinnerungdate->setTimezone(new DateTimeZone('Europe/Berlin'));
        $erinnerungdatum=$terminErinnerungDatum!=null?$erinnerungdate->format('d.m.Y'):null;
        $erinnerung=$terminErinnerungDatum!=null?$erinnerungdate->format('H:i:s'):null;
        $dberrstamp=$terminErinnerungDatum!=null?$erinnerungdatum.' '.$erinnerung:null;
        $isErinnerung=$dberrstamp==null?NULL:$dberrstamp;
        } 
        $hausID=NULL;
        $wohnbereich=NULL;
        if($terminSichtbarkeit=='P'&&$terminWohnbereich!=null){ 
            $haus=explode(':X:',$terminWohnbereich)[0];
            $haus=(strlen(trim($haus))>0&&$this->getHausIdOnName($haus)>0)?$this->getHausIdOnName($haus):NULL;
            $wohnbereich=explode(':X:',$terminWohnbereich)[1];
        } 
        $intervalMuster=1;
        if($rruleTerminWiederholungsMuster>1){
            $intervalMuster=$rruleTerminWiederholungsMuster;
        }
        $ablaufdatum=NULL;
        if($rruleTerminEndeType=="DATE"){
            if($rruleTerminEndeTypeDatum=="NODATE"){
                array_push($errorcount,"Endedatum: Bitte wählen Sie ein korrektes Ablaufdatum für die Serie aus.");
            }else{
                $terminEndeDate=$rruleTerminEndeTypeDatum!=null?new DateTime($rruleTerminEndeTypeDatum):array_push($errorcount,"Endedatum: Bitte wählen Sie ein korrektes Ablaufdatum für die Serie aus.");
                $rruleTerminEndeTypeDatum!=null?$terminEndeDate->setTimezone(new DateTimeZone('Europe/Berlin')):'';
                $terminEndedatum=$rruleTerminEndeTypeDatum!=null?$terminEndeDate->format('d.m.Y'):null; 
                $dbterminEndestamp=$rruleTerminEndeTypeDatum!=null?$terminEndedatum.' 00:00:00':null;
                $ablaufdatum=$dbterminEndestamp==null?NULL:$dbterminEndestamp;
            }
        }
        $totalCount=NULL;
        if($rruleTerminEndeType=="REPEAT"){
            if($rruleTerminEndeTypeWiederholungen==0){
                array_push($errorcount,"Wiederholungen: Bitte geben Sie mindestens 1 Wiederholung an oder ändern Sie die Einstellungen.");
            }else{
                $totalCount=is_numeric($rruleTerminEndeTypeWiederholungen)&&$rruleTerminEndeTypeWiederholungen>0?$rruleTerminEndeTypeWiederholungen:NULL;
            }
        }
        $terminDauer=1440;
        if($rruleTerminDauer<1){
            array_push($errorcount,"Dauer: Bitte legen Sie den Zeitraum des Termin fest.");
        }else{
            $terminDauer=$rruleTerminDauer;
        }
        $BYDAY=NULL;
        $BYMONTHDAY=NULL;
        $BYMONTH=NULL;
        $BYHOUR=$rruleTerminStartDatumZeit!=null&&$terminDauer!=1440?$startdate->format('H'):0;
        $WKST=NULL;
        $BYYEARDAY=NULL;
        $BYWEEKNO=NULL;
        $RRulestring='FREQ=';
        if($RRuleFrequenz=='DAILY'){
            $RRulestring.='DAILY';
            $RRulestring.=';INTERVAL='.$intervalMuster;
        }else if($RRuleFrequenz=='WEEKLY'){
            $RRulestring.='WEEKLY';
            $RRulestring.=';INTERVAL='.$intervalMuster;
            if(count($rruleTerminJaehrlichJahresMusterJahrestag_WochenTageArray)>0){
                $RRulestring.=';BYDAY='.implode(',', $this->convertGermanWeekdaysToEnglish($rruleTerminJaehrlichJahresMusterJahrestag_WochenTageArray));
                $BYDAY=implode(',', $this->convertGermanWeekdaysToEnglish($rruleTerminJaehrlichJahresMusterJahrestag_WochenTageArray));
            }  
        }else if($RRuleFrequenz=='MONTHLY'){
            $RRulestring.='MONTHLY';
            $RRulestring.=';INTERVAL='.$intervalMuster;
            if(count($rruleTerminJaehrlichJahresMusterDatum_TageArray)>0&&$rruleTerminMonatMuster=='DAY'){
                $RRulestring.=';BYMONTHDAY='.implode(',',$rruleTerminJaehrlichJahresMusterDatum_TageArray );
                $BYMONTHDAY=implode(',',$rruleTerminJaehrlichJahresMusterDatum_TageArray );
            }
            if(count($rruleTerminJaehrlichJahresMusterDatum_TageArray)>0&&$rruleTerminMonatMuster=='WEEKDAY'){
                $RRulestring.=';BYDAY='.$this->convertNumberedDaysToEnglishString($rruleTerminJaehrlichJahresMusterDatum_TageArray);
                $BYDAY=$this->convertNumberedDaysToEnglishString($rruleTerminJaehrlichJahresMusterDatum_TageArray);
            } 
        }else{
            $RRulestring.='YEARLY';
            $RRulestring.=';INTERVAL='.$intervalMuster; 
            if($rruleTerminJahresMuster=='DATUM'&&count($rruleTerminJaehrlichJahresMusterDatum_MonateArray)>0&&count($rruleTerminJaehrlichJahresMusterDatum_TageArray)>0){
                $RRulestring.=';BYMONTH='.implode(',',$rruleTerminJaehrlichJahresMusterDatum_MonateArray );
                $RRulestring.=';BYMONTHDAY='.implode(',',$rruleTerminJaehrlichJahresMusterDatum_TageArray );
                $BYMONTHDAY=implode(',',$rruleTerminJaehrlichJahresMusterDatum_TageArray );
                $BYMONTH=implode(',',$rruleTerminJaehrlichJahresMusterDatum_MonateArray );
            }
            if($rruleTerminJahresMuster=='WOCHENTAGMONAT'&&count($rruleTerminJaehrlichJahresMusterDatum_MonateArray)>0&&count($rruleTerminJaehrlichJahresMusterDatum_TageArray)>0){
                $RRulestring.=';BYMONTH='.implode(',',$rruleTerminJaehrlichJahresMusterDatum_MonateArray );
                $RRulestring.=';BYDAY='.$this->convertNumberedDaysToEnglishString($rruleTerminJaehrlichJahresMusterDatum_TageArray);
                $BYDAY=$this->convertNumberedDaysToEnglishString($rruleTerminJaehrlichJahresMusterDatum_TageArray);
                $BYMONTH=implode(',',$rruleTerminJaehrlichJahresMusterDatum_MonateArray );
            }
            if($rruleTerminJahresMuster=='YEARDAY'&&count($rruleTerminJaehrlichJahresMusterJahrestag_TageArray)>0){ 
                $RRulestring.=';BYYEARDAY='.implode(',',$rruleTerminJaehrlichJahresMusterJahrestag_TageArray );
                $BYYEARDAY=implode(',',$rruleTerminJaehrlichJahresMusterJahrestag_TageArray );
            }
            if($rruleTerminJahresMuster=='WEEKNUMBER'&&count($rruleTerminJaehrlichJahresMusterJahrestag_WochenTageArray)>0&&count($rruleTerminJaehrlichJahresMusterJahrestag_WochennummerArray)>0){ 
                $RRulestring.=';BYWEEKNO='.implode(',',$rruleTerminJaehrlichJahresMusterJahrestag_WochennummerArray );
                $RRulestring.=';BYDAY='.implode(',', $this->convertGermanWeekdaysToEnglish($rruleTerminJaehrlichJahresMusterJahrestag_WochenTageArray));
                $BYDAY=implode(',', $this->convertGermanWeekdaysToEnglish($rruleTerminJaehrlichJahresMusterJahrestag_WochenTageArray));
                $BYWEEKNO=implode(',',$rruleTerminJaehrlichJahresMusterJahrestag_WochennummerArray );
            } 
        }

        if(count($errorcount)>0){
            return $errorcount;
        }else{
            //Everything OK make Insert in Table 
              
            $query = "INSERT INTO [".$this->dbnameV."].dbo.rrevents (
            anwender,
            betreff,
            isnote,
            ".(($isErinnerung!=NULL)?"alertrule,":"")."             
            systempart,
            [location],
            floor,
            starttime,
            rfrequency,
            intervalnumber,
            byday,
            bymonthday,
            bymonth,
            byhour,
            wkst,
            byyearday,
            byweekno,
            totalcount,
            ".(($ablaufdatum!=NULL)?"until,":"")."              
            changed,
            kategorie,
            [hexcolor],
            rrulestring,
            duration) VALUES (
            '".$Anwender."',
            '".$terminBetreff."',
            ".(($terminNote !== NULL|| !empty(trim($terminNote)))?"'".$terminNote."'":"NULL").",
            ".(($isErinnerung!=NULL)?"'".$isErinnerung."',":"")." 
            '".$terminSichtbarkeit."',
            ".(($hausID !== NULL)?intval($hausID):"NULL").",
            ".(($wohnbereich !== NULL)?"'".$wohnbereich."'":"NULL").",
            '".date('d.m.Y H:i:s', strtotime($dbstartstamp))."',
            '".$RRuleFrequenz."',
            ".intval($intervalMuster).",
            ".(($BYDAY !== NULL)?"'".$BYDAY."'":"NULL").",
            ".(($BYMONTHDAY !== NULL)?"'".$BYMONTHDAY."'":"NULL").", 
            ".(($BYMONTH !== NULL)?"'".$BYMONTH."'":"NULL").",  
            '".$BYHOUR."',  
            ".(($WKST !== NULL)?"'".$WKST."'":"NULL").", 
            ".(($BYYEARDAY !== NULL)?"'".$BYYEARDAY."'":"NULL").",  
            ".(($BYWEEKNO !== NULL)?"'".$BYWEEKNO."'":"NULL").",   
            ".(($totalCount !== NULL)?"'".$totalCount."'":"NULL").",  
            ".(($ablaufdatum!=NULL)?"'".$ablaufdatum."' ,":"")."  
            NULL,
            ".(($terminKategorieID !== 0)?$terminKategorieID:"NULL").",
            '".$terminFarbe."',
            '".$RRulestring."',
            ".$terminDauer.") ";
            //return $query;
            if ($this->conn->query($query, [])){ 
                return true;
            }else{
                return ['Fehlgeschlagen: Der Eintrag konnte nicht gespeichert werde. Bitte prüfen Sie die Daten und versuchen es erneut.'];
            }
        }
        
        
  
    }
     
    public function insertNewStandardEvent(
            $Anwender,
            $AnwenderTyp,
            $terminBetreff,
            $terminKategorie,
            $terminSichtbarkeit,
            $terminWohnbereich,
            $terminBemerkung,
            $terminErinnerungSwitch,
            $terminErinnerungDatum,
            $standardTerminStartDatumZeit,
            $standardTerminEndeDatumZeit
    ):mixed {
        $errorcount=[];
        $startdate=$standardTerminStartDatumZeit!=null?new DateTime($standardTerminStartDatumZeit):array_push($errorcount,"Startdatum/Uhrzeit: Bitte geben Sie ein gültiges Startdatum an.");
        $startdate->setTimezone(new DateTimeZone('Europe/Berlin'));
        $startdatum=$standardTerminStartDatumZeit!=null?$startdate->format('d.m.Y'):null;
        $startzeit=$standardTerminStartDatumZeit!=null?$startdate->format('H:i:s'):null;
        $startzeitdb=$standardTerminStartDatumZeit!=null?$startdate->format('H:i'):null;
        $dbstartstamp=$standardTerminStartDatumZeit!=null?$startdatum.' '.$startzeit:date('d.m.Y H:i:s.u',time());
        if($startdate&&$standardTerminStartDatumZeit!=null&&$startdate<new DateTime("now",new DateTimeZone('Europe/Berlin'))){
            array_push($errorcount,"Startdatum/Uhrzeit: Bitte geben Sie ein gültiges Startdatum an.");
        }
        $endedate=$standardTerminEndeDatumZeit!=null?new DateTime($standardTerminEndeDatumZeit):array_push($errorcount,"Startdatum/Uhrzeit: Bitte geben Sie ein gültiges Startdatum an.");
        $endedate->setTimezone(new DateTimeZone('Europe/Berlin'));
        $endedatum=$standardTerminStartDatumZeit!=null?$endedate->format('d.m.Y'):null;
        $endezeit=$standardTerminStartDatumZeit!=null?$endedate->format('H:i:s'):null;
        $endezeitdb=$standardTerminStartDatumZeit!=null?$endedate->format('H:i'):null;
        $dbendestamp=$standardTerminEndeDatumZeit!=null?$endedatum.' '.$endezeit:date('d.m.Y H:i:s.u',time());
        if($endedate&&$standardTerminEndeDatumZeit!=null&&$endedate<new DateTime("now",new DateTimeZone('Europe/Berlin'))){
            array_push($errorcount,"Enddatum/Uhrzeit: Bitte geben Sie ein gültiges Enddatum an.");
        }
        if($terminKategorie=='Privater Eintrag'){
            $terminKategorieID=0;
        }else{
            $terminKategorieID=$this->getKategorieIdOnName($terminKategorie)==0?0:$this->getKategorieIdOnName($terminKategorie);
            $terminKategorieID==0?array_push($errorcount,"Kategorie: Die ausgewählte Kategorie konnte nicht gefunden werden."):'';
        }    
        $Anwender=strlen(trim($Anwender))>0?strtoupper(trim($Anwender)):array_push($errorcount,"Rechte: Sie besitzen keine Rechte Einträge in den Kalender zu setzen."); 
        $terminNote=NULL;
        if(strlen(trim($terminBemerkung))>0&&$terminBemerkung!=null){
            $terminNote=trim($terminBemerkung);
        }
        if(strlen(trim($terminSichtbarkeit))==0||$terminSichtbarkeit==null){
            array_push($errorcount,"Sichtbarkeit: Bitte wählen Sie aus welche Stufe verwendet werden soll.");
        } 
        $isErinnerung=NULL;
        if($terminErinnerungSwitch==true){
        $erinnerungdate=$terminErinnerungDatum!=null?new DateTime($terminErinnerungDatum):array_push($errorcount,"Erinnerungdatum/Uhrzeit: Bitte geben Sie ein gültiges Erinnerungsdatum an.");
        $erinnerungdate->setTimezone(new DateTimeZone('Europe/Berlin'));
        $erinnerungdatum=$terminErinnerungDatum!=null?$erinnerungdate->format('d.m.Y'):null;
        $erinnerung=$terminErinnerungDatum!=null?$erinnerungdate->format('H:i:s'):null;
        $dberrstamp=$terminErinnerungDatum!=null?($erinnerungdatum.' '.$erinnerung):null;
        $isErinnerung=$dberrstamp==null?NULL:$dberrstamp;
            if($erinnerungdate&&$terminErinnerungDatum!=null&&$erinnerungdate<new DateTime("now",new DateTimeZone('Europe/Berlin'))){
                array_push($errorcount,"Erinnerungdatum/Uhrzeit: Bitte geben Sie ein gültiges Erinnerungsdatum an.");
            }
        } 
        $hausID=NULL;
        $wohnbereich=NULL;
        if($terminSichtbarkeit=='P'&&$terminWohnbereich!=null){ 
            $haus=explode(':X:',$terminWohnbereich)[0];
            $hausID=(strlen(trim($haus))>0&&$this->getHausIdOnName($haus)>0)?$this->getHausIdOnName($haus):NULL;
            $wohnbereich=explode(':X:',$terminWohnbereich)[1];
        } 


        if(count($errorcount)>0){
            return $errorcount;
        }else{
            //Everything OK make Insert in Table 
              $parts=NULL;
              if($terminKategorie=='Privater Eintrag'&&$terminSichtbarkeit=='ME')
              {
                $parts=NULL;
              }else if($terminSichtbarkeit=='P'){
                $parts='P';
              }else if($terminSichtbarkeit=='PUB'){
                $parts=NULL;
              }else{
                  $parts='V';
              }
            $query = "INSERT INTO [".$this->dbnameV."].dbo.Kalender (
            Beginnzeit,
            Endezeit,
            Betreff,
            Beginn,            
            Ende,
            Notiz,
            Erinnerung,
            Farbe,
            [User],
            Part,
            Haus,
            Wohnbereich,
            Kategorie,
            Hdz,
            changed) VALUES (
            '".$startzeitdb."',
            '".$endezeitdb."', 
            '".$terminBetreff."',
            CAST('".date('d.m.Y H:i:s', strtotime($dbstartstamp))."' AS DATETIME),         
            CAST('".date('d.m.Y H:i:s', strtotime($dbendestamp))."' AS DATETIME),
            ".(($terminNote !== NULL|| !empty(trim($terminNote)))?"'".$terminNote."'":"''").",
            ".(($isErinnerung!=NULL)?"CAST('".date('d.m.Y H:i:s', strtotime($isErinnerung))."' AS DATETIME)":"NULL").",
            '114196255',
            '".(($terminKategorie=='Privater Eintrag'&&$terminSichtbarkeit=='ME')?$Anwender:"")."',
            ".(($parts!=NULL)?"'".$parts."'":"NULL").", 
            ".(($hausID !== NULL)?intval($hausID):"NULL").",
            ".(($wohnbereich !== NULL)?"'".$wohnbereich."'":"NULL").",
            ".(($terminKategorieID !== 0)?$terminKategorieID:"NULL").",
            '".$Anwender."',NULL) ";
             
            if ($this->conn->query($query, [])){ 
                return true;
            }else{
                return ['Fehlgeschlagen: Der Eintrag konnte nicht gespeichert werde. Bitte prüfen Sie die Daten und versuchen es erneut.'];
            }
        } 
 
    }
    public function updateStandardEvent(
            $terminID,
            $Anwender,
            $AnwenderTyp,
            $terminBetreff,
            $terminKategorie,
            $terminSichtbarkeit,
            $terminWohnbereich,
            $terminBemerkung,
            $terminErinnerungSwitch,
            $terminErinnerungDatum,
            $standardTerminStartDatumZeit,
            $standardTerminEndeDatumZeit
    ):mixed {
        $errorcount=[];
        $startdate=$standardTerminStartDatumZeit!=null?new DateTime($standardTerminStartDatumZeit):array_push($errorcount,"Startdatum/Uhrzeit: Bitte geben Sie ein gültiges Startdatum an.");
        $startdate->setTimezone(new DateTimeZone('Europe/Berlin'));
        $startdatum=$standardTerminStartDatumZeit!=null?$startdate->format('d.m.Y'):null;
        $startzeit=$standardTerminStartDatumZeit!=null?$startdate->format('H:i:s'):null;
        $startzeitdb=$standardTerminStartDatumZeit!=null?$startdate->format('H:i'):null;
        $dbstartstamp=$standardTerminStartDatumZeit!=null?$startdatum.' '.$startzeit:date('d.m.Y H:i:s.u',time());
        if($startdate&&$standardTerminStartDatumZeit!=null&&$startdate<new DateTime("now",new DateTimeZone('Europe/Berlin'))){
            array_push($errorcount,"Startdatum/Uhrzeit: Bitte geben Sie ein gültiges Startdatum an.");
        }
        $endedate=$standardTerminEndeDatumZeit!=null?new DateTime($standardTerminEndeDatumZeit):array_push($errorcount,"Startdatum/Uhrzeit: Bitte geben Sie ein gültiges Startdatum an.");
        $endedate->setTimezone(new DateTimeZone('Europe/Berlin'));
        $endedatum=$standardTerminStartDatumZeit!=null?$endedate->format('d.m.Y'):null;
        $endezeit=$standardTerminStartDatumZeit!=null?$endedate->format('H:i:s'):null;
        $endezeitdb=$standardTerminStartDatumZeit!=null?$endedate->format('H:i'):null;
        $dbendestamp=$standardTerminEndeDatumZeit!=null?$endedatum.' '.$endezeit:date('d.m.Y H:i:s.u',time());
        if($endedate&&$standardTerminEndeDatumZeit!=null&&$endedate<new DateTime("now",new DateTimeZone('Europe/Berlin'))){
            array_push($errorcount,"Enddatum/Uhrzeit: Bitte geben Sie ein gültiges Enddatum an.");
        }
        if($terminKategorie=='Privater Eintrag'){
            $terminKategorieID=0;
        }else{
            $terminKategorieID=$this->getKategorieIdOnName($terminKategorie)==0?0:$this->getKategorieIdOnName($terminKategorie);
            $terminKategorieID==0?array_push($errorcount,"Kategorie: Die ausgewählte Kategorie konnte nicht gefunden werden."):'';
        }    
        $Anwender=strlen(trim($Anwender))>0?strtoupper(trim($Anwender)):array_push($errorcount,"Rechte: Sie besitzen keine Rechte Einträge in den Kalender zu setzen."); 
        $terminNote=NULL;
        if(strlen(trim($terminBemerkung))>0&&$terminBemerkung!=null){
            $terminNote=trim($terminBemerkung);
        }
        if(strlen(trim($terminSichtbarkeit))==0||$terminSichtbarkeit==null){
            array_push($errorcount,"Sichtbarkeit: Bitte wählen Sie aus welche Stufe verwendet werden soll.");
        } 
        $isErinnerung=NULL;
        if($terminErinnerungSwitch==true){
        $erinnerungdate=$terminErinnerungDatum!=null?new DateTime($terminErinnerungDatum):array_push($errorcount,"Erinnerungdatum/Uhrzeit: Bitte geben Sie ein gültiges Erinnerungsdatum an.");
        $erinnerungdate->setTimezone(new DateTimeZone('Europe/Berlin'));
        $erinnerungdatum=$terminErinnerungDatum!=null?$erinnerungdate->format('d.m.Y'):null;
        $erinnerung=$terminErinnerungDatum!=null?$erinnerungdate->format('H:i:s'):null;
        $dberrstamp=$terminErinnerungDatum!=null?($erinnerungdatum.' '.$erinnerung):null;
        $isErinnerung=$dberrstamp==null?NULL:$dberrstamp;
            if($erinnerungdate&&$terminErinnerungDatum!=null&&$erinnerungdate<new DateTime("now",new DateTimeZone('Europe/Berlin'))){
                array_push($errorcount,"Erinnerungdatum/Uhrzeit: Bitte geben Sie ein gültiges Erinnerungsdatum an.");
            }
        } 
        $hausID=NULL;
        $wohnbereich=NULL;
        if($terminSichtbarkeit=='P'&&$terminWohnbereich!=null){ 
            $haus=explode(':X:',$terminWohnbereich)[0];
            $hausID=(strlen(trim($haus))>0&&$this->getHausIdOnName($haus)>0)?$this->getHausIdOnName($haus):NULL;
            $wohnbereich=explode(':X:',$terminWohnbereich)[1];
        } 


        if(count($errorcount)>0){
            return $errorcount;
        }else{
            //Everything OK make UPDATE in Table 
              $parts=NULL;
              if($terminKategorie=='Privater Eintrag'&&$terminSichtbarkeit=='ME')
              {
                $parts=NULL;
              }else if($terminSichtbarkeit=='P'){
                $parts='P';
              }else if($terminSichtbarkeit=='PUB'){
                $parts=NULL;
              }else{
                  $parts='V';
              }
            $query = "UPDATE [".$this->dbnameV."].dbo.Kalender SET 
            Beginnzeit='".$startzeitdb."',
            Endezeit='".$endezeitdb."', 
            Betreff='".$terminBetreff."',
            Beginn=CAST('".date('d.m.Y H:i:s', strtotime($dbstartstamp))."' AS DATETIME),            
            Ende=CAST('".date('d.m.Y H:i:s', strtotime($dbendestamp))."' AS DATETIME),
            Notiz=".(($terminNote !== NULL|| !empty(trim($terminNote)))?"'".$terminNote."'":"''").",
            Erinnerung=".(($isErinnerung!=NULL)?"CAST('".date('d.m.Y H:i:s', strtotime($isErinnerung))."' AS DATETIME)":"NULL").",
            Farbe='114196255',
            [User]='".(($terminKategorie=='Privater Eintrag'&&$terminSichtbarkeit=='ME')?$Anwender:"")."',
            Part=".(($parts!=NULL)?"'".$parts."'":"NULL").", 
            Haus=".(($hausID !== NULL)?intval($hausID):"NULL").",
            Wohnbereich=".(($wohnbereich !== NULL)?"'".$wohnbereich."'":"NULL").",
            Kategorie=".(($terminKategorieID !== 0)?$terminKategorieID:"NULL").",
            Hdz='".$Anwender."',
            changed=CURRENT_TIMESTAMP WHERE ID=".$terminID." ";
             
            if ($this->conn->query($query, [])){ 
                return true;
            }else{
                return ['Fehlgeschlagen: Der Eintrag konnte nicht gespeichert werde. Bitte prüfen Sie die Daten und versuchen es erneut.'];
            }
        } 
 
    }
}


?>