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
header("Content-Type: application/json");
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

// GET : Llistat missatges rebuts
// URL: https://memoriaterrassa.cat/api/form_contacte/get/missatgesRebuts
if ($slug === "missatgesRebuts") {

    $db = new Database();

    $query = "SELECT 
	          c.id, c.nomCognoms, c.email, c.telefon, c.missatge, c.form_ip, c.form_user_agent, c.dataEnviament, u.nom, c.estat, c.nom_represaliat
            FROM db_form_contacte AS c
            LEFT JOIN db_form_contacte_respostes AS r ON r.missatge_id = c.id
            LEFT JOIN auth_users AS u ON r.usuari_id = u.id
            GROUP BY c.id
            ORDER BY id DESC";

    try {
        $result = $db->getData($query);

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
    }

    // GET : Fitxa repressialiat > relació de familiars (Intranet)
    // URL: https://memoriaterrassa.cat/api/form_contacte/get/missatgeId?id=${id}
} else if ($slug === "missatgeId") {

    $db = new Database();
    $id = $_GET['id'];

    $query = "SELECT c.id, c.nomCognoms, c.email, c.telefon, c.missatge, c.form_ip, c.form_user_agent, c.dataEnviament, c.estat, c.nom_represaliat
            FROM db_form_contacte AS c
            WHERE c.id = :id";

    try {
        $params = [':id' => $id];
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
    }

    // GET : Respostes emails missatges rebuts
    // URL: https://memoriaterrassa.cat/api/form_contacte/get/RespostaId?id=${id}
} else if ($slug === "RespostaId") {

    $db = new Database();
    $id = $_GET['id'];

    $query = "SELECT r.id, r.missatge_id, r.usuari_id, r.resposta_subject, r.resposta_text, r.email_destinatari, r.data_resposta, r.created_at, r.updated_at, u.nom
            FROM db_form_contacte_respostes AS r
            LEFT JOIN auth_users AS u ON r.usuari_id = u.id
            WHERE r.missatge_id = :id";

    try {
        $params = [':id' => $id];
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
    }

    // GET : Respostes emails missatges rebuts
    // URL: https://memoriaterrassa.cat/api/form_contacte/get/conversacio?id=${id}
} else if ($slug === "conversacio") {


    $db = new Database();

    // Validació bàsica de l'ID
    if (!isset($_GET['id']) || !ctype_digit((string)$_GET['id'])) {
        Response::error(
            MissatgesAPI::error('parametres'),
            ['ID de missatge no vàlid'],
            400
        );
        return;
    }

    $id = (int)$_GET['id'];

    try {
        // 1) Missatge original
        $queryMissatge = "
            SELECT 
                c.id,
                c.nomCognoms,
                c.email,
                c.telefon,
                c.missatge,
                c.form_ip,
                c.form_user_agent,
                c.dataEnviament,
                c.estat,
                c.nom_represaliat
            FROM db_form_contacte AS c
            WHERE c.id = :id
        ";

        // true => UNA sola fila (ajusta si en el teu getData funciona al revés)
        $missatge = $db->getData($queryMissatge, [':id' => $id], true);

        if (empty($missatge)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;
        }

        // 2) Respostes del gestor (intranet)
        $queryRespostesGestor = "
            SELECT 
                r.id,
                r.missatge_id,
                r.usuari_id,
                r.resposta_subject,
                r.resposta_text,
                r.email_destinatari,
                r.data_resposta,
                r.created_at,
                r.updated_at,
                u.nom
            FROM db_form_contacte_respostes AS r
            LEFT JOIN auth_users AS u ON r.usuari_id = u.id
            WHERE r.missatge_id = :id
            ORDER BY r.data_resposta ASC
        ";

        // false => MÚLTIPLES files (ajusta si cal)
        $respostesGestor = $db->getData($queryRespostesGestor, [':id' => $id], false);
        if (empty($respostesGestor)) {
            $respostesGestor = [];
        }

        // 3) Respostes via email (usuari)
        $queryRespostesEmail = "
            SELECT 
                e.id,
                e.missatge_id,
                e.email_remitent,
                e.email_rebut,
                e.subject,
                e.body,
                e.rebut_a,
                e.created_at
            FROM db_form_contacte_respostes_email AS e
            WHERE e.missatge_id = :id
            ORDER BY e.rebut_a ASC
        ";

        $respostesEmail = $db->getData($queryRespostesEmail, [':id' => $id], false);
        if (empty($respostesEmail)) {
            $respostesEmail = [];
        }

        // 4) Construir l'objecte data que espera el frontend
        $data = [
            'missatge'         => $missatge,
            'respostes_gestor' => $respostesGestor,
            'respostes_email'  => $respostesEmail,
        ];

        Response::success(
            MissatgesAPI::success('get'),
            $data,
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
    // Si 'type', 'id' o 'token' están ausentes o 'type' no es 'user' en la URL
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Something get wrong']);
    exit();
}
