<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:8080");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Credentials: true");

// ⚠️ Gérer les requêtes OPTIONS (préflight CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'DB/Request.php'; // Ce fichier doit contenir $userConn

// 🔐 Fonction de vérification du token en BDD
function verifyToken($conn, $token, $idUser): bool {
    $reponse = executeSelect($conn, "SELECT token FROM users WHERE token = :token AND idUser = :idUser", [
        ':token' => $token,
        ':idUser' => $idUser
    ]);

    return count($reponse) > 0;
}

// 🔎 Récupération du token depuis l'en-tête Authorization
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? null;

// 🔽 Lecture du body JSON
$rawBody = file_get_contents("php://input");
$data = json_decode($rawBody, true);

$idUser = $data['idUser'] ?? null;

if ($authHeader && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    $token = $matches[1];

    if ($idUser && verifyToken($userConn, $token, $idUser)) {
        $response = [
            "success" => true,
            "message" => "Token valide."
        ];
    } else {
        $response = [
            "success" => false,
            "message" => "Token invalide ou ID manquant."
        ];
    }
} else {
    $response = [
        "success" => false,
        "message" => "Aucun token fourni."
    ];
}

http_response_code(200);
echo json_encode($response);
