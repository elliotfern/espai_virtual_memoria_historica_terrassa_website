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

// GET : Fitxa repressialiat > relació de familiars (Web publica)
// URL: https://memoriaterrassa.cat/api/familiars/get/fitxaFamiliars?id=${id}
if ($slug === "fitxaFamiliars") {

    $db = new Database();
    $id = $_GET['id'];

    $query = "SELECT 
            f.id, CONCAT_WS(' ', f.nom, f.cognom1, f.cognom2) AS nom_complet,
            f.anyNaixement,
            f.idParent,
            r.relacio_parentiu
            FROM aux_familiars AS f
            LEFT JOIN aux_familiars_relacio AS r ON f.relacio_parentiu = r.id
            WHERE f.idParent = :idParent";

    try {
        $params = [':idParent' => $id];
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

    // GET : Fitxa repressialiat > relació de familiars (Intranet)
    // URL: https://memoriaterrassa.cat/api/familiars/get/familiarsFitxa?id=${id}
} else if ($slug === "familiarsFitxa") {

    $db = new Database();
    $id = $_GET['id'];

    $query = "SELECT f.id, f.nom, f.cognom1, f.cognom2, f.anyNaixement, f.relacio_parentiu, f.idParent, d.nom AS nom_represaliat, d.cognom1 AS     cognom1_represaliat, d.cognom2 AS cognom2_represaliat
    FROM aux_familiars AS f
    LEFT JOIN db_dades_personals AS d ON f.idParent = d.id
    WHERE f.id = :id";

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
} else {
    // Si 'type', 'id' o 'token' están ausentes o 'type' no es 'user' en la URL
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Something get wrong']);
    exit();
}
