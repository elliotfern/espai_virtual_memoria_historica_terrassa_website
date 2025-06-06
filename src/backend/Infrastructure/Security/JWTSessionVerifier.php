<?php

namespace App\Infrastructure\Security;

use App\Domain\Security\SessionVerifierInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class JWTSessionVerifier implements SessionVerifierInterface
{
    public function isSessionValid(): bool
    {
        $jwtSecret = $_ENV['TOKEN'] ?? null;
        if (!$jwtSecret || !isset($_COOKIE['token'])) {
            return false;
        }

        $token = trim($_COOKIE['token']);

        try {
            $decoded = JWT::decode($token, new Key($jwtSecret, 'HS256'));
            $userType = $decoded->user_type ?? null;

            return in_array($userType, [1, 2, 3, 4, 5], true);
        } catch (Exception $e) {
            error_log("Error al verificar sesión JWT: " . $e->getMessage());
            return false;
        }
    }
}
