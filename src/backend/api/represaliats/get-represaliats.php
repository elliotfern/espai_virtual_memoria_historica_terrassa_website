<?php

use App\Config\Database;
use App\Utils\Response;
use App\Utils\MissatgesAPI;
use App\Config\DatabaseConnection;

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

    $query = "SELECT a.id, CONCAT(a.cognom1, ' ', a.cognom2, ', ', a.nom) AS nom_complet, a.data_naixement, a.data_defuncio, a.slug,
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

    $query = "SELECT a.id, CONCAT(a.cognom1, ' ', a.cognom2, ', ', a.nom) AS nom_complet, a.data_naixement, a.data_defuncio, a.slug,
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

    $query2 = "SELECT p.id, p.nom, p.cognom1, p.cognom2, p.categoria, p.slug
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
        $result = $db->getData($query2);

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

    $query = "SELECT a.id, CONCAT(a.cognom1, ' ', a.cognom2, ', ', a.nom) AS nom_complet, a.data_naixement, a.data_defuncio, a.slug,
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

    $query = "SELECT a.id, CONCAT(a.cognom1, ' ', a.cognom2, ', ', a.nom) AS nom_complet, a.data_naixement, a.data_defuncio, a.slug,
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

    $query = "SELECT a.id, CONCAT(a.cognom1, ' ', a.cognom2, ', ', a.nom) AS nom_complet, a.data_naixement, a.data_defuncio, a.slug,
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

    // GET : llistat represaliats - processats/empresonats
    // URL: https://memoriaterrassa.cat/api/represaliats/get/processats
} else if ($slug === 'processats') {
    $db = new Database();

    $query = "SELECT a.id, CONCAT(a.cognom1, ' ', a.cognom2, ', ', a.nom) AS nom_complet, a.data_naixement, a.data_defuncio, a.slug,
                CASE WHEN e.id IS NOT NULL THEN 'Fitxa creada' ELSE 'No' END AS es_processat
                FROM db_dades_personals AS a
                LEFT JOIN db_processats AS e ON a.id = e.idPersona
                WHERE FIND_IN_SET('6', REPLACE(REPLACE(a.categoria, '{', ''), '}', '')) > 0
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

    // GET : llistat represaliats - afusellats
    // URL: https://memoriaterrassa.cat/api/represaliats/get/afusellats
} else if ($slug === 'afusellats') {
    $db = new Database();

    $query = "SELECT a.id, CONCAT(a.cognom1, ' ', a.cognom2, ', ', a.nom) AS nom_complet, a.data_naixement, a.data_defuncio, a.slug,
                CASE WHEN e.id IS NOT NULL THEN 'Fitxa creada' ELSE 'No' END AS es_afusellat
                FROM db_dades_personals AS a
                LEFT JOIN db_afusellats AS e ON a.id = e.idPersona
                WHERE FIND_IN_SET('1', REPLACE(REPLACE(a.categoria, '{', ''), '}', '')) > 0
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

    // GET : Comptador: grup 1 - cost huma
    // URL: https://memoriaterrassa.cat/api/represaliats/get/totalCostHuma
} else if ($slug === 'totalCostHuma') {
    $db = new Database();

    $query = "SELECT COUNT(*) AS total
        FROM db_dades_personals
        WHERE categoria LIKE '%{3}%' OR categoria LIKE '%{4}%' OR categoria LIKE '%{5}%'";

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

    // GET : Comptador: grup 1 - cost huma militars - republica
    // URL: https://memoriaterrassa.cat/api/represaliats/get/totalCombatentsRepublica
} else if ($slug === 'totalCombatentsRepublica') {
    $db = new Database();

    $query = "SELECT COUNT(*) AS total
        FROM db_cost_huma_morts_front
        WHERE condicio IN (2, 4, 1)
        AND bandol = 1";

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

    // GET : Comptador: grup 1 - cost huma militars - sollevats
    // URL: https://memoriaterrassa.cat/api/represaliats/get/totalCombatentsSollevats
} else if ($slug === 'totalCombatentsSollevats') {
    $db = new Database();

    $query = "SELECT COUNT(*) AS total
        FROM db_cost_huma_morts_front
        WHERE condicio = 1
        AND bandol = 2";

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

    // GET : Comptador: grup 1 - cost huma militars - sense definir bàndol
    // URL: https://memoriaterrassa.cat/api/represaliats/get/totalCombatentsSenseDefinir
} else if ($slug === 'totalCombatentsSenseDefinir') {
    $db = new Database();

    $query = "SELECT COUNT(*) AS total
        FROM db_cost_huma_morts_front
        WHERE condicio IN (2, 4, 3, 1)
        AND bandol = 3";

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

    // GET : Comptador: grup 1 - cost huma civils - bombardeigs
    // URL: https://memoriaterrassa.cat/api/represaliats/get/totalCivilsBombardeigs
} else if ($slug === 'totalCivilsBombardeigs') {
    $db = new Database();

    $query = "SELECT COUNT(*) AS total
        FROM db_cost_huma_morts_civils
        WHERE cirscumstancies_mort IN (4, 5, 2)";

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

    // GET : Comptador: grup 1 - cost huma civils - repressio republicana
    // URL: https://memoriaterrassa.cat/api/represaliats/get/totalCivilsRepresaliaRepublicana
} else if ($slug === 'totalCivilsRepresaliaRepublicana') {
    $db = new Database();

    $query = "SELECT COUNT(*) AS total
        FROM db_dades_personals
        WHERE categoria LIKE '%{5}%'";

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

    // GET : Comptador: grup 2 - exiliats i deportats (deportats morts)
    // URL: https://memoriaterrassa.cat/api/represaliats/get/totalDeportatsMorts
} else if ($slug === 'totalDeportatsMorts') {
    $db = new Database();

    $query = "SELECT COUNT(*) AS total
        FROM db_deportats
        WHERE situacio = 1";

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

    // GET : Comptador: grup 2 - exiliats i deportats (deportats alliberats)
    // URL: https://memoriaterrassa.cat/api/represaliats/get/totalDeportatsAlliberats
} else if ($slug === 'totalDeportatsAlliberats') {
    $db = new Database();

    $query = "SELECT COUNT(*) AS total
        FROM db_deportats
        WHERE situacio = 2";

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

    // GET : Comptador: grup 3 - deportats totals
    // URL: https://memoriaterrassa.cat/api/represaliats/get/totalDeportatsTotal
} else if ($slug === 'totalDeportatsTotal') {
    $db = new Database();

    $query = "SELECT COUNT(*) AS total
        FROM db_dades_personals
        WHERE categoria LIKE '%{10,2}%'";

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

    // GET : Comptador: grup 3 - exiliats totals
    // URL: https://memoriaterrassa.cat/api/represaliats/get/totalExiliatsTotal
} else if ($slug === 'totalExiliatsTotal') {
    $db = new Database();

    $query = "SELECT COUNT(*) AS total
        FROM db_dades_personals
        WHERE categoria LIKE '%{10}%'";

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

    // GET : Comptador: grup 3 - exiliats i deportats totals
    // URL: https://memoriaterrassa.cat/api/represaliats/get/totalExiliatsDeportatsTotal
} else if ($slug === 'totalExiliatsDeportatsTotal') {
    $db = new Database();

    $query = "SELECT COUNT(*) AS total
                FROM db_dades_personals
                WHERE categoria LIKE '%{10}%' OR categoria LIKE '%{10,2}%'";

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

    // GET : Comptador: grup 1 - total represaliats
    // URL: https://memoriaterrassa.cat/api/represaliats/get/totalRepresaliats
} else if ($slug === 'totalRepresaliats') {
    $db = new Database();

    $query = "SELECT COUNT(*) AS total
            FROM db_dades_personals
            WHERE categoria LIKE '{1}%' 
            OR categoria LIKE '%{1}%' 
            OR categoria LIKE '%,{1}%' 
            OR categoria LIKE '{1,%'
            OR categoria LIKE '{6}%' 
            OR categoria LIKE '%{6}%' 
            OR categoria LIKE '%,{6}%' 
            OR categoria LIKE '{6,%'
            OR categoria LIKE '{7}%' 
            OR categoria LIKE '%{7}%' 
            OR categoria LIKE '%,{7}%' 
            OR categoria LIKE '{7,%'
            OR categoria LIKE '{11}%' 
            OR categoria LIKE '%{11}%' 
            OR categoria LIKE '%,{11}%' 
            OR categoria LIKE '{11,%'
            OR categoria LIKE '{12}%' 
            OR categoria LIKE '%{12}%' 
            OR categoria LIKE '%,{12}%' 
            OR categoria LIKE '{12,%'
            OR categoria LIKE '{13}%' 
            OR categoria LIKE '%{13}%' 
            OR categoria LIKE '%,{13}%' 
            OR categoria LIKE '{13,%'
            OR categoria LIKE '{14}%' 
            OR categoria LIKE '%{14}%' 
            OR categoria LIKE '%,{14}%' 
            OR categoria LIKE '{14,%'
            OR categoria LIKE '{15}%' 
            OR categoria LIKE '%{15}%' 
            OR categoria LIKE '%,{15}%' 
            OR categoria LIKE '{15,%'
            OR categoria LIKE '{16}%' 
            OR categoria LIKE '%{16}%' 
            OR categoria LIKE '%,{16}%' 
            OR categoria LIKE '{16,%'
            OR categoria LIKE '{17}%' 
            OR categoria LIKE '%{17}%' 
            OR categoria LIKE '%,{17}%' 
            OR categoria LIKE '{17,%';";

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

    // GET : Comptador: grup 1 - total processats
    // URL: https://memoriaterrassa.cat/api/represaliats/get/totalProcessats
} else if ($slug === 'totalProcessats') {
    $db = new Database();

    $query = "SELECT COUNT(*) AS total
                FROM db_dades_personals
                WHERE categoria LIKE '{6}%'
                OR categoria LIKE '%{6}%'
                OR categoria LIKE '%,{6}%'
                OR categoria LIKE '{6,%';";

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

    // GET : Comptador: grup 1 - total afusellats
    // URL: https://memoriaterrassa.cat/api/represaliats/get/totalAfusellats
} else if ($slug === 'totalAfusellats') {
    $db = new Database();

    $query = "SELECT COUNT(*) AS total
                FROM db_dades_personals
                WHERE categoria LIKE '{1}%'
                OR categoria LIKE '%{1}%'
                OR categoria LIKE '%,{1}%'
                OR categoria LIKE '{1,%';";

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
    // GET : grup 1 - LListat Detinguts presó model
    // URL: https://memoriaterrassa.cat/api/represaliats/get/detingutsPresoModel
} else if ($slug === 'detingutsPresoModel') {
    $db = new Database();

    $query = "SELECT a.id, CONCAT(a.cognom1, ' ', a.cognom2, ', ', a.nom) AS nom_complet, a.data_naixement, a.data_defuncio,
                CASE WHEN e.id IS NOT NULL THEN 'Fitxa creada' ELSE 'No' END AS es_PresoModel
                FROM db_dades_personals AS a
                LEFT JOIN db_detinguts_model AS e ON a.id = e.idPersona
                WHERE FIND_IN_SET('12', REPLACE(REPLACE(a.categoria, '{', ''), '}', '')) > 0
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
