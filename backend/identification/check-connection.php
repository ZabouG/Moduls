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



require_once '../DB/Request.php'; // Doit définir $adminConn

if (!isset($_POST['identifiant']) || empty(trim($_POST['identifiant'])) || !isset($_POST['password'])) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Identifiant ou mot de passe manquant."
    ]);
    exit();
}

$identifiant = trim($_POST['identifiant']);
$password = $_POST['password'];

try {
    // On récupère l'utilisateur avec son mot de passe haché
    $stmt = $adminConn->prepare("SELECT * FROM users WHERE identifiant = ? OR email = ?");
    $stmt->execute([$identifiant, $identifiant]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        echo json_encode([
            "success" => true,
            "user" => [
                "id" => $user['id'],
                "identifiant" => $user['identifiant'],
                "email" => $user['email']
                // N’inclus PAS le mot de passe dans la réponse
            ],
            "message" => "Connexion réussie."
        ]);
    } else {
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
