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

// GET : llistat grup exiliats (grup 10)
// URL: https://memoriaterrassa.cat/api/represaliats/get/exiliats
if ($slug === "exiliats") {
    $db = new Database();

    $query = "SELECT a.id, CONCAT(a.cognom1, ' ', a.cognom2, ', ', a.nom) AS nom_complet, a.data_naixement, a.data_defuncio,
                CASE WHEN e.id IS NOT NULL THEN 'Fitxa creada' ELSE 'No' END AS es_exiliat
                FROM db_dades_personals AS a
                LEFT JOIN db_exiliats AS e ON a.id = e.idPersona
                WHERE FIND_IN_SET('10', REPLACE(REPLACE(a.categoria, '{', ''), '}', '')) > 0
                ORDER BY a.cognom1 ASC;";

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

    // GET : llistat grup deportats (grup 10)
    // URL: https://memoriaterrassa.cat/api/represaliats/get/deportats
} else if ($slug === "deportats") {
    $db = new Database();

    $query = "SELECT a.id, CONCAT(a.cognom1, ' ', a.cognom2, ', ', a.nom) AS nom_complet, a.data_naixement, a.data_defuncio,
                CASE WHEN e.id IS NOT NULL THEN 'Fitxa creada' ELSE 'No' END AS es_deportat
                FROM db_dades_personals AS a
                LEFT JOIN db_deportats AS e ON a.id = e.idPersona
                WHERE FIND_IN_SET('2', REPLACE(REPLACE(a.categoria, '{', ''), '}', '')) > 0
                ORDER BY a.cognom1 ASC;";

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

    // GET : llistat de represaliats duplicats a la base de dades
    // URL: https://memoriaterrassa.cat/api/represaliats/get/duplicats
} else if ($slug === "duplicats") {
    $db = new Database();

    $query = "SELECT id, categoria, cognom1, COUNT(*) AS total
        FROM db_dades_personals
        WHERE cognom1 LIKE '%,%'
        GROUP BY cognom1
        HAVING COUNT(*) > 1;";

    $query2 = "SELECT p.id, p.nom, p.cognom1, p.cognom2, p.categoria
        FROM db_dades_personals p
        JOIN (
        SELECT nom, cognom1, cognom2
        FROM db_dades_personals
        GROUP BY nom, cognom1, cognom2
        HAVING COUNT(*) > 1
        ) dups
        ON p.nom = dups.nom AND p.cognom1 = dups.cognom1 AND p.cognom2 = dups.cognom2
        ORDER BY p.nom, p.cognom1, p.cognom2;";

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

    // GET : llistat cost humà - morts al front
    // URL: https://memoriaterrassa.cat/api/represaliats/get/mortsFront
} else if ($slug === 'mortsFront') {
    $db = new Database();

    $query = "SELECT a.id, CONCAT(a.cognom1, ' ', a.cognom2, ', ', a.nom) AS nom_complet, a.data_naixement, a.data_defuncio,
                CASE WHEN e.id IS NOT NULL THEN 'Fitxa creada' ELSE 'No' END AS es_mortFront
                FROM db_dades_personals AS a
                LEFT JOIN db_cost_huma_morts_front AS e ON a.id = e.idPersona
                WHERE FIND_IN_SET('3', REPLACE(REPLACE(a.categoria, '{', ''), '}', '')) > 0
                ORDER BY a.cognom1 ASC;";

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

    // GET : llistat cost humà - morts civils
    // URL: https://memoriaterrassa.cat/api/represaliats/get/mortsCivils
} else if ($slug === 'mortsCivils') {
    $db = new Database();

    $query = "SELECT a.id, CONCAT(a.cognom1, ' ', a.cognom2, ', ', a.nom) AS nom_complet, a.data_naixement, a.data_defuncio,
                CASE WHEN e.id IS NOT NULL THEN 'Fitxa creada' ELSE 'No' END AS es_mortCivil
                FROM db_dades_personals AS a
                LEFT JOIN db_cost_huma_morts_civils AS e ON a.id = e.idPersona
                WHERE FIND_IN_SET('4', REPLACE(REPLACE(a.categoria, '{', ''), '}', '')) > 0
                ORDER BY a.cognom1 ASC;";

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

    // GET : llistat cost humà - represalia republicana
    // URL: https://memoriaterrassa.cat/api/represaliats/get/represaliaRepublicana
} else if ($slug === 'represaliaRepublicana') {
    $db = new Database();

    $query = "SELECT a.id, CONCAT(a.cognom1, ' ', a.cognom2, ', ', a.nom) AS nom_complet, a.data_naixement, a.data_defuncio,
                CASE WHEN e.id IS NOT NULL THEN 'Fitxa creada' ELSE 'No' END AS es_represalia
                FROM db_dades_personals AS a
                LEFT JOIN db_cost_huma_morts_civils AS e ON a.id = e.idPersona
                WHERE FIND_IN_SET('4', REPLACE(REPLACE(a.categoria, '{', ''), '}', '')) > 0
                ORDER BY a.cognom1 ASC;";

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
}
