<?php
// Démarre la session si elle n'est pas déjà commencée
session_start();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <title>Moduls</title>
</head>
<body>
<?php
    // Vérifie si l'utilisateur est connecté
    if (isset($_SESSION["user"])) {
        // Si l'utilisateur est connecté, affiche son nom
        echo "<h1>Bienvenue, ".$_SESSION["user"]."</h1>";
    } else {
        // Sinon, inclut le fichier de connexion
        require("layout/identification/connection.php");
    }
    ?>
</body>
</html>