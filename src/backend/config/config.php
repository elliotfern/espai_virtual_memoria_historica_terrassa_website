<?php
// Cargar librerías externas
require_once __DIR__ . '/../../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../../');
$dotenv->load();
require_once __DIR__ . '/../config/connection.php';

$isAdmin = isUserAdmin();

// Definir constantes de configuración
define('BASE_URL', $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']);
define('APP_ROOT', $_SERVER['DOCUMENT_ROOT']);

define('DOMAIN', "https://memoriaterrassa.cat");

$base_url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];
define("APP_WEB", $base_url);
define("APP_SERVER", $base_url);
define("APP_INTRANET", '/gestio');

// Definicio de constants
define("APP_API", '/api');
define("BACKEND_API", 'src/backend/api');
define("BACKEND_URL", 'public/intranet');

// definicio de url
$url = [
    'auth' => '/auth',
    'auxiliars' => '/auxiliars',
];
