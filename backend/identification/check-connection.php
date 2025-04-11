<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:8282");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Credentials: true");

define('ENCRYPTION_KEY', 'aLw82pMz98bNd5xJq3KZmT1vBcRaFgHu');
define('ENCRYPTION_IV', substr(ENCRYPTION_KEY, 0, 16)); // 16 caractères pour l'IV en AES-256-CBC

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../DB/Request.php';

$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

$identifiant = isset($data['identifiant']) ? trim($data['identifiant']) : null;
$password = isset($data['password']) ? $data['password'] : null;

if (!$identifiant || !$password) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Identifiant ou mot de passe manquant."
    ]);
    exit();
}

try {
    // Vérification de l'utilisateur
    $stmt = $adminConn->prepare("SELECT * FROM users WHERE identifiant = ? OR email = ?");
    $stmt->execute([$identifiant, $identifiant]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $response = [];

    if ($user && password_verify($password, $user['password'])) {

        // Vérifie s'il existe déjà un token pour cet utilisateur
        $stmt = $adminConn->prepare("SELECT * FROM tokens WHERE id_user = ?");
        $stmt->execute([$user['id']]);
        $existingToken = $stmt->fetch(PDO::FETCH_ASSOC);

        $newExpiration = date('Y-m-d H:i:s', time() + 600);

        if ($existingToken) {
            if (strtotime($existingToken['expired_at']) > time()) {
                $stmt = $adminConn->prepare("UPDATE tokens SET expired_at = ? WHERE id_user = ?");
                $stmt->execute([$newExpiration, $user['id']]);
                $token = $existingToken['token'];
            } else {
                $token = bin2hex(random_bytes(16));
                $stmt = $adminConn->prepare("UPDATE tokens SET token = ?, expired_at = ? WHERE id_user = ?");
                $stmt->execute([$token, $newExpiration, $user['id']]);
            }
        } else {
            $token = bin2hex(random_bytes(16));
            $stmt = $adminConn->prepare("INSERT INTO tokens (id_user, token, expired_at) VALUES (?, ?, ?)");
            $stmt->execute([$user['id'], $token, $newExpiration]);
        }
        
        // ⬇️ chiffrement ici, toujours exécuté
        $encryptedToken = openssl_encrypt($token, 'AES-256-CBC', ENCRYPTION_KEY, 0, ENCRYPTION_IV);        

        $response = [
            "success" => true,
            "user" => [
                "id" => $user['id'],
                "identifiant" => $user['identifiant'],
                "email" => $user['email']
            ],
            "token" => $encryptedToken,
            "expired_at" => $newExpiration,
            "message" => "Connexion réussie."
        ];
    } else {
        $response = [
            "success" => false,
            "message" => "Identifiant ou mot de passe incorrect."
        ];
    }
} catch (PDOException $e) {
    http_response_code(500);
    $response = [
        "success" => false,
        "message" => "Erreur base de données : " . $e->getMessage()
    ];
}

echo json_encode($response);
