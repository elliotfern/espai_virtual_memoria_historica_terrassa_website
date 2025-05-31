<?php

$slug = $routeParams[0];

// Configuración de cabeceras para aceptar JSON y responder JSON
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET");

// Definir el dominio permitido
$allowedOrigin = DOMAIN;

// Llamar a la función para verificar el referer
checkReferer($allowedOrigin);

// Verificar que el método de la solicitud sea GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// GET : Llistat arxius i fonts documentals
// URL: /api/fonts/get/llistatArxiusFonts
if ($slug === 'llistatArxiusFonts') {
    $query = "SELECT l.id, l.arxiu, l.codi, l.descripcio, l.web
              FROM aux_bibliografia_arxius_codis AS l
              ORDER BY l.arxiu ASC";

    $result = getData($query);
    echo json_encode($result);

    // GET : Llistat bibliografia
    // URL: /api/fonts/get/llistatBibliografia
} elseif ($slug === 'llistatBibliografia') {
    $query = "SELECT l.id, l.llibre, l.autor, l.editorial, m.ciutat, l.any, l.volum
              FROM aux_bibliografia_llibre_detalls AS l
              LEFT JOIN aux_dades_municipis AS m ON l.ciutat = m.id
              ORDER BY l.llibre";

    $result = getData($query);
    echo json_encode($result);
}
