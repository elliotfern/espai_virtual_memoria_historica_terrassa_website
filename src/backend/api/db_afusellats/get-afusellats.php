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
// ruta GET => "/api/afusellats/get/fitxaRepressio&id=35"
if ($slug === "fitxaRepressio") {
    $db = new Database();
    $id = $_GET['id'];

    $query = "SELECT 
            a.id,
            a.idPersona,
            a.enterrament_lloc,
            a.data_execucio,
            a.lloc_execucio_enterrament,
            a.observacions
            FROM db_afusellats AS a
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

    // 2 Pagina informacio fitxa afusellat
    // ruta GET => "/api/afusellats/get/fitxaId&id=35"
} elseif ($slug === "fitxaId") {
    $db = new Database();
    $id = $_GET['id'];

    $query = "SELECT 
            a.id,
            a.idPersona,
            a.data_execucio,
            e.espai_cat AS lloc_enterrament,
            e2.espai_cat AS lloc_execucio,
            a.observacions,
            m.ciutat AS ciutat_enterrament,
            m2.ciutat AS ciutat_execucio
            FROM db_afusellats AS a
            LEFT JOIN aux_espai AS e ON a.enterrament_lloc = e.id
            LEFT JOIN aux_espai AS e2 ON a.lloc_execucio_enterrament = e2.id
            LEFT JOIN aux_dades_municipis AS m ON e.municipi = m.id
            LEFT JOIN aux_dades_municipis AS m2 ON e2.municipi = m2.id
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
