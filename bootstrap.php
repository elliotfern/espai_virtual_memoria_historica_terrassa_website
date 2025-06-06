<?php
// bootstrap.php

use App\Application\Router;
use App\Application\ViewRenderer;
use App\Application\HttpResponder;
use App\Application\FrontController;
use App\Application\Security\CheckSessionUseCase;
use App\Infrastructure\Security\JWTSessionVerifier;
use App\Infrastructure\Middleware\AuthMiddleware;
use App\Application\Services\TranslationService;
use App\Domain\Common\ValueObject\Language;

// Cargar librerías externas
require_once __DIR__ . '/vendor/autoload.php';
$basePath = __DIR__ . '/';

$envName = $_ENV['APP_ENV'] ?? 'prod'; // default: prod
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/', '.env.' . $envName);
$dotenv->load();

// Detectar idioma y URI
$requestUri = $_GET['uri'] ?? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);


// Detectar idioma desde la URI
$normalizedUri = '/' . trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
if (preg_match('#^/(es|fr|en|ca|it|pt)(/|$)#', $normalizedUri, $matches)) {
    $language = new Language($matches[1]);
} else {
    $language = Language::default();
}

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

// Crea servicios de vista y traducción
// Inicia servicios
$translationService = new TranslationService($language);
$viewRenderer = new ViewRenderer($basePath);

// Crea responder HTTP con todos los servicios necesarios
$responder = new HttpResponder($viewRenderer, $translationService, $authMiddleware);

// Inyecta todo al FrontController (ojo que el orden de parámetros debe coincidir con el constructor)
$frontController = new FrontController(
    $requestUri,
    $apiRouter,           // Router implements RouterInterface
    $privateRouter,
    $publicRouter,
    $viewRenderer,        // ViewRenderer implements ViewRendererInterface
    $responder            // HttpResponder implements HttpResponderInterface
);

// Lanza la aplicación
$frontController->handleRequest();
