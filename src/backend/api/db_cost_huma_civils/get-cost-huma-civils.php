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

// GET : DB cost_huma_civils > informació sobre 1 represaliat
// URL: https://memoriaterrassa.cat/api/api/cost_huma_civils/get/fitxaRepressio?id=${id}
if ($slug === "fitxaRepressio") {
    $id = $_GET['id'] ?? null;
    $db = new Database();

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

    // 2) Pagina informacio fitxa cost humà morts civils
    // ruta GET => "/api/cost_huma_civils/get/fitxaId?id=${id}
} else if ($slug === "fitxaId") {
    $db = new Database();
    $id = $_GET['id'] ?? null;

    $query = "SELECT 
             	c.id,
                c.idPersona,
                c.cirscumstancies_mort AS cirscumstancies_mortId,
                cd.causa_defuncio_ca AS cirscumstancies_mort,
                c.data_trobada_cadaver,
                m1.ciutat AS lloc_trobada_cadaver,
                c.data_detencio,
                c.qui_ordena_afusellat,
                c.qui_executa_afusellat,
                c.qui_detencio,
                m2.ciutat AS lloc_detencio,
                c.data_bombardeig, 	
                m3.ciutat AS municipi_bombardeig,
                lb.lloc_bombardeig_ca AS lloc_bombardeig,
                c.responsable_bombardeig
            FROM db_cost_huma_morts_civils AS c
            LEFT JOIN aux_causa_defuncio AS cd ON c.cirscumstancies_mort = cd.id
            LEFT JOIN aux_dades_municipis AS m1 ON c.lloc_trobada_cadaver = m1.id
            LEFT JOIN aux_dades_municipis AS m2 ON c.lloc_detencio = m2.id
            LEFT JOIN aux_dades_municipis AS m3 ON c.municipi_bombardeig = m3.id
            LEFT JOIN  aux_llocs_bombardeig AS lb ON c.lloc_bombardeig = lb.id
            WHERE c.idPersona = :idPersona";

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
