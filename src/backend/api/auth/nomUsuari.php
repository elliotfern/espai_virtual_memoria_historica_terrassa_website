<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header('Content-Type: application/json');

// Cargar variables de entorno desde .env
$jwtSecret = $_ENV['TOKEN'];
$token = $_COOKIE['token'] ?? null;

if (!$token) {
    http_response_code(401);
    echo json_encode(['error' => 'No token']);
    exit;
}

try {
    $decoded = JWT::decode($token, new Key($jwtSecret, 'HS256'));
    echo json_encode(['username' => $decoded->username]);
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['error' => 'Token inválido']);
}
