<?php


// Configuración por defecto para rutas que requieren sesión, sin header_footer, con header_menu_footer

$defaultRoutesSenseHeaderConfig = [
    'needs_session' => false,
    'header_footer' => true,
    'header_menu_footer' => false,
    'apiSenseHTML' => false
];

$defaultRoutesConfig = [
    'needs_session' => false,
    'header_footer' => false,
    'header_menu_footer' => true,
    'apiSenseHTML' => false
];

$defaultRoutesUserLoggedConfig = [
    'needs_session' => false,
    'userLogged' => true,
    'header_footer' => false,
    'header_menu_footer' => true,
    'apiSenseHTML' => false
];

// Define las rutas base que quieres traducir
$base_routes = [
    // 00. Homepage
    '/' => 'public/web-publica/index.php',
    '/benvinguda' => 'public/web-publica/index.php',
    '/inici' => 'public/web-publica/inici.php',

    // Accés intranet i recuperació contrasenya
    '/acces' => 'public/web-publica/auth/login.php',

    'recuperacio-contrasenya' => 'public/web-publica/auth/recuperacio-contrasenya.php',

    // 1. Base de dades
    '/base-dades/general' => 'public/web-publica/base-dades/base-dades-global.php',
    '/base-dades/cost-huma' => 'public/web-publica/base-dades/base-dades-cost-huma.php',
    '/base-dades/represaliats' => 'public/web-publica/base-dades/base-dades-represaliats.php',
    '/base-dades/exiliats-deportats' => 'public/web-publica/base-dades/base-dades-exiliats.php',

    '/base-dades/filtre-exiliats' => 'public/web-publica/base-dades/filtre-pagina-exiliats.php',

    '/fitxa/{id}' => 'public/web-publica/base-dades/fitxa-represaliat.php',
    '/cerca-represaliat' => 'public/web-publica/base-dades/cerca-represaliat.php',

    // 2. Estudis
    '/documents-estudis' => 'public/web-publica/estudis.php',

    // 3. Fonts documentals
    '/fonts-documentals' => 'public/web-publica/fonts.php',

    // 4. Espai virtual
    '/que-es-espai-virtual' => 'public/web-publica/espai-virtual.php',

    // 5. Contacte
    '/contacte' => 'public/web-publica/contacte.php',

    // 6. Equip investigadors
    '/equip/manel-marquez' => 'public/web-publica/equip/manel-marquez.php',
    '/equip/juan-antonio-olivares' => 'public/web-publica/equip/juan-antonio.php',
    '/equip/elliot-fernandez' => 'public/web-publica/equip/elliot-fernandez.php',
    '/equip/josep-lluis-lacueva' => 'public/web-publica/equip/jose-luis.php',

    // 7. Cronologia
    '/cronologia' => 'public/web-publica/cronologia.php',

    // Altres pagines
    '/credits' => 'public/web-publica/legal/credits.php',
    '/avis-legal' => 'public/web-publica/legal/avis-legal.php',
    '/politica-privacitat' => 'public/web-publica/legal/politica-privacitat.php',

];

// Rutas principales sin idioma explícito (solo para el idioma por defecto)
$routes = [
    // 00. Homepage
    '/' => array_merge($defaultRoutesSenseHeaderConfig, [
        'view' => FRONTEND_URL . '/index.php'
    ]),

    '/benvinguda' => array_merge($defaultRoutesSenseHeaderConfig, [
        'view' => FRONTEND_URL . '/index.php'
    ]),

    '/inici' => array_merge($defaultRoutesConfig, [
        'view' => FRONTEND_URL . '/inici.php'
    ]),

    // 01. Accés intranet i recuperacio password
    '/acces' => array_merge($defaultRoutesUserLoggedConfig, [
        'view' => FRONTEND_URL . '/auth/login.php'
    ]),

    '/recuperacio-contrasenya' => array_merge($defaultRoutesConfig, [
        'view' => FRONTEND_URL . '/auth/recuperacio-contrasenya.php'
    ]),

    '/restabliment-contrasenya' => array_merge($defaultRoutesConfig, [
        'view' => FRONTEND_URL . '/auth/restabliment-contrasenya.php'
    ]),

    // 02. Base de dades
    '/base-dades/general' => [
        'view' => 'public/web-publica/base-dades/base-dades-global.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],

    '/base-dades/cost-huma' => [
        'view' => 'public/web-publica/base-dades/base-dades-cost-huma.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],

    '/base-dades/represaliats' => [
        'view' => 'public/web-publica/base-dades/base-dades-represaliats.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],

    '/base-dades/exiliats' => [
        'view' => 'public/web-publica/base-dades/base-dades-exiliats.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],

    '/base-dades/filtre-exiliats' => [
        'view' => 'public/web-publica/base-dades/filtre-pagina-exiliats.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],

    '/base-dades/fitxa/{id}' =>  [
        'view' => 'public/web-publica/base-dades/fitxa-represaliat.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],

    '/base-dades/cerca-represaliat' =>  [
        'view' => 'public/web-publica/base-dades/cerca-represaliat.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],

    // 2. Estudis
    '/documents-estudis' =>  [
        'view' => 'public/web-publica/estudis.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],


    // 3. Fonts documentals
    'fonts-documentals'  =>  [
        'view' => 'public/web-publica/fonts.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],

    // 4. Espai virtual
    'que-es-espai-virtual' =>  [
        'view' => 'public/web-publica/espai-virtual.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],

    // 5. Contacte
    '/contacte'  =>  [
        'view' => 'public/web-publica/contacte.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],

    // 6. Equip investigadors
    '/equip/manel-marquez'  =>  [
        'view' => 'public/web-publica/equip/manel-marquez.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],

    '/equip/juan-antonio-olivares'  =>  [
        'view' => 'public/web-publica/equip/juan-antonio.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],

    '/equip/elliot-fernandez'  =>  [
        'view' => 'public/web-publica/equip/elliot-fernandez.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],

    '/equip/josep-lluis-lacueva'  =>  [
        'view' => 'public/web-publica/equip/jose-luis.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],

    // Cronologia
    '/cronologia'  =>  [
        'view' => 'public/web-publica/cronologia.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],

    // Altres pagines
    '/credits' => [
        'view' => 'public/web-publica/legal/credits.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],

    '/avis-legal' => [
        'view' => 'public/web-publica/legal/avis-legal.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],

    '/politica-privacitat' => [
        'view' => 'public/web-publica/legal/politica-privacitat.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],

];

// Unir rutas base con rutas específicas de idioma
$routes = $routes + generateLanguageRoutes($base_routes, true);

return $routes;
