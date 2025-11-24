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

// GET : Llistat missatges rebuts
// URL: https://memoriaterrassa.cat/api/form_contacte/get/missatgesRebuts
if ($slug === "missatgesRebuts") {

    $db = new Database();

    $query = "SELECT 
	        id, nomCognoms, email, telefon, missatge, form_ip, form_user_agent, dataEnviament
            FROM db_form_contacte
            ORDER BY id DESC";

    try {
        $result = $db->getData($query);

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
    // URL: https://memoriaterrassa.cat/api/form_contacte/get/missatgeId?id=${id}
} else if ($slug === "missatgeId") {

    $db = new Database();
    $id = $_GET['id'];

    $query = "SELECT 
	        id, nomCognoms, email, telefon, missatge, form_ip, form_user_agent, dataEnviament
            FROM db_form_contacte
            WHERE id = :id";

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
