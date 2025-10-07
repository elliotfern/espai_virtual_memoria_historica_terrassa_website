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
header("Access-Control-Allow-Origin: https://memoriaterrassa.cat");
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

// GET : llistat complet deportats
// URL: https://memoriaterrassa.cat/api/api/deportats/get/llistatComplet
if ($slug === "llistatComplet") {

    $query = "SELECT a.id, dp.cognom1, dp.cognom2, dp.nom, a.copia_exp, dp.data_naixement, dp.edat, dp.data_defuncio,
            e1.ciutat, e1.comarca, e1.provincia, e1.comunitat, e1.pais, e2.ciutat AS ciutat2, e2.comarca AS comarca2, e2.provincia AS provincia2, e2.comunitat AS comunitat2, e2.pais AS pais2, dp.categoria
            FROM db_afusellats AS a
            LEFT JOIN db_dades_personals AS dp ON a.idPersona = dp.id
            LEFT JOIN aux_dades_municipis AS e1 ON dp.municipi_naixement = e1.id
            LEFT JOIN aux_dades_municipis AS e2 ON dp.municipi_defuncio = e2.id
            ORDER BY dp.cognom1 ASC";

    $result = getData2($query);
    echo json_encode($result);

    // 2) Pagina informacio fitxa deportat - web publica
    // ruta GET => "/api/deportats/get/fitxaId?id=${id}
} else if ($slug === "fitxaId") {
    $db = new Database();
    $id = $_GET['id'] ?? null;

    $query = "SELECT 
             	d.id,
                d.idPersona,
                d.data_alliberament,
                sd.situacio_ca AS situacio,
                sd.id AS situacioId,
                COALESCE(m1.ciutat_ca, m1.ciutat) AS ciutat_mort_alliberament,
                e1.estat_ca AS estat_mort_allibertament,

                d.situacioFranca AS situacioFranca_id,
                tp1.tipus_preso_ca AS tipusPresoFranca,
                p1.nom AS situacioFrancaNom,
                COALESCE(m3.ciutat_ca, m3.ciutat) AS ciutat_situacioFranca_preso,
                d.situacioFranca_sortida,
                d.situacioFranca_num_matricula,
                d.situacioFrancaObservacions,

                tp2.tipus_preso_ca AS tipusPreso1,
                p2.nom AS nomPreso1,
                COALESCE(m4.ciutat_ca, m4.ciutat) AS ciutatPreso1,
                d.presoClasificacioData1,
                d.presoClasificacioDataEntrada1,
                d.presoClasificacioMatr1,
                e2.estat_ca AS estat_preso1,

                tp3.tipus_preso_ca AS tipusPreso2,
                p3.nom AS nomPreso2,
                COALESCE(m5.ciutat_ca, m5.ciutat) AS ciutatPreso2,
                d.presoClasificacioData2,
                d.presoClasificacioDataEntrada2,
                d.presoClasificacioMatr2,
                e3.estat_ca AS estat_preso2,

                pce1.tipus_preso_ca AS tipusCamp1,
                c1.nom_ca AS nomCamp1,
                mc1.ciutat AS ciutatCamp1,
                d.deportacio_data_entrada,
                d.deportacio_num_matricula,

                pce2.tipus_preso_ca AS tipusCamp2,
                c2.nom_ca AS nomCamp2,
                mc2.ciutat AS ciutatCamp2,
                d.deportacio_data_entrada_subcamp,
                d.deportacio_nom_matricula_subcamp,
                d.deportacio_observacions
            FROM db_deportats AS d
            LEFT JOIN aux_situacions_deportats AS sd ON d.situacio = sd.id
            LEFT JOIN aux_dades_municipis AS m1 ON d.lloc_mort_alliberament = m1.id
            LEFT JOIN aux_dades_municipis_estat AS e1 ON m1.estat = e1.id

            LEFT JOIN aux_deportacio_preso AS p1 ON d.situacioFranca = p1.id
            LEFT JOIN aux_tipus_presons AS tp1 ON p1.tipus = tp1.id
            LEFT JOIN aux_dades_municipis AS m3 ON p1.municipi = m3.id

            LEFT JOIN aux_deportacio_preso AS p2 ON p2.id = d.presoClasificacio1
            LEFT JOIN aux_tipus_presons AS tp2 ON p2.tipus = tp2.id
            LEFT JOIN aux_dades_municipis AS m4 ON p2.municipi = m4.id
            LEFT JOIN aux_dades_municipis_estat AS e2 ON m4.estat = e2.id

            LEFT JOIN aux_deportacio_preso AS p3 ON p3.id = d.presoClasificacio2
            LEFT JOIN aux_tipus_presons AS tp3 ON p3.tipus = tp3.id
            LEFT JOIN aux_dades_municipis AS m5 ON p3.municipi = m5.id
            LEFT JOIN aux_dades_municipis_estat AS e3 ON m5.estat = e3.id

            LEFT JOIN aux_camps_concentracio AS c1 ON d.deportacio_camp = c1.id
            LEFT JOIN aux_tipus_presons AS pce1 ON c1.tipus = pce1.id
            LEFT JOIN aux_dades_municipis AS mc1 ON c1.municipi = mc1.id

            LEFT JOIN aux_camps_concentracio AS c2 ON d.deportacio_subcamp = c2.id
            LEFT JOIN aux_tipus_presons AS pce2 ON c2.tipus = pce2.id
            LEFT JOIN aux_dades_municipis AS mc2 ON c2.municipi = mc2.id
            WHERE d.idPersona = :idPersona";

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

    // 3) Pagina fitxa repressió deportat
    // ruta GET => "/api/deportats/get/fitxaRepressio?id=35"
} else if ($slug === "fitxaRepressio") {
    $id = $_GET['id'] ?? null;

    $query = "SELECT 
            d.id,
            d.idPersona,
            d.situacio,
            d.data_alliberament,
            d.lloc_mort_alliberament,
            d.situacioFranca,
            d.situacioFranca_sortida,
            d.situacioFranca_num_matricula,
            d.situacioFrancaObservacions,
            d.presoClasificacio1,
            d.presoClasificacioData1,
            d.presoClasificacioDataEntrada1,
            d.presoClasificacioMatr1,
            d.presoClasificacio2,
            d.presoClasificacioData2,
            d.presoClasificacioDataEntrada2,
            d.presoClasificacioMatr2,
            d.deportacio_camp,
            d.deportacio_data_entrada,
            d.deportacio_num_matricula,
            d.deportacio_subcamp,
            d.deportacio_data_entrada_subcamp,
            d.deportacio_nom_matricula_subcamp,
            d.deportacio_observacions
            FROM db_deportats AS d
            WHERE d.idPersona = :idPersona";

    $result = getData2($query, ['idPersona' => $id], true);
    echo json_encode($result);

    // 2) Informacio Preso camp detencio
    // ruta GET => "/api/deportats/get/campDetencio?id=${id}
} else if ($slug === "campDetencio") {
    $db = new Database();
    $id = $_GET['id'] ?? null;

    $query = "SELECT id, tipus, nom, municipi
            FROM aux_deportacio_preso
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

    // 2) Informacio Preso camp detencio
    // ruta GET => "/api/deportats/get/llistatCampsPresons
} else if ($slug === "llistatCampsPresons") {
    $db = new Database();

    $query = "SELECT d.id, d.nom, m.ciutat, t.tipus_preso_ca
            FROM aux_deportacio_preso AS d
            LEFT JOIN aux_dades_municipis AS m ON d.municipi = m.id
            LEFT JOIN aux_tipus_presons AS t ON d.tipus = t.id
            ORDER BY d.nom";
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

    // 2) Informacio Llistat camps de concentracio
    // ruta GET => "/api/deportats/get/llistatCamps
} else if ($slug === "llistatCamps") {
    $db = new Database();

    $query = "SELECT d.id, d.nom_ca, m.ciutat, t.tipus_preso_ca
            FROM aux_camps_concentracio AS d
            LEFT JOIN aux_dades_municipis AS m ON d.municipi = m.id
            LEFT JOIN aux_tipus_presons AS t ON d.tipus = t.id
            ORDER BY d.nom";
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


    // 2) Informacio Camp concentració ID
    // ruta GET => "/api/deportats/get/campConcentracio?id=${id}
} else if ($slug === "campConcentracio") {
    $db = new Database();
    $id = $_GET['id'] ?? null;

    $query = "SELECT id, tipus, nom, municipi
            FROM  aux_camps_concentracio
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
