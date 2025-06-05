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


// 1) DELETE municipi
// ruta DELETE => "/api/auxiliars/delete/municipi/{id}"
if ($slug === "municipi") {
    global $conn;
    /** @var PDO $conn */

    $id = $id ?? null;
    if (!$id) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'ID no proporcionat']);
        exit;
    }

    // Opcional: evitar eliminar si el municipi no existe
    $stmtCheck = $conn->prepare("SELECT id, ciutat FROM aux_dades_municipis WHERE id = :id");
    $stmtCheck->bindParam(':id', $id, PDO::PARAM_INT);
    $stmtCheck->execute();

    $municipi = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if (!$municipi) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Registre no trobat']);
        exit;
    }

    $detalls = "Eliminació de municipi";
    $tipusOperacio = "DELETE";

    try {
        $stmt = $conn->prepare("DELETE FROM aux_dades_municipis WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_DADES_MUNICIPIS,  // Nombre de la tabla afectada
            $id                           // ID del registro modificada
        );

        echo json_encode(['status' => 'success', 'message' => 'Municipi eliminat']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Error del servidor']);
    }

    // 2) DELETE partit politic
    // ruta DELETE => "/api/auxiliars/delete/partitPolitic/{id}"
} else if ($slug === "partitPolitic") {
    global $conn;
    /** @var PDO $conn */

    $id = $id ?? null;
    if (!$id) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'ID no proporcionat']);
        exit;
    }

    // Opcional: evitar eliminar si el municipi no existe
    $stmtCheck = $conn->prepare("SELECT id, partit_politic FROM aux_filiacio_politica WHERE id = :id");
    $stmtCheck->bindParam(':id', $id, PDO::PARAM_INT);
    $stmtCheck->execute();

    $partit = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if (!$partit) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Registre no trobat']);
        exit;
    }

    $detalls = "Eliminació Partit polític: " . $partit['partit_politic'];
    $tipusOperacio = "DELETE";

    try {
        $stmt = $conn->prepare("DELETE FROM aux_filiacio_politica WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_FILIACIO_POLITICA,  // Nombre de la tabla afectada
            $id                           // ID del registro modificada
        );

        echo json_encode(['status' => 'success', 'message' => 'Partit polític eliminat']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Error del servidor']);
    }

    // 3) DELETE sindicat
    // ruta DELETE => "/api/auxiliars/delete/sindicat/{id}"
} else if ($slug === "sindicat") {
    global $conn;
    /** @var PDO $conn */

    $id = $id ?? null;
    if (!$id) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'ID no proporcionat']);
        exit;
    }

    // Opcional: evitar eliminar si el municipi no existe
    $stmtCheck = $conn->prepare("SELECT id, sindicat FROM aux_filiacio_sindical WHERE id = :id");
    $stmtCheck->bindParam(':id', $id, PDO::PARAM_INT);
    $stmtCheck->execute();

    $partit = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if (!$partit) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Registre no trobat']);
        exit;
    }

    try {
        $stmt = $conn->prepare("DELETE FROM aux_filiacio_sindical WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis
        $tipusOperacio = "DELETE";
        $detalls = "Eliminació sindicat: " . $partit['sindicat'];

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_FILIACIO_SINDICAL,  // Nombre de la tabla afectada
            $id                           // ID del registro modificada
        );

        echo json_encode(['status' => 'success', 'message' => 'Sindicat eliminat']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Error del servidor']);
    }

    // 4) DELETE comarca
    // ruta DELETE => "/api/auxiliars/delete/comarca/{id}"
} else if ($slug === "comarca") {
    global $conn;
    /** @var PDO $conn */

    $id = $id ?? null;
    if (!$id) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'ID no proporcionat']);
        exit;
    }

    // Opcional: evitar eliminar si el municipi no existe
    $stmtCheck = $conn->prepare("SELECT id, comarca FROM aux_dades_municipis_comarca WHERE id = :id");
    $stmtCheck->bindParam(':id', $id, PDO::PARAM_INT);
    $stmtCheck->execute();

    $partit = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if (!$partit) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Registre no trobat']);
        exit;
    }

    try {
        $stmt = $conn->prepare("DELETE FROM aux_dades_municipis_comarca WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis
        $tipusOperacio = "DELETE";
        $detalls = "Eliminació de comarca: " . $partit['comarca'];

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_DADES_MUNICIPIS_COMARCA,  // Nombre de la tabla afectada
            $id                           // ID del registro modificada
        );



        $stmtLog = $conn->prepare("INSERT INTO control_registre_canvis (idUser, idPersonaFitxa, tipusOperacio, dataHoraCanvi)
  VALUES (:idUser, :idPersonaFitxa, :tipusOperacio, :dataHoraCanvi)");
        $stmtLog->bindParam(':idUser', $userId);
        $stmtLog->bindParam(':idPersonaFitxa', $idPersonaFitxa);
        $stmtLog->bindParam(':tipusOperacio', $partit);
        $stmtLog->bindParam(':dataHoraCanvi', $dataHora);
        $stmtLog->execute();

        echo json_encode(['status' => 'success', 'message' => 'Comarca eliminada']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Error del servidor']);
    }

    // 4) DELETE provincia
    // ruta DELETE => "/api/auxiliars/delete/provincia/{id}"
} else if ($slug === "provincia") {
    global $conn;
    /** @var PDO $conn */

    $id = $id ?? null;
    if (!$id) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'ID no proporcionat']);
        exit;
    }

    // Opcional: evitar eliminar si el municipi no existe
    $stmtCheck = $conn->prepare("SELECT id, provincia FROM aux_dades_municipis_provincia WHERE id = :id");
    $stmtCheck->bindParam(':id', $id, PDO::PARAM_INT);
    $stmtCheck->execute();

    $partit = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if (!$partit) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Registre no trobat']);
        exit;
    }

    $detalls = "Eliminació provincia: " . $partit['provincia'];
    $tipusOperacio = "DELETE";

    try {
        $stmt = $conn->prepare("DELETE FROM aux_dades_municipis_provincia WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_DADES_MUNICIPIS_COMARCA,  // Nombre de la tabla afectada
            $id                           // ID del registro modificada
        );

        echo json_encode(['status' => 'success', 'message' => 'Provincia eliminada']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Error del servidor']);
    }

    // 5) DELETE Comunitat autonoma / regió
    // ruta DELETE => "/api/auxiliars/delete/comunitat/{id}"
} else if ($slug === "comunitat") {
    global $conn;
    /** @var PDO $conn */

    $id = $id ?? null;
    if (!$id) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'ID no proporcionat']);
        exit;
    }

    // Opcional: evitar eliminar si el municipi no existe
    $stmtCheck = $conn->prepare("SELECT id, comunitat FROM aux_dades_municipis_comunitat WHERE id = :id");
    $stmtCheck->bindParam(':id', $id, PDO::PARAM_INT);
    $stmtCheck->execute();

    $partit = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if (!$partit) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Registre no trobat']);
        exit;
    }

    $detalls = "Eliminació comunitat: " . $partit['comunitat'];
    $tipusOperacio = "DELETE";

    try {
        $stmt = $conn->prepare("DELETE FROM aux_dades_municipis_comunitat WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_DADES_MUNICIPIS_COMUNITAT,  // Nombre de la tabla afectada
            $id                           // ID del registro modificada
        );

        echo json_encode(['status' => 'success', 'message' => 'Comunitat eliminada']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Error del servidor']);
    }

    // 6) DELETE Estat
    // ruta DELETE => "/api/auxiliars/delete/estat/{id}"
} else if ($slug === "estat") {
    global $conn;
    /** @var PDO $conn */

    $id = $id ?? null;
    if (!$id) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'ID no proporcionat']);
        exit;
    }

    // Opcional: evitar eliminar si el municipi no existe
    $stmtCheck = $conn->prepare("SELECT id, estat FROM aux_dades_municipis_estat WHERE id = :id");
    $stmtCheck->bindParam(':id', $id, PDO::PARAM_INT);
    $stmtCheck->execute();

    $partit = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if (!$partit) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Registre no trobat']);
        exit;
    }

    $detalls = "Eliminació Estat: " . $partit['estat'];
    $tipusOperacio = "DELETE";

    try {
        $stmt = $conn->prepare("DELETE FROM aux_dades_municipis_estat WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_DADES_MUNICIPIS_ESTAT,  // Nombre de la tabla afectada
            $id                           // ID del registro modificada
        );

        echo json_encode(['status' => 'success', 'message' => 'Estat eliminat']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Error del servidor']);
    }
}
