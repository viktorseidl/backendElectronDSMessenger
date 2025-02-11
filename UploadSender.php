<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require(__DIR__ . "/Classes/Uploader.php");

    $Uploader = new Uploader();
    $FArr = [];
    if (isset($_FILES['file'])) {
        $FArr = $Uploader->compressFiles($_FILES['file']);
    }
    $IdA = 0;
    if (count($FArr) > 0) {
        //create Entry and return ID
        $fid = $Uploader->getlatestIDAndInsertFiles($FArr);
        if ($fid != false) {
            $IdA = $fid;
        }
    }
    if ($Uploader->insertMailsUploader($_POST['sender'], $_POST['empfanger'], $_POST['prio'], $_POST['date'], $_POST['betr'], $_POST['mess'], $IdA)) {
        header('Content-Type: application/json');
        echo json_encode(true);
        http_response_code(200);
    } else {
        header('Content-Type: application/json');
        echo json_encode(false);
        http_response_code(200);
    }

}
?>