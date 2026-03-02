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
 * GET : Fitxa hores per ID (Intranet)
 * URL: https://memoriaterrassa.cat/api/hores/get/horesId?id=123
 */
if ($slug === "horesId") {

    // ✅ Auth required
    $userUuidAuth = getAuthenticatedUserId(); // string UUID (xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx) o null
    if (!$userUuidAuth) {
        Response::error(
            MissatgesAPI::error('no_autenticat'),
            [],
            401
        );
        return;
    }

    $isAdmin = isUserAdmin();

    // Validació bàsica de l'ID
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

    // Helpers SQL per convertir BINARY(16) a UUID string (format estàndard)
    // (mateix estil que ja estàs fent amb HEX + SUBSTR)
    $sqlUuid = "LOWER(CONCAT_WS('-',
                    SUBSTR(HEX(h.user_uuid), 1, 8),
                    SUBSTR(HEX(h.user_uuid), 9, 4),
                    SUBSTR(HEX(h.user_uuid), 13, 4),
                    SUBSTR(HEX(h.user_uuid), 17, 4),
                    SUBSTR(HEX(h.user_uuid), 21)
                ))";

    // Si no admin: forcem que només pugui llegir el seu propi registre
    $whereOwner = $isAdmin ? "" : " AND h.user_uuid = UUID_TO_BIN(:user_uuid)";

    $query = "
        SELECT
            h.id,
            {$sqlUuid} AS user_uuid,
            h.dia,
            h.hores,
            h.tipus_id AS tipusId,
            t.nom AS tipusNom,
            h.descripcio,
            h.created_at,
            h.updated_at
        FROM db_hores_treballades AS h
        LEFT JOIN aux_tipus_tasca AS t ON t.id = h.tipus_id
        WHERE h.id = :id
        {$whereOwner}
        LIMIT 1
    ";

    try {
        $params = [
            ':id' => $id,
        ];

        if (!$isAdmin) {
            $params[':user_uuid'] = $userUuidAuth;
        }

        // true => UNA sola fila
        $result = $db->getData($query, $params, true);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;
        }

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
        return;
    }

    /**
     * GET : Llistat hores del ME (Intranet)
     * URL: https://memoriaterrassa.cat/api/hores/get/llistatMe?month=2026-03
     */
} else if ($slug === "llistatMe") {

    // ✅ Auth required
    $userUuidAuth = getAuthenticatedUserId(); // UUID string o null
    if (!$userUuidAuth) {
        Response::error(
            MissatgesAPI::error('no_autenticat'),
            [],
            401
        );
        return;
    }

    $db = new Database();

    // Filtro mes opcional: YYYY-MM
    $month = isset($_GET['month']) ? trim((string)$_GET['month']) : '';
    $whereMonth = "";
    $params = [
        ':user_uuid' => $userUuidAuth,
    ];

    if ($month !== '') {
        if (!preg_match('~^\d{4}\-(0[1-9]|1[0-2])$~', $month)) {
            Response::error(
                MissatgesAPI::error('parametres'),
                ['Paràmetre month invàlid (format YYYY-MM)'],
                400
            );
            return;
        }

        // Rang [month-01, nextMonth-01)
        $start = $month . '-01';
        $dt = new DateTime($start);
        $dt->modify('+1 month');
        $end = $dt->format('Y-m-d');

        $whereMonth = " AND h.dia >= :start AND h.dia < :end";
        $params[':start'] = $start;
        $params[':end'] = $end;
    }

    $query = "
        SELECT
            h.id,
            h.dia,
            h.hores,
            h.tipus_id AS tipusId,
            t.nom AS tipusNom,
            h.descripcio,
            h.created_at,
            h.updated_at
        FROM db_hores_treballades AS h
        LEFT JOIN aux_tipus_tasca AS t ON t.id = h.tipus_id
        WHERE h.user_uuid = UUID_TO_BIN(:user_uuid)
        {$whereMonth}
        ORDER BY h.dia DESC, h.id DESC
    ";

    try {
        $result = $db->getData($query, $params);

        if (empty($result)) {
            // en un llistat jo prefereixo retornar [] amb 200
            Response::success(
                MissatgesAPI::success('get'),
                [],
                200
            );
            return;
        }

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }
} else {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Something get wrong']);
    exit();
}
