<?php
header("Content-Type: application/json");

require('../identification/auth.php');

// Vérifier la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Méthode non autorisée."]);
    exit();
}

// Récupérer le payload JSON
$data = json_decode(file_get_contents("php://input"), true);

// Vérifier la présence de l'ID
if (!isset($data['id']) || empty($data['id'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "ID du poste requis."]);
    exit();
}

$postId = $data['id'];
$idUser = $connectedUser['id'];

try {
    // Vérifier que le poste appartient bien à l'utilisateur connecté
    $stmt = $adminConn->prepare("SELECT idUser FROM Poste WHERE id = ?");
    $stmt->execute([$postId]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post || $post['idUser'] !== $idUser) {
        http_response_code(403);
        echo json_encode(["success" => false, "message" => "Accès non autorisé."]);
        exit();
    }

    // Supprimer le poste
    $stmt = $adminConn->prepare("DELETE FROM Poste WHERE id = ?");
    $stmt->execute([$postId]);

    http_response_code(200);
    echo json_encode(["success" => true, "message" => "Poste supprimé avec succès."]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur BDD : " . $e->getMessage()]);
}
