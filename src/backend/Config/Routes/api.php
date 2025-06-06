<?php

define("APP_API", '/api');
define("BACKEND_API", 'src/backend/api');
define("BACKEND_URL", 'public/intranet');
define("DOMAIN", 'https://dev.memoriaterrassa.cat');

// definicio de url
$url = [
    'homepage' => '/homepage',
    'auth' => '/auth',
    'auxiliars' => '/auxiliars',
];

$urlApi = [
    'fonts' => '/fonts',
    'biografies' => '/biografies'

];
// Configuración por defecto para rutas que requieren sesión, sin header_footer, con header_menu_footer
$defaultApiConfig = [
    'needs_session' => false,
    'header_footer' => false,
    'header_menu_footer' => false,
    'apiSenseHTML' => true
];

// Rutas principales sin idioma explícito (solo para el idioma por defecto)
$routes = [

    // 01. Auth
    APP_API . $url['auth'] . '/get/{slug}' => array_merge($defaultApiConfig, [
        'view' => BACKEND_API . $url['auth'] . '/get-auth.php',
    ]),

    APP_API . $url['auth'] . '/post/{slug}' => array_merge($defaultApiConfig, [
        'view' => BACKEND_API . $url['auth'] . '/post-auth.php',
    ]),

    APP_API . $url['auth'] . '/put/{slug}' => array_merge($defaultApiConfig, [
        'view' => BACKEND_API . $url['auth'] . '/put-auth.php',
    ]),

    // 02. Taules auxiliars
    APP_API . $url['auxiliars'] . '/get/{slug}' => array_merge($defaultApiConfig, [
        'view' => BACKEND_API . $url['auxiliars'] . '/get-auxiliars.php',
    ]),

    APP_API . $url['auxiliars'] . '/put/{slug}' => array_merge($defaultApiConfig, [
        'view' => BACKEND_API . $url['auxiliars'] . '/put-auxiliars.php',
    ]),

    APP_API . $url['auxiliars'] . '/post/{slug}' => array_merge($defaultApiConfig, [
        'view' => BACKEND_API . $url['auxiliars'] . '/post-auxiliars.php',
    ]),

    APP_API . $url['auxiliars'] . '/delete/{slug}/{id}' => array_merge($defaultApiConfig, [
        'view' => BACKEND_API . $url['auxiliars'] . '/delete-auxiliars.php',
    ]),

    // API INTRANET OPERACIONS CRUD
    // API db_cost_huma_morts_front
    '/api/cost_huma_front/get/{slug}' => ['view' => 'src/backend/api/db_cost_huma_front/get-cost-huma-front.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/cost_huma_front/put' => ['view' => 'src/backend/api/db_cost_huma_front/put-cost-huma-front.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/cost_huma_front/post' => ['view' => 'src/backend/api/db_cost_huma_front/post-cost-huma-front.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    // API db_cost_huma_morts_civils
    '/api/cost_huma_civils/get/{slug}' => ['view' => 'src/backend/api/db_cost_huma_civils/get-cost-huma-civils.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/cost_huma_civils/put' => ['view' => 'src/backend/api/db_cost_huma_civils/put-cost-huma-civils.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/cost_huma_civils/post' => ['view' => 'src/backend/api/db_cost_huma_civils/post-cost-huma-civils.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    // API db_dades_personals
    '/api/dades_personals/get' => ['view' => 'src/backend/api/db_dades_personals/get-dades-personals.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/dades_personals/put' => ['view' => 'src/backend/api/db_dades_personals/put-dades-personals.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/dades_personals/post' => ['view' => 'src/backend/api/db_dades_personals/post-dades-personals.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    // API db_exiliats
    '/api/exiliats/get/{slug}' => ['view' => 'src/backend/api/db_exiliats/get-exiliats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/exiliats/put' => ['view' => 'src/backend/api/db_exiliats/put-exiliats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/exiliats/post' => ['view' => 'src/backend/api/db_exiliats/post-exiliats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    // API db_deportats
    '/api/deportats/get/{slug}' => ['view' => 'src/backend/api/db_deportats/get-deportats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/deportats/put' => ['view' => 'src/backend/api/db_deportats/put-deportats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/deportats/post' => ['view' => 'src/backend/api/db_deportats/post-deportats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    // API db_familiars
    '/api/familiars/get/{slug}' => ['view' => 'src/backend/api/db_familiars/get-familiars.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/familiars/put' => ['view' => 'src/backend/api/db_familiars/put-familiars.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/familiars/post' => ['view' => 'src/backend/api/db_familiars/post-familiars.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/familiars/delete/{id}' => ['view' => 'src/backend/api/db_familiars/delete-familiars.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    // API db_afusellats
    '/api/afusellats/get' => ['view' => 'src/backend/api/afusellats/get-afusellats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    // API db_processats
    '/api/processats/get' => ['view' => 'src/backend/api/db_processats/get-processats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/processats/put' => ['view' => 'src/backend/api/db_processats/put-processats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/processats/post' => ['view' => 'src/backend/api/db_processats/post-processats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    // API db_depurats
    '/api/depurats/get' => ['view' => 'src/backend/api/db_depurats/get-depurats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/depurats/put' => ['view' => 'src/backend/api/db_depurats/put-depurats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/depurats/post' => ['view' => 'src/backend/api/db_depurats/post-depurats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],


    // API fonts documentals
    APP_API . $urlApi['fonts'] . '/get/{slug}' => array_merge($defaultApiConfig, [
        'view' => BACKEND_API . '/db_fonts_documentals/get-fonts-bibliografia.php',
    ]),

    '/api/fonts_documentals/post/{slug}' => ['view' => 'src/backend/api/db_fonts_documentals/post-fonts-documentals.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/fonts_documentals/put/{slug}' => ['view' => 'src/backend/api/db_fonts_documentals/put-fonts-documentals.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/fonts_documentals/delete/{slug}/{id}' => ['view' => 'src/backend/api/db_fonts_documentals/delete-fonts-documentals.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    // API db_cronologia
    '/api/cronologia/post' => ['view' => 'src/backend/api/cronologia/post-cronologia.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/cronologia/put' => ['view' => 'src/backend/api/cronologia/put-cronologia.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/cronologia/get' => ['view' => 'src/backend/api/cronologia/get-cronologia.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/cronologia/get/esdeveniment' => ['view' => 'src/backend/api/cronologia/get-cronologia-esd.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    // API db_biografies
    '/api/biografia/post' => ['view' => 'src/backend/api/db_biografies/post-biografia.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/biografia/put' => ['view' => 'src/backend/api/db_biografies/put-biografia.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    // API fonts documentals
    APP_API . $urlApi['biografies'] . '/get/{slug}' => array_merge($defaultApiConfig, [
        'view' => BACKEND_API . '/db_biografies/get-biografia.php',
    ]),

    // DB Formulari de contacte
    '/api/form_contacte/get' => ['view' => 'src/backend/api/db_form_contacte/get-form-contacte.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/form_contacte/post' => ['view' => 'src/backend/api/db_form_contacte/post-form-contacte.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

];

return $routes;
