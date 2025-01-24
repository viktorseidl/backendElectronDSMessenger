<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$method = $_SERVER['REQUEST_METHOD'];
$path = $_GET['path'] ?? null; // Assume the frontend sends requests with a 'path' query parameter
$data = json_decode(file_get_contents('php://input'), true);
define('BASE_DIR', __DIR__);

if ($method === 'GET') {
    include(BASE_DIR . '/API/GET.php');
} elseif ($method === 'POST') {
    include(BASE_DIR . '/API/POST.php');
} elseif ($method === 'PUT') {
    include(BASE_DIR . '/API/PUT.php');
} elseif ($method === "DELETE") {
    include(BASE_DIR . '/API/DELETE.php');
} elseif ($method === "OPTIONS") {
    http_response_code(204); // No Content
    exit;
} else {
    echo json_encode(['error' => 'Invalid API endpoint']);
    http_response_code(404);
}
?>