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
    public function _getErgebniserfassung($year=null)//Done
    {
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
    public function _getEvaluierungKontraktur($year=null) //Done
    {
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
    public function _getSicherheitskontrollen($year=null) //Done
    {
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
    public function _getDekubitusprophylaxe($year=null) //Done
    {
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
    public function _getNortonskala($year=null) //Done
    {
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
    public function _getBradenskala($year=null) //Done
    {
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
    public function _getEvalBetreuung($year=null) //Done
    {
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
    public function _getWundvermessung($year=null) //Done
    {
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
    public function _getWundauswertung($year=null) //Done
    {
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
    public function _getEvaluierung($year=null) //Done
    {
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
    public function _getPflegeVisite($year=null)//Done
    {
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
    public function _getSchwerbehindertausweis($year=null) //Done
    {
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
    public function _gettabellenwohngeld($year=null) //Done
    {
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
    public function _getpflegewohngeld($year=null) //Done
    {
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
    public function _getPersonalAusweis($year=null) //Done
    {
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
    public function _getBewohnerGEZ($year=null) //Done
    {
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
    public function _getBewohnerGenehmigungen($year=null) //Done
    { 
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
    public function _getMitarbeiterGeburtstage($year=null)//Done
    {
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
    public function _getBewohnerGeburtstage($year=null)//Done
    {     
        
        $dateformat=explode('.',$this->requestDate)[0].'.'.explode('.',$this->requestDate)[1];  
        $query = "SELECT DISTINCT B.BewohnerNr,B.Name,B.Vorname, Z.Haus as haus,Z.Station as wohnbereich, FORMAT(B.Geburtsdatum, 'dd.MM') as datum, 
        (SELECT TOP(1) id as eid FROM  MedicareHirtenbach.dbo.KalenderKategorien WHERE Kategorie='Geburtstage der Bewohner' and (gelöschtPflege is null or gelöschtPflege = 0)) as eid
        FROM MedicareHirtenbach.dbo.Bewohner B JOIN MedicareHirtenbach.dbo.Zimmer Z ON B.Zimmer=Z.ID
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
    public function getAllEvents():mixed { 
        //GET ALL GEBURTSTAGE BEWOHNER 
        $GeburtstageBewohner=$this->_getBewohnerGeburtstage();
        //GET ALL GEBURTSTAGE MITARBEITER          
        $GeburtstageMitarbeiter=$this->_getMitarbeiterGeburtstage();
        //GET ALL GENEHMIGUNGEN BEWOHNER          
        $BewohnerGenehmigungen=$this->_getBewohnerGenehmigungen();      
        //GET ALL GENEHMIGUNGEN BEWOHNER          
        $BewohnerGEZ=$this->_getBewohnerGEZ();      
        //GET ALL PERSONALAUSWEIS BEWOHNER          
        $BewohnerPersAusweis=$this->_getPersonalAusweis();   
        //GET ALL PERSONALAUSWEIS BEWOHNER          
        $BewohnerPflegewohngeld=$this->_getpflegewohngeld();   
        //GET ALL TABELLENWOHNGELD BEWOHNER          
        $BewohnerTabellenwohngeld=$this->_gettabellenwohngeld();   
        //GET ALL SCHWERBEHINDERTENAUSWEIS BEWOHNER          
        $BewohnerSchwerbehindertausweis=$this->_getSchwerbehindertausweis();   
        //GET ALL PFLEGEVISITE BEWOHNER          
        $BewohnerPflegeVisite=$this->_getPflegeVisite();   
        //GET ALL EVALUIERUNG BEWOHNER          
        $BewohnerEvaluierung=$this->_getEvaluierung();   
        //GET ALL WUNDAUSWERTUNG BEWOHNER          
        $BewohnerWundauswertung=$this->_getWundauswertung();   
        //GET ALL WUNDVERMESSUNG BEWOHNER          
        $BewohnerWundvermessung=$this->_getWundvermessung();   
        //GET ALL EVALUIERUNG BETREUUNG BEWOHNER          
        $BewohnerEvalBetreuung=$this->_getEvalBetreuung();   
        //GET ALL BRADENSKALA BEWOHNER          
        $BewohnerBradenskala=$this->_getBradenskala();   
        //GET ALL NORTONSKALA BEWOHNER          
        $BewohnerNortonskala=$this->_getNortonskala();   
        //GET ALL DEKUBITUSPROPHYLAXE BEWOHNER          
        $BewohnerDekubitusprophylaxe=$this->_getDekubitusprophylaxe();   
        //GET ALL SICHERHEITSKONTROLLEN BEWOHNER          
        $BewohnerSicherheitskontrollen=$this->_getSicherheitskontrollen();   
        //GET ALL EVALUIERUNG KONTRACTUR BEWOHNER          
        $BewohnerEvaluierungKontraktur=$this->_getEvaluierungKontraktur();   
        //GET ALL ERGEBNISERFASSUNG BEWOHNER          
        $BewohnerErgebniserfassung=$this->_getErgebniserfassung();   
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
        WHEN K.[Erinnerung] is not NULL THEN FORMAT(K.[Ende], 'dd.MM.yyyy HH:mm') 
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
                $row['duration']=intval($row['duration']);
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
            $narr=array_merge($GeburtstageMitarbeiter,$GeburtstageBewohner,$BewohnerGenehmigungen,$BewohnerGEZ,$BewohnerPersAusweis,$BewohnerPflegewohngeld,$BewohnerTabellenwohngeld,$BewohnerSchwerbehindertausweis,$BewohnerPflegeVisite,$BewohnerEvaluierung,$BewohnerWundauswertung,$BewohnerWundvermessung,$BewohnerEvalBetreuung,$BewohnerBradenskala,$BewohnerNortonskala, $BewohnerDekubitusprophylaxe,$BewohnerSicherheitskontrollen,$BewohnerEvaluierungKontraktur,$BewohnerErgebniserfassung, $narr);
            return $narr;
        } else {
            return array_merge($GeburtstageMitarbeiter,$GeburtstageBewohner,$BewohnerGenehmigungen,$BewohnerGEZ,$BewohnerPersAusweis,$BewohnerPflegewohngeld,$BewohnerTabellenwohngeld,$BewohnerSchwerbehindertausweis,$BewohnerPflegeVisite,$BewohnerEvaluierung,$BewohnerWundauswertung,$BewohnerWundvermessung,$BewohnerEvalBetreuung,$BewohnerBradenskala,$BewohnerNortonskala, $BewohnerDekubitusprophylaxe,$BewohnerSicherheitskontrollen,$BewohnerEvaluierungKontraktur,$BewohnerErgebniserfassung);
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
    public function deleteEventStampOnViewDaily($id) { 
        $query="DELETE FROM [".$this->dbnameV."].dbo.Kalender WHERE ID=".$id." ";
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
        $rruleTerminJaehrlichJahresMusterJahrestag_WochennummerArray
    ):mixed {
          /*
--------------------------------------------
Zeitkonvertierung in ein Zeitobjekt
$datetime = new DateTime($_POST['yourDate'] ?? ''); // or $_GET / json input
$date = $datetime->format('Y-m-d');    // "2025-05-16"
$time = $datetime->format('H:i:s');    // "15:15:00" (if local timezone set)
$datetime->setTimezone(new DateTimeZone('Europe/Berlin'));
 */ 
/*
    id INT PRIMARY KEY IDENTITY(1,1),
    anwender VARCHAR(50),    
    betreff VARCHAR(255) DEFAULT 'Terminierung',         
    isnote VARCHAR(512) DEFAULT NULL,      
    alertrule DATETIME DEFAULT NULL,         
    systempart VARCHAR(10) CHECK (systempart IN ('ME','P','V','PUB')), 
    location INT DEFAULT NULL,     
    floor VARCHAR(50),        
    starttime DATETIME NOT NULL,             
    rfrequency VARCHAR(10) CHECK (rfrequency IN ('DAILY', 'WEEKLY', 'MONTHLY', 'YEARLY')),  
    intervalnumber INT DEFAULT 1,          
    byday VARCHAR(20),                 
    bymonthday VARCHAR(20),          
    bymonth VARCHAR(20),             
    byhour VARCHAR(50),         
    wkst VARCHAR(2),           
    byyearday VARCHAR(100),    
    byweekno VARCHAR(50),       
    totalcount INT,                       
    until DATETIME DEFAULT NULL,                   
    changed VARCHAR(80) DEFAULT NULL,
    kategorie INT NULL,
    hexcolor VARCHAR(10) DEFAULT NULL,
    rrulestring VARCHAR(255) DEFAULT NULL,   
    duration INT NULL 
 */
/*
    1. DAILY
FREQ=DAILY;INTERVAL=1

2. WEEKLY
FREQ=WEEKLY;INTERVAL=1;BYDAY=MO,WE,FR;

3. MONTHLY
FREQ=MONTHLY;INTERVAL=1;BYMONTHDAY=1,15 ->Spezifische Tage
FREQ=MONTHLY;INTERVAL=1;BYDAY=1MO ->Spezifische Wochentage

4. YEARLY
FREQ=YEARLY;INTERVAL=1;BYMONTH=6;BYMONTHDAY=1
FREQ=YEARLY;INTERVAL=1;BYYEARDAY=100
FREQ=YEARLY;INTERVAL=1;BYMONTH=6;BYDAY=1MO  ; (1st Monday of June)
FREQ=YEARLY;INTERVAL=1;BYWEEKNO=20;BYDAY=MO 
-------------------------------------------    
         */
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
        $rruleTerminJaehrlichJahresMusterJahrestag_WochennummerArray
    ):mixed {
          /*
--------------------------------------------
Zeitkonvertierung in ein Zeitobjekt
$datetime = new DateTime($_POST['yourDate'] ?? ''); // or $_GET / json input
$date = $datetime->format('Y-m-d');    // "2025-05-16"
$time = $datetime->format('H:i:s');    // "15:15:00" (if local timezone set)
$datetime->setTimezone(new DateTimeZone('Europe/Berlin'));
 */ 
/*
    id INT PRIMARY KEY IDENTITY(1,1),
    anwender VARCHAR(50),    
    betreff VARCHAR(255) DEFAULT 'Terminierung',         
    isnote VARCHAR(512) DEFAULT NULL,      
    alertrule DATETIME DEFAULT NULL,         
    systempart VARCHAR(10) CHECK (systempart IN ('ME','P','V','PUB')), 
    location INT DEFAULT NULL,     
    floor VARCHAR(50),        
    starttime DATETIME NOT NULL,             
    rfrequency VARCHAR(10) CHECK (rfrequency IN ('DAILY', 'WEEKLY', 'MONTHLY', 'YEARLY')),  
    intervalnumber INT DEFAULT 1,          
    byday VARCHAR(20),                 
    bymonthday VARCHAR(20),          
    bymonth VARCHAR(20),             
    byhour VARCHAR(50),         
    wkst VARCHAR(2),           
    byyearday VARCHAR(100),    
    byweekno VARCHAR(50),       
    totalcount INT,                       
    until DATETIME DEFAULT NULL,                   
    changed VARCHAR(80) DEFAULT NULL,
    kategorie INT NULL,
    hexcolor VARCHAR(10) DEFAULT NULL,
    rrulestring VARCHAR(255) DEFAULT NULL,   
    duration INT NULL 
 */
/*
    1. DAILY
FREQ=DAILY;INTERVAL=1

2. WEEKLY
FREQ=WEEKLY;INTERVAL=1;BYDAY=MO,WE,FR;

3. MONTHLY
FREQ=MONTHLY;INTERVAL=1;BYMONTHDAY=1,15 ->Spezifische Tage
FREQ=MONTHLY;INTERVAL=1;BYDAY=1MO ->Spezifische Wochentage

4. YEARLY
FREQ=YEARLY;INTERVAL=1;BYMONTH=6;BYMONTHDAY=1
FREQ=YEARLY;INTERVAL=1;BYYEARDAY=100
FREQ=YEARLY;INTERVAL=1;BYMONTH=6;BYDAY=1MO  ; (1st Monday of June)
FREQ=YEARLY;INTERVAL=1;BYWEEKNO=20;BYDAY=MO 
-------------------------------------------    
         */
    }
}


?>