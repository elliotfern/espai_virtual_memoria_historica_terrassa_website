<?php

use App\Config\Database;
use App\Utils\Response;
use App\Utils\MissatgesAPI;

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
    $db = new Database();

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


    try {
        $params = [':idPersona' => $id];
        $result = $db->getData($query, $params, true);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;
        }

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }
}
