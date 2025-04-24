<?php

/* 
auth.php : Verification du token d'authentification
Ce fichier doit être inclus dans toutes les routes nécessitant une authentification
Il vérifie la présence du token dans l'en-tête Authorization de la requête
Si le token est valide, il le déchiffre et vérifie sa validité dans la base de données
Si le token est valide, il récupère les informations de l'utilisateur et les rend accessibles dans la variable $connectedUser
Si le token est invalide ou expiré, il renvoie une réponse 401 Unauthorized
et un message d'erreur au format JSON 
*/

header("Content-Type: application/json");

require('../TokenService.php');
require_once '../DB/Request.php';
$headers = getallheaders();

if (!isset($headers['Authorization'])) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Token manquant."]);
    exit();
}

$authHeader = $headers['Authorization'];
if (!str_starts_with($authHeader, 'Bearer ')) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Format invalide du token."]);
    exit();
}

$encryptedToken = trim(str_replace('Bearer ', '', $authHeader));
$decryptedToken = TokenService::decrypt($encryptedToken);

if (!$decryptedToken) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Échec du déchiffrement du token."]);
    exit();
}

try {
    $stmt = $adminConn->prepare("SELECT t.*, u.id AS user_id, u.identifiant, u.email 
    FROM tokens t 
    JOIN users u ON t.user_id = u.id 
    WHERE t.token = ? AND t.expired_at > ?");
    $stmt->execute([$decryptedToken, time()]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        http_response_code(401);
        echo json_encode(["success" => false, "message" => "Token invalide ou expiré."]);
        exit();
    }

    // 👇 accessible dans toutes tes routes après auth
    $connectedUser = [
        'id' => $result['user_id'],
        'identifiant' => $result['identifiant'],
        'email' => $result['email']
    ];
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur BDD : " . $e->getMessage()]);
    exit();
}
