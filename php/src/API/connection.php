<?php
// Récupérer les données envoyées via une requête POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifier si des données ont été envoyées
    if (!empty($_POST)) {
        // Récupérer les données POST
        $data = $_POST;

        // Exemple : Afficher les données reçues
        echo "<script>console.log(" . json_encode($_POST, JSON_PRETTY_PRINT) . ");</script>";

        require_once("./src/sql.php");
        identification($data);

    } else {

    echo "<script>console.log('Aucune donnée POST reçue.');</script>";

    }
} else {
    echo "<script>console.log('Cette page accepte uniquement les requêtes POST.');</script>";
}
?>