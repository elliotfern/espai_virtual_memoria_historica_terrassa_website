<?php

use App\Config\Database;
use App\Utils\Response;
use App\Utils\MissatgesAPI;

use App\Config\DatabaseConnection;

$conn = DatabaseConnection::getConnection();

if (!$conn) {
    die("No se pudo establecer conexión a la base de datos.");
}


// Configuración de cabeceras para aceptar JSON y responder JSON
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: https://memoriaterrassa.cat");
header("Access-Control-Allow-Methods: GET");

$slug = $routeParams[0];

// GET : Pagina informacio fitxa Detingut Presó Model (pel formulari de modificació de dades)
// URL: /api/preso_model/get/fitxaRepresaliat?id=${id}
if ($slug === 'fitxaRepressio') {
    $id = $_GET['id'];

    $db = new Database();

    $query = "SELECT 
    id,
    idPersona,
    data_empresonament,
    trasllats,
    lloc_trasllat,
    data_trasllat,
    llibertat,
    data_llibertat,
    modalitat,
    vicissituds,
    observacions
    FROM db_detinguts_model
    WHERE idPersona = :idPersona";

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

    // GET : fitxa detingut Presó Model ID
    // URL: /api/preso_model/get/fitxaId?id=${id}
} elseif ($slug === 'fitxaId') {
    $id = $_GET['id'];

    $db = new Database();

    $query = "SELECT 
    p.id,
    p.idPersona,
    p.data_empresonament,
    p.trasllats,
    p.lloc_trasllat,
    p.data_trasllat,
    p.llibertat,
    p.data_llibertat,
    m.modalitat_ca AS modalitat,
    p.vicissituds,
    p.observacions
    FROM db_detinguts_model AS p
    LEFT JOIN aux_modalitat_preso AS m ON p.modalitat = m.id
    WHERE idPersona = :idPersona";

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
