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
header("Access-Control-Allow-Methods: PUT");

// Definir el dominio permitido
$allowedOrigin = DOMAIN;

// Llamar a la función para verificar el referer
checkReferer($allowedOrigin);

// Verificar que el método de la solicitud sea GET
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
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
    // VALIDACIÓN
    // -------------------------
    if (empty($data['id'])) {
        $errors[] = ValidacioErrors::requerit('id');
    }

    if (empty($data['nom'])) {
        $errors[] = ValidacioErrors::requerit('nom');
    }

    if (empty($data['cognoms'])) {
        $errors[] = ValidacioErrors::requerit('cognoms');
    }

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
    $id = (int) $data['id'];
    $nom = trim($data['nom']);
    $cognoms = trim($data['cognoms']);
    $carrec = trim($data['carrec']);

    try {

        $sql = "UPDATE aux_jutges_instructors
                SET 
                    nom = :nom,
                    cognoms = :cognoms,
                    carrec = :carrec
                WHERE id = :id";

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindParam(':cognoms', $cognoms, PDO::PARAM_STR);
        $stmt->bindParam(':carrec', $carrec, PDO::PARAM_STR);

        $stmt->execute();

        // -------------------------
        // AUDITORIA
        // -------------------------
        Audit::registrarCanvi(
            $conn,
            $userId,
            "UPDATE",
            "Modificació jutge instructor auxiliar",
            Tables::AUX_JUTGES_INSTRUCTORS,
            $id
        );

        Response::success(
            MissatgesAPI::success('update'),
            ['id' => $id],
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }
} else if ($slug === 'secretarisInstructors') {

    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    $errors = [];

    // -------------------------
    // VALIDACIÓN
    // -------------------------
    if (empty($data['id'])) {
        $errors[] = ValidacioErrors::requerit('id');
    }

    if (empty($data['nom'])) {
        $errors[] = ValidacioErrors::requerit('nom');
    }

    if (empty($data['cognoms'])) {
        $errors[] = ValidacioErrors::requerit('cognoms');
    }

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
    $id = (int) $data['id'];
    $nom = trim($data['nom']);
    $cognoms = trim($data['cognoms']);
    $carrec = trim($data['carrec']);

    try {

        $sql = "UPDATE aux_secretaris_instructors
                SET
                    nom = :nom,
                    cognoms = :cognoms,
                    carrec = :carrec
                WHERE id = :id";

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindParam(':cognoms', $cognoms, PDO::PARAM_STR);
        $stmt->bindParam(':carrec', $carrec, PDO::PARAM_STR);

        $stmt->execute();

        // -------------------------
        // AUDITORIA
        // -------------------------
        Audit::registrarCanvi(
            $conn,
            $userId,
            "UPDATE",
            "Modificació secretari instructor auxiliar",
            Tables::AUX_SECRETARIS_INSTRUCTORS,
            $id
        );

        Response::success(
            MissatgesAPI::success('update'),
            ['id' => $id],
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }
} else if ($slug === 'presidentTribunal') {

    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    $errors = [];

    // -------------------------
    // VALIDACIÓN
    // -------------------------
    if (empty($data['id'])) {
        $errors[] = ValidacioErrors::requerit('id');
    }

    if (empty($data['nom'])) {
        $errors[] = ValidacioErrors::requerit('nom');
    }

    if (empty($data['cognoms'])) {
        $errors[] = ValidacioErrors::requerit('cognoms');
    }

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
    $id = (int) $data['id'];
    $nom = trim($data['nom']);
    $cognoms = trim($data['cognoms']);
    $carrec = trim($data['carrec']);

    try {

        $sql = "UPDATE aux_presidents_tribunal
                SET
                    nom = :nom,
                    cognoms = :cognoms,
                    carrec = :carrec
                WHERE id = :id";

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindParam(':cognoms', $cognoms, PDO::PARAM_STR);
        $stmt->bindParam(':carrec', $carrec, PDO::PARAM_STR);

        $stmt->execute();

        // -------------------------
        // AUDITORIA
        // -------------------------
        Audit::registrarCanvi(
            $conn,
            $userId,
            "UPDATE",
            "Modificació president tribunal auxiliar",
            Tables::AUX_PRESIDENTS_TRIBUNAL,
            $id
        );

        Response::success(
            MissatgesAPI::success('update'),
            ['id' => $id],
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }
} else if ($slug === "defensor") {

    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    $errors = [];

    // -------------------------
    // VALIDACIÓN
    // -------------------------
    if (empty($data['id'])) {
        $errors[] = ValidacioErrors::requerit('id');
    }

    if (empty($data['nom'])) {
        $errors[] = ValidacioErrors::requerit('nom');
    }

    if (empty($data['cognoms'])) {
        $errors[] = ValidacioErrors::requerit('cognoms');
    }

    // carrec NO es obligatorio

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
    $id = (int) $data['id'];
    $nom = trim($data['nom']);
    $cognoms = trim($data['cognoms']);
    $carrec = !empty($data['carrec']) ? trim($data['carrec']) : null;

    try {

        $sql = "UPDATE aux_defensors
                SET
                    nom = :nom,
                    cognoms = :cognoms,
                    carrec = :carrec
                WHERE id = :id";

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindParam(':cognoms', $cognoms, PDO::PARAM_STR);
        $stmt->bindParam(':carrec', $carrec, PDO::PARAM_STR);

        $stmt->execute();

        // -------------------------
        // AUDITORIA
        // -------------------------
        Audit::registrarCanvi(
            $conn,
            $userId,
            "UPDATE",
            "Modificació defensor auxiliar",
            Tables::AUX_DEFENSORS,
            $id
        );

        Response::success(
            MissatgesAPI::success('update'),
            ['id' => $id],
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }
} else if ($slug === "fiscal") {

    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    $errors = [];

    // -------------------------
    // VALIDACIÓN
    // -------------------------
    if (empty($data['id'])) {
        $errors[] = ValidacioErrors::requerit('id');
    }

    if (empty($data['nom'])) {
        $errors[] = ValidacioErrors::requerit('nom');
    }

    if (empty($data['cognoms'])) {
        $errors[] = ValidacioErrors::requerit('cognoms');
    }

    // carrec NO es obligatorio

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
    $id = (int) $data['id'];
    $nom = trim($data['nom']);
    $cognoms = trim($data['cognoms']);
    $carrec = !empty($data['carrec']) ? trim($data['carrec']) : null;

    try {

        $sql = "UPDATE aux_fiscals
                SET
                    nom = :nom,
                    cognoms = :cognoms,
                    carrec = :carrec
                WHERE id = :id";

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindParam(':cognoms', $cognoms, PDO::PARAM_STR);
        $stmt->bindParam(':carrec', $carrec, PDO::PARAM_STR);

        $stmt->execute();

        // -------------------------
        // AUDITORIA
        // -------------------------
        Audit::registrarCanvi(
            $conn,
            $userId,
            "UPDATE",
            "Modificació fiscal auxiliar",
            Tables::AUX_FISCALS,
            $id
        );

        Response::success(
            MissatgesAPI::success('update'),
            ['id' => $id],
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }
} else if ($slug === "ponent") {

    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    $errors = [];

    // -------------------------
    // VALIDACIÓN
    // -------------------------
    if (empty($data['id'])) {
        $errors[] = ValidacioErrors::requerit('id');
    }

    if (empty($data['nom'])) {
        $errors[] = ValidacioErrors::requerit('nom');
    }

    if (empty($data['cognoms'])) {
        $errors[] = ValidacioErrors::requerit('cognoms');
    }

    // carrec NO es obligatorio

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
    $id = (int) $data['id'];
    $nom = trim($data['nom']);
    $cognoms = trim($data['cognoms']);
    $carrec = !empty($data['carrec']) ? trim($data['carrec']) : null;

    try {

        $sql = "UPDATE aux_ponents
                SET
                    nom = :nom,
                    cognoms = :cognoms,
                    carrec = :carrec
                WHERE id = :id";

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindParam(':cognoms', $cognoms, PDO::PARAM_STR);
        $stmt->bindParam(':carrec', $carrec, PDO::PARAM_STR);

        $stmt->execute();

        // -------------------------
        // AUDITORIA
        // -------------------------
        Audit::registrarCanvi(
            $conn,
            $userId,
            "UPDATE",
            "Modificació ponent auxiliar",
            Tables::AUX_PONENTS,
            $id
        );

        Response::success(
            MissatgesAPI::success('update'),
            ['id' => $id],
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }
} else if ($slug === "tribunalVocals") {

    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    $errors = [];

    // -------------------------
    // VALIDACIÓN
    // -------------------------
    if (empty($data['id'])) {
        $errors[] = ValidacioErrors::requerit('id');
    }

    if (empty($data['nom'])) {
        $errors[] = ValidacioErrors::requerit('nom');
    }

    if (empty($data['cognoms'])) {
        $errors[] = ValidacioErrors::requerit('cognoms');
    }

    // carrec NO es obligatorio

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
    $id = (int) $data['id'];
    $nom = trim($data['nom']);
    $cognoms = trim($data['cognoms']);
    $carrec = !empty($data['carrec']) ? trim($data['carrec']) : null;

    try {

        $sql = "UPDATE aux_tribunal_vocals
                SET
                    nom = :nom,
                    cognoms = :cognoms,
                    carrec = :carrec
                WHERE id = :id";

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindParam(':cognoms', $cognoms, PDO::PARAM_STR);
        $stmt->bindParam(':carrec', $carrec, PDO::PARAM_STR);

        $stmt->execute();

        // -------------------------
        // AUDITORIA
        // -------------------------
        Audit::registrarCanvi(
            $conn,
            $userId,
            "UPDATE",
            "Modificació tribunal vocal auxiliar",
            Tables::AUX_TRIBUNAL_VOCALS,
            $id
        );

        Response::success(
            MissatgesAPI::success('update'),
            ['id' => $id],
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }
}
