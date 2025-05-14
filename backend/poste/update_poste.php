<?php
header("Content-Type: application/json");

require('../identification/auth.php');

// Vérifier la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Méthode non autorisée."]);
    exit();
}

// Récupérer le payload JSON
$data = json_decode(file_get_contents("php://input"), true);

// Vérifier les champs requis
if (!isset($data['id']) || empty($data['id'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "ID du poste requis."]);
    exit();
}

if (!isset($data['Titre']) || empty(trim($data['Titre']))) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Le titre est requis."]);
    exit();
}

if (!isset($data['Description']) || empty(trim($data['Description']))) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "La description est requise."]);
    exit();
}

// Initialiser les données
$idUser = $connectedUser['id'];
$postId = $data['id'];
$titre = trim($data['Titre']);
$description = trim($data['Description']);
$image = isset($data['Image']) ? trim($data['Image']) : null;

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

    // Mettre à jour le poste
    $stmt = $adminConn->prepare("
        UPDATE Poste 
        SET Titre = ?, Description = ?, Image = ? 
        WHERE id = ?
    ");
    $stmt->execute([$titre, $description, $image, $postId]);

    http_response_code(200);
    echo json_encode(["success" => true, "message" => "Poste mis à jour avec succès."]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur BDD : " . $e->getMessage()]);
}
