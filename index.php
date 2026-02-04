<?php

// Configuración inicial para mostrar errores en desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir configuraciones y rutas
require_once __DIR__ . '/src/backend/Config/funcions.php';
require_once __DIR__ . '/src/backend/Config/config.php';
require_once __DIR__ . '/src/backend/Config/connection.php'; // cal eliminar-ho
require_once __DIR__ . '/src/backend/Utils/verificacioSessio.php';
require_once __DIR__ . '/src/backend/Utils/convertirDates.php';
require_once __DIR__ . '/src/backend/Utils/sanitizerHtml.php';
require_once __DIR__ . '/src/backend/routes/routes.php';

// ---------------------------------------------------------
// 1) URI normalizada
// ---------------------------------------------------------
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$requestUri = rtrim($requestUri, '/');
if ($requestUri === '') $requestUri = '/';

// ---------------------------------------------------------
// 2) Detectar idioma SOLO si es el PRIMER segmento completo
//    (evita que "/espai-virtual" se lea como "/es")
// ---------------------------------------------------------
$language2 = 'ca';
$uriForRouting = $requestUri;

// Idiomas soportados
$langPattern = '#^/(ca|es|fr|en|pt|it)(/|$)#';

if (preg_match($langPattern, $requestUri, $m)) {
    $candidate = $m[1];

    // Si usas "ca" sin prefijo normalmente, puedes permitirlo igual sin romper
    // (pero no lo necesitas). Dejamos ca soportado por si lo pones algún día.
    $language2 = $candidate ?: 'ca';

    // Quitar el prefijo de idioma para hacer match con $routes
    // Ej: "/es/espai-virtual/premsa" => "/espai-virtual/premsa"
    $uriForRouting = preg_replace($langPattern, '/', $requestUri);
    $uriForRouting = rtrim($uriForRouting, '/');
    if ($uriForRouting === '') $uriForRouting = '/';
}

// Cargar traducciones correspondientes
$translations = require __DIR__ . "/src/backend/locales/{$language2}.php";

// ---------------------------------------------------------
// 3) Resolver rutas usando $uriForRouting (sin /es, /fr...)
// ---------------------------------------------------------
$routeParams = [];
$routeFound = false;
$view = 'public/includes/404.php';
$noHeaderFooter = false;
$headerMenu = true;
$apiSenseHTML = false;

foreach ($routes as $route => $routeInfo) {
    $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([a-zA-Z0-9_-]+)', $route);

    if (preg_match('#^' . $pattern . '$#', $uriForRouting, $matches)) {
        $routeFound = true;
        $routeParams = array_slice($matches, 1);
        $view = $routeInfo['view'];

        // flags
        $needsSession = $routeInfo['needs_session'] ?? false;
        if ($needsSession) verificarSesion();

        $userLogged = $routeInfo['userLogged'] ?? false;
        if ($userLogged) validarTokenJWT();

        $noHeaderFooter = $routeInfo['header_footer'] ?? false;
        $headerMenu = $routeInfo['header_menu_footer'] ?? false;
        $apiSenseHTML = $routeInfo['apiSenseHTML'] ?? false;

        break;
    }
}

// ---------------------------------------------------------
// 4) Render final (manteniendo tu lógica)
//    OJO: aquí conviene comprobar contra $requestUri (con idioma),
//    pero con patrón de segmento completo
// ---------------------------------------------------------
if (preg_match('#^/(es|fr|en|pt|it)(/|$)#', $requestUri) && preg_match('#^/(es|fr|en|pt|it)$#', $requestUri)) {
    // Si es exactamente "/es" o "/fr" etc (sin más segmentos)
    include 'public/includes/header.php';
    include 'public/web-publica/index.php';
    include 'public/includes/footer-end.php';
} else {
    if ($noHeaderFooter) {
        include 'public/includes/header.php';
        include $view;
        include 'public/includes/footer-end.php';
    } elseif ($headerMenu) {
        include 'public/includes/header.php';
        include 'public/includes/header-menu.php';
        include $view;
        include 'public/includes/footer.php';
        include 'public/includes/footer-end.php';
    } elseif ($apiSenseHTML) {
        include $view;
    } else {
        // Por seguridad, si alguna ruta no define flags bien
        include 'public/includes/header.php';
        include 'public/includes/header-menu.php';
        include $view;
        include 'public/includes/footer.php';
        include 'public/includes/footer-end.php';
    }
}
