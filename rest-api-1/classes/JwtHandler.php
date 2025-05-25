<?php
/**
 * Simple JWT handler class
 * Self-contained implementation without external dependencies
 */
class JwtHandler {
    private $secretKey;
    private $issuer;
    private $algorithm = 'sha256';  // Using native PHP hash algorithm

    /**
     * Constructor for JwtHandler
     * 
     * @param string $secretKey Secret key for JWT signing
     * @param string $issuer Issuer name for the JWT
     */
    public function __construct($secretKey = 'your_secret_key', $issuer = 'rest_api_issuer') {
        $this->secretKey = $secretKey;
        $this->issuer = $issuer;
    }

    /**
     * Generate a new JWT token
     * 
     * @param int $userId User ID to include in the token
     * @param int $expiration Expiration time in seconds
     * @return string The generated JWT token
     */
    public function generateToken($userId, $expiration = 3600) {
        $issuedAt = time();
        $expirationTime = $issuedAt + $expiration;

        $payload = [
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'iss' => $this->issuer,
            'data' => [
                'userId' => $userId
            ]
        ];

        $header = $this->base64UrlEncode(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
        $payload = $this->base64UrlEncode(json_encode($payload));
        $signature = $this->base64UrlEncode(
            hash_hmac($this->algorithm, "$header.$payload", $this->secretKey, true)
        );

        $token = "$header.$payload.$signature";
        error_log("Generated JWT token: $token");
        return $token;
    }

    /**
     * Decode and validate a JWT token
     * 
     * @param string $jwt The JWT token to decode
     * @return object The decoded token payload
     * @throws Exception If token is invalid or expired
     */
    public function decode($jwt) {
        error_log("Attempting to decode JWT: $jwt");
        
        // Split token into parts
        $parts = explode('.', $jwt);
        if (count($parts) != 3) {
            error_log("Invalid token format: expected 3 parts, got " . count($parts));
            throw new \Exception("Invalid token format");
        }

        list($header, $payload, $signature) = $parts;
        
        error_log("Token parts - Header: $header, Payload: $payload, Signature: $signature");

        $valid = hash_hmac(
            $this->algorithm, 
            "$header.$payload", 
            $this->secretKey, 
            true
        );
        
        $valid_signature = $this->base64UrlEncode($valid);
        
        error_log("Expected signature: $valid_signature");
        error_log("Actual signature: $signature");
        
        if ($signature !== $valid_signature) {
            error_log("Invalid token signature");
            throw new \Exception("Invalid token signature");
        }

        $payload_data = json_decode($this->base64UrlDecode($payload));
        
        if (!$payload_data) {
            error_log("Failed to decode payload: " . json_last_error_msg());
            throw new \Exception("Invalid token payload");
        }
        
        error_log("Decoded payload: " . json_encode($payload_data));
        
        if (isset($payload_data->exp) && $payload_data->exp < time()) {
            error_log("Token has expired. Expiry: " . date('Y-m-d H:i:s', $payload_data->exp) . ", Current: " . date('Y-m-d H:i:s'));
            throw new \Exception("Token has expired");
        }

        return $payload_data;
    }

    /**
     * Encode data to Base64URL
     *
     * @param string $data The data to encode
     * @return string The encoded data
     */
    private function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Decode data from Base64URL
     *
     * @param string $data The data to decode
     * @return string The decoded data
     */
    private function base64UrlDecode($data) {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}