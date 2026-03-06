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
            i_ca.nom AS nom
        FROM db_estudis_periodes p
        LEFT JOIN db_estudis_periodes_i18n i_ca ON i_ca.periode_id = p.id AND i_ca.lang = 'ca'
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

    /**
     * GET: Fitxa d'un període per ID (Intranet)
     * URL: /api/estudis/get/periodeId?id=3
     */
} else if ($slug === 'periodeId') {

    // ✅ Validación ID
    if (!isset($_GET['id']) || !ctype_digit((string)$_GET['id'])) {
        Response::error(
            MissatgesAPI::error('parametres'),
            ['ID no vàlid'],
            400
        );
        return;
    }

    $id = (int)$_GET['id'];
    $db = new Database();

    $query = "SELECT
            p.id,
            p.sort_order,
            MAX(CASE WHEN pi.lang = 'ca' THEN pi.nom END) AS nom_ca,
            MAX(CASE WHEN pi.lang = 'es' THEN pi.nom END) AS nom_es,
            MAX(CASE WHEN pi.lang = 'en' THEN pi.nom END) AS nom_en,
            MAX(CASE WHEN pi.lang = 'fr' THEN pi.nom END) AS nom_fr,
            MAX(CASE WHEN pi.lang = 'it' THEN pi.nom END) AS nom_it,
            MAX(CASE WHEN pi.lang = 'pt' THEN pi.nom END) AS nom_pt
        FROM db_estudis_periodes p
        LEFT JOIN db_estudis_periodes_i18n pi
            ON pi.periode_id = p.id
        WHERE p.id = :id
        GROUP BY p.id, p.sort_order
        LIMIT 1";

    try {
        $result = $db->getData($query, [':id' => $id], true);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;
        }

        // Normalizamos nulls por si alguna traducción aún no existe
        $payload = [
            'id' => (int)$result['id'],
            'sort_order' => isset($result['sort_order']) ? (int)$result['sort_order'] : 0,
            'nom_ca' => $result['nom_ca'] ?? '',
            'nom_es' => $result['nom_es'] ?? '',
            'nom_en' => $result['nom_en'] ?? '',
            'nom_fr' => $result['nom_fr'] ?? '',
            'nom_it' => $result['nom_it'] ?? '',
            'nom_pt' => $result['nom_pt'] ?? '',
        ];

        Response::success(
            MissatgesAPI::success('get'),
            $payload,
            200
        );
        return;
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
        return;
    }

    /**
     * GET: Llistat de territoris (Intranet)
     * URL: /api/estudis/get/territoris?lang=ca
     * Retorna: [{id, sort_order, nom}]
     */
} else if ($slug === 'territoris') {

    // lang opcional, default = ca
    $lang = isset($_GET['lang']) ? trim((string)$_GET['lang']) : 'ca';

    $langsAllowed = ['ca', 'es', 'en', 'fr', 'it', 'pt'];
    if (!in_array($lang, $langsAllowed, true)) {
        $lang = 'ca';
    }

    $db = new Database();

    $query = "SELECT
            t.id,
            t.sort_order,
            COALESCE(i_req.nom, i_ca.nom) AS nom
        FROM db_estudis_territoris t
        LEFT JOIN db_estudis_territoris_i18n i_req
            ON i_req.territori_id = t.id AND i_req.lang = :lang
        LEFT JOIN db_estudis_territoris_i18n i_ca
            ON i_ca.territori_id = t.id AND i_ca.lang = 'ca'
        ORDER BY t.sort_order ASC, t.id ASC
    ";

    try {
        $rows = $db->getData($query, [':lang' => $lang]);

        if (empty($rows)) {
            $rows = [];
        }

        Response::success(
            MissatgesAPI::success('get'),
            $rows,
            200
        );
        return;
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
        return;
    }
} else {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Something get wrong']);
    exit();
}
