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

// GET : Pagina informacio fitxa Depurat (pel formulari de modificació de dades)
// URL: /api/depurats/get/fitxaRepresaliat?id=${id}
if ($slug === 'fitxaRepressio') {
    $id = $_GET['id'];

    $db = new Database();

    $query = "SELECT 
    id,
    idPersona,
    tipus_professional,
    professio,
    empresa AS empresa_id,
    sancio,
    observacions
    FROM db_depurats
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

    // GET : fitxa Depurat ID
    // URL: /api/depurats/get/fitxaId?id=${id}
} elseif ($slug === 'fitxaId') {
    $id = $_GET['id'];

    $db = new Database();

    $query = "SELECT 
    p.id,
    p.idPersona,
    p.tipus_professional,
    o.ofici_cat AS professio,
    em.empresa_ca AS empresa,
    p.sancio,
    p.observacions
    FROM db_depurats AS p
    LEFT JOIN aux_oficis AS o ON p.professio = o.id
    LEFT JOIN aux_empreses AS em ON p.empresa = em.id
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
