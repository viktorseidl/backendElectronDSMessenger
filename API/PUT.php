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
    case 'movetoInbox':
        require(__DIR__ . '/../Classes/Messages.php');
        //$IDarr = sanitizeInput($data['arr'] ?? '');
        $anwender = base64_decode(sanitizeInput($_GET['a'] ?? ''));
        $dbtype = (base64_decode(sanitizeInput($_GET['t'] ?? ''))) == "P" ? 'pflege' : 'verwaltung';
        $ID = sanitizeInput($data['mid'] ?? '');
        $Messages = new Messages($dbtype, $anwender);
        $result = $Messages->moveBackToInboxMessageOnID($ID);
        echo json_encode($result ?: false);
        break;
    case 'MarkReadMessageArr':
        require(__DIR__ . '/../Classes/Messages.php');
        //$IDarr = sanitizeInput($data['arr'] ?? '');
        $anwender = base64_decode(sanitizeInput($_GET['a'] ?? ''));
        $readunread = sanitizeInput($_GET['b'] ?? ''); //0=read 1=unread
        $dbtype = (base64_decode(sanitizeInput($_GET['t'] ?? ''))) == "P" ? 'pflege' : 'verwaltung';
        $ID = json_decode($data['arr'] ?? '');
        $Messages = new Messages($dbtype, $anwender);
        $result = $Messages->markAsReadMessageArray($ID, $readunread);
        echo json_encode($result ?: false);
        break;
    case 'updateMoveDailyView':
        require(__DIR__ . '/../Classes/Calendar.php');
        //$IDarr = sanitizeInput($data['arr'] ?? '');
        $obj = json_decode(base64_decode(sanitizeInput($_GET['a'] ?? '')));
        $newstarthour = $obj->newstarthour;
        $newstarthourstamp = strtotime(str_replace('.', '-', $newstarthour));
        $oldstart = $obj->oldstart;
        $oldstartstamp = strtotime(str_replace('.', '-', $oldstart));
        $oldend = $obj->oldend;
        $oldendstamp = strtotime(str_replace('.', '-', $oldend));
        $diff=$oldendstamp-$oldstartstamp;
        $Calendar = new Calendar("", "", "",1);
        $result =$Calendar->updateMovementStampViewDaily($newstarthourstamp,($newstarthourstamp+$diff), $obj->id);
        echo json_encode([$result,date('H:i',$newstarthourstamp),date('H:i',$newstarthourstamp+$diff)] ?: false);
        break;
    case 'updateStandardEventInKalendar':
        require(__DIR__ . '/../Classes/Calendar.php');
        $data = json_decode($data);
        $terminID = sanitizeInput($data->terminID ?? ''); // Username  like 'HAE' 
        $Anwender = sanitizeInput($data->terminAnwender ?? ''); // Username  like 'HAE' 
        $AnwenderTyp = sanitizeInput($data->terminAnwenderTyp ?? ''); //P - V
        $terminBetreff = sanitizeInput($data->terminBetreff ?? ''); //Betreff des Termins 
        $terminKategorie = sanitizeInput($data->terminKategorie ?? ''); //Kategorie des Termins = Name der Kategorie ->  Geburtstag der Bewohner
        $terminSichtbarkeit = sanitizeInput($data->terminSichtbarkeit ?? ''); //Sichtbarkeit ["ME"=Privat,"PUB"=Öffentlich (Pflege+Verwaltung),"P"=Pflege,"V"=Verwaltung] 
        $terminWohnbereich = sanitizeInput($data->terminWohnbereich ?? ''); //Name des Wohnbereich
        $terminBemerkung = sanitizeInput($data->terminBemerkung ?? ''); //Bemerkung EmptyString/String
        $terminErinnerungSwitch = sanitizeInput($data->terminErinnerungSwitch ?? ''); //Erinnerung Switch true/false
        $terminErinnerungDatum = sanitizeInput($data->terminErinnerungDatum ?? ''); //Erinnerung Datum null/ true/2025-05-22T22:00:00.000Z
        $standardTerminStartDatumZeit = sanitizeInput($data->standardTerminStartDatumZeit ?? ''); //Erinnerung Datum null/ true/2025-05-22T22:00:00.000Z
        $standardTerminEndeDatumZeit = sanitizeInput($data->standardTerminEndeDatumZeit ?? ''); //Erinnerung Datum null/ true/2025-05-22T22:00:00.000Z
        $Calendar = new Calendar("V", $Anwender, "",1); 
        $result=$Calendar->updateStandardEvent(
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
        ); 
        echo json_encode($result ?: false);
        break;
    case 'updateRRuleEventInKalendar':
        require(__DIR__ . '/../Classes/Calendar.php'); 
        $data = json_decode($data);
        $TerminID=sanitizeInput((string)$data->terminObjectID ?? '');
        $Anwender = sanitizeInput($data->terminAnwender ?? ''); // Username  like 'HAE' 
        $AnwenderTyp = sanitizeInput($data->terminAnwenderTyp ?? ''); //P - V
        $terminBetreff = sanitizeInput($data->terminBetreff ?? ''); //Betreff des Termins 
        $terminKategorie = sanitizeInput($data->terminKategorie ?? ''); //Kategorie des Termins = Name der Kategorie ->  Geburtstag der Bewohner
        $terminSichtbarkeit = sanitizeInput($data->terminSichtbarkeit ?? ''); //Sichtbarkeit ["ME"=Privat,"PUB"=Öffentlich (Pflege+Verwaltung),"P"=Pflege,"V"=Verwaltung] 
        $terminFarbe = sanitizeInput($data->rruleTerminColor ?? ''); //Sichtbarkeit ["ME"=Privat,"PUB"=Öffentlich (Pflege+Verwaltung),"P"=Pflege,"V"=Verwaltung] 
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
         $result=$Calendar->updateNewRRuleEvent(
            $TerminID,
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
        ); 
        echo json_encode($result ?: false); 
        break;
    case 'MarkReadMessageOnID':
        require(__DIR__ . '/../Classes/Messages.php');
        //$IDarr = sanitizeInput($data['arr'] ?? '');
        $anwender = base64_decode(sanitizeInput($_GET['a'] ?? ''));
        $readunread = sanitizeInput($_GET['b'] ?? ''); //0=read 1=unread
        $dbtype = (base64_decode(sanitizeInput($_GET['t'] ?? ''))) == "P" ? 'pflege' : 'verwaltung';
        $ID = sanitizeInput($data['mid'] ?? '');
        $Messages = new Messages($dbtype, $anwender);
        $result = $Messages->markAsReadMessageOnID($ID, $readunread);
        echo json_encode($result ?: false);
        break;
    case 'updateNotePositionOnID':
        require(__DIR__ . '/../Classes/Notes.php');
        //$IDarr = sanitizeInput($data['arr'] ?? '');
        $anwender = base64_decode(sanitizeInput($_GET['a'] ?? ''));
        $dbtype = 'verwaltung';
        $data = json_decode($data);
        $ID = sanitizeInput($data->mid ?? '');
        $x = sanitizeInput($data->xk ?? '');
        $y = sanitizeInput($data->yk ?? '');
        $Notes = new Notes($dbtype, $anwender);
        $result = $Notes->updateNotePositionOnID($anwender, $ID, $x, $y);
        echo json_encode($result ?: false);
        break;
    case 'updateNotePriorityOnID':
        require(__DIR__ . '/../Classes/Notes.php');
        //$IDarr = sanitizeInput($data['arr'] ?? '');
        $anwender = base64_decode(sanitizeInput($_GET['a'] ?? ''));
        $dbtype = 'verwaltung';
        $data = json_decode($data);
        $ID = sanitizeInput($data->mid ?? '');
        $prio = sanitizeInput($data->prio ?? '');
        $Notes = new Notes($dbtype, $anwender);
        $result = $Notes->updateNotePriorityOnID($anwender, $ID, $prio);
        echo json_encode($result ?: false);
        break;
    case 'updateNoteColorOnID':
        require(__DIR__ . '/../Classes/Notes.php');
        //$IDarr = sanitizeInput($data['arr'] ?? '');
        $anwender = base64_decode(sanitizeInput($_GET['a'] ?? ''));
        $dbtype = 'verwaltung';
        $data = json_decode($data);
        $ID = sanitizeInput($data->mid ?? '');
        $color = sanitizeInput($data->color ?? '');
        $Notes = new Notes($dbtype, $anwender);
        $result = $Notes->updateNoteColorOnID($anwender, $ID, $color);
        echo json_encode($result ?: false);
        break;
    case 'updateNoteTextOnID':
        require(__DIR__ . '/../Classes/Notes.php');
        //$IDarr = sanitizeInput($data['arr'] ?? '');
        $anwender = base64_decode(sanitizeInput($_GET['a'] ?? ''));
        $dbtype = 'verwaltung';
        $data = json_decode($data);
        $ID = sanitizeInput($data->mid ?? '');
        $txt = sanitizeInput($data->txt ?? '');
        $Notes = new Notes($dbtype, $anwender);
        $result = $Notes->updateNoteTextOnID($anwender, $ID, $txt);
        echo json_encode($result ?: false);
        break;
    case 'updateNoteRestoreOnID':
        require(__DIR__ . '/../Classes/Notes.php');
        //$IDarr = sanitizeInput($data['arr'] ?? '');
        $anwender = base64_decode(sanitizeInput($_GET['a'] ?? ''));
        $dbtype = 'verwaltung';
        $data = json_decode($data);
        $ID = sanitizeInput($data->mid ?? '');
        $Notes = new Notes($dbtype, $anwender);
        $result = $Notes->updateNoteRestoreOnID($anwender, $ID);
        echo json_encode($result ?: false);
        break;




    default:
        echo json_encode(['error' => 'Invalid API endpoint']);
        http_response_code(404);
        break;
}

?>