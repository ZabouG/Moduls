<?php
    $servername = "172.18.0.11:3306";
    $username = "root";
    $password = "rootpassword";
    $dbname = "moduls";
    
    try {
        // Create connection
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        ?>     <script> console.log("👌 Connection DB reussi")</script> <?php
    } catch (PDOException $e) {
        ?>     <script> console.log(".🚨 Connection Raté")</script> <?php
        die("Connection failed: " . $e->getMessage());
    }
    
    function request($sql) {
        global $conn;

        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $json = json_encode($stmt->fetchAll());
            return $json;
        } catch (PDOException $e) {
            ?>     <script> console.log(".🚨 Requete Raté")</script> <?php
            die("Requete failed: " . $e->getMessage());
        }
    }
?>