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

    // Si no admin: forcem que només pugui llegir el seu propi registre
    $whereOwner = $isAdmin ? "" : " AND h.user_id = :user_id";

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
        WHERE h.id = :id
        {$whereOwner}
        LIMIT 1
    ";

    try {
        $params = [
            ':id' => $id,
        ];

        if (!$isAdmin) {
            $params[':user_id'] = $userUuidAuth;
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
        ':user_id' => $userUuidAuth,
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
        WHERE h.user_id = :user_id
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

    /**
     * GET : Llistat complet registre horari (ADMIN)
     * URL: https://memoriaterrassa.cat/api/hores/get/llistatAdmin?month=2026-03
     */
} else if ($slug === "llistatAdmin") {

    // Auth
    $userId = getAuthenticatedUserId();
    if (!$userId) {
        Response::error(MissatgesAPI::error('no_autenticat'), [], 401);
        return;
    }

    // Admin only
    if (!isUserAdmin()) {
        Response::error(MissatgesAPI::error('no_permisos'), [], 403);
        return;
    }

    $db = new Database();

    // Filtro mes opcional
    $month = isset($_GET['month']) ? trim((string)$_GET['month']) : '';
    $whereMonth = '';
    $params = [];

    if ($month !== '') {
        if (!preg_match('~^\d{4}\-(0[1-9]|1[0-2])$~', $month)) {
            Response::error(MissatgesAPI::error('parametres'), ['Paràmetre month invàlid (YYYY-MM)'], 400);
            return;
        }

        $start = $month . '-01';
        $dt = new DateTime($start);
        $dt->modify('+1 month');
        $end = $dt->format('Y-m-d');

        $whereMonth = " WHERE h.dia >= :start AND h.dia < :end ";
        $params[':start'] = $start;
        $params[':end'] = $end;
    }

    $query = "
        SELECT
            h.id,
            h.user_id AS userId,
            u.nom AS userNom,
            u.email AS userEmail,
            h.dia,
            h.hores,
            h.tipus_id AS tipusId,
            t.nom AS tipusNom,
            h.descripcio,
            h.created_at,
            h.updated_at
        FROM db_hores_treballades AS h
        LEFT JOIN auth_users AS u ON u.id = h.user_id
        LEFT JOIN aux_tipus_tasca AS t ON t.id = h.tipus_id
        {$whereMonth}
        ORDER BY h.dia DESC, h.id DESC
    ";

    try {
        $result = $db->getData($query, $params);

        // En listados, mejor devolver [] con 200
        if (empty($result)) $result = [];

        Response::success(MissatgesAPI::success('get'), $result, 200);
        return;
    } catch (PDOException $e) {
        Response::error(MissatgesAPI::error('errorBD'), [$e->getMessage()], 500);
        return;
    }

    /**
     * GET : Mesos disponibles (ADMIN)
     * URL: https://memoriaterrassa.cat/api/hores/get/mesosDisponiblesAdmin
     */
} else if ($slug === "mesosDisponiblesAdmin") {

    $userId = getAuthenticatedUserId();
    if (!$userId) {
        Response::error(MissatgesAPI::error('no_autenticat'), [], 401);
        return;
    }

    if (!isUserAdmin()) {
        Response::error(MissatgesAPI::error('no_permisos'), [], 403);
        return;
    }

    $db = new Database();

    $query = "
        SELECT DISTINCT DATE_FORMAT(h.dia, '%Y-%m') AS ym
        FROM db_hores_treballades h
        ORDER BY ym DESC
    ";

    try {
        $rows = $db->getData($query);
        $months = [];

        foreach ($rows as $r) {
            if (!empty($r['ym'])) $months[] = $r['ym'];
        }

        Response::success(MissatgesAPI::success('get'), $months, 200);
        return;
    } catch (PDOException $e) {
        Response::error(MissatgesAPI::error('errorBD'), [$e->getMessage()], 500);
        return;
    }

    /**
     * GET: Resum hores per usuari (ADMIN)
     * URL: /api/hores/get/resumUsuari?user_id=123
     */
} else if ($slug === 'resumUsuari') {

    $adminId = getAuthenticatedUserId();
    if (!$adminId) {
        Response::error(MissatgesAPI::error('no_autenticat'), [], 401);
        return;
    }

    if (!isUserAdmin()) {
        Response::error(MissatgesAPI::error('no_permisos'), [], 403);
        return;
    }

    if (!isset($_GET['user_id']) || !ctype_digit((string)$_GET['user_id'])) {
        Response::error(MissatgesAPI::error('parametres'), ["user_id invàlid"], 400);
        return;
    }

    $userId = (int)$_GET['user_id'];
    $db = new Database();

    try {
        // 1) Info usuari
        $qUser = "SELECT id, nom, email FROM auth_users WHERE id = :id LIMIT 1";
        $user = $db->getData($qUser, [':id' => $userId], true);
        if (empty($user)) {
            Response::error(MissatgesAPI::error('not_found'), [], 404);
            return;
        }

        // 2) Months
        $qMonths = "
      SELECT
        YEAR(h.dia) AS y,
        MONTH(h.dia) AS m,
        SUM(h.hores) AS hores
      FROM db_hores_treballades h
      WHERE h.user_id = :user_id
      GROUP BY YEAR(h.dia), MONTH(h.dia)
      ORDER BY y DESC, m DESC
    ";
        $months = $db->getData($qMonths, [':user_id' => $userId], false);
        if (empty($months)) $months = [];

        // 3) Years
        $qYears = "
      SELECT
        YEAR(h.dia) AS y,
        SUM(h.hores) AS hores
      FROM db_hores_treballades h
      WHERE h.user_id = :user_id
      GROUP BY YEAR(h.dia)
      ORDER BY y DESC
    ";
        $years = $db->getData($qYears, [':user_id' => $userId], false);
        if (empty($years)) $years = [];

        // 4) Total absolut
        $qTotal = "
      SELECT COALESCE(SUM(h.hores), 0) AS hores
      FROM db_hores_treballades h
      WHERE h.user_id = :user_id
    ";
        $totalRow = $db->getData($qTotal, [':user_id' => $userId], true);
        $total = (int)($totalRow['hores'] ?? 0);

        $payload = [
            'user' => $user,
            'months' => $months,
            'years' => $years,
            'total' => $total,
        ];

        Response::success(MissatgesAPI::success('get'), $payload, 200);
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
