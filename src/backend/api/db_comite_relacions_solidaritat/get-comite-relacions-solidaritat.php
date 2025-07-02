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

// GET : Pagina informacio fitxa Comitè relacions solidaritat (pel formulari de modificació de dades)
// URL: /api/comite_relacions_solidaritat/get/fitxaRepresaliat?id=${id}
if ($slug === 'fitxaRepressio') {
    $id = $_GET['id'];

    $db = new Database();

    $query = "SELECT id, idPersona
            FROM db_comite_relacions_solidaritat 
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

    // GET : fitxa Web publica - detingut comitè relacions solidaritat ID
    // URL: /api/comite_relacions_solidaritat/get/fitxaId?id=${id}
} elseif ($slug === 'fitxaId') {
    $id = $_GET['id'];

    $db = new Database();

    $query = "SELECT c.id, c.idPersona
        FROM db_comite_relacions_solidaritat AS c
        WHERE c.idPersona = :idPersona";

    try {
        $params = [':idPersona' => $id];
        $result = $db->getData($query, $params, false);

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

    // GET : Llistat empresonaments comitè relacions solidaritat ID
    // URL: /api/comite_relacions_solidaritat/get/empresonatId?id=${id}
} elseif ($slug === 'empresonatId') {
    $id = $_GET['id'];
    $db = new Database();

    $query = "SELECT c.id, c.idPersona, CONCAT(me.nom, ' ', me.cognom1, ' ', me.cognom2) AS nom
            FROM  db_comite_relacions_solidaritat AS c
            LEFT JOIN db_dades_personals AS me ON c.idPersona = me.id
            WHERE c.idPersona = :idPersona";

    try {
        $params = [':idPersona' => $id];
        $result = $db->getData($query, $params, false);

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
