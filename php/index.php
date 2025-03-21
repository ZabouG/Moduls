<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <title>Moduls</title>
</head>
<body>
    test DB

    <?php
    $servername = "172.20.0.2:3306";
    $username = "root";
    $password = "rootpassword";
    $dbname = "moduls";
    
    try {
        // Create connection
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "Connected successfully";
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
    
    ?>
</body>
</html>