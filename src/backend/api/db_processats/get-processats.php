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

// GET : Pagina informacio fitxa processat
// URL: /api/processats/get/fitxaRepresaliat?id=${id}
if ($slug === 'fitxaRepressio') {
    $id = $_GET['id'];

    $db = new Database();

    $query = "SELECT 
    id,
    idPersona,
    copia_exp,
    tipus_procediment,
    tipus_judici,
    num_causa,
    data_inici_proces,
    jutge_instructor,
    secretari_instructor,
    jutjat,
    any_inicial,
    any_final,
    consell_guerra_data,
    lloc_consell_guerra,
    president_tribunal,
    defensor,
    fiscal,
    ponent,
    tribunal_vocals,
    acusacio,
    acusacio_2,
    testimoni_acusacio,
    sentencia_data,
    pena,
    sentencia,
    commutacio,
    observacions
    FROM db_processats
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
