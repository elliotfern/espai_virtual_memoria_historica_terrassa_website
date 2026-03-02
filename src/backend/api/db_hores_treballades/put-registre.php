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

header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: PUT");

$allowedOrigin = DOMAIN;
checkReferer($allowedOrigin);

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    Response::error('Method not allowed', [], 405);
    exit;
}

/**
 * PUT : Actualitzar registre hores (del ME)
 * URL: https://memoriaterrassa.cat/api/hores/put/hores
 * Body JSON: { id, dia, hores, tipusId, descripcio }
 */
if ($slug === "hores") {

    // Auth
    $userUuidAuth = getAuthenticatedUserId();
    $userId = getAuthenticatedUserId();
    if (!$userUuidAuth || !$userId) {
        Response::error(
            MissatgesAPI::error('no_autenticat'),
            [],
            401
        );
        return;
    }

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
    $id       = isset($data['id']) ? (int)$data['id'] : 0;
    $dia      = isset($data['dia']) ? trim((string)$data['dia']) : '';
    $hores    = isset($data['hores']) ? (int)$data['hores'] : -1;
    $tipusId  = isset($data['tipusId']) ? (int)$data['tipusId'] : 0;
    $descr    = isset($data['descripcio']) ? trim((string)$data['descripcio']) : null;
    if ($descr === '') $descr = null;

    $errors = [];

    if ($id <= 0) {
        $errors[] = "Falta un 'id' vàlid.";
    }

    if ($dia === '' || !preg_match('~^\d{4}\-(0[1-9]|1[0-2])\-(0[1-9]|[12]\d|3[01])$~', $dia)) {
        $errors[] = "Camp 'dia' invàlid (format YYYY-MM-DD).";
    }

    if ($hores < 0 || $hores > 16) {
        $errors[] = "Camp 'hores' invàlid (0..16).";
    }

    if ($tipusId <= 0) {
        $errors[] = "Camp 'tipusId' és obligatori.";
    } else {
        $stmt = $conn->prepare("SELECT 1 FROM aux_tipus_tasca WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $tipusId]);
        if (!$stmt->fetchColumn()) {
            $errors[] = "No existeix el tipus de tasca indicat (tipusId=$tipusId).";
        }
    }

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

        // 1) Verifica que el registre existeix i és del ME
        $sqlOwn = "
            SELECT id, dia
            FROM db_hores_treballades
            WHERE id = :id
              AND user_id = :user_id
            LIMIT 1
        ";
        $stmtOwn = $conn->prepare($sqlOwn);
        $stmtOwn->execute([
            ':id' => $id,
            ':user_id' => $userUuidAuth,
        ]);

        $row = $stmtOwn->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            Response::error(
                MissatgesAPI::error('not_found'),
                ['Registre inexistent o sense permisos'],
                404
            );
            return;
        }

        // 2) Control UNIQUE (user_id, dia) si canvia el dia i ja existeix un altre registre aquell dia
        $sqlDup = "
            SELECT id
            FROM db_hores_treballades
            WHERE user_id = :user_id
              AND dia = :dia
              AND id <> :id
            LIMIT 1
        ";
        $stmtDup = $conn->prepare($sqlDup);
        $stmtDup->execute([
            ':user_id' => $userUuidAuth,
            ':dia' => $dia,
            ':id' => $id,
        ]);
        $dupId = $stmtDup->fetchColumn();

        if ($dupId) {
            Response::error(
                "Ja existeix un altre registre per aquest dia.",
                ['existing_id' => (int)$dupId],
                409
            );
            return;
        }

        // 3) Update
        $sqlUp = "
            UPDATE db_hores_treballades
            SET
                dia = :dia,
                hores = :hores,
                tipus_id = :tipus_id,
                descripcio = :descripcio
            WHERE id = :id
              AND user_id = :user_id
            LIMIT 1
        ";

        $stmtUp = $conn->prepare($sqlUp);
        $stmtUp->bindValue(':dia', $dia, PDO::PARAM_STR);
        $stmtUp->bindValue(':hores', $hores, PDO::PARAM_INT);
        $stmtUp->bindValue(':tipus_id', $tipusId, PDO::PARAM_INT);
        $stmtUp->bindValue(':descripcio', $descr, $descr === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmtUp->bindValue(':id', $id, PDO::PARAM_INT);
        $stmtUp->bindValue(':user_id', $userUuidAuth, PDO::PARAM_INT);

        $stmtUp->execute();

        // 4) Auditoria
        $tipusOperacio = "Modificació registre hores";
        $detalls = "UPDATE";
        Audit::registrarCanvi(
            $conn,
            $userId,
            $tipusOperacio,
            $detalls,
            Tables::DB_HORES_TREBALLADES ?? 'db_hores_treballades',
            $id
        );

        Response::success(
            "El registre d'hores s'ha actualitzat correctament.",
            ['id' => $id],
            200
        );
        return;
    } catch (PDOException $e) {

        // Duplicate key por UNIQUE(user_id,dia)
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
