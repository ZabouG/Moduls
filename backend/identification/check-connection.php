<?php
ob_start(); // Empêche tout output parasite

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:8282");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    ob_end_clean();
    exit();
}

require_once '../DB/Request.php'; // Doit contenir $adminConn

$rawBody = file_get_contents("php://input");
$data = json_decode($rawBody, true);

$identifiant = $data['identifiant'] ?? null;
$password = $data['password'] ?? null;

if (!$identifiant || !$password) {
    http_response_code(400);
    ob_end_clean();
    echo json_encode([
        "success" => false,
        "message" => "Identifiant ou mot de passe manquant."
    ]);
    exit();
}

try {
    $stmt = $adminConn->prepare("SELECT * FROM users WHERE identifiant = ?");
    $stmt->execute([$identifiant]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    ob_end_clean();

    if ($user && password_verify($password, $user['password'])) {
        echo json_encode([
            "success" => true,
            "message" => "Connexion réussie.",
            "user" => $user,
            "token" => bin2hex(random_bytes(16)) // Simule un token
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Identifiant ou mot de passe incorrect."
        ]);
    }
} catch (PDOException $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Erreur base de données : " . $e->getMessage()
    ]);
}
