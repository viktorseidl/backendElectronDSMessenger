<?php
function sanitizeInput($input)
{
    return htmlspecialchars(strip_tags(trim($input)));
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