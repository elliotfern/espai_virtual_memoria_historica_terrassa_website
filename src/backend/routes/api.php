<?php

// Define las rutas base que quieres traducir
$base_routes = [
    // API INTRANET
    '/api/auth/get' => 'src/backend/api/auth/auth.php',
    '/api/auth/login' => 'src/backend/api/auth/login-process.php',

    // API WEB PUBLICA
    '/api/represaliats/get' => 'src/backend/api/represaliats/get-represaliats.php',
    '/api/afusellats/get' => 'src/backend/api/afusellats/get-afusellats.php',
    '/api/auxiliars/get' => 'src/backend/api/auxiliars/get-aux.php',
    '/api/exiliats/get' => 'src/backend/api/exiliats/get-exiliats.php',

];

// Rutas principales sin idioma explícito (solo para el idioma por defecto)
$routes = [
    // API INTRANET
    '/api/auth/get' => ['view' => 'src/backend/api/auth/auth.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false, 'apiSenseHTML' => true],

    '/api/auth/login' => ['view' => 'src/backend/api/auth/login-process.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    // API WEB PUBLICA
    '/api/represaliats/get' => ['view' => 'src/backend/api/represaliats/get-represaliats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/afusellats/get' => ['view' => 'src/backend/api/afusellats/get-afusellats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/auxiliars/get' => ['view' => 'src/backend/api/auxiliars/get-aux.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/exiliats/get' => ['view' => 'src/backend/api/exiliats/get-exiliats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],
];

// Unir rutas base con rutas específicas de idioma
$routes = $routes + generateLanguageRoutes($base_routes, false);

return $routes;
