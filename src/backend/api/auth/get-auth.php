<?php

use App\Config\DatabaseConnection;

$conn = DatabaseConnection::getConnection();

if (!$conn) {
    die("No se pudo establecer conexión a la base de datos.");
}

// Configuración de cabeceras para aceptar JSON y responder JSON
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET");

$slug = $routeParams[0];

// Obtener el parámetro id de la URL
$id = isset($_GET['id']) ? $_GET['id'] : null;

if ($id && !filter_var($id, FILTER_VALIDATE_INT)) {
    echo json_encode(['status' => 'error', 'message' => 'ID invàlid, no és un número.']);
    exit();
} else {
    // Si es un número válido, lo convertimos a entero
    $id = (int)$id;
}

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Cargar variables de entorno desde .env
$jwtSecret = $_ENV['TOKEN'];



// Definir el dominio permitido
$allowedOrigin = DOMAIN;

// Llamar a la función para verificar el referer
checkReferer($allowedOrigin);

// Verificar que el método de la solicitud sea GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// GET : Consulta si l'usuari és administrador
// URL: https://memoriaterrassa.cat/api/auth/get/isAdmin
if ($slug === "isAdmin") {
    // Comprobar si la cookie 'token' está presente
    if (!isset($_COOKIE['token'])) {
        echo json_encode(['isAdmin' => false]);
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
                in_array($decoded->user_type, [1])
            ) {
                echo json_encode(['isAdmin' => true]);
                exit;
            }
        } catch (Exception $e) {
            // Token inválido, expirado o manipulado
            error_log("JWT inválido: " . $e->getMessage());
        }
    }

    // Si no cumple, no es admin
    echo json_encode(['isAdmin' => false]);

    // GET : Consulta si l'usuari és autor (grup 2)
    // URL: https://memoriaterrassa.cat/api/auth/get/isAutor
} else if ($slug === "isAutor") {

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


    // GET : Consulta si l'usuari és Logged (grup 3 o 4)
    // URL: https://memoriaterrassa.cat/api/auth/get/isLogged
} else if ($slug === "isLogged") {

    if (!isset($_COOKIE['token'])) {
        echo json_encode(['isLogged' => false]);
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
                in_array($decoded->user_type, [3, 4]) // Grupos 3 y 4
            ) {
                echo json_encode(['isLogged' => true]);
                exit;
            }
        } catch (Exception $e) {
            // Token inválido, expirado o manipulado
            error_log("JWT inválido: " . $e->getMessage());
        }
    }

    // Si no cumple, no es logged
    echo json_encode(['isLogged' => false]);


    // GET: tancar la sessió d'usuari
    // URL: https://memoriaterrassa.cat/api/auth/get/logOut
} else if ($slug === "logOut") {

    $arr_cookie_options = array(
        'expires' => time() - 3600,
        'path' => '/',
        'domain' => 'memoriaterrassa.cat',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict'
    );

    //Elimina les cookies
    setcookie('token', '', $arr_cookie_options);

    // Respuesta en formato JSON o redirige
    echo json_encode(['message' => 'OK']);

    exit;

    // GET: consulta a la cookie token el nom d'usuari
    // URL: https://memoriaterrassa.cat/api/auth/get/nomUsuari
} else if ($slug === "nomUsuari") {
    $token = $_COOKIE['token'] ?? null;

    if (!$token) {
        http_response_code(401);
        echo json_encode(['error' => 'No token']);
        exit;
    }

    try {
        $decoded = JWT::decode($token, new Key($jwtSecret, 'HS256'));
        echo json_encode([
            'username' => $decoded->username,
            'avatar' => $decoded->avatar
        ]);
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(['error' => 'Token inválido']);
    }

    // GET: consulta llistat d'usuaris
    // URL: https://memoriaterrassa.cat/api/auth/get/llistatUsuaris
} else if ($slug === "llistatUsuaris") {

    $query = "SELECT u.nom, u.email, u.biografia_cat, t.tipus, u.id
                FROM auth_users AS u
                LEFT JOIN auth_users_tipus AS t ON u.user_type = t.id
                ORDER BY u.id ASC";

    $result = getData($query);
    echo json_encode($result);

    // GET: consulta llistat de tipus d'usuaris
    // URL: https://memoriaterrassa.cat/api/auth/get/tipusUsuaris
} else if ($slug === "tipusUsuaris") {

    $query = "SELECT u.id, u.tipus
                FROM auth_users_tipus AS u
                ORDER BY u.id";

    $result = getData($query);
    echo json_encode($result);

    // GET: consulta informacio sobre un usuari
    // URL: https://memoriaterrassa.cat/api/auth/get/usuari
} else if ($slug === "usuari") {

    $query = "SELECT u.nom, u.email, u.biografia_cat, u.user_type, u.id, u.avatar
                FROM auth_users AS u
                WHERE u.id = :id";

    $result = getData($query, ['id' => $id], true);
    echo json_encode($result);
} else {
    echo json_encode(['error' => 'No hi ha cap consulta disponible']);
}
