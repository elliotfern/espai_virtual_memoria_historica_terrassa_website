<?php

use App\Config\DatabaseConnection;
use App\Config\Database;
use App\Utils\Response;
use App\Utils\MissatgesAPI;

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

            // Verifica si el usuario tiene permiso
            if (
                isset($decoded->user_type) &&
                in_array($decoded->user_type, [3, 4, 6]) // Grupos 3 y 4
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

    $db = new Database();
    $query = "SELECT u.nom, u.email, i.bio_curta_ca, t.tipus, u.id, i.id AS idBio
                FROM auth_users AS u
                LEFT JOIN auth_users_tipus AS t ON u.user_type = t.id
                LEFT JOIN auth_users_i18n AS i ON u.id = i.id_user
                ORDER BY u.id ASC";

    try {
        $result = $db->getData($query);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;  // o exit; según cómo funcione Response::error
        }

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }

    // GET: consulta llistat de tipus d'usuaris
    // URL: https://memoriaterrassa.cat/api/auth/get/tipusUsuaris
} else if ($slug === "tipusUsuaris") {

    $db = new Database();
    $query = "SELECT u.id, u.tipus
                FROM auth_users_tipus AS u
                ORDER BY u.id";

    try {
        $result = $db->getData($query);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;  // o exit; según cómo funcione Response::error
        }

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }


    // GET: consulta informacio sobre un usuari
    // URL: https://memoriaterrassa.cat/api/auth/get/usuari?id=${id}
} else if ($slug === "usuari") {

    $id = $_GET['id'] ?? null;
    $db = new Database();

    $query = "SELECT u.nom, u.email, u.user_type, u.id, u.avatar
                FROM auth_users AS u
                WHERE u.id = :id";

    try {
        $params = [':id' => $id];
        $result = $db->getData($query, $params, true);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;  // o exit; según cómo funcione Response::error
        }

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }

    // GET: consulta informacio sobre un usuari - biografia idiomes
    // URL: https://memoriaterrassa.cat/api/auth/get/usuariBiografia?id=${id}
} else if ($slug === "usuariBiografia") {

    $id = $_GET['id'] ?? null;
    $db = new Database();

    $query = "SELECT id, id_user, bio_curta_ca, bio_curta_es, bio_curta_en,	bio_curta_it, bio_curta_fr, bio_curta_pt, bio_ca, bio_es, bio_en, bio_fr, bio_it, bio_pt, nom
                FROM auth_users_i18n AS i
                LEFT JOIN auth_users AS u ON i.id_user = u.id
                WHERE i.id_user = :id";

    try {
        $params = [':id' => $id];
        $result = $db->getData($query, $params, true);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;  // o exit; según cómo funcione Response::error
        }

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }
} else {
    echo json_encode(['error' => 'No hi ha cap consulta disponible']);
}
