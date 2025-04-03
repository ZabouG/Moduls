<?php
    $servername = "www.moduls.fr:3306";
    $username = "admin";
    $password = "CUc6yjxaH8DcwYcxAbhw";
    $dbname = "moduls";
    
    try {
        // Create connection
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
    
// Fonction sécurisée pour l'authentification
function identification($data) {
    global $conn;

    if($data["ACTION"] == "connection"){

        $sql = "SELECT password FROM users WHERE email = :email";
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute(["email" => $data["email"]]);
            $user = $stmt->fetch();
    
            // Vérifier si l'utilisateur existe et si le mot de passe est correct
            if ($user && password_verify($data["password"], $user["password"])) {
                echo "<script>console.log('👌 Utilisateur authentifié');</script>";
                $reponse = false;
            } else {
                echo "<script>console.log('🚨 Identifiants incorrects');</script>";
                $reponse = false;
            }
        } catch (PDOException $e) {
            echo "<script>console.log('🚨 Erreur SQL: " . addslashes($e->getMessage()) . "');</script>";
            $reponse = false;
        }

    } elseif ($data["ACTION"] == "inscription") {

        $sql = "INSERT INTO users (email, password) VALUES (:email, :password)";
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                "email" => $data["email"],
                "password" => password_hash($data["password"], PASSWORD_DEFAULT)
            ]);
            echo "<script>console.log('👌 Utilisateur inscrit');</script>";
            return true;
        } catch (PDOException $e) {
            echo "<script>console.log('🚨 Erreur SQL: " . addslashes($e->getMessage()) . "');</script>";
            $reponse = false;
        }

    } else {
        
        echo "<script>console.log('🚨 Action inconnue');</script>";
    $reponse = false;
        ;
    }

    return $reponse;

}

?>