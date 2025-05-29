<?php

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
        'view' => BACKEND_URL . $urlIntranet['homepage'] . '/admin.php'
    ]),

    APP_INTRANET . '/admin' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranet['homepage'] . '/admin.php'
    ]),

    // 01. Pàgines base de dades
    APP_INTRANET . '/base-dades/general' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranet['base_dades'] . '/llistat_general.php'
    ]),

    APP_INTRANET . '/base-dades/represaliats' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranet['base_dades'] . '/llistat_represaliats.php'
    ]),

    APP_INTRANET . '/base-dades/exiliats-deportats' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranet['base_dades'] . '/llistat_exiliats.php'
    ]),

    APP_INTRANET . '/base-dades/cost-huma' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranet['base_dades'] . '/llistat_cost_huma.php'
    ]),

    // 01. Modificació dades de les fitxes
    APP_INTRANET . '/base-dades/modifica-fitxa/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranet['base_dades'] . '/modifica_fitxa_persona.php'
    ]),

    APP_INTRANET . '/base-dades/nova-fitxa' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranet['base_dades'] . '/modifica_fitxa_persona.php'
    ]),

    APP_INTRANET . '/base-dades/modifica-repressio/{categoriaId}/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranet['base_dades'] . '/modifica_fitxa_tipus_repressio.php'
    ]),

    // 2. Pàgines taules auxiliars
    APP_INTRANET . $url['auxiliars'] => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranet['auxiliars'] . '/index.php'
    ]),

    APP_INTRANET . $url['auxiliars'] . '/llistat-municipis' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranet['auxiliars'] . '/llistat-municipis.php'
    ]),

    APP_INTRANET . $url['auxiliars'] . '/llistat-partits-politics' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranet['auxiliars'] . '/llistat-partits-politics.php'
    ]),

    APP_INTRANET . $url['auxiliars'] . '/llistat-sindicats' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranet['auxiliars'] . '/llistat-sindicats.php'
    ]),

    APP_INTRANET . $url['auxiliars'] . '/llistat-usuaris' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranet['auxiliars'] . '/llistat-usuaris.php'
    ]),

    APP_INTRANET . $url['auxiliars'] . '/modifica-usuari/{slug}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranet['auxiliars'] . '/form-usuari.php'
    ]),

    APP_INTRANET . $url['auxiliars'] . '/nou-usuari' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranet['auxiliars'] . '/form-usuari.php'
    ]),

    APP_INTRANET . $url['auxiliars'] . '/modifica-avatar-usuari/{slug}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranet['auxiliars'] . '/form-usuari-avatar.php'
    ]),

    APP_INTRANET . $url['auxiliars'] . '/nou-avatar-usuari' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranet['auxiliars'] . '/form-usuari-avatar.php'
    ]),


    APP_INTRANET . $url['auxiliars'] . '/nou-municipi' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranet['auxiliars'] . '/form-municipi.php'
    ]),

    APP_INTRANET . $url['auxiliars'] . '/modifica-municipi/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranet['auxiliars'] . '/form-municipi.php'
    ]),

    APP_INTRANET . $url['auxiliars'] . '/nova-comarca' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranet['auxiliars'] . '/form-comarca.php'
    ]),

    APP_INTRANET . $url['auxiliars'] . '/modifica-comarca/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranet['auxiliars'] . '/form-comarca.php'
    ]),

    APP_INTRANET . $url['auxiliars'] . '/nova-provincia' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranet['auxiliars'] . '/form-provincia.php'
    ]),

    APP_INTRANET . $url['auxiliars'] . '/modifica-provincia/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranet['auxiliars'] . '/form-provincia.php'
    ]),

    APP_INTRANET . $url['auxiliars'] . '/nova-comunitat' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranet['auxiliars'] . '/form-comunitat.php'
    ]),

    APP_INTRANET . $url['auxiliars'] . '/modifica-comunitat/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranet['auxiliars'] . '/form-comunitat.php'
    ]),

    APP_INTRANET . $url['auxiliars'] . '/nou-estat' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranet['auxiliars'] . '/form-estat.php'
    ]),

    APP_INTRANET . $url['auxiliars'] . '/modifica-estat/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranet['auxiliars'] . '/form-estat.php'
    ]),

    APP_INTRANET . $url['auxiliars'] . '/nou-ofici' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranet['auxiliars'] . '/form-ofici.php'
    ]),

    APP_INTRANET . $url['auxiliars'] . '/modifica-ofici/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranet['auxiliars'] . '/form-ofici.php'
    ]),

    APP_INTRANET . $url['auxiliars'] . '/nova-tipologia-espai' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranet['auxiliars'] . '/form-tipologia-espai.php'
    ]),

    APP_INTRANET . $url['auxiliars'] . '/modifica-tipologia-espai/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranet['auxiliars'] . '/form-tipologia-espai.php'
    ]),

    APP_INTRANET . $url['auxiliars'] . '/nova-causa-mort' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranet['auxiliars'] . '/form-causa-mort.php'
    ]),

    APP_INTRANET . $url['auxiliars'] . '/modifica-causa-mort/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranet['auxiliars'] . '/form-causa-mort.php'
    ]),

    APP_INTRANET . $url['auxiliars'] . '/nou-carrec-empresa' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranet['auxiliars'] . '/form-carrec-empresa.php'
    ]),

    APP_INTRANET . $url['auxiliars'] . '/modifica-carrec-empresa/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranet['auxiliars'] . '/form-carrec-empresa.php'
    ]),

    APP_INTRANET . $url['auxiliars'] . '/nou-sub-sector-economic' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranet['auxiliars'] . '/form-sub-sector-economic.php'
    ]),

    APP_INTRANET . $url['auxiliars'] . '/modifica-sub-sector-economic/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranet['auxiliars'] . '/form-sub-sector-economic.php'
    ]),

    APP_INTRANET . $url['auxiliars'] . '/nou-partit-politic' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranet['auxiliars'] . '/form-partit-politic.php'
    ]),

    APP_INTRANET . $url['auxiliars'] . '/modifica-partit-politic/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranet['auxiliars'] . '/form-partit-politic.php'
    ]),

    APP_INTRANET . $url['auxiliars'] . '/nou-sindicat' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranet['auxiliars'] . '/form-sindicat.php'
    ]),

    APP_INTRANET . $url['auxiliars'] . '/modifica-sindicat/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranet['auxiliars'] . '/form-sindicat.php'
    ]),



    // Gestió familiars
    '/gestio/tots/fitxa/familiars/{id}' => ['view' => 'public/intranet/db_familiars/fitxa-familiar.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    '/gestio/tots/fitxa/familiar/modifica/{id}/{id}' => ['view' => 'public/intranet/db_familiars/modifica-nou-familiar.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    '/gestio/tots/fitxa/familiar/elimina/{id}' => ['view' => 'public/intranet/db_familiars/elimina-familiar.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    '/gestio/tots/fitxa/familiar/nou/{id}' => ['view' => 'public/intranet/db_familiars/modifica-nou-familiar.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    // Gestió fonts documentals (bibliografia i arxius)
    '/gestio/tots/fitxa/fonts-documentals/fitxa/{id}' => ['view' => 'public/intranet/db_fonts_documentals/fitxa-fonts-documentals.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    // llibres
    '/gestio/tots/fitxa/fonts-documentals/nou-llibre/{id}' => ['view' => 'public/intranet/db_fonts_documentals/modifica-nou-llibre.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    '/gestio/tots/fitxa/fonts-documentals/modifica-llibre/{id}/{id}' => ['view' => 'public/intranet/db_fonts_documentals/modifica-nou-llibre.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    '/gestio/tots/fitxa/fonts-documentals/crear-llibre' => ['view' => 'public/intranet/db_fonts_documentals/afegir-nou-llibre.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    // arxius
    '/gestio/tots/fitxa/fonts-documentals/nou-arxiu/{id}' => ['view' => 'public/intranet/db_fonts_documentals/modifica-nou-arxiu.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    '/gestio/tots/fitxa/fonts-documentals/modifica-arxiu/{id}/{id}' => ['view' => 'public/intranet/db_fonts_documentals/modifica-nou-arxiu.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    '/gestio/tots/fitxa/fonts-documentals/crear-arxiu' => ['view' => 'public/intranet/db_fonts_documentals/afegir-nou-arxiu.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    // REGISTRE CANVIS
    '/gestio/registre-canvis' => ['view' => 'public/intranet/control_registre_canvis/index.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    '/gestio/control-acces' => ['view' => 'public/intranet/control_registre_canvis/control-acces.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    // CRONOLOGIA
    '/gestio/cronologia' => ['view' => 'public/intranet/cronologia/index.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    '/gestio/cronologia/afegir-esdeveniment' => ['view' => 'public/intranet/cronologia/afegir-esdeveniment.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    '/gestio/cronologia/modifica-esdeveniment/{id}' => ['view' => 'public/intranet/cronologia/afegir-esdeveniment.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    // Biografia
    '/gestio/tots/fitxa/biografia/fitxa/{id}' => ['view' => 'public/intranet/biografia/fitxa-biografia.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    '/gestio/tots/fitxa/biografia/nova-biografia/{id}' => ['view' => 'public/intranet/biografia/modifica-nova-biografia.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    '/gestio/tots/fitxa/biografia/modifica-biografia/{id}/{id}' => ['view' => 'public/intranet/biografia/modifica-nova-biografia.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],


];

return $routes;
