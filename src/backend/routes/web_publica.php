<?php

// Define las rutas base que quieres traducir
$base_routes = [
    // HOMEPAGE
    '/' => 'public/web-publica/index.php',
    '/benvinguda' => 'public/web-publica/index.php',

];

// Rutas principales sin idioma explícito (solo para el idioma por defecto)
$routes = [
    // ACCES SECCIO GESTIO
    '/' => ['view' => 'public/web-publica/index.php', 'needs_session' => false, 'no_header_footer' => false],

    // HOMEPAGE GESTIO
    '/benvinguda' => ['view' => 'public/web-publica/index.php', 'needs_session' => false, 'no_header_footer' => false],

];

// Unir rutas base con rutas específicas de idioma
$routes = $routes + generateLanguageRoutes($base_routes, true);

return $routes;
