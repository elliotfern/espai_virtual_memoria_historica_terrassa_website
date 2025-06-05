<?php

use App\Config\Tables;
use App\Config\Audit;
use App\Config\DatabaseConnection;

$conn = DatabaseConnection::getConnection();

if (!$conn) {
    die("No se pudo establecer conexión a la base de datos.");
}


$id = $routeParams[1];
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


// 1) DELETE ref_bibliografica
// ruta DELETE => "/api/fonts_documentals/delete/ref_bibliografica/{id}"
if ($slug === 'ref_bibliografica') {
    global $conn;
    /** @var PDO $conn */

    $id = $id ?? null;
    if (!$id) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'ID no proporcionat']);
        exit;
    }

    // Opcional: evitar eliminar si el municipi no existe
    $stmtCheck = $conn->prepare("SELECT id FROM aux_bibliografia_llibres WHERE id = :id");
    $stmtCheck->bindParam(':id', $id, PDO::PARAM_INT);
    $stmtCheck->execute();

    $row = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Registre no trobat']);
        exit;
    }

    $valor = "Delete ref_bibliografica";
    $dataHora = date('Y-m-d H:i:s');
    $idPersonaFitxa = NULL;

    try {
        $stmt = $conn->prepare("DELETE FROM aux_bibliografia_llibres WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $stmtLog = $conn->prepare("INSERT INTO control_registre_canvis (idUser, idPersonaFitxa, tipusOperacio, dataHoraCanvi)
  VALUES (:idUser, :idPersonaFitxa, :tipusOperacio, :dataHoraCanvi)");
        $stmtLog->bindParam(':idUser', $userId);
        $stmtLog->bindParam(':idPersonaFitxa', $idPersonaFitxa);
        $stmtLog->bindParam(':tipusOperacio', $valor);
        $stmtLog->bindParam(':dataHoraCanvi', $dataHora);
        $stmtLog->execute();

        echo json_encode(['status' => 'success', 'message' => 'Familiar eliminat']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Error del servidor']);
    }

    // 2) DELETE ref_arxivistica
    // ruta DELETE => "/api/fonts_documentals/delete/ref_arxivistica/{id}"
} else if ($slug === 'ref_arxiu') {
    global $conn;
    /** @var PDO $conn */

    $id = $id ?? null;
    if (!$id) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'ID no proporcionat']);
        exit;
    }

    // Opcional: evitar eliminar si el municipi no existe
    $stmtCheck = $conn->prepare("SELECT id FROM aux_bibliografia_arxius WHERE id = :id");
    $stmtCheck->bindParam(':id', $id, PDO::PARAM_INT);
    $stmtCheck->execute();

    $row = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Registre no trobat']);
        exit;
    }

    $valor = "Delete ref_arxiu";
    $dataHora = date('Y-m-d H:i:s');
    $idPersonaFitxa = NULL;

    try {
        $stmt = $conn->prepare("DELETE FROM aux_bibliografia_arxius WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $stmtLog = $conn->prepare("INSERT INTO control_registre_canvis (idUser, idPersonaFitxa, tipusOperacio, dataHoraCanvi)
  VALUES (:idUser, :idPersonaFitxa, :tipusOperacio, :dataHoraCanvi)");
        $stmtLog->bindParam(':idUser', $userId);
        $stmtLog->bindParam(':idPersonaFitxa', $idPersonaFitxa);
        $stmtLog->bindParam(':tipusOperacio', $valor);
        $stmtLog->bindParam(':dataHoraCanvi', $dataHora);
        $stmtLog->execute();

        echo json_encode(['status' => 'success', 'message' => 'Familiar eliminat']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Error del servidor']);
    }
}
