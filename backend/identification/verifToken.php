<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:8282");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../DB/Request.php';
require_once '../tokenService.php';

$headers = getallheaders();

if (!isset($headers['Authorization'])) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Aucun token fourni."]);
    exit();
}

$authHeader = $headers['Authorization'];
if (!preg_match('/Bearer\s(.+)/', $authHeader, $matches)) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Format de token invalide."]);
    exit();
}

$encryptedToken = $matches[1];

$tokenRaw = TokenService::decrypt($encryptedToken);

if (!$tokenRaw) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Token illisible."]);
    exit();
}

$tokenHashed = hash('sha256', $tokenRaw);

$stmt = $adminConn->prepare("SELECT * FROM tokens WHERE token = ?");
$stmt->execute([$tokenHashed]);
$tokenRow = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tokenRow) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Token invalide."]);
    exit();
}


if (strtotime($tokenRow['expired_at']) < time()) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Token expiré."]);
    exit();
}

// ✅ Token valide
echo json_encode([
    "success" => true,
    "message" => "Token valide.",
    "id_user" => $tokenRow['id_user']
]);