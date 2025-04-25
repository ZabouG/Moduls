<?php
require_once 'Database.php';

// Création de l'objet admin
$adminDb = new Database('www.moduls.fr:3306', 'moduls', 'admin', 'CUc6yjxaH8DcwYcxAbhw');
$adminConn = $adminDb->getConnection();