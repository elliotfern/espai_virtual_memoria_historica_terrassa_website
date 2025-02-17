<?php

// Define las rutas base que quieres traducir
$base_routes = [
    // HOMEPAGE
    '/' => 'public/web-publica/index.php',
    '/benvinguda' => 'public/web-publica/index.php',
    '/inici' => 'public/web-publica/inici.php',

    // 1. BASE DE DADES
    '/base-dades/general' => 'public/web-publica/base-dades-global.php',
    '/base-dades/cost-huma' => 'public/web-publica/base-dades-cost-huma.php',
    '/base-dades/represaliats' => 'public/web-publica/base-dades-represaliats.php',
    '/base-dades/exiliats-deportats' => 'public/web-publica/base-dades-exiliats.php',
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
    '/' => [
        'view' => 'public/web-publica/index.php',
        'needs_session' => false,
        'header_footer' => true,
        'header_menu_footer' => false
    ],

    // HOMEPAGE GESTIO
    '/benvinguda' => [
        'view' => 'public/web-publica/index.php',
        'needs_session' => false,
        'header_footer' => true,
        'header_menu_footer' => false
    ],

    '/inici' => [
        'view' => 'public/web-publica/inici.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],


    // 1. Base de dades
    '/base-dades/general' => [
        'view' => 'public/web-publica/base-dades-global.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],

    '/base-dades/cost-huma' => [
        'view' => 'public/web-publica/base-dades-cost-huma.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],

    '/base-dades/represaliats' => [
        'view' => 'public/web-publica/base-dades-represaliats.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],

    '/base-dades/exiliats' => [
        'view' => 'public/web-publica/base-dades-exiliats.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],

    '/base-dades/fitxa/{id}' =>  [
        'view' => 'public/web-publica/fitxa-represaliat.php',
        'needs_session' => false,
        'header_footer' => false,
        'header_menu_footer' => true
    ],


    '/represaliats' => ['view' => 'public/web-publica/represaliats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => true],

    '/exiliats' => ['view' => 'public/web-publica/exiliats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => true],

    '/cost-huma' => ['view' => 'public/web-publica/cost-huma.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => true],

    '/contacte' => ['view' => 'public/web-publica/contacte.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => true],

    '/credits' => ['view' => 'public/web-publica/credits.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => true],

    '/avis-legal' => ['view' => 'public/web-publica/avis-legal.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => true],

    '/politica-privacitat' => ['view' => 'public/web-publica/politica-privacitat.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => true],



];

// Unir rutas base con rutas específicas de idioma
$routes = $routes + generateLanguageRoutes($base_routes, true);

return $routes;
