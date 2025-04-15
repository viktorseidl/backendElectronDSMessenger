<?php
function sanitizeInput($input)
{
    return htmlspecialchars(strip_tags(trim($input)));
}
switch ($path) {
    case 'DeleteMessageOnID':
        require(__DIR__ . '/../Classes/Messages.php');
        $anwender = base64_decode(sanitizeInput($_GET['a'] ?? ''));
        $dbtype = (base64_decode(sanitizeInput($_GET['t'] ?? ''))) == "P" ? 'pflege' : 'verwaltung';
        $ID = sanitizeInput($data['mid'] ?? '');
        $Messages = new Messages($dbtype, $anwender);
        $result = $Messages->deleteMessagesOnID($ID);
        echo json_encode($result ?: false);
        break;
    case 'DeleteMessageArr':
        require(__DIR__ . '/../Classes/Messages.php');
        $anwender = base64_decode(sanitizeInput($_GET['a'] ?? ''));
        $dbtype = (base64_decode(sanitizeInput($_GET['t'] ?? ''))) == "P" ? 'pflege' : 'verwaltung';
        $IDarr = json_decode($data['arr'] ?? '');
        $Messages = new Messages($dbtype, $anwender);
        $result = $Messages->deleteMessagesArrayOnID($IDarr);
        echo json_encode($result ?: false);
        break;
    case 'deleteEventOnDailyView':
        require(__DIR__ . '/../Classes/Calendar.php');
        //$IDarr = sanitizeInput($data['arr'] ?? '');
        $obj = json_decode(base64_decode(sanitizeInput($_GET['a'] ?? '')));
        $event = $obj->id;
        $Calendar = new Calendar("", "", "",1);
        $result =$Calendar->deleteEventStampOnViewDaily($obj->id);
        echo json_encode($result ?: false);
        break;
    case 'updateNoteDeleteOnID':
        require(__DIR__ . '/../Classes/Notes.php');
        //$IDarr = sanitizeInput($data['arr'] ?? '');
        $anwender = base64_decode(sanitizeInput($_GET['a'] ?? ''));
        $dbtype = 'verwaltung';
        $data = json_decode($data);
        $ID = sanitizeInput($data->mid ?? '');
        $Notes = new Notes($dbtype, $anwender);
        $result = $Notes->updateNoteDeleteOnID($anwender, $ID);
        echo json_encode($result ?: false);
        break;
    default:
        echo json_encode(['error' => 'Invalid API endpoint']);
        http_response_code(404);
        break;
}

?>