<?php

use App\Config\Database;
use App\Utils\Response;
use App\Utils\MissatgesAPI;
use App\Config\DatabaseConnection;
use App\Config\Audit;
use App\Config\Tables;

$conn = DatabaseConnection::getConnection();
if (!$conn) {
    die("No se pudo establecer conexión a la base de datos.");
}

$slug = $routeParams[0] ?? '';

// Headers
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: PUT");

$allowedOrigin = DOMAIN;
checkReferer($allowedOrigin);

// Method check
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    Response::error('Method not allowed', [], 405);
    exit;
}

/**
 * PUT : Modificar antecedent
 * URL: /api/antecedents/put/antecedent
 */
if ($slug === "antecedent") {

    // Auth
    $userId = getAuthenticatedUserId();
    if (!$userId) {
        Response::error(
            MissatgesAPI::error('no_autenticat'),
            [],
            401
        );
        return;
    }

    // Body JSON
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    if (!is_array($data)) {
        Response::error(
            'JSON invàlid',
            ['Format de dades incorrecte'],
            400
        );
        return;
    }

    // Camps base
    $id = isset($data['id']) ? (int)$data['id'] : 0;
    $ordre = isset($data['ordre']) ? (int)$data['ordre'] : -1;
    $imageId = isset($data['image_id']) && $data['image_id'] !== '' ? (int)$data['image_id'] : null;
    $layoutImageLeft = isset($data['layout_image_left']) ? (int)$data['layout_image_left'] : 0;
    $showInTimeline = isset($data['show_in_timeline']) ? (int)$data['show_in_timeline'] : 1;

    // I18N català
    $anyTextCa = isset($data['any_text_ca']) ? trim((string)$data['any_text_ca']) : '';
    $titolCa = isset($data['titol_ca']) ? trim((string)$data['titol_ca']) : '';
    $contingutHtmlCa = isset($data['contingut_html_ca']) ? trim((string)$data['contingut_html_ca']) : '';
    $linkUrlCa = isset($data['link_url_ca']) ? trim((string)$data['link_url_ca']) : '';

    // I18N castellà
    $anyTextEs = isset($data['any_text_es']) ? trim((string)$data['any_text_es']) : '';
    $titolEs = isset($data['titol_es']) ? trim((string)$data['titol_es']) : '';
    $contingutHtmlEs = isset($data['contingut_html_es']) ? trim((string)$data['contingut_html_es']) : '';
    $linkUrlEs = isset($data['link_url_es']) ? trim((string)$data['link_url_es']) : '';

    // I18N anglès
    $anyTextEn = isset($data['any_text_en']) ? trim((string)$data['any_text_en']) : '';
    $titolEn = isset($data['titol_en']) ? trim((string)$data['titol_en']) : '';
    $contingutHtmlEn = isset($data['contingut_html_en']) ? trim((string)$data['contingut_html_en']) : '';
    $linkUrlEn = isset($data['link_url_en']) ? trim((string)$data['link_url_en']) : '';

    // I18N francès
    $anyTextFr = isset($data['any_text_fr']) ? trim((string)$data['any_text_fr']) : '';
    $titolFr = isset($data['titol_fr']) ? trim((string)$data['titol_fr']) : '';
    $contingutHtmlFr = isset($data['contingut_html_fr']) ? trim((string)$data['contingut_html_fr']) : '';
    $linkUrlFr = isset($data['link_url_fr']) ? trim((string)$data['link_url_fr']) : '';

    // I18N italià
    $anyTextIt = isset($data['any_text_it']) ? trim((string)$data['any_text_it']) : '';
    $titolIt = isset($data['titol_it']) ? trim((string)$data['titol_it']) : '';
    $contingutHtmlIt = isset($data['contingut_html_it']) ? trim((string)$data['contingut_html_it']) : '';
    $linkUrlIt = isset($data['link_url_it']) ? trim((string)$data['link_url_it']) : '';

    // I18N portuguès
    $anyTextPt = isset($data['any_text_pt']) ? trim((string)$data['any_text_pt']) : '';
    $titolPt = isset($data['titol_pt']) ? trim((string)$data['titol_pt']) : '';
    $contingutHtmlPt = isset($data['contingut_html_pt']) ? trim((string)$data['contingut_html_pt']) : '';
    $linkUrlPt = isset($data['link_url_pt']) ? trim((string)$data['link_url_pt']) : '';

    $errors = [];

    // Validacions base
    if ($id <= 0) {
        $errors[] = "El camp 'id' és obligatori.";
    }

    if ($ordre < 0) {
        $errors[] = "El camp 'ordre' és obligatori i ha de ser un enter igual o superior a 0.";
    }

    if (!in_array($layoutImageLeft, [0, 1], true)) {
        $errors[] = "El camp 'layout_image_left' és invàlid.";
    }

    if (!in_array($showInTimeline, [0, 1], true)) {
        $errors[] = "El camp 'show_in_timeline' és invàlid.";
    }

    if ($anyTextCa === '') {
        $errors[] = "El camp 'any_text_ca' és obligatori.";
    }

    if ($titolCa === '') {
        $errors[] = "El camp 'titol_ca' és obligatori.";
    }

    if ($contingutHtmlCa === '') {
        $errors[] = "El camp 'contingut_html_ca' és obligatori.";
    }

    $maxAnyTextLen = 150;
    $maxTitolLen = 255;
    $maxUrlLen = 500;

    foreach (
        [
            'any_text_ca' => $anyTextCa,
            'any_text_es' => $anyTextEs,
            'any_text_en' => $anyTextEn,
            'any_text_fr' => $anyTextFr,
            'any_text_it' => $anyTextIt,
            'any_text_pt' => $anyTextPt,
        ] as $field => $value
    ) {
        if ($value !== '' && mb_strlen($value, 'UTF-8') > $maxAnyTextLen) {
            $errors[] = "El camp '{$field}' no pot superar {$maxAnyTextLen} caràcters.";
        }
    }

    foreach (
        [
            'titol_ca' => $titolCa,
            'titol_es' => $titolEs,
            'titol_en' => $titolEn,
            'titol_fr' => $titolFr,
            'titol_it' => $titolIt,
            'titol_pt' => $titolPt,
        ] as $field => $value
    ) {
        if ($value !== '' && mb_strlen($value, 'UTF-8') > $maxTitolLen) {
            $errors[] = "El camp '{$field}' no pot superar {$maxTitolLen} caràcters.";
        }
    }

    foreach (
        [
            'link_url_ca' => $linkUrlCa,
            'link_url_es' => $linkUrlEs,
            'link_url_en' => $linkUrlEn,
            'link_url_fr' => $linkUrlFr,
            'link_url_it' => $linkUrlIt,
            'link_url_pt' => $linkUrlPt,
        ] as $field => $value
    ) {
        if ($value !== '' && mb_strlen($value, 'UTF-8') > $maxUrlLen) {
            $errors[] = "El camp '{$field}' no pot superar {$maxUrlLen} caràcters.";
        }
    }

    if ($imageId !== null && $imageId <= 0) {
        $errors[] = "El camp 'image_id' és invàlid.";
    }

    if (!empty($errors)) {
        Response::error(
            "S'han produït errors en la validació",
            $errors,
            400
        );
        return;
    }

    try {
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Comprovar antecedent existent
        $stmtExisteix = $conn->prepare("SELECT id FROM db_web_antecedents WHERE id = :id LIMIT 1");
        $stmtExisteix->execute([':id' => $id]);
        if (!$stmtExisteix->fetchColumn()) {
            Response::error(
                "L'antecedent indicat no existeix.",
                [],
                404
            );
            return;
        }

        // Validació FK imatge
        if ($imageId !== null) {
            $stmtImage = $conn->prepare("SELECT id FROM aux_imatges WHERE id = :id LIMIT 1");
            $stmtImage->execute([':id' => $imageId]);

            if (!$stmtImage->fetchColumn()) {
                Response::error(
                    "La imatge indicada no existeix.",
                    [],
                    400
                );
                return;
            }
        }

        $conn->beginTransaction();

        // 1) UPDATE principal
        $sqlAntecedent = "
            UPDATE db_web_antecedents
            SET
                ordre = :ordre,
                image_id = :image_id,
                layout_image_left = :layout_image_left,
                show_in_timeline = :show_in_timeline,
                date_modified = NOW()
            WHERE id = :id
            LIMIT 1
        ";
        $stmtAntecedent = $conn->prepare($sqlAntecedent);
        $stmtAntecedent->bindValue(':ordre', $ordre, PDO::PARAM_INT);
        $stmtAntecedent->bindValue(':image_id', $imageId, $imageId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmtAntecedent->bindValue(':layout_image_left', $layoutImageLeft, PDO::PARAM_INT);
        $stmtAntecedent->bindValue(':show_in_timeline', $showInTimeline, PDO::PARAM_INT);
        $stmtAntecedent->bindValue(':id', $id, PDO::PARAM_INT);
        $stmtAntecedent->execute();

        // 2) DELETE i18n anterior
        $sqlDeleteI18n = "DELETE FROM db_web_antecedents_i18n WHERE antecedent_id = :antecedent_id";
        $stmtDeleteI18n = $conn->prepare($sqlDeleteI18n);
        $stmtDeleteI18n->bindValue(':antecedent_id', $id, PDO::PARAM_INT);
        $stmtDeleteI18n->execute();

        // 3) REINSERT i18n
        $i18nRows = [
            'ca' => [
                'any_text' => $anyTextCa,
                'titol' => $titolCa,
                'contingut_html' => $contingutHtmlCa,
                'link_url' => $linkUrlCa,
            ],
            'es' => [
                'any_text' => $anyTextEs,
                'titol' => $titolEs,
                'contingut_html' => $contingutHtmlEs,
                'link_url' => $linkUrlEs,
            ],
            'en' => [
                'any_text' => $anyTextEn,
                'titol' => $titolEn,
                'contingut_html' => $contingutHtmlEn,
                'link_url' => $linkUrlEn,
            ],
            'fr' => [
                'any_text' => $anyTextFr,
                'titol' => $titolFr,
                'contingut_html' => $contingutHtmlFr,
                'link_url' => $linkUrlFr,
            ],
            'it' => [
                'any_text' => $anyTextIt,
                'titol' => $titolIt,
                'contingut_html' => $contingutHtmlIt,
                'link_url' => $linkUrlIt,
            ],
            'pt' => [
                'any_text' => $anyTextPt,
                'titol' => $titolPt,
                'contingut_html' => $contingutHtmlPt,
                'link_url' => $linkUrlPt,
            ],
        ];

        $sqlI18n = "
            INSERT INTO db_web_antecedents_i18n
                (antecedent_id, lang, any_text, titol, contingut_html, link_url)
            VALUES
                (:antecedent_id, :lang, :any_text, :titol, :contingut_html, :link_url)
        ";
        $stmtI18n = $conn->prepare($sqlI18n);

        foreach ($i18nRows as $lang => $row) {
            $hasContent = (
                $row['any_text'] !== '' ||
                $row['titol'] !== '' ||
                $row['contingut_html'] !== '' ||
                $row['link_url'] !== ''
            );

            if (!$hasContent) {
                continue;
            }

            $stmtI18n->bindValue(':antecedent_id', $id, PDO::PARAM_INT);
            $stmtI18n->bindValue(':lang', $lang, PDO::PARAM_STR);
            $stmtI18n->bindValue(':any_text', $row['any_text'] !== '' ? $row['any_text'] : null, $row['any_text'] !== '' ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmtI18n->bindValue(':titol', $row['titol'] !== '' ? $row['titol'] : null, $row['titol'] !== '' ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmtI18n->bindValue(':contingut_html', $row['contingut_html'] !== '' ? $row['contingut_html'] : null, $row['contingut_html'] !== '' ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmtI18n->bindValue(':link_url', $row['link_url'] !== '' ? $row['link_url'] : null, $row['link_url'] !== '' ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmtI18n->execute();
        }

        $conn->commit();

        // 4) Auditoria
        Audit::registrarCanvi(
            $conn,
            $userId,
            "Modificació antecedent",
            "UPDATE",
            Tables::DB_WEB_ANTECEDENTS,
            $id
        );

        Response::success(
            "L'antecedent s'ha modificat correctament.",
            ['id' => $id],
            200
        );
        return;
    } catch (PDOException $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }

        $code = $e->errorInfo[1] ?? null;

        if ((int)$code === 1062) {
            Response::error(
                "S'ha detectat un registre duplicat.",
                [],
                409
            );
            return;
        }

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
