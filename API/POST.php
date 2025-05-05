<?php
function sanitizeInput($input)
{
    if(is_array($input)){
        $n=[];
        foreach($input as $value){
            if(is_array($value)){
                $bn=[];
                foreach($value as $bivalue){
                    if(is_array($bivalue)){
                        array_push($bn,$bivalue);
                    }else{
                        array_push($bn,htmlspecialchars(strip_tags(trim($bivalue)))); 
                    }
                }
                array_push($n,$bn);
            }else{
                array_push($n,htmlspecialchars(strip_tags(trim($value))));  
            }
        }
        return $n;
    }else{
    return htmlspecialchars(strip_tags(trim($input)));
    }
}
switch ($path) {
    case 'testDBConnection':
        require(__DIR__ . '/../Classes/SetupDb.php');
        $host = sanitizeInput($data['host'] ?? '');
        $dbname = sanitizeInput($data['dbname'] ?? '');
        $dbnamepflege = sanitizeInput($data['dbnamepflege'] ?? '');
        $user = sanitizeInput($data['user'] ?? '');
        $password = sanitizeInput($data['pass'] ?? '');
        $result = false;
        $Setup = new SetupDb($host, $dbname, $dbnamepflege, $user, $password);
        $result = $Setup->checkDBCredentials();
        echo json_encode($result);
        http_response_code(200);
        break;
    case 'testDBTables':
        require(__DIR__ . '/../Classes/SetupDb.php');
        $host = sanitizeInput($data['host'] ?? '');
        $dbname = sanitizeInput($data['dbname'] ?? '');
        $dbnamepflege = sanitizeInput($data['dbnamepflege'] ?? '');
        $user = sanitizeInput($data['user'] ?? '');
        $password = sanitizeInput($data['pass'] ?? '');
        $result = false;
        $Setup = new SetupDb($host, $dbname, $dbnamepflege, $user, $password);
        $result = $Setup->checkOrCreateTables();
        echo json_encode($result);
        http_response_code(200);
        break;
    case 'checkCredentials':
        require(__DIR__ . '/../Classes/Login.php');
        $dbtype = sanitizeInput($data['dbtype'] ?? '');
        $user = sanitizeInput($data['user'] ?? '');
        $password = sanitizeInput($data['pass'] ?? '');
        $Login = new Login($dbtype, $user, $password);
        echo json_encode($Login->loginByApplikation());
        http_response_code(200);
        break;
    case 'checkCredentialsExternal':
        require(__DIR__ . '/../Classes/Login.php');
        $dbtype = sanitizeInput($data['dbtype'] ?? '');
        $user = sanitizeInput($data['user'] ?? '');
        $password = sanitizeInput($data['pass'] ?? '');  //Must be md5
        $Login = new Login($dbtype, $user, $password);
        echo json_encode($Login->loginByExternCall());
        http_response_code(200);
        break;
    case 'addNewNote':
        require(__DIR__ . '/../Classes/Notes.php');
        //$IDarr = sanitizeInput($data['arr'] ?? '');
        $anwender = base64_decode(sanitizeInput($_GET['a'] ?? ''));
        $dbtype = 'verwaltung';
        $data = json_decode($data);
        $ID = sanitizeInput($data->pid ?? '');
        $Notes = new Notes($dbtype, $anwender);
        $result = $Notes->addNewNote($anwender, $ID);
        echo json_encode($result ?: false);
        break;
    case 'addNewStandardEventToKalendar': 
        require(__DIR__ . '/../Classes/Calendar.php'); 
        $data = json_decode($data);
        $Anwender = sanitizeInput($data->terminAnwender ?? ''); // Username  like 'HAE' 
        $AnwenderTyp = sanitizeInput($data->terminAnwenderTyp ?? ''); //P - V
        $terminBetreff = sanitizeInput($data->terminBetreff ?? ''); //Betreff des Termins 
        $terminKategorie = sanitizeInput($data->terminKategorie ?? ''); //Kategorie des Termins = Name der Kategorie ->  Geburtstag der Bewohner
        $terminSichtbarkeit = sanitizeInput($data->terminSichtbarkeit ?? ''); //Sichtbarkeit ["ME"=Privat,"PUB"=Öffentlich (Pflege+Verwaltung),"P"=Pflege,"V"=Verwaltung] 
        $terminWohnbereich = sanitizeInput($data->terminWohnbereich ?? ''); //Name des Wohnbereich
        $terminBemerkung = sanitizeInput($data->terminBemerkung ?? ''); //Bemerkung EmptyString/String
        $terminErinnerungSwitch = sanitizeInput($data->terminErinnerungSwitch ?? ''); //Erinnerung Switch true/false
        $terminErinnerungDatum = sanitizeInput($data->terminErinnerungDatum ?? ''); //Erinnerung Datum null/ true/2025-05-22T22:00:00.000Z
        $Calendar = new Calendar("V", $Anwender, "",1);
        /*$result=$Calendar->insertNewStandardEvent();*/ 
        echo json_encode([$Anwender,
        $AnwenderTyp,
        $terminBetreff,
        $terminKategorie,
        $terminSichtbarkeit,
        $terminWohnbereich,
        $terminBemerkung,
        $terminErinnerungSwitch,
        $terminErinnerungDatum,] ?: false);
        break;
    case 'addNewRRuleEventToKalendar':
        require(__DIR__ . '/../Classes/Calendar.php'); 
        $data = json_decode($data);
        $Anwender = sanitizeInput($data->terminAnwender ?? ''); // Username  like 'HAE' 
        $AnwenderTyp = sanitizeInput($data->terminAnwenderTyp ?? ''); //P - V
        $terminBetreff = sanitizeInput($data->terminBetreff ?? ''); //Betreff des Termins 
        $terminKategorie = sanitizeInput($data->terminKategorie ?? ''); //Kategorie des Termins = Name der Kategorie ->  Geburtstag der Bewohner
        $terminSichtbarkeit = sanitizeInput($data->terminSichtbarkeit ?? ''); //Sichtbarkeit ["ME"=Privat,"PUB"=Öffentlich (Pflege+Verwaltung),"P"=Pflege,"V"=Verwaltung] 
        $terminWohnbereich = sanitizeInput($data->terminWohnbereich ?? ''); //Name des Wohnbereich
        $terminBemerkung = sanitizeInput($data->terminBemerkung ?? ''); //Bemerkung EmptyString/String
        $terminErinnerungSwitch = sanitizeInput($data->terminErinnerungSwitch ?? ''); //Erinnerung Switch true/false
        $terminErinnerungDatum = sanitizeInput($data->terminErinnerungDatum ?? ''); //Erinnerung Datum null/ true/2025-05-22T22:00:00.000Z
        $RRuleFrequenz = sanitizeInput($data->rruleTerminFrequenz ?? ''); //Frequenz DAILY, MONTHLY, WEEKLY & YEARLY
        $rruleTerminDauer = sanitizeInput($data->rruleTerminDauer ?? ''); //Dauer in Minuten 
        $rruleTerminStartDatumZeit = sanitizeInput($data->rruleTerminStartDatumZeit ?? ''); //StartDatum/zeit: 2025-05-22T22:00:00.000Z
        $rruleTerminEndeType = sanitizeInput($data->rruleTerminEndeType ?? ''); //Ende-Typ: NODATE, DATE oder REPEAT 
        $rruleTerminEndeTypeDatum = sanitizeInput($data->rruleTerminEndeTypeDatum ?? ''); //Datum Ende wenn DATE: 2025-05-22T22:00:00.000Z
        $rruleTerminEndeTypeWiederholungen = sanitizeInput($data->rruleTerminEndeTypeWiederholungen ?? ''); //Datum Ende in wiederholungen wenn REPEAT
        $rruleTerminJahresMuster = sanitizeInput($data->rruleTerminJahresMuster ?? ''); // ON YEARLY= DATUM, WOCHENTAGMONAT,YEARDAY, WEEKNUMBER
        $rruleTerminMonatMuster = sanitizeInput($data->rruleTerminMonatMuster ?? ''); // ON MONTHLY= DAY, WEEKDAY
        $rruleTerminWiederholungsMuster = sanitizeInput($data->rruleTerminWiederholungsMuster ?? ''); // wiederholungen 1 alle tage,monate,jahre,...
        $rruleTerminJaehrlichJahresMusterDatum_MonateArray = sanitizeInput($data->rruleTerminJaehrlichJahresMusterDatum_MonateArray ?? ''); // Monate array [1,2,12]
        $rruleTerminJaehrlichJahresMusterDatum_TageArray = sanitizeInput($data->rruleTerminJaehrlichJahresMusterDatum_TageArray ?? ''); // Monate array [1,2,12] oder [1:MO,2:DI,...]
        $rruleTerminJaehrlichJahresMusterJahrestag_TageArray = sanitizeInput($data->rruleTerminJaehrlichJahresMusterJahrestag_TageArray ?? ''); // Jahrestage array [1,2,365] 
        $rruleTerminJaehrlichJahresMusterJahrestag_WochenTageArray = sanitizeInput($data->rruleTerminJaehrlichJahresMusterJahrestag_WochenTageArray ?? ''); // WochenTage array [ "DI", "MI", "DO" ]
        $rruleTerminJaehrlichJahresMusterJahrestag_WochennummerArray = sanitizeInput($data->rruleTerminJaehrlichJahresMusterJahrestag_WochennummerArray ?? ''); // Wochennummer array [ 1, 2, 52 ]
        
        $Calendar = new Calendar("V", $Anwender, "",1);
        $result=$Calendar->insertNewRRuleEvent(
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
        );
         
 
        echo json_encode([
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
            $rruleTerminJaehrlichJahresMusterJahrestag_WochennummerArray] ?: false); 
        break;
    default:
        echo json_encode(['error' => 'Invalid API endpoint']);
        http_response_code(404);
        break;
}

?>