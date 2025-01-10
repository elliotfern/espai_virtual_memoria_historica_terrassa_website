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

    // API db_cost_huma_morts_civils
    '/api/cost_huma_civils/get' => 'src/backend/api/db_cost_huma_civils/get-cost-huma-civils.php',
    '/api/cost_huma_civils/put' => 'src/backend/api/db_cost_huma_civils/put-cost-huma-civils.php',
    '/api/cost_huma_civils/post' => 'src/backend/api/db_cost_huma_civils/post-cost-huma-civils.php',

    // API db_dades_personals
    '/api/dades_personals/get' => 'src/backend/api/db_dades_personals/get-dades-personals.php',
    '/api/dades_personals/put' => 'src/backend/api/db_dades_personals/put-dades-personals.php',
    '/api/dades_personals/post' => 'src/backend/api/db_dades_personals/post-dades-personals.php',

    // API db_exiliats
    '/api/exiliats/get' => 'src/backend/api/db_exiliats/get-exiliats.php',
    '/api/exiliats/put' => 'src/backend/api/db_exiliats/put-exiliats.php',
    '/api/exiliats/post' => 'src/backend/api/db_exiliats/post-exiliats.php',

    // API db_deportats
    '/api/deportats/get' => 'src/backend/api/db_deportats/get-deportats.php',
    '/api/deportats/put' => 'src/backend/api/db_deportats/put-deportats.php',
    '/api/exiliats/post' => 'src/backend/api/db_deportats/post-deportats.php',

    // API db_familiars
    '/api/familiars/get' => 'src/backend/api/db_familiars/get-familiars.php',
    '/api/familiars/put' => 'src/backend/api/db_familiars/put-familiars.php',
    '/api/familiars/post' => 'src/backend/api/db_familiars/post-familiars.php',

    // API db_afusellats
    '/api/afusellats/get' => 'src/backend/api/afusellats/get-afusellats.php',

    // API taules auxiliars
    '/api/auxiliars/get' => 'src/backend/api/auxiliars/get-auxiliars.php',
    '/api/auxiliars/post' => 'src/backend/api/auxiliars/post-auxiliars.php',
    '/api/auxiliars/put' => 'src/backend/api/auxiliars/put-auxiliars.php',
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

    // API db_cost_huma_morts_civils
    '/api/cost_huma_civils/get' => ['view' => 'src/backend/api/db_cost_huma_civils/get-cost-huma-civils.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/cost_huma_civils/put' => ['view' => 'src/backend/api/db_cost_huma_civils/put-cost-huma-civils.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/cost_huma_civils/post' => ['view' => 'src/backend/api/db_cost_huma_civils/post-cost-huma-civils.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    // API db_dades_personals
    '/api/dades_personals/get' => ['view' => 'src/backend/api/db_dades_personals/get-dades-personals.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/dades_personals/put' => ['view' => 'src/backend/api/db_dades_personals/put-dades-personals.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/dades_personals/post' => ['view' => 'src/backend/api/db_dades_personals/post-dades-personals.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    // API db_exiliats
    '/api/exiliats/get' => ['view' => 'src/backend/api/db_exiliats/get-exiliats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/exiliats/put' => ['view' => 'src/backend/api/db_exiliats/put-exiliats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/exiliats/post' => ['view' => 'src/backend/api/db_exiliats/post-exiliats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    // API db_deportats
    '/api/deportats/get' => ['view' => 'src/backend/api/db_deportats/get-deportats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/deportats/put' => ['view' => 'src/backend/api/db_deportats/put-deportats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/deportats/post' => ['view' => 'src/backend/api/db_deportats/post-deportats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    // API db_familiars
    '/api/familiars/get' => ['view' => 'src/backend/api/db_familiars/get-familiars.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/familiars/put' => ['view' => 'src/backend/api/db_familiars/put-familiars.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/familiars/post' => ['view' => 'src/backend/api/db_familiars/post-familiars.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    // API WEB PUBLICA

    '/api/afusellats/get' => ['view' => 'src/backend/api/afusellats/get-afusellats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/auxiliars/get' => ['view' => 'src/backend/api/auxiliars/get-auxiliars.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/auxiliars/post' => ['view' => 'src/backend/api/auxiliars/post-auxiliars.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/auxiliars/put' => ['view' => 'src/backend/api/auxiliars/put-auxiliars.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

];

// Unir rutas base con rutas específicas de idioma
$routes = $routes + generateLanguageRoutes($base_routes, false);

return $routes;
