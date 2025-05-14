<?php
header("Content-Type: application/json");

require('../identification/auth.php');

// Vérifier la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Méthode non autorisée."]);
    exit();
}

// Vérifier la présence de l'ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "ID du poste requis."]);
    exit();
}

$postId = $_GET['id'];

try {
    $stmt = $adminConn->prepare("SELECT * FROM Poste WHERE id = ?");
    $stmt->execute([$postId]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Poste introuvable."]);
        exit();
    }

    http_response_code(200);
    echo json_encode(["success" => true, "post" => $post]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur BDD : " . $e->getMessage()]);
}
