<?php

use App\Config\DatabaseConnection;
use App\Config\Database;
use App\Utils\Response;
use App\Utils\MissatgesAPI;

$conn = DatabaseConnection::getConnection();

if (!$conn) {
    die("No se pudo establecer conexión a la base de datos.");
}

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

// GET : Fitxa repressialiat > relació de biografies (web publica)
// URL: https://memoriaterrassa.cat/api/biografies/get/fitxaBiografia?id=${id}
if ($slug === "fitxaBiografia") {
    $id = $_GET['id'] ?? null;

    $query = "SELECT 
            b.id, b.idRepresaliat, b.biografiaCa, b.biografiaEs, b.biografiaEn, b.biografiaIt, b.biografiaPt, b.biografiaFr
            FROM db_biografies AS b
            WHERE b.idRepresaliat = :idRepresaliat";

    $result = getData2($query, ['idRepresaliat' => $id], false);
    echo json_encode($result);

    // GET : relació de biografies (intranet)
    // URL: https://memoriaterrassa.cat/api/biografies/get/biografies?id=${idRepresaliat}
    //      https://memoriaterrassa.cat/api/biografies/get/biografies?id=51
} else if ($slug === "biografies") {

    $id = $_GET['id'];
    $db = new Database();

    $query = "SELECT 
    d.nom,
    d.cognom1,
    d.cognom2,
    d.slug,
    d.id AS idRepresaliat,
    b.biografiaCa,
    b.biografiaEs,
    b.biografiaEn,
    b.id
    FROM db_dades_personals AS d
    LEFT JOIN db_biografies AS b ON d.id = b.idRepresaliat
    WHERE b.idRepresaliat = :idRepresaliat";

    try {
        $params = [':idRepresaliat' => $id];
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
