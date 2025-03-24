    <?php
    $page = isset($_GET["page"]) ? $_GET["page"] : "";
    
    if ($page !== "inscription") {
        require("formulaire/form_connection.html");
    } else {
        require("formulaire/form_inscription.html");
    }

    require("./src/API/connection.php");

    ?>
</div>