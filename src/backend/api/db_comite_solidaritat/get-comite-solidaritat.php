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

// GET : Pagina informacio fitxa Comitè solidaritat (pel formulari de modificació de dades)
// URL: /api/comite_solidaritat/get/fitxaRepresaliat?id=${id}
if ($slug === 'fitxaRepressio') {
    $id = $_GET['id'];

    $db = new Database();

    $query = "SELECT id, idPersona, advocat, motiu, any_detencio, observacions
            FROM db_detinguts_comite_solidaritat
            WHERE id = :id";

    try {
        $params = [':id' => $id];
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

    // GET : fitxa Web publica - detingut comitè solidaritat ID
    // URL: /api/comite_solidaritat/get/fitxaId?id=${id}
} elseif ($slug === 'fitxaId') {
    $id = $_GET['id'];

    $db = new Database();

    $query = "SELECT c.id, c.idPersona, c.advocat, me.motiuEmpresonament_ca AS motiu, c.any_detencio, c.observacions
        FROM db_detinguts_comite_solidaritat AS c
        LEFT JOIN aux_motius_empresonament AS me ON c.motiu = me.id
        WHERE c.idPersona = :idPersona";

    try {
        $params = [':idPersona' => $id];
        $result = $db->getData($query, $params, false);

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

    // GET : Llistat empresonaments comitè solidaritat ID
    // URL: /api/comite_solidaritat/get/empresonatId?id=${id}
} elseif ($slug === 'empresonatId') {
    $id = $_GET['id'];
    $db = new Database();

    $query = "SELECT c.id, c.idPersona, c.advocat, e.motiuEmpresonament_ca AS motiu, c.any_detencio, c.observacions
            FROM db_detinguts_comite_solidaritat AS c
            LEFT JOIN aux_motius_empresonament AS e ON c.motiu = e.id
            WHERE c.idPersona = :idPersona";

    try {
        $params = [':idPersona' => $id];
        $result = $db->getData($query, $params, false);

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
