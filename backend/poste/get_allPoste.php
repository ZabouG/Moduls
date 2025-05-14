<?php
header("Content-Type: application/json");

require('../identification/auth.php');

try {
    $stmt = $adminConn->prepare("SELECT * FROM Poste ORDER BY create_at DESC");
    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    http_response_code(200);
    echo json_encode(["success" => true, "posts" => $posts]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur BDD : " . $e->getMessage()]);
}
