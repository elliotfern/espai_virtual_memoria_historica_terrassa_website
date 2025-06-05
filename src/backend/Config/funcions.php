<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Función para validar si una cookie está definida y no vacía
function getSanitizedCookie($name)
{
    return isset($_COOKIE[$name]) ? trim(htmlspecialchars($_COOKIE[$name], ENT_QUOTES, 'UTF-8')) : null;
}

function isUserLogged(): bool
{
    // Cargar variables de entorno desde .env
    $jwtSecret = $_ENV['TOKEN'];

    if (!isset($_COOKIE['token'])) {
        return false;
    }

    $token = trim($_COOKIE['token']);

    try {
        $decoded = JWT::decode($token, new Key($jwtSecret, 'HS256'));

        // Comprobamos si el usuario es admin (user_type = 1)
        if (isset($decoded->user_type) && in_array($decoded->user_type, [1, 2, 3, 4])) {
            return true;
        }
    } catch (Exception $e) {
        // Token inválido, expirado o manipulado
        error_log("Error en isUserLogged(): " . $e->getMessage());
    }

    return false;
}


function isUserAdmin(): bool
{
    // Cargar variables de entorno desde .env
    $jwtSecret = $_ENV['TOKEN'];

    if (!isset($_COOKIE['token'])) {
        return false;
    }

    $token = trim($_COOKIE['token']);

    try {
        $decoded = JWT::decode($token, new Key($jwtSecret, 'HS256'));

        // Comprobamos si el usuario es admin (user_type = 1)
        if (isset($decoded->user_type) && in_array($decoded->user_type, [1])) {
            return true;
        }
    } catch (Exception $e) {
        // Token inválido, expirado o manipulado
        error_log("Error en isUserAdmin(): " . $e->getMessage());
    }

    return false;
}

function isUserAutor(): bool
{
    // Cargar variables de entorno desde .env
    $jwtSecret = $_ENV['TOKEN'];

    if (!isset($_COOKIE['token'])) {
        return false;
    }

    $token = trim($_COOKIE['token']);

    try {
        $decoded = JWT::decode($token, new Key($jwtSecret, 'HS256'));

        // Comprobamos si el usuario es autor (user_type = 2)
        if (isset($decoded->user_type) && in_array($decoded->user_type, [2])) {
            return true;
        }
    } catch (Exception $e) {
        // Token inválido, expirado o manipulado
        error_log("Error en isUserAutor(): " . $e->getMessage());
    }

    return false;
}

function isUserCategoria($userType): bool
{
    // Cargar variables de entorno desde .env
    $jwtSecret = $_ENV['TOKEN'];

    if (!isset($_COOKIE['token'])) {
        return false;
    }

    $token = trim($_COOKIE['token']);

    try {
        $decoded = JWT::decode($token, new Key($jwtSecret, 'HS256'));

        // Comprobamos si el usuario es autor (user_type = 2)
        if (isset($decoded->user_type) && in_array($decoded->user_type, [$userType])) {
            return true;
        }
    } catch (Exception $e) {
        // Token inválido, expirado o manipulado
        error_log("Error en isUserAutor(): " . $e->getMessage());
    }

    return false;
}


/**
 * Verifica que la solicitud provenga del dominio permitido.
 *
 * @param string $allowedOrigin El dominio permitido.
 * @return void
 */
function checkReferer($allowedOrigin)
{
    // Verificar que la cabecera 'Referer' esté presente y sea válida
    if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], $allowedOrigin) === 0) {
        // El Referer contiene la URL del dominio permitido
        header("Access-Control-Allow-Origin: " . $allowedOrigin);
    } else {
        // Si la cabecera 'Referer' no es válida, denegar el acceso
        header("HTTP/1.1 403 Forbidden");
        echo json_encode(['error' => 'Accés no permés']);
        exit();
    }
}

function getData2($query, $params = [], $single = false)
{
    global $conn;
    /** @var PDO $conn */

    try {
        // Preparar la consulta
        $stmt = $conn->prepare($query);

        // Si hay parámetros, los vinculamos
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
        }

        // Ejecutar la consulta
        $stmt->execute();

        // Si esperamos un solo resultado, usamos fetch()
        if ($single) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            // Si esperamos varios resultados, usamos fetchAll()
            $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Verificar si hay resultados
        if ($row) {
            return $row;
        } else {
            return ['status' => 'error', 'message' => 'No hi ha cap registre disponible.'];
        }
    } catch (PDOException $e) {
        return ['status' => 'error', 'message' => 'Error a la consulta'];
    }
}
