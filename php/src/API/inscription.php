<?php

require_once('src/sql.php'); // Inclure la connexion avant de l'utiliser


function inscription($post){
    global $conn;
    foreach ($post as $key => $value) {
        echo "<script>console.log('🔑 $key: $value');</script>";
    }

    // Vérification des champs nécessaires dans $post
    if (!isset($post["Nom"], $post["Prenom"], $post["email"], $post["password"], $post["Identifiant"])) {
        echo "<script>console.log('🚨 Champs manquants !');</script>";
        return false; // Retourne false si un des champs requis est manquant
    }

    // Vérification si l'identifiant ou l'email existe déjà
    // Vous pouvez adapter cette vérification en fonction de la structure de votre base de données
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE identifiant = :identifiant OR email = :email");
    $stmt->execute([
        'identifiant' => $post['Identifiant'],
        'email' => $post['email']
    ]);
    $exists = $stmt->fetchColumn();
    
    if ($exists > 0) {
        echo "<script>console.log('🚨 Identifiant ou email déjà pris !');</script>";
        return false; // Retourne false si l'identifiant ou l'email existe déjà
    }

    // Préparation de la requête SQL pour l'insertion de l'utilisateur
    $sql = "INSERT INTO users (nom, prenom, email, password, identifiant) VALUES (:nom, :prenom, :email, :password, :identifiant)";
    
    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            "email" => $post["email"],
            "password" => password_hash($post["password"], PASSWORD_DEFAULT),
            "nom" => $post["Nom"],
            "prenom" => $post["Prenom"],
            "identifiant" => $post["Identifiant"]
        ]);
        
        // Si l'inscription réussit
        echo "<script>console.log('👌 Utilisateur inscrit');</script>";
        
        // Démarre la session si ce n'est pas déjà fait
        if (!isset($_SESSION)) {
            session_start();
        }

        $_SESSION["user"] = $post["identifiant"]; // Sauvegarde l'identifiant dans la session
        
        return true; // Retourne true pour signaler que l'inscription a réussi
    } catch (PDOException $e) {
        // Gestion de l'exception
        echo "<script>console.log('🚨 Erreur SQL: " . addslashes($e->getMessage()) . "');</script>";
        return false; // Retourne false en cas d'erreur
    }
}
?>
