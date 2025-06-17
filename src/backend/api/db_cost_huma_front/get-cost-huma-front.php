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
// URL: https://memoriaterrassa.cat/api/cost_huma_front/get/fitxaRepressio?id=${id}
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

    // 2) Pagina informacio fitxa cost humà combatents al front
    // ruta GET => "/api/cost_huma_front/get/fitxaId?id=${id}
} else if ($slug === "fitxaId") {
    $db = new Database();
    $id = $_GET['id'] ?? null;

    $query = "SELECT 
             	d.id,
                d.idPersona,
                c.condicio_ca AS condicio,
                b.bandol_ca AS bandol,
                d.any_lleva,
                d.unitat_inicial,
                cm.cos_militar_ca AS cos,
                d.unitat_final,
                d.graduacio_final,
                d.periple_militar,
                cd.causa_defuncio_ca AS circumstancia_mort,
                d.desaparegut_data,
                m1.ciutat AS desaparegut_lloc,
                d.desaparegut_data_aparicio,
                m2.ciutat AS desaparegut_lloc_aparicio
            FROM db_cost_huma_morts_front AS d
            LEFT JOIN aux_condicio AS c ON d.condicio = c.id
            LEFT JOIN aux_bandol AS b ON d.bandol = b.id
            LEFT JOIN aux_cossos_militars AS cm ON d.cos = cm.id
            LEFT JOIN aux_causa_defuncio AS cd ON d.circumstancia_mort = cd.id
            LEFT JOIN aux_dades_municipis AS m1 ON d.desaparegut_lloc = m1.id
            LEFT JOIN aux_dades_municipis AS m2 ON d.desaparegut_lloc_aparicio = m2.id
            WHERE d.idPersona = :idPersona";

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
