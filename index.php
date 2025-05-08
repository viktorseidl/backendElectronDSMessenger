<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
require __DIR__.'/vendor/autoload.php';
$method = $_SERVER['REQUEST_METHOD'];
$path = $_GET['path'] ?? null; // Assume the frontend sends requests with a 'path' query parameter
$data = json_decode(file_get_contents('php://input'), true);

if ($method === 'GET') {
    include(__DIR__ . '/API/GET.php');
} elseif ($method === 'POST') {
    include(__DIR__ . '/API/POST.php');
} elseif ($method === 'PUT') {
    include(__DIR__ . '/API/PUT.php');
} elseif ($method === "DELETE") {
    include(__DIR__ . '/API/DELETE.php');
} elseif ($method === "OPTIONS") {
    http_response_code(204); // No Content
    exit;
} else {
    echo json_encode(['error' => 'Invalid API endpoint']);
    http_response_code(404);
}
?>