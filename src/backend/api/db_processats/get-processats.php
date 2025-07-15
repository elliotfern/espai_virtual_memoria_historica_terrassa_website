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
    data_detencio,
    lloc_detencio,
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
    observacions,
    anyDetingut
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

    // GET : fitxa processat ID
    // URL: /api/processats/get/fitxaId?id=${id}
} elseif ($slug === 'fitxaId') {
    $id = $_GET['id'];

    $db = new Database();

    $query = "SELECT 
    p.id,
    p.idPersona,
    p.data_detencio,
    m2.ciutat AS lloc_detencio,
    p.copia_exp,
    pj.procediment_ca AS tipus_procediment,
    tp.tipusJudici_ca AS tipus_judici,
    p.num_causa,
    p.data_inici_proces,
    p.jutge_instructor,
    p.secretari_instructor,
    j.jutjat_ca AS jutjat,
    p.any_inicial,
    p.any_final,
    p.consell_guerra_data,
    m.ciutat AS lloc_consell_guerra,
    p.president_tribunal,
    p.defensor,
    p.fiscal,
    p.ponent,
    p.tribunal_vocals,
    a1.acusacio_ca AS acusacio,
    a2.acusacio_ca AS acusacio_2,
    p.testimoni_acusacio,
    p.sentencia_data,
    pe.pena_ca AS pena,
    se.sentencia_ca AS sentencia,
    p.commutacio,
    p.observacions,
    p.anyDetingut
    FROM db_processats AS p
    LEFT JOIN aux_procediment_judicial AS pj ON p.tipus_procediment = pj.id
    LEFT JOIN aux_tipus_judici AS tp ON p.tipus_judici = tp.id
    LEFT JOIN aux_jutjats AS j ON p.jutjat = j.id
    LEFT JOIN aux_dades_municipis AS m ON p.lloc_consell_guerra = m.id
    LEFT JOIN aux_acusacions AS a1 ON p.acusacio = a1.id
    LEFT JOIN aux_acusacions AS a2 ON p.acusacio_2 = a2.id
    LEFT JOIN aux_sentencies AS se ON p.sentencia = se.id
    LEFT JOIN aux_penes AS pe ON p.pena = pe.id
    LEFT JOIN aux_dades_municipis AS m2 ON p.lloc_detencio = m2.id
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
