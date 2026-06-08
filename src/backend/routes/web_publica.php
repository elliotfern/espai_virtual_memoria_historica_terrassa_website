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
    '/' => './web-publica/index.php',
    '/benvinguda' => './web-publica/index.php',
    '/inici' => './web-publica/inici.php',

    // Accés intranet i recuperació contrasenya
    '/acces' => './web-publica/auth/login.php',

    'recuperacio-contrasenya' => './web-publica/auth/recuperacio-contrasenya.php',

    // 1. Base de dades
    '/base-dades/general' => './web-publica/base-dades/base-dades-global.php',
    '/base-dades/cost-huma' => './web-publica/base-dades/filtre-pagina-cost-huma.php',
    '/base-dades/represaliats' => './web-publica/base-dades/base-dades-represaliats.php',
    '/base-dades/exiliats-deportats' => './web-publica/base-dades/filtre-pagina-exiliats.php',
    '/base-dades/geolocalitzacio' => './web-publica/base-dades/geolocalitzacio.php',


    '/fitxa/{id}' => './web-publica/base-dades/fitxa-represaliat.php',
    '/cerca-represaliat' => './web-publica/base-dades/cerca-represaliat.php',

    // 2. Estudis
    '/documents-estudis' => './web-publica/estudis/estudis.php',

    '/documents-estudis/fitxa/{id}' => './web-publica/estudis/estudi-detalls.php',

    // 3. Fonts documentals
    '/fonts-documentals' => './web-publica/fonts.php',

    // 4. Espai virtual
    'espai-virtual/que-es-espai-virtual' => './web-publica/espai-virtual/espai-virtual.php',

    // 4.1. Antecedents
    '/espai-virtual/antecedents' => './web-publica/espai-virtual/antecedents.php',

    // 4.2. Aparicions premsa
    '/espai-virtual/premsa' => './web-publica/espai-virtual/premsa.php',

    '/espai-virtual/premsa-aparicio/{id}' => './web-publica/espai-virtual/premsa-aparicio-detalls.php',

    // 4.2. Materials comunicacio
    '/espai-virtual/materials-comunicacio' => './web-publica/espai-virtual/materials.php',

    // 5. Contacte
    '/contacte' => './web-publica/contacte.php',

    // 6. Equip investigadors
    '/equip/{slug}' => './web-publica/equip/plantilla.php',

    // 7. Cronologia
    '/cronologia' => './web-publica/cronologia.php',

    // 8. Links
    '/links' => './web-publica/links.php',

    // Altres pagines
    '/credits' => './web-publica/legal/credits.php',
    '/avis-legal' => './web-publica/legal/avis-legal.php',
    '/politica-privacitat' => './web-publica/legal/politica-privacitat.php',
    '/politica-cookies' => './web-publica/legal/politica-cookies.php',

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
        'view' => './web-publica/base-dades/base-dades-global.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],

    '/base-dades/cost-huma' => [
        'view' => './web-publica/base-dades/filtre-pagina-cost-huma.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],

    '/base-dades/represaliats' => [
        'view' => './web-publica/base-dades/base-dades-represaliats.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],

    '/base-dades/exiliats' => [
        'view' => './web-publica/base-dades/filtre-pagina-exiliats.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],

    '/base-dades/fitxa/{id}' =>  [
        'view' => './web-publica/base-dades/fitxa-represaliat.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],

    '/base-dades/cerca-represaliat' =>  [
        'view' => './web-publica/base-dades/cerca-represaliat.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],

    '/base-dades/geolocalitzacio' =>  [
        'view' => './web-publica/base-dades/geolocalitzacio.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],

    // 2. Estudis
    '/documents-estudis' =>  [
        'view' => './web-publica/estudis/estudis.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],

    '/documents-estudis/fitxa/{id}' =>  [
        'view' => './web-publica/estudis/estudi-detalls.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],

    // 3. Fonts documentals
    '/fonts-documentals'  =>  [
        'view' => './web-publica/fonts.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],

    // 4. Espai virtual
    '/espai-virtual/que-es-espai-virtual' =>  [
        'view' => './web-publica/espai-virtual/espai-virtual.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],

    '/espai-virtual/antecedents' =>  [
        'view' => './web-publica/espai-virtual/antecedents.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],

    '/espai-virtual/premsa' =>  [
        'view' => './web-publica/espai-virtual/premsa.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],

    '/espai-virtual/premsa-aparicio/{id}' =>  [
        'view' => './web-publica/espai-virtual/premsa-aparicio-detalls.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],

    '/espai-virtual/materials-comunicacio' =>  [
        'view' => './web-publica/materials.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],

    // 5. Contacte
    '/contacte'  =>  [
        'view' => './web-publica/contacte.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],

    // 6. Equip investigadors
    '/equip/{slug}'  =>  [
        'view' => './web-publica/equip/plantilla.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],

    // Cronologia
    '/cronologia'  =>  [
        'view' => './web-publica/cronologia.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],

    // 8. Links
    '/links'  =>  [
        'view' => './web-publica/links.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],

    // Altres pagines
    '/credits' => [
        'view' => './web-publica/legal/credits.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],

    '/avis-legal' => [
        'view' => './web-publica/legal/avis-legal.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],

    '/politica-privacitat' => [
        'view' => './web-publica/legal/politica-privacitat.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],

    '/politica-cookies' => [
        'view' => './web-publica/legal/politica-cookies.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],

];

// Unir rutas base con rutas específicas de idioma
$routes = $routes + generateLanguageRoutes($base_routes, true);

return $routes;
