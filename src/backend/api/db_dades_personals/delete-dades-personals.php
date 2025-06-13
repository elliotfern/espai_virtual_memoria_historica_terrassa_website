<?php

use App\Config\Tables;
use App\Config\Audit;
use App\Config\DatabaseConnection;

$conn = DatabaseConnection::getConnection();

if (!$conn) {
    die("No se pudo establecer conexión a la base de datos.");
}

$slug = $routeParams[0];

// Configuración de cabeceras para aceptar JSON y responder JSON
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: DELETE");

// Definir el dominio permitido
$allowedOrigin = DOMAIN;

// Llamar a la función para verificar el referer
checkReferer($allowedOrigin);

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin !== 'https://memoriaterrassa.cat') {
    header("HTTP/1.1 403 Forbidden");
    exit;
}

// Verificar que el método de la solicitud sea DELETE
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// verifica que sigui un usuari legitim
$userId = getAuthenticatedUserId();
if (!$userId) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

$userAdmin = isUserAdmin();
if (!$userAdmin) {
    http_response_code(403);
    echo json_encode(['error' => 'No tens permís per eliminar registres.']);
    exit;
}


// 1) DELETE municipi
// ruta DELETE => "/api/dades_personals/delete/eliminaDuplicat?id={id}"
if ($slug === "eliminaDuplicat") {
    global $conn;
    /** @var PDO $conn */

    $id = $_GET['id'] ?? null;
    if (!$id) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'ID no proporcionat']);
        exit;
    }

    // Opcional: evitar eliminar si el municipi no existe
    $stmtCheck = $conn->prepare("SELECT id, nom, cognom1, cognom2 FROM db_dades_personals WHERE id = :id");
    $stmtCheck->bindParam(':id', $id, PDO::PARAM_INT);
    $stmtCheck->execute();

    $municipi = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if (!$municipi) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Registre no trobat']);
        exit;
    }

    $detalls = "Eliminació de fitxa duplicada";
    $tipusOperacio = "DELETE";

    try {
        $stmt = $conn->prepare("DELETE FROM db_dades_personals WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::DB_DADES_PERSONALS,  // Nombre de la tabla afectada
            $id                           // ID del registro modificada
        );

        echo json_encode(['status' => 'success', 'message' => 'Fitxa eliminada']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Error del servidor']);
    }
}
