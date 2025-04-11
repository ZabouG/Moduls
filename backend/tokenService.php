<?php
require_once './config.php';
class TokenService {
    public static function encrypt($token) {
        return openssl_encrypt($token, 'AES-256-CBC', ENCRYPTION_KEY, 0, ENCRYPTION_IV);
    }

    public static function decrypt($encryptedToken) {
        return openssl_decrypt($encryptedToken, 'AES-256-CBC', ENCRYPTION_KEY, 0, ENCRYPTION_IV);
    }
}