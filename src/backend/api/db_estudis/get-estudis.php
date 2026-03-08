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

    /**
     * GET: Fitxa d'un territori per ID (Intranet)
     * URL: /api/estudis/get/territoriId?id=3
     */
} else if ($slug === 'territoriId') {

    // Validación ID
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
            t.id,
            t.sort_order,
            MAX(CASE WHEN ti.lang = 'ca' THEN ti.nom END) AS nom_ca,
            MAX(CASE WHEN ti.lang = 'es' THEN ti.nom END) AS nom_es,
            MAX(CASE WHEN ti.lang = 'en' THEN ti.nom END) AS nom_en,
            MAX(CASE WHEN ti.lang = 'fr' THEN ti.nom END) AS nom_fr,
            MAX(CASE WHEN ti.lang = 'it' THEN ti.nom END) AS nom_it,
            MAX(CASE WHEN ti.lang = 'pt' THEN ti.nom END) AS nom_pt
        FROM db_estudis_territoris t
        LEFT JOIN db_estudis_territoris_i18n ti
            ON ti.territori_id = t.id
        WHERE t.id = :id
        GROUP BY t.id, t.sort_order
        LIMIT 1
    ";

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
     * GET: Llistat de tipus (Intranet)
     * URL: /api/estudis/get/tipus?lang=ca
     * Retorna: [{id, sort_order, nom}]
     */
} else if ($slug === 'tipus') {


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
        FROM db_estudis_tipus t
        LEFT JOIN db_estudis_tipus_i18n i_req
            ON i_req.tipus_id = t.id AND i_req.lang = :lang
        LEFT JOIN db_estudis_tipus_i18n i_ca
            ON i_ca.tipus_id = t.id AND i_ca.lang = 'ca'
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

    /**
     * GET: Fitxa d'un tipus per ID (Intranet)
     * URL: /api/estudis/get/tipusId?id=3
     */
} else if ($slug === 'tipusId') {

    // Auth requerida
    $userId = getAuthenticatedUserId();
    if (!$userId) {
        Response::error(
            MissatgesAPI::error('no_autenticat'),
            [],
            401
        );
        return;
    }

    // Validación ID
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

    $query = "
        SELECT
            t.id,
            t.sort_order,
            MAX(CASE WHEN ti.lang = 'ca' THEN ti.nom END) AS nom_ca,
            MAX(CASE WHEN ti.lang = 'es' THEN ti.nom END) AS nom_es,
            MAX(CASE WHEN ti.lang = 'en' THEN ti.nom END) AS nom_en,
            MAX(CASE WHEN ti.lang = 'fr' THEN ti.nom END) AS nom_fr,
            MAX(CASE WHEN ti.lang = 'it' THEN ti.nom END) AS nom_it,
            MAX(CASE WHEN ti.lang = 'pt' THEN ti.nom END) AS nom_pt
        FROM db_estudis_tipus t
        LEFT JOIN db_estudis_tipus_i18n ti
            ON ti.tipus_id = t.id
        WHERE t.id = :id
        GROUP BY t.id, t.sort_order
        LIMIT 1
    ";

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
     * GET: Llistat d'estudis (Intranet)
     * URL: /api/estudis/get/estudis?lang=ca
     */
} else if ($slug === 'estudis') {

    // Auth requerida
    $userId = getAuthenticatedUserId();
    if (!$userId) {
        Response::error(
            MissatgesAPI::error('no_autenticat'),
            [],
            401
        );
        return;
    }

    // Idioma opcional, per defecte català
    $lang = isset($_GET['lang']) ? trim((string)$_GET['lang']) : 'ca';
    $langsAllowed = ['ca', 'es', 'en', 'fr', 'it', 'pt'];

    if (!in_array($lang, $langsAllowed, true)) {
        $lang = 'ca';
    }

    $db = new Database();

    $query = "SELECT
            e.id,
            e.slug,
            e.any_publicacio,

            COALESCE(ei_req.titol, ei_ca.titol) AS titol,
            COALESCE(pi_req.nom, pi_ca.nom) AS periode,
            COALESCE(ti_req.nom, ti_ca.nom) AS territori,
            COALESCE(tyi_req.nom, tyi_ca.nom) AS tipus,

            GROUP_CONCAT(DISTINCT u.nom ORDER BY ea.sort_order ASC, u.nom ASC SEPARATOR ', ') AS autors

        FROM db_estudis e

        LEFT JOIN db_estudis_i18n ei_req
            ON ei_req.estudi_id = e.id AND ei_req.lang = :lang
        LEFT JOIN db_estudis_i18n ei_ca
            ON ei_ca.estudi_id = e.id AND ei_ca.lang = 'ca'

        LEFT JOIN db_estudis_periodes_i18n pi_req
            ON pi_req.periode_id = e.periode_id AND pi_req.lang = :lang
        LEFT JOIN db_estudis_periodes_i18n pi_ca
            ON pi_ca.periode_id = e.periode_id AND pi_ca.lang = 'ca'

        LEFT JOIN db_estudis_territoris_i18n ti_req
            ON ti_req.territori_id = e.territori_id AND ti_req.lang = :lang
        LEFT JOIN db_estudis_territoris_i18n ti_ca
            ON ti_ca.territori_id = e.territori_id AND ti_ca.lang = 'ca'

        LEFT JOIN db_estudis_tipus_i18n tyi_req
            ON tyi_req.tipus_id = e.tipus_id AND tyi_req.lang = :lang
        LEFT JOIN db_estudis_tipus_i18n tyi_ca
            ON tyi_ca.tipus_id = e.tipus_id AND tyi_ca.lang = 'ca'

        LEFT JOIN db_estudis_autors ea
            ON ea.estudi_id = e.id
        LEFT JOIN db_estudis_autors_noms u
            ON u.id = ea.autor_id

        GROUP BY
            e.id,
            e.slug,
            e.any_publicacio,
            ei_req.titol,
            ei_ca.titol,
            pi_req.nom,
            pi_ca.nom,
            ti_req.nom,
            ti_ca.nom,
            tyi_req.nom,
            tyi_ca.nom

        ORDER BY
            e.any_publicacio DESC,
            titol ASC,
            e.id DESC
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

    /**
     * GET: Fitxa d'un estudi per ID (Intranet)
     * URL: /api/estudis/get/estudiId?id=3
     */
} else if ($slug === 'estudiId') {

    // Validación ID
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
            e.id,
            e.slug,
            e.any_publicacio,
            e.periode_id,
            e.territori_id,
            e.tipus_id,

            MAX(CASE WHEN ei.lang = 'ca' THEN ei.titol END) AS titol_ca,
            MAX(CASE WHEN ei.lang = 'ca' THEN ei.resum END) AS resum_ca,
            MAX(CASE WHEN ei.lang = 'ca' THEN ei.url_document END) AS url_document_ca,

            MAX(CASE WHEN ei.lang = 'es' THEN ei.titol END) AS titol_es,
            MAX(CASE WHEN ei.lang = 'es' THEN ei.resum END) AS resum_es,
            MAX(CASE WHEN ei.lang = 'es' THEN ei.url_document END) AS url_document_es,

            MAX(CASE WHEN ei.lang = 'en' THEN ei.titol END) AS titol_en,
            MAX(CASE WHEN ei.lang = 'en' THEN ei.resum END) AS resum_en,
            MAX(CASE WHEN ei.lang = 'en' THEN ei.url_document END) AS url_document_en,

            MAX(CASE WHEN ei.lang = 'fr' THEN ei.titol END) AS titol_fr,
            MAX(CASE WHEN ei.lang = 'fr' THEN ei.resum END) AS resum_fr,
            MAX(CASE WHEN ei.lang = 'fr' THEN ei.url_document END) AS url_document_fr,

            MAX(CASE WHEN ei.lang = 'it' THEN ei.titol END) AS titol_it,
            MAX(CASE WHEN ei.lang = 'it' THEN ei.resum END) AS resum_it,
            MAX(CASE WHEN ei.lang = 'it' THEN ei.url_document END) AS url_document_it,

            MAX(CASE WHEN ei.lang = 'pt' THEN ei.titol END) AS titol_pt,
            MAX(CASE WHEN ei.lang = 'pt' THEN ei.resum END) AS resum_pt,
            MAX(CASE WHEN ei.lang = 'pt' THEN ei.url_document END) AS url_document_pt

        FROM db_estudis e
        LEFT JOIN db_estudis_i18n ei
            ON ei.estudi_id = e.id
        WHERE e.id = :id
        GROUP BY
            e.id,
            e.slug,
            e.any_publicacio,
            e.periode_id,
            e.territori_id,
            e.tipus_id
        LIMIT 1
    ";

    $queryAutors = "
        SELECT autor_id
        FROM db_estudis_autors
        WHERE estudi_id = :id
        ORDER BY sort_order ASC, autor_id ASC
    ";

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

        $rowsAutors = $db->getData($queryAutors, [':id' => $id]);
        $autors = [];

        if (!empty($rowsAutors)) {
            foreach ($rowsAutors as $row) {
                $autors[] = (int)$row['autor_id'];
            }
        }

        $payload = [
            'id' => (int)$result['id'],
            'slug' => $result['slug'] ?? '',
            'any_publicacio' => isset($result['any_publicacio']) ? (int)$result['any_publicacio'] : null,
            'periode_id' => isset($result['periode_id']) ? (int)$result['periode_id'] : 0,
            'territori_id' => isset($result['territori_id']) ? (int)$result['territori_id'] : 0,
            'tipus_id' => isset($result['tipus_id']) ? (int)$result['tipus_id'] : 0,
            'autors' => $autors,

            'titol_ca' => $result['titol_ca'] ?? '',
            'resum_ca' => $result['resum_ca'] ?? '',
            'url_document_ca' => $result['url_document_ca'] ?? '',

            'titol_es' => $result['titol_es'] ?? '',
            'resum_es' => $result['resum_es'] ?? '',
            'url_document_es' => $result['url_document_es'] ?? '',

            'titol_en' => $result['titol_en'] ?? '',
            'resum_en' => $result['resum_en'] ?? '',
            'url_document_en' => $result['url_document_en'] ?? '',

            'titol_fr' => $result['titol_fr'] ?? '',
            'resum_fr' => $result['resum_fr'] ?? '',
            'url_document_fr' => $result['url_document_fr'] ?? '',

            'titol_it' => $result['titol_it'] ?? '',
            'resum_it' => $result['resum_it'] ?? '',
            'url_document_it' => $result['url_document_it'] ?? '',

            'titol_pt' => $result['titol_pt'] ?? '',
            'resum_pt' => $result['resum_pt'] ?? '',
            'url_document_pt' => $result['url_document_pt'] ?? '',
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
     * GET: Llistat públic d'estudis
     * URL: /api/estudis/get/estudisPublic?lang=ca
     */
} else if ($slug === 'estudisPublic') {

    $lang = isset($_GET['lang']) ? trim((string)$_GET['lang']) : 'ca';
    $langsAllowed = ['ca', 'es', 'en', 'fr', 'it', 'pt'];

    if (!in_array($lang, $langsAllowed, true)) {
        $lang = 'ca';
    }

    $db = new Database();

    $query = "SELECT
            e.id,
            e.slug,
            e.any_publicacio,

            -- Títol / resum en idioma demanat amb fallback a català
            COALESCE(ei_req.titol, ei_ca.titol, '') AS titol,
            COALESCE(ei_req.resum, ei_ca.resum, '') AS resum,

            -- Catàlegs en idioma demanat amb fallback a català
            COALESCE(pi_req.nom, pi_ca.nom, '') AS periode,
            COALESCE(ti_req.nom, ti_ca.nom, '') AS territori,
            COALESCE(ty_req.nom, ty_ca.nom, '') AS tipus,

            -- Autors concatenats
            GROUP_CONCAT(DISTINCT u.nom ORDER BY ea.sort_order ASC, u.nom ASC SEPARATOR ', ') AS autors,

            -- Documents per idioma
            ei_req.url_document AS url_req,
            ei_ca.url_document AS url_ca,
            ei_es.url_document AS url_es,
            ei_en.url_document AS url_en,
            ei_fr.url_document AS url_fr,
            ei_it.url_document AS url_it,
            ei_pt.url_document AS url_pt

        FROM db_estudis e

        LEFT JOIN db_estudis_i18n ei_req
            ON ei_req.estudi_id = e.id
           AND ei_req.lang = :lang

        LEFT JOIN db_estudis_i18n ei_ca
            ON ei_ca.estudi_id = e.id
           AND ei_ca.lang = 'ca'

        LEFT JOIN db_estudis_i18n ei_es
            ON ei_es.estudi_id = e.id
           AND ei_es.lang = 'es'

        LEFT JOIN db_estudis_i18n ei_en
            ON ei_en.estudi_id = e.id
           AND ei_en.lang = 'en'

        LEFT JOIN db_estudis_i18n ei_fr
            ON ei_fr.estudi_id = e.id
           AND ei_fr.lang = 'fr'

        LEFT JOIN db_estudis_i18n ei_it
            ON ei_it.estudi_id = e.id
           AND ei_it.lang = 'it'

        LEFT JOIN db_estudis_i18n ei_pt
            ON ei_pt.estudi_id = e.id
           AND ei_pt.lang = 'pt'

        LEFT JOIN db_estudis_periodes_i18n pi_req
            ON pi_req.periode_id = e.periode_id
           AND pi_req.lang = :lang

        LEFT JOIN db_estudis_periodes_i18n pi_ca
            ON pi_ca.periode_id = e.periode_id
           AND pi_ca.lang = 'ca'

        LEFT JOIN db_estudis_territoris_i18n ti_req
            ON ti_req.territori_id = e.territori_id
           AND ti_req.lang = :lang

        LEFT JOIN db_estudis_territoris_i18n ti_ca
            ON ti_ca.territori_id = e.territori_id
           AND ti_ca.lang = 'ca'

        LEFT JOIN db_estudis_tipus_i18n ty_req
            ON ty_req.tipus_id = e.tipus_id
           AND ty_req.lang = :lang

        LEFT JOIN db_estudis_tipus_i18n ty_ca
            ON ty_ca.tipus_id = e.tipus_id
           AND ty_ca.lang = 'ca'

        LEFT JOIN db_estudis_autors ea
            ON ea.estudi_id = e.id

        LEFT JOIN db_estudis_autors_noms u
            ON u.id = ea.autor_id

        GROUP BY
            e.id,
            e.slug,
            e.any_publicacio,
            ei_req.titol,
            ei_ca.titol,
            ei_req.resum,
            ei_ca.resum,
            pi_req.nom,
            pi_ca.nom,
            ti_req.nom,
            ti_ca.nom,
            ty_req.nom,
            ty_ca.nom,
            ei_req.url_document,
            ei_ca.url_document,
            ei_es.url_document,
            ei_en.url_document,
            ei_fr.url_document,
            ei_it.url_document,
            ei_pt.url_document

        ORDER BY
            e.any_publicacio DESC,
            titol ASC,
            e.id DESC
    ";

    try {
        $rows = $db->getData($query, [':lang' => $lang]);

        if (empty($rows)) {
            Response::success(MissatgesAPI::success('get'), [], 200);
            return;
        }

        $payload = [];

        foreach ($rows as $row) {
            $docs = [
                'ca' => trim((string)($row['url_ca'] ?? '')),
                'es' => trim((string)($row['url_es'] ?? '')),
                'en' => trim((string)($row['url_en'] ?? '')),
                'fr' => trim((string)($row['url_fr'] ?? '')),
                'it' => trim((string)($row['url_it'] ?? '')),
                'pt' => trim((string)($row['url_pt'] ?? '')),
            ];

            $urlReq = trim((string)($row['url_req'] ?? ''));

            $documentUrl = '';
            $documentLang = null;
            $isFallbackDocument = 0;

            // 1) idioma actual
            if ($urlReq !== '') {
                $documentUrl = $urlReq;
                $documentLang = $lang;
                $isFallbackDocument = 0;
            }
            // 2) català
            else if (!empty($docs['ca'])) {
                $documentUrl = $docs['ca'];
                $documentLang = 'ca';
                $isFallbackDocument = 1;
            }
            // 3) castellà
            else if (!empty($docs['es'])) {
                $documentUrl = $docs['es'];
                $documentLang = 'es';
                $isFallbackDocument = 1;
            }
            // 4) qualsevol altre disponible
            else {
                foreach (['en', 'fr', 'it', 'pt'] as $fallbackLang) {
                    if (!empty($docs[$fallbackLang])) {
                        $documentUrl = $docs[$fallbackLang];
                        $documentLang = $fallbackLang;
                        $isFallbackDocument = 1;
                        break;
                    }
                }
            }

            $payload[] = [
                'id' => (int)$row['id'],
                'slug' => $row['slug'] ?? '',
                'any_publicacio' => isset($row['any_publicacio']) ? (int)$row['any_publicacio'] : null,
                'titol' => $row['titol'] ?? '',
                'resum' => $row['resum'] ?? '',
                'periode' => $row['periode'] ?? '',
                'territori' => $row['territori'] ?? '',
                'tipus' => $row['tipus'] ?? '',
                'autors' => $row['autors'] ?? '',
                'url_document' => $documentUrl !== '' ? $documentUrl : null,
                'document_lang' => $documentLang,
                'is_fallback_document' => $isFallbackDocument,
            ];
        }

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
} else {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Something get wrong']);
    exit();
}
