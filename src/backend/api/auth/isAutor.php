<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Cargar variables de entorno desde .env
$jwtSecret = $_ENV['TOKEN'];

// Comprobar si la cookie 'token' está presente
if (!isset($_COOKIE['token'])) {
    echo json_encode(['isAutor' => false]);
    exit;
}

$token = trim($_COOKIE['token']); // Obtener y limpiar el token de la cookie

if (!empty($token)) {
    try {
        // Verifica y decodifica el token
        $decoded = JWT::decode($token, new Key($jwtSecret, 'HS256'));

        // Verifica si el usuario tiene permisos de administrador
        if (
            isset($decoded->user_type) &&
            in_array($decoded->user_type, [2])
        ) {
            echo json_encode(['isAutor' => true]);
            exit;
        }
    } catch (Exception $e) {
        // Token inválido, expirado o manipulado
        error_log("JWT inválido: " . $e->getMessage());
    }
}

// Si no cumple, no es admin
echo json_encode(['isAutor' => false]);
