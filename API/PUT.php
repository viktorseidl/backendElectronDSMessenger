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




    default:
        echo json_encode(['error' => 'Invalid API endpoint']);
        http_response_code(404);
        break;
}

?>