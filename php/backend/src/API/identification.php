<?php

require_once('src/sql.php'); // Inclure la connexion avant de l'utiliser


$action = $_POST["ACTION"];

switch ($action) {
    case "connection":
        connection($_POST);
        break;
    case "inscription":
        inscription($_POST);
        break;
    default:
        echo "Action non reconnue";
        break;
}

require_once('src/class/user.php'); // Inclure la classe User après la connexion à la base de données

function connection($post): bool{
    global $conn;
    foreach ($post as $key => $value) {
        echo "<script>console.log('🔑 $key: $value');</script>";
    }
    
    $identity = $post["identifiant"];
    $password = password_hash($post["password"], PASSWORD_DEFAULT);


    // Vérification des champs nécessaires dans $post
    if (!isset($identity, $password)) {
        echo "<script>console.log('🚨 Champs manquants !');</script>";
        return false; // Retourne false si un des champs requis est manquant
    }

    try{

        $sql = "SELECT * FROM users WHERE identifiant = :identifiant OR email = :identifiant AND password = :password";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            "identifiant" => $identity,
            "password" => $password
        ]);
    
        $user = $stmt->fetch(PDO::FETCH_ASSOC); // Récupère l'utilisateur correspondant    

    } catch (PDOException $e) {
        // Gestion de l'exception
        echo "<script>console.log('🚨 Erreur SQL: " . addslashes($e->getMessage()) . "');</script>";
        return false; // Retourne false en cas d'erreur
    }


    if ($user) {
        // Si l'utilisateur existe, on le crée en tant qu'objet User
        $User = new User($user["id"], $user["nom"], $user["prenom"], $user["email"], $user["password"], $user["identifiant"]);
        
        // Démarre la session si ce n'est pas déjà fait
        if (!isset($_SESSION)) {
            session_start();
        }
        
        $_SESSION["user"] = $User; // Sauvegarde l'utilisateur dans la session
        
        echo "<script>console.log('👌 Utilisateur connecté');</script>";
        return true; // Retourne l'utilisateur connecté
    } else {
        echo "<script>console.log('🚨 Identifiant ou mot de passe incorrect');</script>";
        return false; // Retourne null si l'identifiant ou le mot de passe est incorrect
    }


}

function inscription($post): bool{
    global $conn;
    foreach ($post as $key => $value) {
        echo "<script>console.log('🔑 $key: $value');</script>";
    }

    // Vérification des champs nécessaires dans $post
    if (!isset($post["Nom"], $post["Prenom"], $post["email"], $post["password"], $post["identifiant"])) {
        echo "<script>console.log('🚨 Champs manquants !');</script>";
        return false; // Retourne false si un des champs requis est manquant
    }

    // Vérification si l'identifiant ou l'email existe déjà
    // Vous pouvez adapter cette vérification en fonction de la structure de votre base de données
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE identifiant = :identifiant OR email = :email");
    $stmt->execute([
        'identifiant' => $post['identifiant'],
        'email' => $post['email']
    ]);
    $exists = $stmt->fetchColumn();
    
    if ($exists > 0) {
        echo "<script>console.log('🚨 identifiant ou email déjà pris !');</script>";
        return false; // Retourne false si l'identifiant ou l'email existe déjà
    }

    $_SESSION["user"] = $post["identifiant"]; // Sauvegarde l'identifiant dans la session

    // Préparation de la requête SQL pour l'insertion de l'utilisateur
    $sql = "INSERT INTO users (nom, prenom, email, password, identifiant) VALUES (:nom, :prenom, :email, :password, :identifiant)";
    
    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            "email" => $post["email"],
            "password" => password_hash($post["password"], PASSWORD_DEFAULT),
            "nom" => $post["Nom"],
            "prenom" => $post["Prenom"],
            "identifiant" => $post["identifiant"]
        ]);
        
        // Si l'inscription réussit
        echo "<script>console.log('👌 Utilisateur inscrit');</script>";
        
        // Démarre la session si ce n'est pas déjà fait
        if (!isset($_SESSION)) {
            session_start();
        }

        $sql = "SELECT * FROM users WHERE id = :id";

        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                "id" => $conn->lastInsertId()
            ]);
            $User = $stmt->fetch(PDO::FETCH_ASSOC); // Récupère l'utilisateur nouvellement inscrit
            $User = new User($User["id"], $User["nom"], $User["prenom"], $User["email"], $User["password"], $User["identifiant"]);
            $_SESSION["user"] = $User; // Sauvegarde l'utilisateur dans la session
        } catch (PDOException $e) {
            // Gestion de l'exception
            echo "<script>console.log('🚨 Erreur SQL: " . addslashes($e->getMessage()) . "');</script>";
            return false; // Retourne false en cas d'erreur
        }
        
        return true; 
    } catch (PDOException $e) {
        // Gestion de l'exception
        echo "<script>console.log('🚨 Erreur SQL: " . addslashes($e->getMessage()) . "');</script>";
        return false; // Retourne false en cas d'erreur
    }
}
?>
