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

// GET : Pagina informacio fitxa Detingut Guardia Urbana (pel formulari de modificació de dades)
// URL: /api/detinguts_guardia_urbana/get/fitxaRepresaliat?id=${id}
if ($slug === 'fitxaRepressio') {
    $id = $_GET['id'];

    $db = new Database();

    $query = "SELECT 
            id,
            idPersona,
            data_empresonament,
            data_sortida,
            motiu_empresonament,
            qui_ordena_detencio,
            top,
            observacions
            FROM db_detinguts_guardia_urbana
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

    // GET : fitxa detingut guardia urbana ID
    // URL: /api/detinguts_guardia_urbana/get/fitxaId?id=${id}
} elseif ($slug === 'fitxaId') {
    $id = $_GET['id'];

    $db = new Database();

    $query = "SELECT 
        gu.id,
        gu.idPersona,
        gu.data_empresonament,
        gu.data_sortida,
        me.motiuEmpresonament_ca AS motiu_empresonament,
        sr.carrec AS qui_ordena_detencio,
        sr.nom_institucio,
        srg.grup,
        gu.top,
        gu.observacions
        FROM db_detinguts_guardia_urbana AS gu
        LEFT JOIN aux_motius_empresonament AS me ON gu.motiu_empresonament = me.id
        LEFT JOIN aux_sistema_repressiu AS sr ON gu.qui_ordena_detencio = sr.id
        LEFT JOIN aux_sistema_repressiu_grup AS srg ON sr.grup_institucio = srg.id
        WHERE gu.idPersona = :idPersona";

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
