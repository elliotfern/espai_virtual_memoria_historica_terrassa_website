<?php

use App\Config\Database;
use App\Utils\Response;
use App\Utils\MissatgesAPI;
use App\Config\DatabaseConnection;

$conn = DatabaseConnection::getConnection();

if (!$conn) {
    die("No se pudo establecer conexión a la base de datos.");
}

$slug = $routeParams[0];

// Configuración de cabeceras para aceptar JSON y responder JSON
header("Content-Type: application/json; charset=utf-8");
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

/**
 * GET: Llistat de períodes (Intranet)
 * URL: https://memoriaterrassa.cat/api/estudis/get/periodes?lang=1
 * Retorna: [{id, sort_order, nom}]
 */
if ($slug === 'periodes') {

    $db = new Database();

    $query = "SELECT
            p.id,
            p.sort_order,
            COALESCE(i_req.nom, i_ca.nom) AS nom
        FROM db_estudis_periodes p
        LEFT JOIN db_estudis_periodes_i18n i_ca
            ON i_ca.periode_id = p.id AND i_ca.lang_id = 1
        ORDER BY p.sort_order ASC, p.id ASC
    ";

    try {
        $rows = $db->getData($query);

        if (empty($rows)) $rows = [];

        Response::success(MissatgesAPI::success('get'), $rows, 200);
        return;
    } catch (PDOException $e) {
        Response::error(MissatgesAPI::error('errorBD'), [$e->getMessage()], 500);
        return;
    }
} else {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Something get wrong']);
    exit();
}
