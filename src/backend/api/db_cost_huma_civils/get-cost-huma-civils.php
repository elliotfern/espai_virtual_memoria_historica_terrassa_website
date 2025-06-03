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

// GET : DB cost_huma_civils > informació sobre 1 represaliat
// URL: https://memoriaterrassa.cat/api/api/cost_huma_civils/get/fitxaRepressio?id=${id}
if ($slug === "fitxaRepressio") {
    $id = $_GET['id'] ?? null;

    $query = "SELECT 
    c.id,
    c.cirscumstancies_mort,
    c.data_trobada_cadaver,
    c.lloc_trobada_cadaver,
    c.data_detencio,
    c.lloc_detencio,
    c.data_bombardeig,
    c.municipi_bombardeig,
    c.lloc_bombardeig,
    c.responsable_bombardeig,
    c.qui_detencio,
    c.qui_executa_afusellat,
    c.qui_ordena_afusellat,
    d.nom,
    d.cognom1,
    d.cognom2
    FROM db_cost_huma_morts_civils AS c
    LEFT JOIN db_dades_personals AS d ON c.idPersona = d.id
    WHERE c.idPersona = :idPersona";

    $result = getData($query, ['idPersona' => $id], true);
    echo json_encode($result);
}
