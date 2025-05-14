<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:8282");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Credentials: true");

/*
Verification si le pseudo est déjà pris
*/

// ⚠️ Gérer les requêtes OPTIONS (préflight CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../DB/Request.php'; // Assure-toi que $adminConn est bien défini ici

// 🔎 Vérifie que l'identifiant est bien envoyé
if (!isset($_POST['identifiant']) || empty(trim($_POST['identifiant']))) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Aucun identifiant fourni."
    ]);
    exit();
}

$identifiant = trim($_POST['identifiant']);

try {
    $stmt = $adminConn->prepare("SELECT COUNT(*) FROM users WHERE identifiant = ?");
    $stmt->execute([$identifiant]);
    $count = $stmt->fetchColumn();

    echo json_encode([
        "success" => true,
        "exists" => $count > 0,
        "message" => $count > 0 ? "Identifiant déjà pris." : "Identifiant disponible."
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Erreur:DB base de données : " . $e->getMessage()
    ]); 
}
