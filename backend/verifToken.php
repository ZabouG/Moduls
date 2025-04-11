<?php
ob_start();
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:8282");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Credentials: true");

error_reporting(E_ALL);
ini_set('display_errors', 1);

// 🛑 OPTIONS = réponse immédiate (préflight CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 📂 Log debug simple
function debug_log($label, $value) {
    file_put_contents('verif_token_debug.log', "$label: " . print_r($value, true) . "\n", FILE_APPEND);
}

// 🔐 Récupère correctement le header Authorization
function getAuthHeader(): ?string {
    foreach (['HTTP_AUTHORIZATION', 'REDIRECT_HTTP_AUTHORIZATION'] as $key) {
        if (isset($_SERVER[$key]) && stripos($_SERVER[$key], 'Bearer ') === 0) {
            return $_SERVER[$key];
        }
    }

    if (function_exists('apache_request_headers')) {
        foreach (apache_request_headers() as $key => $value) {
            if (strtolower($key) === 'authorization' && stripos($value, 'Bearer ') === 0) {
                return $value;
            }
        }
    }

    if (function_exists('getallheaders')) {
        foreach (getallheaders() as $key => $value) {
            if (strtolower($key) === 'authorization' && stripos($value, 'Bearer ') === 0) {
                return $value;
            }
        }
    }

    return null;
}

// ✅ Bloc de sécurité global
try {
    require_once './DB/Request.php';
    require_once './TokenService.php';

    if (!isset($adminConn)) {
        throw new Exception("Connexion à la base de données non initialisée.");
    }

    $authHeader = getAuthHeader();
    debug_log('🔐 Authorization header', $authHeader ?: 'Non défini');

    if (!$authHeader) {
        http_response_code(401);
        throw new Exception("Token manquant.");
    }

    if (!str_starts_with($authHeader, 'Bearer ')) {
        http_response_code(401);
        throw new Exception("Format invalide du token.");
    }

    $encryptedToken = trim(str_replace('Bearer ', '', $authHeader));
    $decryptedToken = TokenService::decrypt($encryptedToken);
    debug_log('🔓 Token déchiffré', $decryptedToken ?: 'Échec du déchiffrement');

    if (!$decryptedToken) {
        http_response_code(401);
        throw new Exception("Échec du déchiffrement du token.");
    }

    $stmt = $adminConn->prepare("SELECT u.id, u.identifiant, u.email 
                                 FROM tokens t
                                 JOIN users u ON t.id_user = u.id
                                 WHERE t.token = ? AND t.expired_at > ?");
    $stmt->execute([$decryptedToken, time()]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    debug_log('📦 Résultat SQL', $user ?: 'Aucun utilisateur trouvé');

    if (!$user) {
        http_response_code(401);
        throw new Exception("Token invalide ou expiré.");
    }

    echo json_encode([
        "success" => true,
        "message" => "Token valide.",
        "user" => $user
    ]);
    flush();

} catch (Throwable $e) {
    // Log et réponse propre côté frontend
    debug_log('🔥 Erreur capturée', $e->getMessage());

    echo json_encode([
        "success" => false,
        "message" => "Erreur serveur : " . $e->getMessage()
    ]);
    http_response_code(http_response_code() === 200 ? 500 : http_response_code());
}
ob_end_flush();
