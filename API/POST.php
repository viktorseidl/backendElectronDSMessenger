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
    case 'checkCredentials':
        require(__DIR__ . '/../Classes/Login.php');
        $dbtype = sanitizeInput($data['dbtype'] ?? '');
        $user = sanitizeInput($data['user'] ?? '');
        $password = sanitizeInput($data['pass'] ?? '');
        $Login = new Login($dbtype, $user, $password);
        echo json_encode($Login->loginByApplikation());
        exit();
        //$result = $Login->loginByApplikation();
        //echo $result;
        //http_response_code(200);
        break;
    //MG
//123456


    default:
        echo json_encode(['error' => 'Invalid API endpoint']);
        http_response_code(404);
        break;
}

?>