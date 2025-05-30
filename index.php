<?php

// Configuración inicial para mostrar errores en desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir configuraciones y rutas
require_once __DIR__ . '/src/backend/config/funcions.php';
require_once __DIR__ . '/src/backend/config/config.php';
require_once __DIR__ . '/src/backend/utils/verificacioSessio.php';
require_once __DIR__ . '/src/backend/routes/routes.php';

// Obtener la ruta solicitada
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Normalizar la ruta eliminando barras finales
$requestUri = rtrim($requestUri, '/');

if ($requestUri === '') {
    $requestUri = '/';
}

$lang = ltrim($requestUri, '/');

if ($lang === '') {
    $lang = 'ca';
}

// Obtener la ruta solicitada
$requestUri2 = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Normalizar la ruta eliminando barras finales
$requestUri2 = rtrim($requestUri2, '/');

// Detectar el idioma desde la URL (primer segmento después del dominio)
preg_match('#^/(fr|en|es|pt|it)#', $requestUri, $matches);

// Asignar el idioma detectado o usar un valor predeterminado
$language2 = $matches[1] ?? 'ca';

if ($language2 === '') {
    $language2 = 'ca';
}

// Cargar las traducciones correspondientes al idioma
$translations = require __DIR__ . "/src/backend/locales/{$language2}.php";

// Inicializar una variable para los parámetros de la ruta
$routeParams = [];

// Buscar si la ruta es una ruta dinámica y extraer los parámetros
$routeFound = false;
foreach ($routes as $route => $routeInfo) {
    // Crear un patrón para la ruta dinámica reemplazando los parámetros {param} por expresiones regulares
    $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([a-zA-Z0-9_-]+)', $route);

    if (preg_match('#^' . $pattern . '$#', $requestUri, $matches)) {
        // Si encontramos la ruta, extraemos los parámetros
        $routeFound = true;
        $routeParams = array_slice($matches, 1);  // El primer elemento es la ruta misma, los parámetros son los siguientes

        // Asignamos la vista asociada a la ruta
        $view = $routeInfo['view'];
        break;
    }
}

// Si la ruta no es encontrada, asignamos la página 404
if (!$routeFound) {
    $view = 'public/includes/404.php';
    $noHeaderFooter = false;
    $headerMenu = true;
    $apiSenseHTML = false;
} else {
    // Verificar si la ruta requiere sesión
    $needsSession = $routeInfo['needs_session'] ?? false;
    if ($needsSession) {
        verificarSesion(); // Llamada a la función de verificación de sesión
    }

    // Verificar si la ruta ha de tenir redirecció per usuari ja registrat
    $userLogged = $routeInfo['userLogged'] ?? false;
    if ($userLogged) {
        validarTokenJWT(); // Llamada a la función de verificación de sesión
    }

    // Determinar si la vista necesita encabezado y pie de página
    $noHeaderFooter = $routeInfo['header_footer'] ?? false;

    // Determinar si la vista el menu del header
    $headerMenu = $routeInfo['header_menu_footer'] ?? false;

    $apiSenseHTML = $routeInfo['apiSenseHTML'] ?? false;
}

// **No hacer redirección**, solo no incluir header y footer si estamos en /es
if (preg_match('#^/(es|fr|en|pt|it)$#', $requestUri)) {
    // Si estamos en /es, no hacemos nada más
    include 'public/includes/header.php';
    include 'public/web-publica/index.php';
    include 'public/includes/footer-end.php';
} else {
    // Incluir encabezado y pie de página si no se especifica que no lo tenga
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
        // Solo incluir la vista asociada a la ruta
        include $view;
    }
}
