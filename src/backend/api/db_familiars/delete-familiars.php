<?php

use App\Config\Tables;
use App\Config\Audit;
use App\Config\DatabaseConnection;

$conn = DatabaseConnection::getConnection();

if (!$conn) {
    die("No se pudo establecer conexión a la base de datos.");
}


$id = $routeParams[0];

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


// 1) DELETE familiar
// ruta DELETE => "/api/familiars/delete/{id}"

global $conn;
/** @var PDO $conn */

$id = $id ?? null;
if (!$id) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'ID no proporcionat']);
    exit;
}

// Opcional: evitar eliminar si el municipi no existe
$stmtCheck = $conn->prepare("SELECT id, nom, cognom1, cognom2 FROM aux_familiars WHERE id = :id");
$stmtCheck->bindParam(':id', $id, PDO::PARAM_INT);
$stmtCheck->execute();

$row = $stmtCheck->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Registre no trobat']);
    exit;
}

$nom = $row['nom'];
$cognom1 = $row['cognom1'];
$cognom2 = $row['cognom2'];
$nomComplet = $nom . " " . $cognom1 . " " . $cognom2;

try {
    $stmt = $conn->prepare("DELETE FROM aux_familiars WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis
    $tipusOperacio = "DELETE";
    $detalls = "Eliminació de familiar: " . $nomComplet;

    Audit::registrarCanvi(
        $conn,
        $userId,                      // ID del usuario que hace el cambio
        $tipusOperacio,             // Tipus operacio
        $detalls,                       // Descripción de la operación
        Tables::AUX_FAMILIARS,  // Nombre de la tabla afectada
        $id                           // ID del registro modificada
    );

    echo json_encode(['status' => 'success', 'message' => 'Familiar eliminat']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error del servidor']);
}
