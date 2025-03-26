<?php
header("Content-Type: application/json"); // Indique que la réponse est en JSON

require_once("../sql.php");
global $conn;

// Vérifier si l'identifiant est bien envoyé via POST
if (!isset($_POST['Identifiant']) || empty($_POST['Identifiant'])) {
    echo json_encode(["error" => "Aucun identifiant fourni"]);
    exit;
}

$identifiant = trim($_POST['Identifiant']); // Nettoyer l'entrée

// Vérifier si l'identifiant existe déjà dans la base de données
$query = $conn->prepare("SELECT COUNT(*) FROM users WHERE identifiant = ?");
$query->execute([$identifiant]);
$count = $query->fetchColumn();

if ($count > 0) {
    echo json_encode(["exists" => true]); // L'identifiant est déjà pris
} else {
    echo json_encode(["exists" => false]); // L'identifiant est disponible
}
?>
