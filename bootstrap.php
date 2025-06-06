<?php
// bootstrap.php

use App\Application\FrontController;
use App\Application\Security\CheckSessionUseCase;
use App\Infrastructure\Security\JWTSessionVerifier;
use App\Infrastructure\Middleware\AuthMiddleware;
use App\Application\Router;

// Cargar librerías externas
require_once __DIR__ . '/vendor/autoload.php';

$envName = $_ENV['APP_ENV'] ?? 'prod'; // default: prod
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/', '.env.' . $envName);
$dotenv->load();

// Detectar idioma y URI
$requestUri = $_GET['uri'] ?? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$lang = $_GET['lang'] ?? null;

// Carga rutas
$apiRoutes = require __DIR__ . '/src/backend/Config/Routes/api.php';
$privateRoutes = require __DIR__ . '/src/backend/Config/Routes/private.php';
$publicRoutes = require __DIR__ . '/src/backend/Config/Routes/public.php';

// Crea routers
$apiRouter = new Router($apiRoutes);
$privateRouter = new Router($privateRoutes);
$publicRouter = new Router($publicRoutes);

// Crea middleware de sesión
$sessionVerifier = new JWTSessionVerifier();
$checkSessionUseCase = new CheckSessionUseCase($sessionVerifier);
$authMiddleware = new AuthMiddleware($checkSessionUseCase);

// Inyecta todo al FrontController
$frontController = new FrontController(
    $requestUri,
    $lang,
    $apiRouter,
    $privateRouter,
    $publicRouter,
    $authMiddleware
);

// Lanza la aplicación
$frontController->handleRequest();


// Incluir configuraciones y utilidades
//require_once __DIR__ . '/src/backend/Config/config.php';
//require_once __DIR__ . '/src/backend/Config/funcions.php';

//require_once __DIR__ . '/src/backend/Utils/verificacioSessio.php';
//require_once __DIR__ . '/src/backend/Utils/convertirDates.php';
//require_once __DIR__ . '/src/backend/Utils/sanitizerHtml.php';
