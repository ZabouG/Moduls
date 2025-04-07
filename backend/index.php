<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    TEST

    <?php
try {
    $pdo = new PDO("mysql:host=www.moduls.fr;dbname=moduls;charset=utf8", 'user', 'user_password');
    echo "Connexion réussie.";
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
</body>
</html>