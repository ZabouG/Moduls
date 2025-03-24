<?php
// Récupérer les données envoyées via une requête POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifier si des données ont été envoyées
    if (!empty($_POST)) {
        // Récupérer les données POST
        $data = $_POST;

        // Exemple : Afficher les données reçues
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    } else {
        echo 'Aucune donnée POST reçue.';
    }
} else {
    echo 'Cette page accepte uniquement les requêtes POST.';
}
?>