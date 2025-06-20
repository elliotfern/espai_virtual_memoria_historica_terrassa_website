<?php

use App\Config\Tables;
use App\Config\Audit;
use App\Config\DatabaseConnection;
use App\Utils\MissatgesAPI;
use App\Utils\Response;
use App\Utils\ValidacioErrors;

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

// Verificar que el método de la solicitud sea PUT
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

$inputData = file_get_contents('php://input');
$data = json_decode($inputData, true);

if (!$data['idPersona']) {
    http_response_code(400); // Bad Request
    echo json_encode(["status" => 'error', 'message' => 'Falta IDPersona']);
    exit;
}

$idPersona = $data['idPersona'];

// Inicializar un array para los errores
$errors = [];

// Validación de los datos recibidos

// Si hay errores, devolver una respuesta con los errores
if (!empty($errors)) {
    http_response_code(400); // Bad Request
    echo json_encode(["status" => "error", "message" => "S'han produït errors en la validació", "errors" => $errors]);
    exit;
}

// Si no hay errores, crear las variables PHP y preparar la consulta PDO
$id = $data['id'];
$tipus_professional = !empty($data['tipus_professional']) ? $data['tipus_professional'] : NULL;
$professio = !empty($data['professio']) ? $data['professio'] : NULL;
$empresa = !empty($data['empresa']) ? $data['empresa'] : NULL;
$sancio = !empty($data['sancio']) ? $data['sancio'] : NULL;
$observacions = !empty($data['observacions']) ? $data['observacions'] : NULL;

// Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
try {

    global $conn;
    /** @var PDO $conn */

    // Crear la consulta SQL
    $sql = "UPDATE db_depurats SET
        idPersona = :idPersona,
        tipus_professional = :tipus_professional,
        professio = :professio,
        empresa = :empresa,
        sancio = :sancio,
        observacions = :observacions
    WHERE id = :id";

    // Preparar la consulta
    $stmt = $conn->prepare($sql);

    // Enlazar los parámetros con los valores de las variables PHP
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':idPersona', $idPersona, PDO::PARAM_INT);
    $stmt->bindParam(':tipus_professional', $tipus_professional, PDO::PARAM_INT);
    $stmt->bindParam(':professio', $professio, PDO::PARAM_INT);
    $stmt->bindParam(':empresa', $empresa, PDO::PARAM_INT);
    $stmt->bindParam(':sancio', $sancio, PDO::PARAM_STR);
    $stmt->bindParam(':observacions', $observacions, PDO::PARAM_STR);

    // Ejecutar la consulta
    $stmt->execute();

    // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis
    $detalls = "Modificació fitxa repressió depurats";
    $tipusOperacio = "UPDATE";

    Audit::registrarCanvi(
        $conn,
        $userId,                      // ID del usuario que hace el cambio
        $tipusOperacio,             // Tipus operacio
        $detalls,                       // Descripción de la operación
        Tables::DB_DEPURATS,  // Nombre de la tabla afectada
        $id                           // ID del registro modificada
    );

    // Respuesta de éxito
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
