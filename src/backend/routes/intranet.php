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
    '/gestio/tots/fitxa/{id}' => 'public/intranet/0_tots/fitxa-persona.php',
    '/gestio/tots/fitxa/modifica/{id}' => 'public/intranet/0_tots/modifica-fitxa-persona.php',

    // 1. Represaliats
    '/gestio/represaliats' => 'public/intranet/1_represaliats/index.php',
    '/gestio/represaliats/processats' => 'public/intranet/1_represaliats/processats_empresonats.php',
    '/gestio/represaliats/afusellats' => 'public/intranet/1_represaliats/afusellats.php',

    // 2. Exili
    '/gestio/exiliats' => 'public/intranet/2_exili/index.php',
    '/gestio/exiliats/exili-deportacio' => 'public/intranet/2_exili/exili_deportats.php',

    // 3. Cost huma
    '/gestio/cost-huma' => 'public/pages/3_cost_huma/index.php',

];

// Rutas principales sin idioma explícito (solo para el idioma por defecto)
$routes = [
    // ACCES SECCIO GESTIO
    '/gestio/entrada' => ['view' => 'public/intranet/00_homepage/login.php', 'needs_session' => false, 'no_header_footer' => false],

    // HOMEPAGE GESTIO
    '/gestio' => ['view' => 'public/intranet/00_homepage/admin.php', 'needs_session' => true, 'no_header_footer' => false],

    '/gestio/admin' => ['view' => 'public/intranet/00_homepage/admin.php', 'needs_session' => true, 'no_header_footer' => false],

    // LLISTAT TOTS
    '/gestio/tots' => ['view' => 'public/intranet/0_tots/index.php', 'needs_session' => true, 'no_header_footer' => false],

];

// Unir rutas base con rutas específicas de idioma
$routes = $routes + generateLanguageRoutes($base_routes, false);

return $routes;
