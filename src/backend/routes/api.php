<?php

// Define las rutas base que quieres traducir
$base_routes = [
    // API INTRANET
    '/api/auth/get' => 'src/backend/api/auth/auth.php',
    '/api/auth/login' => 'src/backend/api/auth/login-process.php',

    // API INTRANET OPERACIONS CRUD
    // API db_cost_huma_morts_front
    '/api/cost_huma_front/put' => 'src/backend/api/db_cost_huma_front/put-cost-huma-front.php',
    '/api/cost_huma_front/post' => 'src/backend/api/db_cost_huma_front/post-cost-huma-front.php',

    // API db_dades_personals
    '/api/dades_personals/get' => 'src/backend/api/db_dades_personals/get-dades-personals.php',
    '/api/dades_personals/put' => 'src/backend/api/db_dades_personals/put-dades-personals.php',
    '/api/dades_personals/post' => 'src/backend/api/db_dades_personals/post-dades-personals.php',

    // API WEB PUBLICA
    '/api/afusellats/get' => 'src/backend/api/afusellats/get-afusellats.php',
    '/api/auxiliars/get' => 'src/backend/api/auxiliars/get-aux.php',
    '/api/exiliats/get' => 'src/backend/api/exiliats/get-exiliats.php',

];

// Rutas principales sin idioma explícito (solo para el idioma por defecto)
$routes = [
    // API INTRANET
    '/api/auth/get' => ['view' => 'src/backend/api/auth/auth.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false, 'apiSenseHTML' => true],

    '/api/auth/login' => ['view' => 'src/backend/api/auth/login-process.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    // API INTRANET OPERACIONS CRUD
    // API db_cost_huma_morts_front
    '/api/cost_huma_front/get' => ['view' => 'src/backend/api/db_cost_huma_front/get-cost-huma-front.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/cost_huma_front/put' => ['view' => 'src/backend/api/db_cost_huma_front/put-cost-huma-front.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/cost_huma_front/post' => ['view' => 'src/backend/api/db_cost_huma_front/post-cost-huma-front.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    // API db_dades_personals
    '/api/dades_personals/get' => ['view' => 'src/backend/api/db_dades_personals/get-dades-personals.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/dades_personals/put' => ['view' => 'src/backend/api/db_dades_personals/put-dades-personals.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/dades_personals/post' => ['view' => 'src/backend/api/db_dades_personals/post-dades-personals.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    // API WEB PUBLICA

    '/api/afusellats/get' => ['view' => 'src/backend/api/afusellats/get-afusellats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/auxiliars/get' => ['view' => 'src/backend/api/auxiliars/get-aux.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/exiliats/get' => ['view' => 'src/backend/api/exiliats/get-exiliats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],
];

// Unir rutas base con rutas específicas de idioma
$routes = $routes + generateLanguageRoutes($base_routes, false);

return $routes;
