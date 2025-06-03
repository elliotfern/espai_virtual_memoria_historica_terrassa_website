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

// GET : DB cost_huma_front > informació sobre 1 represaliat
// URL: https://memoriaterrassa.cat/api/api/cost_huma_front/get/fitxaRepressio?id=${id}
if ($slug === "fitxaRepressio") {
    $id = $_GET['id'] ?? null;

    $query = "SELECT 
    f.id,
    f.idPersona,
    f.condicio,
    f.bandol,
    f.any_lleva,
    f.unitat_inicial,
    f.cos,
    f.unitat_final ,
    f.graduacio_final,
    f.periple_militar,
    f.circumstancia_mort,
    f.desaparegut_data,
    f.desaparegut_lloc,
    f.desaparegut_data_aparicio,
    f.desaparegut_lloc_aparicio
    FROM db_cost_huma_morts_front AS f
    WHERE f.idPersona = :idPersona";

    $result = getData($query, ['idPersona' => $id], true);
    echo json_encode($result);
}
