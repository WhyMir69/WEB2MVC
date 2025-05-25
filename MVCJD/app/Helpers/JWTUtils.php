<?php

namespace Helpers;

require_once 'D:\phpsite\JWT\php-jwt-main\src\JWT.php';
require_once 'D:\phpsite\JWT\php-jwt-main\src\Key.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTUtils {
    private static $secretKey;
    private static $algorithm = 'HS256'; // Algorithm used for signing the token
    private static $defaultExpiration = 3600; // Default token validity (1 hour)

    // Initialize the secret key from environment variables
    public static function initialize(): void {
        self::$secretKey = getenv('JWT_SECRET_KEY') ?: 'default_secret_key';
    }

    // Generate a JWT token
    public static function generateToken(array $payload, ?int $expiration = null): string {
        $issuedAt = time();
        $expiration = $expiration ?? $issuedAt + self::$defaultExpiration;

        $payload['iat'] = $issuedAt; // Issued at
        $payload['exp'] = $expiration; // Expiration time

        return JWT::encode($payload, self::$secretKey, self::$algorithm);
    }

    // Validate and decode a JWT token
    public static function validateToken(string $token): ?object {
        try {
            return JWT::decode($token, new Key(self::$secretKey, self::$algorithm));
        } catch (\Firebase\JWT\ExpiredException $e) {
            error_log("Token expired: " . $e->getMessage());
            return null;
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            error_log("Invalid signature: " . $e->getMessage());
            return null;
        } catch (\Exception $e) {
            error_log("Token validation failed: " . $e->getMessage());
            return null;
        }
    }
}

// Initialize the secret key
JWTUtils::initialize();