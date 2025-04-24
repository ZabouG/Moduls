<?php
define('ENCRYPTION_KEY', 'aLw82pMz98bNd5xJq3KZmT1vBcRaFgHu'); // 32 caractères
define('ENCRYPTION_IV', substr(ENCRYPTION_KEY, 0, 16));

class TokenService {
    public static function encrypt($token) {
        return openssl_encrypt($token, 'AES-256-CBC', ENCRYPTION_KEY, 0, ENCRYPTION_IV);
    }

    public static function decrypt($encryptedToken) {
        return openssl_decrypt($encryptedToken, 'AES-256-CBC', ENCRYPTION_KEY, 0, ENCRYPTION_IV);
    }
}