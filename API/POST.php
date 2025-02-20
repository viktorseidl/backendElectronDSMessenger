<?php
function sanitizeInput($input)
{
    return htmlspecialchars(strip_tags(trim($input)));
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


    default:
        echo json_encode(['error' => 'Invalid API endpoint']);
        http_response_code(404);
        break;
}

?>