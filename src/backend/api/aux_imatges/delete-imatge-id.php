<?php

declare(strict_types=1);

use App\Config\DatabaseConnection;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

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

$userId = getAuthenticatedUserId();
if (!$userId) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit;
}


try {
    // Lee JSON o x-www-form-urlencoded
    $raw = file_get_contents('php://input') ?: '';
    $data = json_decode($raw, true);
    $idPersona = isset($data['idPersona']) ? (int)$data['idPersona']
        : (isset($_POST['idPersona']) ? (int)$_POST['idPersona'] : 0);

    if ($idImatge <= 0) {
        throw new RuntimeException('idPersona invàlid.');
    }

    $conn = DatabaseConnection::getConnection();
    if (!$conn) throw new RuntimeException('Sense connexió a BD.');

    // (Opcional) comprueba que existe
    $chk = $conn->prepare('SELECT id FROM db_dades_personals WHERE idPersona=:idPersona');
    $chk->execute([':id' => $idPersona]);
    if (!$chk->fetchColumn()) {
        throw new RuntimeException('La fitxa no existeix.');
    }

    // Desvincular imagen (ambos campos a NULL)
    // Si SOLO quieres poner a NULL 'img' y NO 'imatgePerfil',
    // usa esta query en su lugar:
    $upd = $conn->prepare("UPDATE db_dades_personals SET img = NULL WHERE idPersona = :idPersona");

    $upd->execute([':id' => $idPersona]);

    echo json_encode(['status' => 'ok']);
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
