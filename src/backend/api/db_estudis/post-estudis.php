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
} else {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Something get wrong']);
    exit();
}
