<?php

// Define las rutas base que quieres traducir
$base_routes = [
    // API INTRANET
    '/gestio/entrada' => 'public/intranet/00_homepage/login.php',

    //intranet
    '/gestio' => 'public/intranet/00_homepage/admin.php',
    '/gestio/admin' => 'public/intranet/00_homepage/admin.php',

    // 0. Llistat complet
    '/gestio/tots' => 'public/intranet/0_tots/index.php',

    // MODIFICA FITXA PERSONA
    '/gestio/tots/fitxa/modifica/{id}' => 'public/intranet/0_tots/modifica-fitxa-persona.php',

    // GESTIÓ FAMILIARS
    '/gestio/tots/fitxa/familiars/{id}' => 'public/intranet/db_familiars/fitxa-familiar.php',
    '/gestio/tots/fitxa/familiar/modifica/{id}/{id}' => 'public/intranet/db_familiars/modifica-nou-familiar.php',
    '/gestio/tots/fitxa/familiar/elimina/{id}' => 'public/intranet/db_familiars/elimina-familiar.php',
    '/gestio/tots/fitxa/familiar/nou/{id}' => 'public/intranet/db_familiars/modifica-nou-familiar.php',

    // INSEREIX NOVA FITXA PERSONA
    '/gestio/tots/fitxa-nova' => 'public/intranet/0_tots/nova-fitxa-persona.php',

    // PÀGINES DE CONTROL DE CANVIS
    '/gestio/registre-canvis' => 'public/intranet/control_registre_canvis/index.php',
    '/gestio/control-acces' => 'public/intranet/control_registre_canvis/control-acces.php',

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

    // AUXILIARS
    '/gestio/municipi/nou' => 'public/intranet/db_auxiliars/nou-municipi.php',
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
    '/gestio/municipi/nou' => ['view' => 'public/intranet/db_auxiliars/nou-municipi.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

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

];

// Unir rutas base con rutas específicas de idioma
$routes = $routes + generateLanguageRoutes($base_routes, false);

return $routes;
