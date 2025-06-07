<?php

use Dotenv\Dotenv;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function data_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    return $data;
}

// Función que verifica si el usuario tiene un token válido
function verificarSesion()
{
    // Cargar variables de entorno desde .env
    $jwtSecret = $_ENV['TOKEN'];

    // Verifica si la cookie del token existe y es válida
    if (!isset($_COOKIE['token'])) {
        header('Location: /acces'); // Redirige a login si no existe el token
        exit();
    }

    $token = trim($_COOKIE['token']);

    try {
        // Decodificar el token JWT
        $decoded = JWT::decode($token, new Key($jwtSecret, 'HS256'));

        // Obtener user_id y user_type del payload
        $userType = $decoded->user_type ?? null;

        // Verificar si user_type es 1 (admin) o 2 (usuario regular) o 3/4 (usuari col·laborador) o 5 (usuari logged)
        if (!in_array($userType, [1, 2, 3, 4, 5])) {
            header('Location: /acces'); // Redirige si el user_type no es válido (no es admin ni usuario regular)
            exit();
        }
    } catch (Exception $e) {
        // Si el token es inválido, ha expirado o no es manipulable
        error_log("Error al verificar sesión: " . $e->getMessage());
        header('Location: /acces'); // Redirige a login si el token no es válido
        exit();
    }
}

function validarTokenJWT()
{

    if (!isset($_COOKIE['token'])) {
        return false; // No hay token, dejar que el flujo continúe (por ejemplo, mostrar login)
    }

    $token = $_COOKIE['token'];
    $jwtSecret = $_ENV['TOKEN'];

    try {
        $decoded = JWT::decode($token, new Key($jwtSecret, 'HS256'));

        $userType = $decoded->user_type ?? null;

        // Asegurarse de que el userType es válido
        if (in_array($userType, [1, 2, 3, 4, 5])) {
            header('Location: /gestio');
            exit;
        }

        return false; // user_type no permitido
    } catch (Exception $e) {
        return false; // Token inválido, expirado, etc.
    }
}

function getAuthenticatedUserId(): ?int
{

    $jwtSecret = $_ENV['TOKEN'];
    $cookieName = 'token';

    if (!isset($_COOKIE[$cookieName])) {
        return null; // No hay cookie
    }

    $token = $_COOKIE[$cookieName];

    try {
        $decoded = JWT::decode($token, new Key($jwtSecret, 'HS256'));

        // Supongamos que el payload tiene: { "sub": 123 }
        return $decoded->user_id ?? null;
    } catch (Exception $e) {
        // Token inválido, expirado, etc.
        return null;
    }
}
