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
        $result = $db->getData($query, $params);

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

    // GET : fitxa detingut/consell de guerra Intranet (per ID)
    // URL: /api/processats/get/fitxaIntranetId?id=${id}
} else if ($slug === 'fitxaIntranetId') {
    $id = $_GET['id'];

    $db = new Database();

    $query = "SELECT 
    p.id,
    p.idPersona,
    p.data_detencio,
    p.lloc_detencio,
    p.copia_exp,
    p.tipus_procediment,
    p.tipus_judici,
    p.num_causa,
    p.num_registre,
    p.data_inici_proces,
    p.jutge_instructor,
    p.secretari_instructor,
    p.jutjat,
    p.anyDetingut,
    p.any_inicial,
    p.any_final,
    p.consell_guerra_data,
    p.lloc_consell_guerra,
    p.president_tribunal,
    p.defensor,
    p.fiscal,
    p.ponent,
    p.tribunal_vocals,
    p.acusacio,
    p.acusacio_2,
    p.testimoni_acusacio,
    p.sentencia_data,
    p.sentencia,
    p.pena,
    p.commutacio,
    p.observacions
    FROM db_processats AS p
    WHERE p.id = :id";

    $queryJutges = "SELECT jutge_id
    FROM db_processats_jutges_instructors
    WHERE processat_id = :id";

    $querySecretaris = "SELECT secretari_id
    FROM db_processats_secretaris_instructors
    WHERE processat_id = :id";

    $queryPresident = "SELECT president_id
    FROM db_processats_presidents_tribunal
    WHERE processat_id = :id
    ";

    $queryDefensors = "SELECT defensor_id
    FROM db_processats_defensors
    WHERE processat_id = :id
    ";

    $queryFiscals = "SELECT fiscal_id
    FROM db_processats_fiscals
    WHERE processat_id = :id
    ";

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

        if (isset($result[0])) {
            $result = $result[0];
        }

        $jutges = $db->getData(
            $queryJutges,
            [':id' => $id],
            false
        );

        $secretaris = $db->getData(
            $querySecretaris,
            [':id' => $id],
            false
        );

        $presidents = $db->getData($queryPresident, [':id' => $id], false);
        $defensors = $db->getData($queryDefensors, [':id' => $id], false);
        $fiscals = $db->getData($queryFiscals, [':id' => $id], false);

        // array de IDs
        $result['jutges_instructors'] = array_map(
            fn($row) => (int)($row['jutge_id'] ?? 0),
            $jutges ?: []
        );

        $result['secretaris_instructors'] = array_map(
            fn($row) => (int)($row['secretari_id'] ?? 0),
            $secretaris ?: []
        );

        $result['presidents_tribunals'] = array_map(
            fn($row) => (int)($row['president_id'] ?? 0),
            $presidents ?: []
        );

        $result['defensors'] = array_map(
            fn($row) => (int)($row['defensor_id'] ?? 0),
            $defensors ?: []
        );

        $result['fiscals'] = array_map(
            fn($row) => (int)($row['fiscal_id'] ?? 0),
            $fiscals ?: []
        );

        // legacy temporal
        $result['jutge_instructor_old'] = $result['jutge_instructor'] ?? null;
        $result['secretari_instructor_old'] = $result['secretari_instructor'] ?? null;
        $result['president_tribunal_old'] = $result['president_tribunal'] ?? null;
        $result['defensor_old'] = $result['defensor'] ?? null;
        $result['fiscal_old'] = $result['fiscal'] ?? null;

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
