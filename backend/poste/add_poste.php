<?php
header("Content-Type: application/json");

require('../identification/auth.php'); // Ce fichier initialise $connectedUser et gère l'authentification

// Vérifier la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Méthode non autorisée
    echo json_encode(["success" => false, "message" => "Méthode non autorisée."]);
    exit();
}

// Récupérer le payload JSON
$data = json_decode(file_get_contents("php://input"), true);

// Vérifier les champs requis
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
$titre = trim($data['Titre']);
$description = trim($data['Description']);
$image = isset($data['Image']) ? trim($data['Image']) : null;

try {
    // Préparer la requête d'insertion
    $stmt = $adminConn->prepare("
        INSERT INTO Poste (idUser, Titre, Description, Image) 
        VALUES (?, ?, ?, ?)
    ");

    $stmt->execute([$idUser, $titre, $description, $image]);

    // Récupérer l'ID du poste nouvellement inséré
    $postId = $adminConn->lastInsertId();

    http_response_code(201);
    echo json_encode([
        "success" => true,
        "message" => "Poste créé avec succès.",
        "poste" => [
            "id" => $postId,
            "idUser" => $idUser,
            "Titre" => $titre,
            "Description" => $description,
            "Image" => $image,
            "create_at" => date("Y-m-d H:i:s")
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur BDD : " . $e->getMessage()]);
}
