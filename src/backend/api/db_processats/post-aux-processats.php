<?php

use App\Config\Tables;
use App\Config\Audit;
use App\Config\DatabaseConnection;
use App\Utils\MissatgesAPI;
use App\Utils\Response;
use App\Utils\ValidacioErrors;

$slug = $routeParams[0];
$conn = DatabaseConnection::getConnection();

if (!$conn) {
    die("No se pudo establecer conexión a la base de datos.");
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

$userId = getAuthenticatedUserId();
if (!$userId) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

if ($slug === 'jutgesInstructors') {

    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    $errors = [];

    // -------------------------
    // VALIDACIÓN BÁSICA
    // -------------------------
    if (empty($data['nom'])) {
        $errors[] = ValidacioErrors::requerit('nom');
    }

    if (empty($data['cognoms'])) {
        $errors[] = ValidacioErrors::requerit('cognoms');
    }

    if (empty($data['carrec'])) {
        $errors[] = ValidacioErrors::requerit('carrec');
    }

    // Si hay errores
    if (!empty($errors)) {
        Response::error(
            MissatgesAPI::error('validacio'),
            $errors,
            400
        );
    }

    // -------------------------
    // SANITIZACIÓN
    // -------------------------
    $nom = trim($data['nom']);
    $cognoms = trim($data['cognoms']);
    $carrec = trim($data['carrec']);

    try {

        $sql = "INSERT INTO aux_jutges_instructors (
                    nom,
                    cognoms,
                    carrec
                ) VALUES (
                    :nom,
                    :cognoms,
                    :carrec
                )";

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindParam(':cognoms', $cognoms, PDO::PARAM_STR);
        $stmt->bindParam(':carrec', $carrec, PDO::PARAM_STR);

        $stmt->execute();

        $id = $conn->lastInsertId();

        // -------------------------
        // AUDITORIA
        // -------------------------
        Audit::registrarCanvi(
            $conn,
            $userId,
            "INSERT",
            "Creació jutge instructor auxiliar",
            Tables::AUX_JUTGES_INSTRUCTORS,
            $id
        );

        Response::success(
            MissatgesAPI::success('create'),
            ['id' => $id],
            201
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }
}
