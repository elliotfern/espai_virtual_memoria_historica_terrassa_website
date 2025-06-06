<?php

define("APP_INTRANET", '/gestio');

$urlIntranetDir = [
    'homepage' => '/00_homepage',
    'base_dades' => '/01_base_dades',
    'auxiliars' => '/02_taules_auxiliars',
    'fonts' => '/03_fonts_documentals',
    'familiars' => '/04_familiars',
    'biografies' => '/05_biografies',
];

$urlIntranet = [
    'homepage' => '/admin',
    'base_dades' => '/base-dades',
    'auxiliars' => '/auxiliars',
    'fonts' => '/fonts-documentals',
    'cronologia' => '/cronologia',
    'familiars' => '/familiars',
    'biografies' => '/biografies',
];


// Configuración por defecto para rutas que requieren sesión, sin header_footer, con header_menu_footer
$defaultProtectedConfig = [
    'needs_session' => true,
    'header_footer' => false,
    'header_menu_footer' => true,
    'apiSenseHTML' => false
];

// Rutas principales sin idioma explícito (solo para el idioma por defecto)
$routes = [

    // 00. Intranet Homepage
    APP_INTRANET => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['homepage'] . '/admin.php'
    ]),

    APP_INTRANET . '/admin' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['homepage'] . '/admin.php'
    ]),

    // 01. Pàgines base de dades
    APP_INTRANET . '/base-dades/general' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['base_dades'] . '/llistat_general.php'
    ]),

    APP_INTRANET . '/base-dades/represaliats' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['base_dades'] . '/llistat_represaliats.php'
    ]),

    APP_INTRANET . '/base-dades/exiliats-deportats' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['base_dades'] . '/llistat_exiliats.php'
    ]),

    APP_INTRANET . '/base-dades/cost-huma' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['base_dades'] . '/llistat_cost_huma.php'
    ]),

    // 01. Modificació dades de les fitxes
    APP_INTRANET . '/base-dades/modifica-fitxa/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['base_dades'] . '/modifica_fitxa_persona.php'
    ]),

    APP_INTRANET . '/base-dades/nova-fitxa' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['base_dades'] . '/modifica_fitxa_persona.php'
    ]),

    APP_INTRANET . '/base-dades/modifica-repressio/{categoriaId}/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['base_dades'] . '/modifica_fitxa_tipus_repressio.php'
    ]),

    // 2. Pàgines taules auxiliars
    APP_INTRANET . $urlIntranet['auxiliars'] => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/index.php'
    ]),

    // 2.1 Municipis
    APP_INTRANET . $urlIntranet['auxiliars'] . '/llistat-municipis' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/llistat-municipis.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/nou-municipi' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-municipi.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/modifica-municipi/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-municipi.php'
    ]),

    // 2.2 Partits politics
    APP_INTRANET . $urlIntranet['auxiliars'] . '/llistat-partits-politics' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/llistat-partits-politics.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/nou-partit-politic' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-partit-politic.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/modifica-partit-politic/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-partit-politic.php'
    ]),

    // 2.3 Sindicats
    APP_INTRANET . $urlIntranet['auxiliars'] . '/llistat-sindicats' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/llistat-sindicats.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/nou-sindicat' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-sindicat.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/modifica-sindicat/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-sindicat.php'
    ]),


    // 2.4 Llistat taula usuaris i avatars
    APP_INTRANET . $urlIntranet['auxiliars'] . '/llistat-usuaris' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/llistat-usuaris.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/modifica-usuari/{slug}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-usuari.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/nou-usuari' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-usuari.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/modifica-avatar-usuari/{slug}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-usuari-avatar.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/nou-avatar-usuari' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-usuari-avatar.php'
    ]),

    // 2.5 Llistat comarques
    APP_INTRANET . $urlIntranet['auxiliars'] . '/llistat-comarques' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/llistat-comarques.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/nova-comarca' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-comarca.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/modifica-comarca/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-comarca.php'
    ]),

    // 2.6 Llistat províncies
    APP_INTRANET . $urlIntranet['auxiliars'] . '/llistat-provincies' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/llistat-provincies.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/nova-provincia' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-provincia.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/modifica-provincia/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-provincia.php'
    ]),

    // 2.7 Llistat comunitats autònomes
    APP_INTRANET . $urlIntranet['auxiliars'] . '/llistat-comunitats' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/llistat-comunitats.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/nova-comunitat' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-comunitat.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/modifica-comunitat/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-comunitat.php'
    ]),

    // 2.8 Llistat països - estats
    APP_INTRANET . $urlIntranet['auxiliars'] . '/llistat-estats' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/llistat-estats.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/nou-estat' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-estat.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/modifica-estat/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-estat.php'
    ]),

    // 2.9 Llistat oficis
    APP_INTRANET . $urlIntranet['auxiliars'] . '/llistat-oficis' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/llistat-oficis.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/nou-ofici' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-ofici.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/modifica-ofici/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-ofici.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/nova-tipologia-espai' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-tipologia-espai.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/modifica-tipologia-espai/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-tipologia-espai.php'
    ]),


    // Causa de mort
    APP_INTRANET . $urlIntranet['auxiliars'] . '/llistat-causa-mort' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/llistat-causa-mort.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/nova-causa-mort' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-causa-mort.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/modifica-causa-mort/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-causa-mort.php'
    ]),



    APP_INTRANET . $urlIntranet['auxiliars'] . '/nou-sub-sector-economic' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-sub-sector-economic.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/modifica-sub-sector-economic/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-sub-sector-economic.php'
    ]),

    // Categories de repressió
    APP_INTRANET . $urlIntranet['auxiliars'] . '/llistat-categories-repressio' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/llistat-categories-repressio.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/modifica-categoria-repressio/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-categoria-repressio.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/nova-categoria-repressio/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-categoria-repressio.php'
    ]),

    // carrecs empresa
    APP_INTRANET . $urlIntranet['auxiliars'] . '/llistat-carrecs-empresa' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/llistat-carrecs-empresa.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/nou-carrec-empresa' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-carrec-empresa.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/modifica-carrec-empresa/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-carrec-empresa.php'
    ]),


    // Gestió familiars
    APP_INTRANET . $urlIntranet['familiars'] . '/modifica-familiar/{id}/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['familiars'] . '/form-familiar.php'
    ]),

    APP_INTRANET . $urlIntranet['familiars'] . '/nou-familiar/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['familiars'] . '/form-familiar.php'
    ]),

    // GESTIÓ FONTS DOCUMENTALS (bibliografia i arxius)
    // Bibliografia / llibres > fitxa represaliat (llistat de tots els fonts vinculats a una fitxa)
    APP_INTRANET . $urlIntranet['fonts'] . '/fitxa/afegir-bibliografia/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['fonts'] . '/form-fitxa-llibre.php'
    ]),

    APP_INTRANET . $urlIntranet['fonts'] . '/fitxa/modifica-bibliografia/{id}/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['fonts'] . '/form-fitxa-llibre.php'
    ]),

    APP_INTRANET . $urlIntranet['fonts'] . '/fitxa/afegir-arxiu/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['fonts'] . '/form-fitxa-arxiu.php'
    ]),

    APP_INTRANET . $urlIntranet['fonts'] . '/fitxa/modifica-arxiu/{id}/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['fonts'] . '/form-fitxa-arxiu.php'
    ]),

    // Bibliografia
    APP_INTRANET . $urlIntranet['fonts'] . '/llistat-llibres' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['fonts'] . '/llistat-llibres.php'
    ]),

    APP_INTRANET . $urlIntranet['fonts'] . '/nou-llibre' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['fonts'] . '/form-llibre.php'
    ]),

    APP_INTRANET . $urlIntranet['fonts'] . '/modifica-llibre/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['fonts'] . '/form-llibre.php'
    ]),

    // Arxius
    APP_INTRANET . $urlIntranet['fonts'] . '/llistat-arxius' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['fonts'] . '/llistat-arxius.php'
    ]),

    APP_INTRANET . $urlIntranet['fonts'] . '/nou-arxiu' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['fonts'] . '/form-arxiu.php'
    ]),

    APP_INTRANET . $urlIntranet['fonts'] . '/modifica-arxiu/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['fonts'] . '/form-arxiu.php'
    ]),


    // REGISTRE CANVIS
    '/gestio/registre-canvis' => ['view' => 'public/intranet/control_registre_canvis/index.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    '/gestio/control-acces' => ['view' => 'public/intranet/control_registre_canvis/control-acces.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    // CRONOLOGIA
    '/gestio/cronologia' => ['view' => 'public/intranet/cronologia/index.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    '/gestio/cronologia/afegir-esdeveniment' => ['view' => 'public/intranet/cronologia/afegir-esdeveniment.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    '/gestio/cronologia/modifica-esdeveniment/{id}' => ['view' => 'public/intranet/cronologia/afegir-esdeveniment.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    // Biografia
    APP_INTRANET . $urlIntranet['biografies'] . '/modifica-biografia/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['biografies'] . '/form-biografia.php'
    ]),

    APP_INTRANET . $urlIntranet['biografies'] . '/nova-biografia/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['biografies'] . '/form-biografia.php'
    ]),

];

return $routes;
