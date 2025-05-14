<?php

// Define las rutas base que quieres traducir
$base_routes = [
    // 0. Entrada Pàgina login i homepage
    '/gestio/entrada' => 'public/intranet/00_homepage/login.php',
    '/gestio' => 'public/intranet/00_homepage/admin.php',
    '/gestio/admin' => 'public/intranet/00_homepage/admin.php',

    // 1. Llistats pàgines grups represaliats
    '/gestio/tots' => 'public/intranet/0_tots/index.php',
    '/gestio/exiliats' => 'public/intranet/2_exili/index.php',
    '/gestio/represaliats' => 'public/intranet/1_represaliats/index.php',
    '/gestio/cost-huma' => 'public/pages/3_cost_huma/index.php',

    // 2. Pàgines de control canvis i usuaris intranet
    '/gestio/registre-canvis' => 'public/intranet/control_registre_canvis/index.php',
    '/gestio/control-acces' => 'public/intranet/control_registre_canvis/control-acces.php',


    // LÒGICA DE MODIFICACIONS DE LES DADES A LA INTRANET:
    // MODIFICA FITXA PERSONA
    '/gestio/tots/fitxa/modifica/{id}' => 'public/intranet/0_tots/modifica-fitxa-persona.php',

    // GESTIÓ FAMILIARS
    '/gestio/tots/fitxa/familiars/{id}' => 'public/intranet/db_familiars/fitxa-familiar.php',
    '/gestio/tots/fitxa/familiar/modifica/{id}/{id}' => 'public/intranet/db_familiars/modifica-nou-familiar.php',
    '/gestio/tots/fitxa/familiar/elimina/{id}' => 'public/intranet/db_familiars/elimina-familiar.php',
    '/gestio/tots/fitxa/familiar/nou/{id}' => 'public/intranet/db_familiars/modifica-nou-familiar.php',

    // INSEREIX NOVA FITXA PERSONA
    '/gestio/tots/fitxa-nova' => 'public/intranet/0_tots/nova-fitxa-persona.php',

    // MODIFICA REPRESSIO
    '/gestio/tots/fitxa/categoria/modifica/{categoriaId}/{id}' => 'public/intranet/0_tots/modifica-fitxa-repressio.php',

    // 1. Represaliats
    '/gestio/represaliats' => 'public/intranet/1_represaliats/index.php',
    '/gestio/represaliats/processats' => 'public/intranet/1_represaliats/processats_empresonats.php',
    '/gestio/represaliats/afusellats' => 'public/intranet/1_represaliats/afusellats.php',

    // 2. Exili
    '/gestio/exiliats' => 'public/intranet/2_exili/index.php',
    '/gestio/exiliats/exili-deportacio' => 'public/intranet/2_exili/exili_deportats.php',

    // 3. Cost huma
    '/gestio/cost-huma' => 'public/pages/3_cost_huma/index.php',

    // 3. Pàgines taules auxiliars
    APP_INTRANET . $url['auxiliars'] => BACKEND_URL . $url['auxiliars'] . '/index.php',

    APP_INTRANET . $url['auxiliars'] . '/llistat-usuaris' => BACKEND_URL . $url['auxiliars'] . '/usuaris.php',
    APP_INTRANET . $url['auxiliars'] . '/fitxa-usuari/{slug}' => BACKEND_URL . $url['auxiliars'] . '/fitxa-usuari.php',
    APP_INTRANET . $url['auxiliars'] . '/modifica-usuari/{slug}' => BACKEND_URL . $url['auxiliars'] . '/form-usuari.php',
    APP_INTRANET . $url['auxiliars'] . '/nou-usuari' => BACKEND_URL . $url['auxiliars'] . '/form-usuari.php',

    APP_INTRANET . $url['auxiliars'] . '/llistat-municipis' => BACKEND_URL . $url['auxiliars'] . '/municipis.php',

    '/gestio/municipi/nou' => 'public/intranet/db_auxiliars/nou-municipi.php',
    '/gestio/municipi/modifica/{id}' => 'public/intranet/db_auxiliars/nou-municipi.php',
    '/gestio/comarca/nou' => 'public/intranet/db_auxiliars/nou-comarca.php',
    '/gestio/provincia/nou' => 'public/intranet/db_auxiliars/nou-provincia.php',
    '/gestio/comunitat/nou' => 'public/intranet/db_auxiliars/nou-comunitat.php',
    '/gestio/estat/nou' => 'public/intranet/db_auxiliars/nou-estat.php',

    '/gestio/ofici/nou' => 'public/intranet/db_auxiliars/nou-ofici.php',
    '/gestio/tipologia-espai/nou' => 'public/intranet/db_auxiliars/nou-tipologia-espai.php',
    '/gestio/causa-mort/nou' => 'public/intranet/db_auxiliars/nou-causa-mort.php',
    '/gestio/carrec-empresa/nou' => 'public/intranet/db_auxiliars/nou-carrec-empresa.php',
    '/gestio/sub-sector-economic/nou' => 'public/intranet/db_auxiliars/nou-subsector-economic.php',
    '/gestio/partit-politic/nou' => 'public/intranet/db_auxiliars/nou-partit-politic.php',
    '/gestio/sindicat/nou' => 'public/intranet/db_auxiliars/nou-sindicat.php',

    // Gestió fons documentals
    '/gestio/tots/fitxa/fonts-documentals/fitxa/{id}' => 'public/intranet/db_fonts_documentals/fitxa-fonts-documentals.php',
    '/gestio/tots/fitxa/fonts-documentals/modifica-llibre/{id}/{id}' => 'public/intranet/db_fonts_documentals/modifica-nou-llibre.php',
    '/gestio/tots/fitxa/fonts-documentals/nou-llibre/{id}' => 'public/intranet/db_fonts_documentals/modifica-nou-llibre.php',

    '/gestio/tots/fitxa/fonts-documentals/crear-llibre' => 'public/intranet/db_fonts_documentals/afegir-nou-llibre.php',
    '/gestio/tots/fitxa/fonts-documentals/crear-arxiu' => 'public/intranet/db_fonts_documentals/afegir-nou-arxiu.php',

    // Cronologia 
    '/gestio/cronologia' => 'public/intranet/cronologia/index.php',
    '/gestio/cronologia/afegir-esdeveniment' => 'public/intranet/cronologia/afegir-esdeveniment.php',
    '/gestio/cronologia/modifica-esdeveniment/{id}' => 'public/intranet/cronologia/afegir-esdeveniment.php',

    // Biografia
    '/gestio/tots/fitxa/biografia/fitxa/{id}' => 'public/intranet/biografia/fitxa-biografia.php',
    '/gestio/tots/fitxa/biografia/nova-biografia/{id}' => 'public/intranet/biografia/modifica-nova-biografia.php',
    '/gestio/tots/fitxa/biografia/modifica-biografia/{id}/{id}' => 'public/intranet/biografia/modifica-nova-biografia.php',
];

// Rutas principales sin idioma explícito (solo para el idioma por defecto)
$routes = [
    // ACCES SECCIO GESTIO
    '/gestio/entrada' => ['view' => 'public/intranet/00_homepage/login.php', 'needs_session' => false, 'header_footer' => true, 'header_menu_footer' => false, 'apiSenseHTML' => false],

    // HOMEPAGE GESTIO
    '/gestio' => ['view' => 'public/intranet/00_homepage/admin.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    '/gestio/admin' => ['view' => 'public/intranet/00_homepage/admin.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    // LLISTAT TOTS
    '/gestio/tots' => ['view' => 'public/intranet/0_tots/index.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    '/gestio/represaliats' => ['view' => 'public/intranet/1_represaliats/index.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    '/gestio/exiliats' => ['view' => 'public/intranet/2_exili/index.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    '/gestio/cost-huma' => ['view' => 'public/intranet/3_cost_huma/index.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    // 3. Pàgines taules auxiliars
    APP_INTRANET . $url['auxiliars'] => [
        'view' => BACKEND_URL . $url['auxiliars'] . '/index.php',
        'needs_session' => true,
        'header_footer' => false,
        'header_menu_footer' => true,
        'apiSenseHTML' => false
    ],

    APP_INTRANET . $url['auxiliars'] . '/llistat-municipis' => [
        'view' =>  BACKEND_URL . $url['auxiliars'] . '/municipis.php',
        'needs_session' => true,
        'header_footer' => false,
        'header_menu_footer' => true,
        'apiSenseHTML' => false
    ],

    APP_INTRANET . $url['auxiliars'] . '/llistat-usuaris' => [
        'view' => BACKEND_URL . $url['auxiliars'] .  '/usuaris.php',
        'needs_session' => true,
        'header_footer' => false,
        'header_menu_footer' => true,
        'apiSenseHTML' => false
    ],

    APP_INTRANET . $url['auxiliars'] . '/fitxa-usuari/{slug}' => [
        'view' => BACKEND_URL . $url['auxiliars'] .  '/fitxa-usuari.php',
        'needs_session' => true,
        'header_footer' => false,
        'header_menu_footer' => true,
        'apiSenseHTML' => false
    ],

    APP_INTRANET . $url['auxiliars'] . '/modifica-usuari/{slug}' => [
        'view' => BACKEND_URL . $url['auxiliars'] .  '/form-usuari.php',
        'needs_session' => true,
        'header_footer' => false,
        'header_menu_footer' => true,
        'apiSenseHTML' => false
    ],

    APP_INTRANET . $url['auxiliars'] . '/nou-usuari' => [
        'view' => BACKEND_URL . $url['auxiliars'] .  '/form-usuari.php',
        'needs_session' => true,
        'header_footer' => false,
        'header_menu_footer' => true,
        'apiSenseHTML' => false
    ],


    // BASE DE DADES PERSONALS

    '/gestio/tots/fitxa/modifica/{id}' => ['view' => 'public/intranet/0_tots/modifica-fitxa-persona.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    '/gestio/tots/fitxa-nova' => ['view' => 'public/intranet/0_tots/nova-fitxa-persona.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    // Modificacio fitxes
    '/gestio/tots/fitxa/categoria/modifica/{categoriaId}/{id}' => ['view' => 'public/intranet/0_tots/modifica-fitxa-repressio.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

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

    // AUXILIARS
    // municipis
    '/gestio/municipi/nou' => ['view' => 'public/intranet/db_auxiliars/nou-municipi.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],
    '/gestio/municipi/modifica/{id}' => ['view' => 'public/intranet/db_auxiliars/nou-municipi.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    '/gestio/comarca/nou' => ['view' => 'public/intranet/db_auxiliars/nou-comarca.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    '/gestio/provincia/nou' => ['view' => 'public/intranet/db_auxiliars/nou-provincia.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    '/gestio/comunitat/nou' => ['view' => 'public/intranet/db_auxiliars/nou-comunitat.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    '/gestio/estat/nou' => ['view' => 'public/intranet/db_auxiliars/nou-estat.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    // ALtres pagines auxiliars
    '/gestio/ofici/nou' => ['view' => 'public/intranet/db_auxiliars/nou-ofici.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    '/gestio/tipologia-espai/nou' => ['view' => 'public/intranet/db_auxiliars/nou-tipologia-espai.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    '/gestio/causa-mort/nou' => ['view' => 'public/intranet/db_auxiliars/nou-causa-mort.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    '/gestio/carrec-empresa/nou' => ['view' => 'public/intranet/db_auxiliars/nou-carrec-empresa.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    '/gestio/sub-sector-economic/nou' => ['view' => 'public/intranet/db_auxiliars/nou-subsector-economic.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    '/gestio/partit-politic/nou' => ['view' => 'public/intranet/db_auxiliars/nou-partit-politic.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    '/gestio/sindicat/nou' => ['view' => 'public/intranet/db_auxiliars/nou-sindicat.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

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

// Unir rutas base con rutas específicas de idioma
$routes = $routes + generateLanguageRoutes($base_routes, false);

return $routes;
