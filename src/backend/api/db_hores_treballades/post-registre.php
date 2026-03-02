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
 * POST : Crear registre hores (1 per dia i usuari)
 * URL: https://memoriaterrassa.cat/api/hores/post/hores
 */
if ($slug === "hores") {

    // Auth
    $userUuidAuth = getAuthenticatedUserUuid(); // ✅ UUID string
    $userId = getAuthenticatedUserUuid();         // ✅ int (auditoria)      // per auditoria (si la tens així)
    if (!$userUuidAuth || !$userId) {
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

    // Recollir camps
    $dia       = isset($data['dia']) ? trim((string)$data['dia']) : '';
    $hores     = isset($data['hores']) ? (int)$data['hores'] : -1;
    $tipusId   = isset($data['tipusId']) ? (int)$data['tipusId'] : 0;
    $descr     = isset($data['descripcio']) ? trim((string)$data['descripcio']) : null;
    if ($descr === '') $descr = null;

    $errors = [];

    // Validació dia
    if ($dia === '' || !preg_match('~^\d{4}\-(0[1-9]|1[0-2])\-(0[1-9]|[12]\d|3[01])$~', $dia)) {
        $errors[] = "Camp 'dia' invàlid (format YYYY-MM-DD).";
    }

    // Validació hores (enteres)
    if ($hores < 0 || $hores > 16) {
        $errors[] = "Camp 'hores' invàlid (0..16).";
    }

    // Validació tipus
    if ($tipusId <= 0) {
        $errors[] = "Camp 'tipusId' és obligatori.";
    } else {
        $stmt = $conn->prepare("SELECT 1 FROM aux_tipus_tasca WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $tipusId]);
        if (!$stmt->fetchColumn()) {
            $errors[] = "No existeix el tipus de tasca indicat (tipusId=$tipusId).";
        }
    }

    // Validació descripció
    if ($descr !== null && mb_strlen($descr, 'UTF-8') > 500) {
        $errors[] = "La descripció no pot superar 500 caràcters.";
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

        // 1) Comprovar duplicat (1 registre/dia/usuari)
        $sqlCheck = "
            SELECT id
            FROM db_hores_treballades
            WHERE user_uuid = UNHEX(REPLACE(:user_uuid, '-', ''))
              AND dia = :dia
            LIMIT 1
        ";
        $stmtCheck = $conn->prepare($sqlCheck);
        $stmtCheck->execute([
            ':user_uuid' => $userUuidAuth,
            ':dia'       => $dia,
        ]);
        $existingId = $stmtCheck->fetchColumn();

        if ($existingId) {
            Response::error(
                "Ja existeix un registre per aquest dia.",
                ['existing_id' => (int)$existingId],
                409
            );
            return;
        }

        // 2) Insert
        $sql = "
            INSERT INTO db_hores_treballades
                (user_uuid, dia, hores, tipus_id, descripcio, created_at)
            VALUES
                (UNHEX(REPLACE(:user_uuid, '-', '')), :dia, :hores, :tipus_id, :descripcio, NOW())
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':user_uuid', $userUuidAuth, PDO::PARAM_STR);
        $stmt->bindValue(':dia', $dia, PDO::PARAM_STR);
        $stmt->bindValue(':hores', $hores, PDO::PARAM_INT);
        $stmt->bindValue(':tipus_id', $tipusId, PDO::PARAM_INT);
        $stmt->bindValue(':descripcio', $descr, $descr === null ? PDO::PARAM_NULL : PDO::PARAM_STR);

        $stmt->execute();
        $id = (int)$conn->lastInsertId();

        // 3) Auditoria (si l'estàs usant)
        // Ajusta Tables::... si tens constant per aquesta taula
        $tipusOperacio = "Creació registre hores";
        $detalls = "INSERT";
        Audit::registrarCanvi(
            $conn,
            $userId,
            $tipusOperacio,
            $detalls,
            Tables::DB_HORES_TREBALLADES,
            $id
        );

        Response::success(
            "El registre d'hores s'ha desat correctament.",
            ['id' => $id],
            200
        );
        return;
    } catch (PDOException $e) {

        // Duplicate key (per si entra pel UNIQUE també)
        $code = $e->errorInfo[1] ?? null;
        if ((int)$code === 1062) {
            Response::error(
                "Ja existeix un registre per aquest dia.",
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
