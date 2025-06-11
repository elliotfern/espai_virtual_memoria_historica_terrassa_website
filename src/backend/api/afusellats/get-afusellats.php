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

// 1 Pagina informacio fitxa afusellat
// ruta GET => "/api/afusellats/get/fitxa&id=35"
if ($slug === "fitxa") {
    $db = new Database();
    $id = $_GET['id'];

    $query = "SELECT 
            pj.procediment_cat, 
            pj.id AS procediment_id, 
            a.num_causa, 
            a.data_inici_proces, 
            a.jutge_instructor, 
            a.secretari_instructor, 
            j.jutjat_cat AS jutjat, 
            j.id AS jutjat_id, 
            a.any_inicial, 
            a.consell_guerra_data, 
            a.president_tribunal, 
            a.defensor, 
            a.fiscal, 
            a.ponent, 
            a.tribunal_vocals, 
            acu.acusacio_cat AS acusacio, 
            acu.id AS acusacio_id, 
            acu2.acusacio_cat AS acusacio2, 
            acu2.id AS acusacio_id2, 
            a.testimoni_acusacio, 
            a.sentencia_data, 
            sen.sentencia_cat AS sentencia, 
            sen.id AS sentencia_id, 
            a.data_sentencia, 
            a.data_execucio,
            a.ref_num_arxiu, 
            a.font_1, 
            a.font_2, 
            a.familiars, 
            a.observacions
            FROM db_afusellats AS a
            LEFT JOIN aux_procediment_judicial AS pj ON a.procediment = pj.id
            LEFT JOIN aux_jutjats as j ON a.jutjat = j.id
            LEFT JOIN aux_acusacions AS acu ON a.acusacio = acu.id
            LEFT JOIN aux_acusacions AS acu2 ON a.acusacio_2 = acu2.id
            LEFT JOIN aux_sentencies AS sen ON a.sentencia = sen.id
            WHERE a.idPersona = :idPersona";
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
} else {
    // Si 'type', 'id' o 'token' están ausentes o 'type' no es 'user' en la URL
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Something get wrong']);
    exit();
}
