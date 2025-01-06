<?php

// Define las rutas base que quieres traducir
$base_routes = [
    // HOMEPAGE
    '/' => 'public/web-publica/index.php',
    '/benvinguda' => 'public/web-publica/index.php',
    '/inici' => 'public/web-publica/inici.php',

    // PAGINES ESPECIFIQUES
    '/represaliats' => 'public/web-publica/represaliats.php',
    '/exiliats' => 'public/web-publica/exiliats.php',
    '/cost-huma' => 'public/web-publica/cost-huma.php',

    '/base-dades-global' => 'public/web-publica/base-dades-global.php',
    '/fitxa/{id}' => 'public/web-publica/fitxa-represaliat.php',

    // ALTRES PAGINES
    '/contacte' => 'public/web-publica/contacte.php',
    '/credits' => 'public/web-publica/credits.php',
    '/avis-legal' => 'public/web-publica/avis-legal.php',
    '/politica-privacitat' => 'public/web-publica/politica-privacitat.php',

];

// Rutas principales sin idioma explícito (solo para el idioma por defecto)
$routes = [
    // ACCES SECCIO GESTIO
    '/' => ['view' => 'public/web-publica/index.php', 'needs_session' => false, 'header_footer' => true, 'header_menu_footer' => false],

    // HOMEPAGE GESTIO
    '/benvinguda' => ['view' => 'public/web-publica/index.php', 'needs_session' => false, 'header_footer' => true, 'header_menu_footer' => false],

    '/inici' => ['view' => 'public/web-publica/inici.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => true],

    '/represaliats' => ['view' => 'public/web-publica/represaliats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => true],

    '/exiliats' => ['view' => 'public/web-publica/exiliats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => true],

    '/cost-huma' => ['view' => 'public/web-publica/cost-huma.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => true],

    '/contacte' => ['view' => 'public/web-publica/contacte.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => true],

    '/credits' => ['view' => 'public/web-publica/credits.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => true],

    '/avis-legal' => ['view' => 'public/web-publica/avis-legal.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => true],

    '/politica-privacitat' => ['view' => 'public/web-publica/politica-privacitat.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => true],

    '/base-dades-global' => ['view' => 'public/web-publica/base-dades-global.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => true],

    '/fitxa/{id}' =>  ['view' => 'public/web-publica/fitxa-represaliat.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => true],

];

// Unir rutas base con rutas específicas de idioma
$routes = $routes + generateLanguageRoutes($base_routes, true);

return $routes;
