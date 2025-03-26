    <?php
    $page = isset($_GET["page"]) ? $_GET["page"] : "";
    
    if ($page !== "inscription") {
        require("formulaire/form_connection.html");
    } else {
        require("formulaire/form_inscription.html");
    }
    $post = isset($_POST) ? $_POST: "";
    if ($post) {
        require_once("src/API/inscription.php");
        inscription($post);
    }
    ?>
</div>