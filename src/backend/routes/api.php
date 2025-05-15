<?php
$base_routes = [
    // API INTRANET

    // 01. Auth
    // URL: https://memoriaterrassa.cat/api/auth/
    APP_API . $url['auth'] . '/login' => BACKEND_API . $url['auth'] . '/login.php',
    APP_API . $url['auth'] . '/logout' => BACKEND_API . $url['auth'] . '/logout.php',
    APP_API . $url['auth'] . '/isAdmin' => BACKEND_API . $url['auth'] . '/isAdmin.php',
    APP_API . $url['auth'] . '/isAutor' => BACKEND_API . $url['auth'] . '/isAutor.php',
    APP_API . $url['auth'] . '/usuari' => BACKEND_API . $url['auth'] . '/nomUsuari.php',

    // 02. Taules auxiliars
    // URL: https://memoriaterrassa.cat/api/auxiliars/
    APP_API . $url['auxiliars'] . '/get/usuaris' => BACKEND_API . $url['auxiliars'] . '/get/get-usuaris.php',
    APP_API . $url['auxiliars'] . '/get/usuari/{id}' => BACKEND_API . $url['auxiliars'] . '/get/get-usuari.php',
    APP_API . $url['auxiliars'] . '/post/usuari' => BACKEND_API . $url['auxiliars'] . '/post/post-usuari.php',
    APP_API . $url['auxiliars'] . '/put/usuari' => BACKEND_API . $url['auxiliars'] . '/put/put-usuari.php',
    APP_API . $url['auxiliars'] . '/get/tipusUsuaris' => BACKEND_API . $url['auxiliars'] . '/get/get-tipus-usuaris.php',

    APP_API . $url['auxiliars'] . '/get/municipis' => BACKEND_API . $url['auxiliars'] . '/get/get-municipis.php',
    APP_API . $url['auxiliars'] . '/get/partits' => BACKEND_API . $url['auxiliars'] . '/get/get-partits.php',
    APP_API . $url['auxiliars'] . '/get/sindicats' => BACKEND_API . $url['auxiliars'] . '/get/get-sindicats.php',

    '/api/auxiliars/get' => 'src/backend/api/auxiliars/get-auxiliars.php',
    '/api/auxiliars/post' => 'src/backend/api/auxiliars/post-auxiliars.php',
    '/api/auxiliars/put' => 'src/backend/api/auxiliars/put-auxiliars.php',

    // API INTRANET OPERACIONS CRUD
    // API db_dades_personals
    '/api/dades_personals/get' => 'src/backend/api/db_dades_personals/get-dades-personals.php',
    '/api/dades_personals/put' => 'src/backend/api/db_dades_personals/put-dades-personals.php',
    '/api/dades_personals/post' => 'src/backend/api/db_dades_personals/post-dades-personals.php',

    // API db_cost_huma_morts_front
    '/api/cost_huma_front/get' => 'src/backend/api/db_cost_huma_front/get-cost-huma-front.php',
    '/api/cost_huma_front/put' => 'src/backend/api/db_cost_huma_front/put-cost-huma-front.php',
    '/api/cost_huma_front/post' => 'src/backend/api/db_cost_huma_front/post-cost-huma-front.php',

    // API db_cost_huma_morts_civils
    '/api/cost_huma_civils/get' => 'src/backend/api/db_cost_huma_civils/get-cost-huma-civils.php',
    '/api/cost_huma_civils/put' => 'src/backend/api/db_cost_huma_civils/put-cost-huma-civils.php',
    '/api/cost_huma_civils/post' => 'src/backend/api/db_cost_huma_civils/post-cost-huma-civils.php',

    // API db_represalia_republicana
    '/api/represalia_republicana/get' => 'src/backend/api/db_represalia_republicana/get-represalia-republicana.php',
    '/api/represalia_republicana/put' => 'src/backend/api/db_represalia_republicana/put-represalia-republicana.php',
    '/api/represalia_republicana/post' => 'src/backend/api/db_represalia_republicana/post-represalia-republicana.php',

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

    // API db_processats
    '/api/processats/get' => 'src/backend/api/db_processats/get-processats.php',
    '/api/processats/put' => 'src/backend/api/db_processats/put-processats.php',
    '/api/processats/post' => 'src/backend/api/db_processats/post-processats.php',

    // API db_depurats
    '/api/depurats/get' => 'src/backend/api/db_depurats/get-depurats.php',
    '/api/depurats/put' => 'src/backend/api/db_depurats/put-depurats.php',
    '/api/depurats/post' => 'src/backend/api/db_depurats/post-depurats.php',



    // API db_fonts documentals (aux_bibliografia_llibre_detalls)
    '/api/fonts_documentals/post' => 'src/backend/api/db_fonts_documentals/post-fonts-documentals.php',
    '/api/fonts_documentals/put' => 'src/backend/api/db_fonts_documentals/put-fonts-documentals.php',

    '/api/fonts_documentals/post/arxiu' => 'src/backend/api/db_fonts_documentals/post-arxiu-fonts-documentals.php',
    '/api/fonts_documentals/post/llibre' => 'src/backend/api/db_fonts_documentals/post-llibre-fonts-documentals.php',

    // API db_cronologia
    '/api/cronologia/post' => 'src/backend/api/cronologia/post-cronologia.php',
    '/api/cronologia/put' => 'src/backend/api/cronologia/put-cronologia.php',
    '/api/cronologia/get' => 'src/backend/api/cronologia/get-cronologia.php',
    '/api/cronologia/get/esdeveniment' => 'src/backend/api/cronologia/get-esd.php',

    // API db_biografies
    '/api/biografia/post' => 'src/backend/api/db_biografies/post-biografia.php',
    '/api/biografia/put' => 'src/backend/api/db_biografies/put-biografia.php',
    '/api/biografia/get' => 'src/backend/api/db_biografies/put-biografia.php',
];

// Rutas principales sin idioma explícito (solo para el idioma por defecto)
$routes = [
    // API INTRANET

    // 01. Auth
    APP_API . $url['auth'] . '/isAdmin' => [
        'view' => BACKEND_API . $url['auth'] . '/isAdmin.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => false,
        'apiSenseHTML' => true
    ],

    APP_API . $url['auth'] . '/isAutor' => [
        'view' => BACKEND_API . $url['auth'] . '/isAutor.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => false,
        'apiSenseHTML' => true
    ],

    APP_API . $url['auth'] . '/logout' => [
        'view' => BACKEND_API . $url['auth'] . '/logout.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => false,
        'apiSenseHTML' => true
    ],

    APP_API . $url['auth'] . '/login' => [
        'view' => BACKEND_API . $url['auth'] . '/login.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => false,
        'apiSenseHTML' => true
    ],

    APP_API . $url['auth'] . '/usuari' => [
        'view' => BACKEND_API . $url['auth'] . '/nomUsuari.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => false,
        'apiSenseHTML' => true
    ],

    // 02. Taules auxiliars
    APP_API . $url['auxiliars'] . '/get/usuaris' => [
        'view' => BACKEND_API . $url['auxiliars'] . '/get/get-usuaris.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => false,
        'apiSenseHTML' => true
    ],

    APP_API . $url['auxiliars'] . '/get/usuari/{id}' => [
        'view' => BACKEND_API . $url['auxiliars'] . '/get/get-usuari.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => false,
        'apiSenseHTML' => true
    ],

    APP_API . $url['auxiliars'] . '/put/usuari' => [
        'view' => BACKEND_API . $url['auxiliars'] . '/put/put-usuari.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => false,
        'apiSenseHTML' => true
    ],

    APP_API . $url['auxiliars'] . '/post/usuari' => [
        'view' => BACKEND_API . $url['auxiliars'] . '/post/post-usuari.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => false,
        'apiSenseHTML' => true
    ],

    APP_API . $url['auxiliars'] . '/get/tipusUsuaris' => [
        'view' => BACKEND_API . $url['auxiliars'] . '/get/get-tipus-usuaris.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => false,
        'apiSenseHTML' => true
    ],

    APP_API . $url['auxiliars'] . '/get/municipis' => [
        'view' => BACKEND_API . $url['auxiliars'] . '/get/get-municipis.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => false,
        'apiSenseHTML' => true
    ],

    APP_API . $url['auxiliars'] . '/get/partits' => [
        'view' => BACKEND_API . $url['auxiliars'] . '/get/get-partits.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => false,
        'apiSenseHTML' => true
    ],

    APP_API . $url['auxiliars'] . '/get/sindicats' => [
        'view' => BACKEND_API . $url['auxiliars'] . '/get/get-sindicats.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => false,
        'apiSenseHTML' => true
    ],

    '/api/auxiliars/get' => ['view' => 'src/backend/api/auxiliars/get-auxiliars.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/auxiliars/post' => ['view' => 'src/backend/api/auxiliars/post-auxiliars.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/auxiliars/put' => ['view' => 'src/backend/api/auxiliars/put-auxiliars.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

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

    // API db_represalia_republicana
    '/api/represalia_republicana/get' => ['view' => 'src/backend/api/db_represalia_republicana/get-represalia-republicana.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/represalia_republicana/put' => ['view' => 'src/backend/api/db_represalia_republicana/put-represalia-republicana.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/represalia_republicana/post' => ['view' => 'src/backend/api/db_represalia_republicana/post-represalia-republicana.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

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
    '/api/fonts_documentals/post' => ['view' => 'src/backend/api/db_fonts_documentals/post-fonts-documentals.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/fonts_documentals/post/arxiu' => ['view' => 'src/backend/api/db_fonts_documentals/post-arxiu-fonts-documentals.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/fonts_documentals/post/llibre' => ['view' => 'src/backend/api/db_fonts_documentals/post-llibre-fonts-documentals.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/fonts_documentals/put' => ['view' => 'src/backend/api/db_fonts_documentals/put-fonts-documentals.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    // API db_cronologia
    '/api/cronologia/post' => ['view' => 'src/backend/api/cronologia/post-cronologia.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/cronologia/put' => ['view' => 'src/backend/api/cronologia/put-cronologia.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/cronologia/get' => ['view' => 'src/backend/api/cronologia/get-cronologia.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/cronologia/get/esdeveniment' => ['view' => 'src/backend/api/cronologia/get-cronologia-esd.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    // API db_biografies
    '/api/biografia/post' => ['view' => 'src/backend/api/db_biografies/post-biografia.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/biografia/put' => ['view' => 'src/backend/api/db_biografies/put-biografia.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/biografia/get' => ['view' => 'src/backend/api/db_biografies/put-biografia.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

];

// Unir rutas base con rutas específicas de idioma
$routes = $routes + generateLanguageRoutes($base_routes, false);

return $routes;
