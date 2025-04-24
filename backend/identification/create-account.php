<?php
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

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['identifiant'], $input['email'], $input['password'], $input['Nom'], $input['Prenom'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Champs manquants."]);
    exit();
}

$identifiant = trim($input['identifiant']);
$email = trim($input['email']);
$password = trim($input['password']);
$nom = trim($input['Nom']);
$prenom = trim($input['Prenom']);

if (strlen($identifiant) < 3 || strlen($password) < 8) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Identifiant ou mot de passe trop court."]);
    exit();
}

try {
    // Vérifie si l'identifiant ou l'email est déjà utilisé
    $stmt = $adminConn->prepare("SELECT COUNT(*) FROM users WHERE identifiant = ? OR email = ?");
    $stmt->execute([$identifiant, $email]);
    if ($stmt->fetchColumn() > 0) {
        http_response_code(409);
        echo json_encode(["success" => false, "message" => "Identifiant ou email déjà utilisé."]);
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insère l'utilisateur
    $stmt = $adminConn->prepare("INSERT INTO users (identifiant, email, password, nom, prenom) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$identifiant, $email, $hashedPassword, $nom, $prenom]);

    $userId = $adminConn->lastInsertId();
    $expirationTime = time() + 3600;
    $tokenRaw = "$identifiant:$email:$expirationTime";
    $tokenEncrypted = TokenService::encrypt($tokenRaw);
    $tokenHashed = hash('sha256', $tokenRaw);

    // Enregistre le token
    $stmt = $adminConn->prepare("INSERT INTO tokens (user_id, token, created_at, expired_at) VALUES (?, ?, ?, ?)");
    $stmt->execute([$userId, $tokenHashed, date('Y-m-d H:i:s'), date('Y-m-d H:i:s', $expirationTime)]);

    echo json_encode([
        "success" => true,
        "user" => [
            "identifiant" => $identifiant,
            "email" => $email,
            "token" => $tokenEncrypted
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur serveur : " . $e->getMessage()]);
}
