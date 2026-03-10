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
 * GET: Llistat d'antecedents (Intranet)
 * URL: https://memoriaterrassa.cat/api/antecedents/get/antecedents
 * Retorna: [{id, ordre, image_id, layout_image_left, show_in_timeline, any_text, titol}]
 */
if ($slug === 'antecedents') {

    $db = new Database();

    $query = "SELECT
            a.id,
            a.ordre,
            a.image_id,
            a.layout_image_left,
            a.show_in_timeline,
            i.any_text,
            i.titol
        FROM db_web_antecedents a
        LEFT JOIN db_web_antecedents_i18n i
            ON i.antecedent_id = a.id
           AND i.lang = 'ca'
        ORDER BY a.ordre ASC, a.id ASC
    ";

    try {

        $rows = $db->getData($query);

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
     * GET: Fitxa d'un antecedent per ID (Intranet)
     * URL: https://memoriaterrassa.cat/api/antecedents/get/antecedentId?id=3
     */
} else if ($slug === 'antecedentId') {

    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($id <= 0) {
        Response::error(MissatgesAPI::error('validacio'), ['id invàlid'], 400);
        return;
    }

    $db = new Database();

    $queryAntecedent = "
        SELECT
            id,
            ordre,
            image_id,
            layout_image_left,
            show_in_timeline,
            date_created,
            date_modified
        FROM db_web_antecedents
        WHERE id = :id
        LIMIT 1
    ";

    $queryIdiomes = "
        SELECT
            id,
            antecedent_id,
            lang,
            any_text,
            titol,
            contingut_html,
            link_url
        FROM db_web_antecedents_i18n
        WHERE antecedent_id = :id
        ORDER BY
            CASE lang
                WHEN 'ca' THEN 1
                WHEN 'es' THEN 2
                WHEN 'en' THEN 3
                WHEN 'fr' THEN 4
                WHEN 'it' THEN 5
                WHEN 'pt' THEN 6
                ELSE 99
            END ASC,
            id ASC
    ";

    try {
        $antecedentRows = $db->getData($queryAntecedent, ['id' => $id]);

        if (empty($antecedentRows)) {
            Response::error(MissatgesAPI::error('registreNoTrobat'), ['Antecedent no trobat'], 404);
            return;
        }

        $idiomesRows = $db->getData($queryIdiomes, ['id' => $id]);

        $data = $antecedentRows[0];
        $data['idiomes'] = $idiomesRows ?: [];

        Response::success(MissatgesAPI::success('get'), $data, 200);
        return;
    } catch (PDOException $e) {
        Response::error(MissatgesAPI::error('errorBD'), [$e->getMessage()], 500);
        return;
    }

    /**
     * GET: Dades del formulari d'un antecedent per ID (Intranet)
     * URL: https://memoriaterrassa.cat/api/antecedents/get/formAntecedent?id=3
     */
} else if ($slug === 'formAntecedent') {

    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

    if ($id <= 0) {
        Response::error(MissatgesAPI::error('validacio'), ['ID no vàlid'], 400);
        return;
    }

    $db = new Database();

    $queryAntecedent = "
        SELECT
            id,
            ordre,
            image_id,
            layout_image_left,
            show_in_timeline
        FROM db_web_antecedents
        WHERE id = :id
        LIMIT 1
    ";

    $queryIdiomes = "
        SELECT
            lang,
            any_text,
            titol,
            contingut_html,
            link_url
        FROM db_web_antecedents_i18n
        WHERE antecedent_id = :id
    ";

    try {
        $rowsAntecedent = $db->getData($queryAntecedent, ['id' => $id]);

        if (empty($rowsAntecedent)) {
            Response::error(MissatgesAPI::error('registreNoTrobat'), ['Antecedent no trobat'], 404);
            return;
        }

        $rowsIdiomes = $db->getData($queryIdiomes, ['id' => $id]);

        $data = [
            'id' => (int) $rowsAntecedent[0]['id'],
            'ordre' => (int) $rowsAntecedent[0]['ordre'],
            'image_id' => isset($rowsAntecedent[0]['image_id']) ? (int) $rowsAntecedent[0]['image_id'] : null,
            'layout_image_left' => (int) $rowsAntecedent[0]['layout_image_left'],
            'show_in_timeline' => (int) $rowsAntecedent[0]['show_in_timeline'],

            'any_text_ca' => '',
            'titol_ca' => '',
            'contingut_html_ca' => '',
            'link_url_ca' => '',

            'any_text_es' => '',
            'titol_es' => '',
            'contingut_html_es' => '',
            'link_url_es' => '',

            'any_text_en' => '',
            'titol_en' => '',
            'contingut_html_en' => '',
            'link_url_en' => '',

            'any_text_fr' => '',
            'titol_fr' => '',
            'contingut_html_fr' => '',
            'link_url_fr' => '',

            'any_text_it' => '',
            'titol_it' => '',
            'contingut_html_it' => '',
            'link_url_it' => '',

            'any_text_pt' => '',
            'titol_pt' => '',
            'contingut_html_pt' => '',
            'link_url_pt' => '',
        ];

        if (!empty($rowsIdiomes)) {
            foreach ($rowsIdiomes as $row) {
                $lang = $row['lang'] ?? '';

                if (!in_array($lang, ['ca', 'es', 'en', 'fr', 'it', 'pt'], true)) {
                    continue;
                }

                $data['any_text_' . $lang] = $row['any_text'] ?? '';
                $data['titol_' . $lang] = $row['titol'] ?? '';
                $data['contingut_html_' . $lang] = $row['contingut_html'] ?? '';
                $data['link_url_' . $lang] = $row['link_url'] ?? '';
            }
        }

        Response::success(MissatgesAPI::success('get'), $data, 200);
        return;
    } catch (PDOException $e) {
        Response::error(MissatgesAPI::error('errorBD'), [$e->getMessage()], 500);
        return;
    }
} else {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Something get wrong']);
    exit();
}
