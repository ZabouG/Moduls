<?php
require_once 'Database.php';

// Création de l'objet admin
$adminDb = new Database('www.moduls.fr', 'moduls', 'admin', 'CUc6yjxaH8DcwYcxAbhw');
$adminConn = $adminDb->getConnection();

// Création de l'objet user
$userDb = new Database('www.moduls.fr', 'moduls', 'user', 'user_password');
$userConn = $userDb->getConnection();

function executeSelect(PDO $connection, string $query, array $params = []) {
    try {
        $stmt = $connection->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        echo "Erreur lors de l'exécution de la requête : " . $e->getMessage();
        return false;
    }
}