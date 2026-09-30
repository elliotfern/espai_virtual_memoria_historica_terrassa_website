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
header("Access-Control-Allow-Methods: GET");

$slug = $routeParams[0];

// GET : Llistat activitats agenda
// URL: /api/agenda/get/llistatActivitats
if ($slug === 'llistatActivitats') {

    $db = new Database();

    $query = "SELECT
                LOWER(HEX(a.id)) AS id,
                a.titol,
                a.slug,
                a.descripcio,
                a.data,
                a.lloc,
                a.adreca,
                a.imatge,
                a.actiu,
                i.nomArxiu
              FROM db_agenda AS a
              LEFT JOIN aux_imatges AS i ON a.imatge = i.id
              ORDER BY a.data DESC";

    try {
        $result = $db->getData($query, [], false);

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
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Something get wrong']);
    exit();
}
