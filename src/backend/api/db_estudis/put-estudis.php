<?php

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
 * PUT : Modificar període històric
 * URL: /api/estudis/put/periode
 */
if ($slug === "periode") {

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

    // Camps
    $id        = isset($data['id']) ? (int)$data['id'] : 0;
    $sortOrder = isset($data['sort_order']) ? (int)$data['sort_order'] : -1;

    $nomCa = isset($data['nom_ca']) ? trim((string)$data['nom_ca']) : '';
    $nomEs = isset($data['nom_es']) ? trim((string)$data['nom_es']) : '';
    $nomEn = isset($data['nom_en']) ? trim((string)$data['nom_en']) : '';
    $nomFr = isset($data['nom_fr']) ? trim((string)$data['nom_fr']) : '';
    $nomIt = isset($data['nom_it']) ? trim((string)$data['nom_it']) : '';
    $nomPt = isset($data['nom_pt']) ? trim((string)$data['nom_pt']) : '';

    $errors = [];

    // Validacions
    if ($id <= 0) {
        $errors[] = "El camp 'id' és obligatori.";
    }

    if ($sortOrder < 0) {
        $errors[] = "El camp 'sort_order' és obligatori i ha de ser un enter igual o superior a 0.";
    }

    if ($nomCa === '') {
        $errors[] = "El camp 'nom_ca' és obligatori.";
    }

    $maxLen = 160;

    foreach (
        [
            'nom_ca' => $nomCa,
            'nom_es' => $nomEs,
            'nom_en' => $nomEn,
            'nom_fr' => $nomFr,
            'nom_it' => $nomIt,
            'nom_pt' => $nomPt,
        ] as $field => $value
    ) {
        if ($value !== '' && mb_strlen($value, 'UTF-8') > $maxLen) {
            $errors[] = "El camp '{$field}' no pot superar {$maxLen} caràcters.";
        }
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
        $conn->beginTransaction();

        // 1) Comprovar que existeix el període
        $sqlExists = "
            SELECT id
            FROM db_estudis_periodes
            WHERE id = :id
            LIMIT 1
        ";
        $stmtExists = $conn->prepare($sqlExists);
        $stmtExists->execute([':id' => $id]);
        $exists = $stmtExists->fetchColumn();

        if (!$exists) {
            $conn->rollBack();
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;
        }

        // 2) Update taula principal
        $sqlUpdatePeriode = "
            UPDATE db_estudis_periodes
            SET sort_order = :sort_order
            WHERE id = :id
            LIMIT 1
        ";
        $stmtUpdatePeriode = $conn->prepare($sqlUpdatePeriode);
        $stmtUpdatePeriode->bindValue(':sort_order', $sortOrder, PDO::PARAM_INT);
        $stmtUpdatePeriode->bindValue(':id', $id, PDO::PARAM_INT);
        $stmtUpdatePeriode->execute();

        // 3) Gestionar i18n
        $i18nRows = [
            'ca' => $nomCa,
            'es' => $nomEs,
            'en' => $nomEn,
            'fr' => $nomFr,
            'it' => $nomIt,
            'pt' => $nomPt,
        ];

        $sqlExistsI18n = "
            SELECT id
            FROM db_estudis_periodes_i18n
            WHERE periode_id = :periode_id
              AND lang = :lang
            LIMIT 1
        ";
        $stmtExistsI18n = $conn->prepare($sqlExistsI18n);

        $sqlInsertI18n = "
            INSERT INTO db_estudis_periodes_i18n
                (periode_id, lang, nom)
            VALUES
                (:periode_id, :lang, :nom)
        ";
        $stmtInsertI18n = $conn->prepare($sqlInsertI18n);

        $sqlUpdateI18n = "
            UPDATE db_estudis_periodes_i18n
            SET nom = :nom
            WHERE periode_id = :periode_id
              AND lang = :lang
            LIMIT 1
        ";
        $stmtUpdateI18n = $conn->prepare($sqlUpdateI18n);

        $sqlDeleteI18n = "
            DELETE FROM db_estudis_periodes_i18n
            WHERE periode_id = :periode_id
              AND lang = :lang
            LIMIT 1
        ";
        $stmtDeleteI18n = $conn->prepare($sqlDeleteI18n);

        foreach ($i18nRows as $lang => $nom) {
            $stmtExistsI18n->execute([
                ':periode_id' => $id,
                ':lang' => $lang,
            ]);
            $existingI18nId = $stmtExistsI18n->fetchColumn();

            if ($nom === '') {
                // Si ve buit, eliminem la traducció si existeix
                if ($existingI18nId) {
                    $stmtDeleteI18n->execute([
                        ':periode_id' => $id,
                        ':lang' => $lang,
                    ]);
                }
                continue;
            }

            if ($existingI18nId) {
                $stmtUpdateI18n->execute([
                    ':nom' => $nom,
                    ':periode_id' => $id,
                    ':lang' => $lang,
                ]);
            } else {
                $stmtInsertI18n->execute([
                    ':periode_id' => $id,
                    ':lang' => $lang,
                    ':nom' => $nom,
                ]);
            }
        }

        $conn->commit();

        // 4) Auditoria
        Audit::registrarCanvi(
            $conn,
            $userId,
            "Modificació període històric",
            "UPDATE",
            Tables::DB_ESTUDIS_PERIODES,
            $id
        );

        Response::success(
            "El període històric s'ha modificat correctament.",
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
    /**
     * PUT : Modificar territori
     * URL: /api/estudis/put/territori
     */
} else if ($slug === "territori") {

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

    // Camps
    $id        = isset($data['id']) ? (int)$data['id'] : 0;
    $sortOrder = isset($data['sort_order']) ? (int)$data['sort_order'] : -1;

    $nomCa = isset($data['nom_ca']) ? trim((string)$data['nom_ca']) : '';
    $nomEs = isset($data['nom_es']) ? trim((string)$data['nom_es']) : '';
    $nomEn = isset($data['nom_en']) ? trim((string)$data['nom_en']) : '';
    $nomFr = isset($data['nom_fr']) ? trim((string)$data['nom_fr']) : '';
    $nomIt = isset($data['nom_it']) ? trim((string)$data['nom_it']) : '';
    $nomPt = isset($data['nom_pt']) ? trim((string)$data['nom_pt']) : '';

    $errors = [];

    if ($id <= 0) {
        $errors[] = "El camp 'id' és obligatori.";
    }

    if ($sortOrder < 0) {
        $errors[] = "El camp 'sort_order' és obligatori i ha de ser un enter igual o superior a 0.";
    }

    if ($nomCa === '') {
        $errors[] = "El camp 'nom_ca' és obligatori.";
    }

    $maxLen = 160;

    foreach (
        [
            'nom_ca' => $nomCa,
            'nom_es' => $nomEs,
            'nom_en' => $nomEn,
            'nom_fr' => $nomFr,
            'nom_it' => $nomIt,
            'nom_pt' => $nomPt,
        ] as $field => $value
    ) {
        if ($value !== '' && mb_strlen($value, 'UTF-8') > $maxLen) {
            $errors[] = "El camp '{$field}' no pot superar {$maxLen} caràcters.";
        }
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
        $conn->beginTransaction();

        // 1) Comprovar que existeix
        $sqlExists = "
            SELECT id
            FROM db_estudis_territoris
            WHERE id = :id
            LIMIT 1
        ";
        $stmtExists = $conn->prepare($sqlExists);
        $stmtExists->execute([':id' => $id]);
        $exists = $stmtExists->fetchColumn();

        if (!$exists) {
            $conn->rollBack();
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;
        }

        // 2) Update taula principal
        $sqlUpdateTerritori = "
            UPDATE db_estudis_territoris
            SET sort_order = :sort_order
            WHERE id = :id
            LIMIT 1
        ";
        $stmtUpdateTerritori = $conn->prepare($sqlUpdateTerritori);
        $stmtUpdateTerritori->bindValue(':sort_order', $sortOrder, PDO::PARAM_INT);
        $stmtUpdateTerritori->bindValue(':id', $id, PDO::PARAM_INT);
        $stmtUpdateTerritori->execute();

        // 3) Gestionar i18n
        $i18nRows = [
            'ca' => $nomCa,
            'es' => $nomEs,
            'en' => $nomEn,
            'fr' => $nomFr,
            'it' => $nomIt,
            'pt' => $nomPt,
        ];

        $sqlExistsI18n = "
            SELECT id
            FROM db_estudis_territoris_i18n
            WHERE territori_id = :territori_id
              AND lang = :lang
            LIMIT 1
        ";
        $stmtExistsI18n = $conn->prepare($sqlExistsI18n);

        $sqlInsertI18n = "
            INSERT INTO db_estudis_territoris_i18n
                (territori_id, lang, nom)
            VALUES
                (:territori_id, :lang, :nom)
        ";
        $stmtInsertI18n = $conn->prepare($sqlInsertI18n);

        $sqlUpdateI18n = "
            UPDATE db_estudis_territoris_i18n
            SET nom = :nom
            WHERE territori_id = :territori_id
              AND lang = :lang
            LIMIT 1
        ";
        $stmtUpdateI18n = $conn->prepare($sqlUpdateI18n);

        $sqlDeleteI18n = "
            DELETE FROM db_estudis_territoris_i18n
            WHERE territori_id = :territori_id
              AND lang = :lang
            LIMIT 1
        ";
        $stmtDeleteI18n = $conn->prepare($sqlDeleteI18n);

        foreach ($i18nRows as $lang => $nom) {
            $stmtExistsI18n->execute([
                ':territori_id' => $id,
                ':lang' => $lang,
            ]);
            $existingI18nId = $stmtExistsI18n->fetchColumn();

            if ($nom === '') {
                if ($existingI18nId) {
                    $stmtDeleteI18n->execute([
                        ':territori_id' => $id,
                        ':lang' => $lang,
                    ]);
                }
                continue;
            }

            if ($existingI18nId) {
                $stmtUpdateI18n->execute([
                    ':nom' => $nom,
                    ':territori_id' => $id,
                    ':lang' => $lang,
                ]);
            } else {
                $stmtInsertI18n->execute([
                    ':territori_id' => $id,
                    ':lang' => $lang,
                    ':nom' => $nom,
                ]);
            }
        }

        $conn->commit();

        // 4) Auditoria
        Audit::registrarCanvi(
            $conn,
            $userId,
            "Modificació territori",
            "UPDATE",
            Tables::DB_ESTUDIS_TERRITORIS,
            $id
        );

        Response::success(
            "El territori s'ha modificat correctament.",
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

    /**
     * PUT : Modificar tipus
     * URL: /api/estudis/put/tipus
     */
} else if ($slug === "tipus") {

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

    // Camps
    $id        = isset($data['id']) ? (int)$data['id'] : 0;
    $sortOrder = isset($data['sort_order']) ? (int)$data['sort_order'] : -1;

    $nomCa = isset($data['nom_ca']) ? trim((string)$data['nom_ca']) : '';
    $nomEs = isset($data['nom_es']) ? trim((string)$data['nom_es']) : '';
    $nomEn = isset($data['nom_en']) ? trim((string)$data['nom_en']) : '';
    $nomFr = isset($data['nom_fr']) ? trim((string)$data['nom_fr']) : '';
    $nomIt = isset($data['nom_it']) ? trim((string)$data['nom_it']) : '';
    $nomPt = isset($data['nom_pt']) ? trim((string)$data['nom_pt']) : '';

    $errors = [];

    if ($id <= 0) {
        $errors[] = "El camp 'id' és obligatori.";
    }

    if ($sortOrder < 0) {
        $errors[] = "El camp 'sort_order' és obligatori i ha de ser un enter igual o superior a 0.";
    }

    if ($nomCa === '') {
        $errors[] = "El camp 'nom_ca' és obligatori.";
    }

    $maxLen = 160;

    foreach (
        [
            'nom_ca' => $nomCa,
            'nom_es' => $nomEs,
            'nom_en' => $nomEn,
            'nom_fr' => $nomFr,
            'nom_it' => $nomIt,
            'nom_pt' => $nomPt,
        ] as $field => $value
    ) {
        if ($value !== '' && mb_strlen($value, 'UTF-8') > $maxLen) {
            $errors[] = "El camp '{$field}' no pot superar {$maxLen} caràcters.";
        }
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
        $conn->beginTransaction();

        // 1) Comprovar que existeix
        $sqlExists = "
            SELECT id
            FROM db_estudis_tipus
            WHERE id = :id
            LIMIT 1
        ";
        $stmtExists = $conn->prepare($sqlExists);
        $stmtExists->execute([':id' => $id]);
        $exists = $stmtExists->fetchColumn();

        if (!$exists) {
            $conn->rollBack();
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;
        }

        // 2) Update taula principal
        $sqlUpdateTipus = "
            UPDATE db_estudis_tipus
            SET sort_order = :sort_order
            WHERE id = :id
            LIMIT 1
        ";
        $stmtUpdateTipus = $conn->prepare($sqlUpdateTipus);
        $stmtUpdateTipus->bindValue(':sort_order', $sortOrder, PDO::PARAM_INT);
        $stmtUpdateTipus->bindValue(':id', $id, PDO::PARAM_INT);
        $stmtUpdateTipus->execute();

        // 3) Gestionar i18n
        $i18nRows = [
            'ca' => $nomCa,
            'es' => $nomEs,
            'en' => $nomEn,
            'fr' => $nomFr,
            'it' => $nomIt,
            'pt' => $nomPt,
        ];

        $sqlExistsI18n = "
            SELECT id
            FROM db_estudis_tipus_i18n
            WHERE tipus_id = :tipus_id
              AND lang = :lang
            LIMIT 1
        ";
        $stmtExistsI18n = $conn->prepare($sqlExistsI18n);

        $sqlInsertI18n = "
            INSERT INTO db_estudis_tipus_i18n
                (tipus_id, lang, nom)
            VALUES
                (:tipus_id, :lang, :nom)
        ";
        $stmtInsertI18n = $conn->prepare($sqlInsertI18n);

        $sqlUpdateI18n = "
            UPDATE db_estudis_tipus_i18n
            SET nom = :nom
            WHERE tipus_id = :tipus_id
              AND lang = :lang
            LIMIT 1
        ";
        $stmtUpdateI18n = $conn->prepare($sqlUpdateI18n);

        $sqlDeleteI18n = "
            DELETE FROM db_estudis_tipus_i18n
            WHERE tipus_id = :tipus_id
              AND lang = :lang
            LIMIT 1
        ";
        $stmtDeleteI18n = $conn->prepare($sqlDeleteI18n);

        foreach ($i18nRows as $lang => $nom) {
            $stmtExistsI18n->execute([
                ':tipus_id' => $id,
                ':lang' => $lang,
            ]);
            $existingI18nId = $stmtExistsI18n->fetchColumn();

            if ($nom === '') {
                if ($existingI18nId) {
                    $stmtDeleteI18n->execute([
                        ':tipus_id' => $id,
                        ':lang' => $lang,
                    ]);
                }
                continue;
            }

            if ($existingI18nId) {
                $stmtUpdateI18n->execute([
                    ':nom' => $nom,
                    ':tipus_id' => $id,
                    ':lang' => $lang,
                ]);
            } else {
                $stmtInsertI18n->execute([
                    ':tipus_id' => $id,
                    ':lang' => $lang,
                    ':nom' => $nom,
                ]);
            }
        }

        $conn->commit();

        // 4) Auditoria
        Audit::registrarCanvi(
            $conn,
            $userId,
            "Modificació tipus",
            "UPDATE",
            Tables::DB_ESTUDIS_TIPUS,
            $id
        );

        Response::success(
            "El tipus s'ha modificat correctament.",
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

    /**
     * PUT : Modificar estudi
     * URL: /api/estudis/put/estudi
     */
} else if ($slug === "estudi") {

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
    $id            = isset($data['id']) ? (int)$data['id'] : 0;
    $slugValue     = isset($data['slug']) ? trim((string)$data['slug']) : '';
    $anyPublicacio = isset($data['any_publicacio']) && $data['any_publicacio'] !== '' ? (int)$data['any_publicacio'] : null;
    $periodeId     = isset($data['periode_id']) ? (int)$data['periode_id'] : 0;
    $territoriId   = isset($data['territori_id']) ? (int)$data['territori_id'] : 0;
    $tipusId       = isset($data['tipus_id']) ? (int)$data['tipus_id'] : 0;

    // Autors
    $autors = $data['autors'] ?? [];
    if (!is_array($autors)) {
        $autors = [];
    }

    // I18N
    $titolCa = isset($data['titol_ca']) ? trim((string)$data['titol_ca']) : '';
    $resumCa = isset($data['resum_ca']) ? trim((string)$data['resum_ca']) : '';
    $urlCa   = isset($data['url_document_ca']) ? trim((string)$data['url_document_ca']) : '';

    $titolEs = isset($data['titol_es']) ? trim((string)$data['titol_es']) : '';
    $resumEs = isset($data['resum_es']) ? trim((string)$data['resum_es']) : '';
    $urlEs   = isset($data['url_document_es']) ? trim((string)$data['url_document_es']) : '';

    $titolEn = isset($data['titol_en']) ? trim((string)$data['titol_en']) : '';
    $resumEn = isset($data['resum_en']) ? trim((string)$data['resum_en']) : '';
    $urlEn   = isset($data['url_document_en']) ? trim((string)$data['url_document_en']) : '';

    $titolFr = isset($data['titol_fr']) ? trim((string)$data['titol_fr']) : '';
    $resumFr = isset($data['resum_fr']) ? trim((string)$data['resum_fr']) : '';
    $urlFr   = isset($data['url_document_fr']) ? trim((string)$data['url_document_fr']) : '';

    $titolIt = isset($data['titol_it']) ? trim((string)$data['titol_it']) : '';
    $resumIt = isset($data['resum_it']) ? trim((string)$data['resum_it']) : '';
    $urlIt   = isset($data['url_document_it']) ? trim((string)$data['url_document_it']) : '';

    $titolPt = isset($data['titol_pt']) ? trim((string)$data['titol_pt']) : '';
    $resumPt = isset($data['resum_pt']) ? trim((string)$data['resum_pt']) : '';
    $urlPt   = isset($data['url_document_pt']) ? trim((string)$data['url_document_pt']) : '';

    $errors = [];

    // Validacions bàsiques
    if ($id <= 0) {
        $errors[] = "El camp 'id' és obligatori.";
    }

    if ($slugValue === '') {
        $errors[] = "El camp 'slug' és obligatori.";
    } elseif (!preg_match('~^[a-z0-9]+(?:-[a-z0-9]+)*$~', $slugValue)) {
        $errors[] = "El camp 'slug' té un format invàlid.";
    } elseif (mb_strlen($slugValue, 'UTF-8') > 190) {
        $errors[] = "El camp 'slug' no pot superar 190 caràcters.";
    }

    if ($anyPublicacio !== null && ($anyPublicacio < 1000 || $anyPublicacio > 2100)) {
        $errors[] = "El camp 'any_publicacio' és invàlid.";
    }

    if ($periodeId <= 0) {
        $errors[] = "El camp 'periode_id' és obligatori.";
    }

    if ($territoriId <= 0) {
        $errors[] = "El camp 'territori_id' és obligatori.";
    }

    if ($tipusId <= 0) {
        $errors[] = "El camp 'tipus_id' és obligatori.";
    }

    if ($titolCa === '') {
        $errors[] = "El camp 'titol_ca' és obligatori.";
    }

    if (empty($autors)) {
        $errors[] = "Cal seleccionar almenys un autor.";
    }

    $maxTitleLen = 255;
    $maxUrlLen = 600;

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
        if ($value !== '' && mb_strlen($value, 'UTF-8') > $maxTitleLen) {
            $errors[] = "El camp '{$field}' no pot superar {$maxTitleLen} caràcters.";
        }
    }

    foreach (
        [
            'url_document_ca' => $urlCa,
            'url_document_es' => $urlEs,
            'url_document_en' => $urlEn,
            'url_document_fr' => $urlFr,
            'url_document_it' => $urlIt,
            'url_document_pt' => $urlPt,
        ] as $field => $value
    ) {
        if ($value !== '' && mb_strlen($value, 'UTF-8') > $maxUrlLen) {
            $errors[] = "El camp '{$field}' no pot superar {$maxUrlLen} caràcters.";
        }
    }

    // Validar autors
    $autorsNet = [];
    foreach ($autors as $autorId) {
        $autorId = (int)$autorId;
        if ($autorId > 0) {
            $autorsNet[] = $autorId;
        }
    }
    $autorsNet = array_values(array_unique($autorsNet));

    if (empty($autorsNet)) {
        $errors[] = "La llista d'autors és invàlida.";
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

        // Existeix estudi?
        $stmtExists = $conn->prepare("SELECT id FROM db_estudis WHERE id = :id LIMIT 1");
        $stmtExists->execute([':id' => $id]);
        if (!$stmtExists->fetchColumn()) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;
        }

        // Slug únic excepte el propi
        $stmtSlug = $conn->prepare("
            SELECT id
            FROM db_estudis
            WHERE slug = :slug AND id <> :id
            LIMIT 1
        ");
        $stmtSlug->execute([
            ':slug' => $slugValue,
            ':id' => $id,
        ]);
        if ($stmtSlug->fetchColumn()) {
            Response::error(
                "Ja existeix un estudi amb aquest slug.",
                [],
                409
            );
            return;
        }

        // Validacions FK
        $stmtPeriode = $conn->prepare("SELECT id FROM db_estudis_periodes WHERE id = :id LIMIT 1");
        $stmtPeriode->execute([':id' => $periodeId]);
        if (!$stmtPeriode->fetchColumn()) {
            Response::error("El període indicat no existeix.", [], 400);
            return;
        }

        $stmtTerritori = $conn->prepare("SELECT id FROM db_estudis_territoris WHERE id = :id LIMIT 1");
        $stmtTerritori->execute([':id' => $territoriId]);
        if (!$stmtTerritori->fetchColumn()) {
            Response::error("El territori indicat no existeix.", [], 400);
            return;
        }

        $stmtTipus = $conn->prepare("SELECT id FROM db_estudis_tipus WHERE id = :id LIMIT 1");
        $stmtTipus->execute([':id' => $tipusId]);
        if (!$stmtTipus->fetchColumn()) {
            Response::error("El tipus indicat no existeix.", [], 400);
            return;
        }

        $stmtAutor = $conn->prepare("SELECT id FROM db_estudis_autors_noms WHERE id = :id LIMIT 1");
        foreach ($autorsNet as $autorId) {
            $stmtAutor->execute([':id' => $autorId]);
            if (!$stmtAutor->fetchColumn()) {
                Response::error("Un dels autors indicats no existeix.", ['autor_id' => $autorId], 400);
                return;
            }
        }

        $conn->beginTransaction();

        // 1) UPDATE principal
        $sqlEstudi = "
            UPDATE db_estudis
            SET
                slug = :slug,
                periode_id = :periode_id,
                territori_id = :territori_id,
                tipus_id = :tipus_id,
                any_publicacio = :any_publicacio,
                date_modified = NOW()
            WHERE id = :id
            LIMIT 1
        ";
        $stmtEstudi = $conn->prepare($sqlEstudi);
        $stmtEstudi->bindValue(':slug', $slugValue, PDO::PARAM_STR);
        $stmtEstudi->bindValue(':periode_id', $periodeId, PDO::PARAM_INT);
        $stmtEstudi->bindValue(':territori_id', $territoriId, PDO::PARAM_INT);
        $stmtEstudi->bindValue(':tipus_id', $tipusId, PDO::PARAM_INT);
        $stmtEstudi->bindValue(':any_publicacio', $anyPublicacio, $anyPublicacio === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmtEstudi->bindValue(':id', $id, PDO::PARAM_INT);
        $stmtEstudi->execute();

        // 2) Gestionar i18n
        $i18nRows = [
            'ca' => ['titol' => $titolCa, 'resum' => $resumCa, 'url' => $urlCa],
            'es' => ['titol' => $titolEs, 'resum' => $resumEs, 'url' => $urlEs],
            'en' => ['titol' => $titolEn, 'resum' => $resumEn, 'url' => $urlEn],
            'fr' => ['titol' => $titolFr, 'resum' => $resumFr, 'url' => $urlFr],
            'it' => ['titol' => $titolIt, 'resum' => $resumIt, 'url' => $urlIt],
            'pt' => ['titol' => $titolPt, 'resum' => $resumPt, 'url' => $urlPt],
        ];

        $sqlExistsI18n = "
            SELECT id
            FROM db_estudis_i18n
            WHERE estudi_id = :estudi_id
              AND lang = :lang
            LIMIT 1
        ";
        $stmtExistsI18n = $conn->prepare($sqlExistsI18n);

        $sqlInsertI18n = "
            INSERT INTO db_estudis_i18n
                (estudi_id, lang, titol, resum, url_document)
            VALUES
                (:estudi_id, :lang, :titol, :resum, :url_document)
        ";
        $stmtInsertI18n = $conn->prepare($sqlInsertI18n);

        $sqlUpdateI18n = "
            UPDATE db_estudis_i18n
            SET
                titol = :titol,
                resum = :resum,
                url_document = :url_document
            WHERE estudi_id = :estudi_id
              AND lang = :lang
            LIMIT 1
        ";
        $stmtUpdateI18n = $conn->prepare($sqlUpdateI18n);

        $sqlDeleteI18n = "
            DELETE FROM db_estudis_i18n
            WHERE estudi_id = :estudi_id
              AND lang = :lang
            LIMIT 1
        ";
        $stmtDeleteI18n = $conn->prepare($sqlDeleteI18n);

        foreach ($i18nRows as $lang => $row) {
            $hasContent = ($row['titol'] !== '' || $row['resum'] !== '' || $row['url'] !== '');

            $stmtExistsI18n->execute([
                ':estudi_id' => $id,
                ':lang' => $lang,
            ]);
            $existingI18nId = $stmtExistsI18n->fetchColumn();

            if (!$hasContent) {
                if ($existingI18nId) {
                    $stmtDeleteI18n->execute([
                        ':estudi_id' => $id,
                        ':lang' => $lang,
                    ]);
                }
                continue;
            }

            if ($existingI18nId) {
                $stmtUpdateI18n->bindValue(':titol', $row['titol'] !== '' ? $row['titol'] : null, $row['titol'] !== '' ? PDO::PARAM_STR : PDO::PARAM_NULL);
                $stmtUpdateI18n->bindValue(':resum', $row['resum'] !== '' ? $row['resum'] : null, $row['resum'] !== '' ? PDO::PARAM_STR : PDO::PARAM_NULL);
                $stmtUpdateI18n->bindValue(':url_document', $row['url'] !== '' ? $row['url'] : null, $row['url'] !== '' ? PDO::PARAM_STR : PDO::PARAM_NULL);
                $stmtUpdateI18n->bindValue(':estudi_id', $id, PDO::PARAM_INT);
                $stmtUpdateI18n->bindValue(':lang', $lang, PDO::PARAM_STR);
                $stmtUpdateI18n->execute();
            } else {
                $stmtInsertI18n->bindValue(':estudi_id', $id, PDO::PARAM_INT);
                $stmtInsertI18n->bindValue(':lang', $lang, PDO::PARAM_STR);
                $stmtInsertI18n->bindValue(':titol', $row['titol'] !== '' ? $row['titol'] : null, $row['titol'] !== '' ? PDO::PARAM_STR : PDO::PARAM_NULL);
                $stmtInsertI18n->bindValue(':resum', $row['resum'] !== '' ? $row['resum'] : null, $row['resum'] !== '' ? PDO::PARAM_STR : PDO::PARAM_NULL);
                $stmtInsertI18n->bindValue(':url_document', $row['url'] !== '' ? $row['url'] : null, $row['url'] !== '' ? PDO::PARAM_STR : PDO::PARAM_NULL);
                $stmtInsertI18n->execute();
            }
        }

        // 3) Rehacer autors
        $stmtDeleteAutors = $conn->prepare("
            DELETE FROM db_estudis_autors
            WHERE estudi_id = :estudi_id
        ");
        $stmtDeleteAutors->execute([':estudi_id' => $id]);

        $sqlAutors = "
            INSERT INTO db_estudis_autors
                (estudi_id, autor_id, sort_order)
            VALUES
                (:estudi_id, :autor_id, :sort_order)
        ";
        $stmtAutors = $conn->prepare($sqlAutors);

        foreach ($autorsNet as $index => $autorId) {
            $stmtAutors->bindValue(':estudi_id', $id, PDO::PARAM_INT);
            $stmtAutors->bindValue(':autor_id', $autorId, PDO::PARAM_INT);
            $stmtAutors->bindValue(':sort_order', $index + 1, PDO::PARAM_INT);
            $stmtAutors->execute();
        }

        $conn->commit();

        // 4) Auditoria
        Audit::registrarCanvi(
            $conn,
            $userId,
            "Modificació estudi",
            "UPDATE",
            Tables::DB_ESTUDIS,
            $id
        );

        Response::success(
            "L'estudi s'ha modificat correctament.",
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
