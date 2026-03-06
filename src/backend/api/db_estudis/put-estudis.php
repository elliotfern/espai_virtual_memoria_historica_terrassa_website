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
} else {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Something get wrong']);
    exit();
}
