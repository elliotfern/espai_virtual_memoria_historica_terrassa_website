<?php
// Cargar librerías externas
require_once __DIR__ . '/../../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../../');
$dotenv->load();
require_once __DIR__ . '/../config/connection.php';

$isAdmin = isUserAdmin();
$isAutor = isUserAutor();
$isLogged = isUserLogged();
$isUserExili = isUserCategoria(3);
$isUserCostHuma = isUserCategoria(4);

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
define("FRONTEND_URL", 'public/web-publica/');

// definicio de url
$url = [
    'homepage' => '/homepage',
    'auth' => '/auth',
    'auxiliars' => '/auxiliars',
];

$urlIntranetDir = [
    'homepage' => '/00_homepage',
    'base_dades' => '/01_base_dades',
    'auxiliars' => '/02_taules_auxiliars',
    'fonts' => '/03_fonts_documentals',
];

$urlIntranet = [
    'homepage' => '/admin',
    'base_dades' => '/base-dades',
    'auxiliars' => '/auxiliars',
    'fonts' => '/fonts-documentals',
    'cronologia' => '/cronologia'
];

$urlApi = [
    'fonts' => '/fonts',

];
