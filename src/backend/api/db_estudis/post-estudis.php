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
header("Access-Control-Allow-Methods: POST");

$allowedOrigin = DOMAIN;
checkReferer($allowedOrigin);

// Method check
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::error('Method not allowed', [], 405);
    exit;
}

/**
 * POST : Crear període històric
 * URL: /api/estudis/post/periode
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
    $sortOrder = isset($data['sort_order']) ? (int)$data['sort_order'] : -1;

    $nomCa = isset($data['nom_ca']) ? trim((string)$data['nom_ca']) : '';
    $nomEs = isset($data['nom_es']) ? trim((string)$data['nom_es']) : '';
    $nomEn = isset($data['nom_en']) ? trim((string)$data['nom_en']) : '';
    $nomFr = isset($data['nom_fr']) ? trim((string)$data['nom_fr']) : '';
    $nomIt = isset($data['nom_it']) ? trim((string)$data['nom_it']) : '';
    $nomPt = isset($data['nom_pt']) ? trim((string)$data['nom_pt']) : '';

    $errors = [];

    // Validacions
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

        // 1) INSERT taula principal
        $sqlPeriode = "
            INSERT INTO db_estudis_periodes
                (sort_order)
            VALUES
                (:sort_order)
        ";
        $stmtPeriode = $conn->prepare($sqlPeriode);
        $stmtPeriode->bindValue(':sort_order', $sortOrder, PDO::PARAM_INT);
        $stmtPeriode->execute();

        $periodeId = (int)$conn->lastInsertId();

        // 2) INSERT i18n
        $i18nRows = [
            'ca' => $nomCa,
            'es' => $nomEs,
            'en' => $nomEn,
            'fr' => $nomFr,
            'it' => $nomIt,
            'pt' => $nomPt,
        ];

        $sqlI18n = "
            INSERT INTO db_estudis_periodes_i18n
                (periode_id, lang, nom)
            VALUES
                (:periode_id, :lang, :nom)
        ";
        $stmtI18n = $conn->prepare($sqlI18n);

        foreach ($i18nRows as $lang => $nom) {
            if ($nom === '') {
                continue;
            }

            $stmtI18n->bindValue(':periode_id', $periodeId, PDO::PARAM_INT);
            $stmtI18n->bindValue(':lang', $lang, PDO::PARAM_STR);
            $stmtI18n->bindValue(':nom', $nom, PDO::PARAM_STR);
            $stmtI18n->execute();
        }

        $conn->commit();

        // 3) Auditoria
        $tipusOperacio = "Creació període històric";
        $detalls = "INSERT";
        Audit::registrarCanvi(
            $conn,
            $userId,
            $tipusOperacio,
            $detalls,
            Tables::DB_ESTUDIS_PERIODES,
            $periodeId
        );

        Response::success(
            "El període històric s'ha desat correctament.",
            ['id' => $periodeId],
            200
        );
        return;
    } catch (PDOException $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }

        $code = $e->errorInfo[1] ?? null;

        // Per si tens UNIQUE (periode_id, lang) i alguna inserció rara duplica
        if ((int)$code === 1062) {
            Response::error(
                "Ja existeix un registre duplicat.",
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
     * POST : Crear territori
     * URL: /api/estudis/post/territori
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
    $sortOrder = isset($data['sort_order']) ? (int)$data['sort_order'] : -1;

    $nomCa = isset($data['nom_ca']) ? trim((string)$data['nom_ca']) : '';
    $nomEs = isset($data['nom_es']) ? trim((string)$data['nom_es']) : '';
    $nomEn = isset($data['nom_en']) ? trim((string)$data['nom_en']) : '';
    $nomFr = isset($data['nom_fr']) ? trim((string)$data['nom_fr']) : '';
    $nomIt = isset($data['nom_it']) ? trim((string)$data['nom_it']) : '';
    $nomPt = isset($data['nom_pt']) ? trim((string)$data['nom_pt']) : '';

    $errors = [];

    // Validacions
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

        // 1) INSERT taula principal
        $sqlTerritori = "
            INSERT INTO db_estudis_territoris
                (sort_order)
            VALUES
                (:sort_order)
        ";
        $stmtTerritori = $conn->prepare($sqlTerritori);
        $stmtTerritori->bindValue(':sort_order', $sortOrder, PDO::PARAM_INT);
        $stmtTerritori->execute();

        $territoriId = (int)$conn->lastInsertId();

        // 2) INSERT i18n
        $i18nRows = [
            'ca' => $nomCa,
            'es' => $nomEs,
            'en' => $nomEn,
            'fr' => $nomFr,
            'it' => $nomIt,
            'pt' => $nomPt,
        ];

        $sqlI18n = "
            INSERT INTO db_estudis_territoris_i18n
                (territori_id, lang, nom)
            VALUES
                (:territori_id, :lang, :nom)
        ";
        $stmtI18n = $conn->prepare($sqlI18n);

        foreach ($i18nRows as $lang => $nom) {
            if ($nom === '') {
                continue;
            }

            $stmtI18n->bindValue(':territori_id', $territoriId, PDO::PARAM_INT);
            $stmtI18n->bindValue(':lang', $lang, PDO::PARAM_STR);
            $stmtI18n->bindValue(':nom', $nom, PDO::PARAM_STR);
            $stmtI18n->execute();
        }

        $conn->commit();

        // 3) Auditoria
        Audit::registrarCanvi(
            $conn,
            $userId,
            "Creació territori",
            "INSERT",
            Tables::DB_ESTUDIS_TERRITORIS,
            $territoriId
        );

        Response::success(
            "El territori s'ha desat correctament.",
            ['id' => $territoriId],
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
                "Ja existeix un registre duplicat.",
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
     * POST : Crear tipus
     * URL: /api/estudis/post/tipus
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
    $sortOrder = isset($data['sort_order']) ? (int)$data['sort_order'] : -1;

    $nomCa = isset($data['nom_ca']) ? trim((string)$data['nom_ca']) : '';
    $nomEs = isset($data['nom_es']) ? trim((string)$data['nom_es']) : '';
    $nomEn = isset($data['nom_en']) ? trim((string)$data['nom_en']) : '';
    $nomFr = isset($data['nom_fr']) ? trim((string)$data['nom_fr']) : '';
    $nomIt = isset($data['nom_it']) ? trim((string)$data['nom_it']) : '';
    $nomPt = isset($data['nom_pt']) ? trim((string)$data['nom_pt']) : '';

    $errors = [];

    // Validacions
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

        // 1) INSERT taula principal
        $sqlTipus = "
            INSERT INTO db_estudis_tipus
                (sort_order)
            VALUES
                (:sort_order)
        ";
        $stmtTipus = $conn->prepare($sqlTipus);
        $stmtTipus->bindValue(':sort_order', $sortOrder, PDO::PARAM_INT);
        $stmtTipus->execute();

        $tipusId = (int)$conn->lastInsertId();

        // 2) INSERT i18n
        $i18nRows = [
            'ca' => $nomCa,
            'es' => $nomEs,
            'en' => $nomEn,
            'fr' => $nomFr,
            'it' => $nomIt,
            'pt' => $nomPt,
        ];

        $sqlI18n = "
            INSERT INTO db_estudis_tipus_i18n
                (tipus_id, lang, nom)
            VALUES
                (:tipus_id, :lang, :nom)
        ";
        $stmtI18n = $conn->prepare($sqlI18n);

        foreach ($i18nRows as $lang => $nom) {
            if ($nom === '') {
                continue;
            }

            $stmtI18n->bindValue(':tipus_id', $tipusId, PDO::PARAM_INT);
            $stmtI18n->bindValue(':lang', $lang, PDO::PARAM_STR);
            $stmtI18n->bindValue(':nom', $nom, PDO::PARAM_STR);
            $stmtI18n->execute();
        }

        $conn->commit();

        // 3) Auditoria
        Audit::registrarCanvi(
            $conn,
            $userId,
            "Creació tipus",
            "INSERT",
            Tables::DB_ESTUDIS_TIPUS,
            $tipusId
        );

        Response::success(
            "El tipus s'ha desat correctament.",
            ['id' => $tipusId],
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
                "Ja existeix un registre duplicat.",
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
     * POST : Crear estudi
     * URL: /api/estudis/post/estudi
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

    if ($urlCa === '') {
        $errors[] = "El camp 'url_document_ca' és obligatori.";
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

        // Validacions de FK / unicitat abans de transacció
        $stmtSlug = $conn->prepare("SELECT id FROM db_estudis WHERE slug = :slug LIMIT 1");
        $stmtSlug->execute([':slug' => $slugValue]);
        if ($stmtSlug->fetchColumn()) {
            Response::error(
                "Ja existeix un estudi amb aquest slug.",
                [],
                409
            );
            return;
        }

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

        $stmtAutor = $conn->prepare("SELECT id FROM auth_users WHERE id = :id LIMIT 1");
        foreach ($autorsNet as $autorId) {
            $stmtAutor->execute([':id' => $autorId]);
            if (!$stmtAutor->fetchColumn()) {
                Response::error("Un dels autors indicats no existeix.", ['autor_id' => $autorId], 400);
                return;
            }
        }

        $conn->beginTransaction();

        // 1) INSERT principal
        $sqlEstudi = "
            INSERT INTO db_estudis
                (slug, periode_id, territori_id, tipus_id, any_publicacio, date_created)
            VALUES
                (:slug, :periode_id, :territori_id, :tipus_id, :any_publicacio, NOW())
        ";
        $stmtEstudi = $conn->prepare($sqlEstudi);
        $stmtEstudi->bindValue(':slug', $slugValue, PDO::PARAM_STR);
        $stmtEstudi->bindValue(':periode_id', $periodeId, PDO::PARAM_INT);
        $stmtEstudi->bindValue(':territori_id', $territoriId, PDO::PARAM_INT);
        $stmtEstudi->bindValue(':tipus_id', $tipusId, PDO::PARAM_INT);
        $stmtEstudi->bindValue(':any_publicacio', $anyPublicacio, $anyPublicacio === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmtEstudi->execute();

        $estudiId = (int)$conn->lastInsertId();

        // 2) INSERT i18n
        $i18nRows = [
            'ca' => ['titol' => $titolCa, 'resum' => $resumCa, 'url' => $urlCa],
            'es' => ['titol' => $titolEs, 'resum' => $resumEs, 'url' => $urlEs],
            'en' => ['titol' => $titolEn, 'resum' => $resumEn, 'url' => $urlEn],
            'fr' => ['titol' => $titolFr, 'resum' => $resumFr, 'url' => $urlFr],
            'it' => ['titol' => $titolIt, 'resum' => $resumIt, 'url' => $urlIt],
            'pt' => ['titol' => $titolPt, 'resum' => $resumPt, 'url' => $urlPt],
        ];

        $sqlI18n = "
            INSERT INTO db_estudis_i18n
                (estudi_id, lang, titol, resum, url_document)
            VALUES
                (:estudi_id, :lang, :titol, :resum, :url_document)
        ";
        $stmtI18n = $conn->prepare($sqlI18n);

        foreach ($i18nRows as $lang => $row) {
            $hasContent = ($row['titol'] !== '' || $row['resum'] !== '' || $row['url'] !== '');
            if (!$hasContent) {
                continue;
            }

            $stmtI18n->bindValue(':estudi_id', $estudiId, PDO::PARAM_INT);
            $stmtI18n->bindValue(':lang', $lang, PDO::PARAM_STR);
            $stmtI18n->bindValue(':titol', $row['titol'] !== '' ? $row['titol'] : null, $row['titol'] !== '' ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmtI18n->bindValue(':resum', $row['resum'] !== '' ? $row['resum'] : null, $row['resum'] !== '' ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmtI18n->bindValue(':url_document', $row['url'] !== '' ? $row['url'] : null, $row['url'] !== '' ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmtI18n->execute();
        }

        // 3) INSERT autors
        $sqlAutors = "
            INSERT INTO db_estudis_autors
                (estudi_id, autor_id, sort_order)
            VALUES
                (:estudi_id, :autor_id, :sort_order)
        ";
        $stmtAutors = $conn->prepare($sqlAutors);

        foreach ($autorsNet as $index => $autorId) {
            $stmtAutors->bindValue(':estudi_id', $estudiId, PDO::PARAM_INT);
            $stmtAutors->bindValue(':autor_id', $autorId, PDO::PARAM_INT);
            $stmtAutors->bindValue(':sort_order', $index + 1, PDO::PARAM_INT);
            $stmtAutors->execute();
        }

        $conn->commit();

        // 4) Auditoria
        Audit::registrarCanvi(
            $conn,
            $userId,
            "Creació estudi",
            "INSERT",
            Tables::DB_ESTUDIS,
            $estudiId
        );

        Response::success(
            "L'estudi s'ha desat correctament.",
            ['id' => $estudiId],
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
