<?php
function sanitizeInput($input)
{
    return htmlspecialchars(strip_tags(trim($input)));
}
switch ($path) {
    case 'getMessagesAllReceived':
        require(__DIR__ . '/../Classes/Messages.php');
        $anwender = base64_decode(sanitizeInput($_GET['a'] ?? ''));
        $dbtype = (base64_decode(sanitizeInput($_GET['t'] ?? ''))) == "P" ? 'pflege' : 'verwaltung';
        $Messages = new Messages($dbtype, $anwender);
        $result = $Messages->getAllMessages();
        echo json_encode($result);
        //echo json_encode($result ?: ['error' => 'No Administrators']);
        break;
    case 'getMessagesAllSend':
        require(__DIR__ . '/../Classes/Messages.php');
        $anwender = base64_decode(sanitizeInput($_GET['a'] ?? ''));
        $dbtype = (base64_decode(sanitizeInput($_GET['t'] ?? ''))) == "P" ? 'pflege' : 'verwaltung';
        $Messages = new Messages($dbtype, $anwender);
        $result = $Messages->getAllMessagesSend();
        echo json_encode($result);
        //echo json_encode($result ?: ['error' => 'No Administrators']);
        break;
    case 'getMessagesAllTrash':
        require(__DIR__ . '/../Classes/Messages.php');
        $anwender = base64_decode(sanitizeInput($_GET['a'] ?? ''));
        $dbtype = (base64_decode(sanitizeInput($_GET['t'] ?? ''))) == "P" ? 'pflege' : 'verwaltung';
        $Messages = new Messages($dbtype, $anwender);
        $result = $Messages->getAllMessagesTrash();
        echo json_encode($result);
        //echo json_encode($result ?: ['error' => 'No Administrators']);
        break;
    case 'getAllFiles':
        require(__DIR__ . '/../Classes/Messages.php');
        $anwender = base64_decode(sanitizeInput($_GET['a'] ?? ''));
        $dbtype = (base64_decode(sanitizeInput($_GET['t'] ?? ''))) == "P" ? 'pflege' : 'verwaltung';
        $Messages = new Messages($dbtype, $anwender);
        $result = $Messages->getAllFiles();
        echo json_encode($result);
        //echo json_encode($result ?: ['error' => 'No Administrators']);
        break;




    default:
        echo json_encode(['error' => 'Invalid API endpoint']);
        http_response_code(404);
        break;
}
?>