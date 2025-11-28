<?php

use App\Config\Database;
use App\Utils\Response;
use App\Utils\MissatgesAPI;
use App\Config\DatabaseConnection;

$conn = DatabaseConnection::getConnection();

// ✅ Crear instància de la teva classe Database (AQUÍ ESTAVA EL PROBLEMA)
$db = new Database();

if (!$conn) {
    throw new Exception("No es pot establir connexió amb la base de dades.");
}

// Configuración de cabeceras para aceptar JSON y responder JSON
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");

// Definir el dominio permitido
$allowedOrigin = DOMAIN;

// Llamar a la función para verificar el referer
checkReferer($allowedOrigin);

// Verificar que el método de la solicitud sea GET
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// Leer JSON del body
$inputData = file_get_contents('php://input');
$data = json_decode($inputData, true) ?? [];

if (empty($data['id']) || !ctype_digit((string)$data['id'])) {
    Response::error(
        MissatgesAPI::error('parametres'),
        ['ID de missatge no vàlid'],
        400
    );
    return;
}

$id = (int)$data['id'];

// Datos a actualizar
$updateData = [
    'estat' => 4,
];

// WHERE y parámetros
$where = 'id = :id';
$whereParams = [
    ':id' => $id,
];

try {
    $ok = $db->updateData('db_form_contacte', $updateData, $where, $whereParams);

    if (!$ok) {
        // Puede ser que no haya filas afectadas (id no existe o ya estaba a 4)
        Response::error(
            MissatgesAPI::error('errorBD'),
            ['No s\'ha pogut actualitzar l\'estat del missatge o ja estava tancat.'],
            500
        );
        return;
    }

    Response::success(
        'Conversa tancada correctament.',
        [],
        200
    );
} catch (PDOException $e) {
    Response::error(
        MissatgesAPI::error('errorBD'),
        [$e->getMessage()],
        500
    );
}
