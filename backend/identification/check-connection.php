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


$input = json_decode(file_get_contents("php://input"), true);
require_once '../DB/Request.php';
require_once '../tokenService.php'; // à adapter selon ton arborescence

if (!isset($input['identifiant']) || empty(trim($input['identifiant']))) {
    echo json_encode(["success" => false, "message" => "Aucun identifiant fourni."]);
    exit();
}

if (!isset($input['password']) || empty(trim($input['password']))) {
    echo json_encode(["success" => false, "message" => "Aucun mot de passe fourni."]);
    exit();
}

$identifiant = trim($input['identifiant']);
$password = $input['password'];

try {
    $stmt = $adminConn->prepare("SELECT id, identifiant, email, password FROM users WHERE (identifiant = ? OR email = ?)");
    $stmt->execute([$identifiant, $identifiant]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {

        // Génération du token brut
        $expirationTime = time() + 3600;
        $tokenRaw = $user['identifiant'] . ':' . $user['email'] . ':' . $expirationTime;

        // Token pour le client (chiffré)
        $tokenEncrypted = TokenService::encrypt($tokenRaw);

        // Hash pour stockage en base
        $tokenHashed = hash('sha256', $tokenRaw);

        // Suppression de l'ancien token
        $stmt = $adminConn->prepare("DELETE FROM tokens WHERE id_user = ?");
        $stmt->execute([$user['id']]);

        // Insertion du nouveau token
        $stmt = $adminConn->prepare("INSERT INTO tokens (id_user, token, created_at, expired_at) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $user['id'],
            $tokenHashed,
            date('Y-m-d H:i:s'),
            date('Y-m-d H:i:s', $expirationTime)
        ]);

        echo json_encode([
            "success" => true,
            "message" => "Connexion réussie.",
            "user" => [
                "identifiant" => $user['identifiant'],
                "email" => $user['email'],
                "token" => $tokenEncrypted // envoyé au client uniquement
            ]
        ]);
    } else {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Identifiant ou mot de passe incorrect."
        ]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Erreur base de données : " . $e->getMessage()
    ]);
}
