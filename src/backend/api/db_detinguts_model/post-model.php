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

// inici
$inputData = file_get_contents('php://input');
$data = json_decode($inputData, true);

if (!$data['idPersona']) {
    http_response_code(400); // Bad Request
    echo json_encode(["status" => 'error', 'message' => 'Falta IDPersona']);
    exit;
}

$idPersona = $data['idPersona'];

// Comprobación directa en la misma sección del PUT
global $conn;
/** @var PDO $conn */
$stmtCheck = $conn->prepare("SELECT COUNT(*) FROM db_detinguts_model WHERE idPersona = :idPersona");
$stmtCheck->execute(['idPersona' => $idPersona]);
$errorDuplicat = $stmtCheck->fetchColumn() > 0;

if ($errorDuplicat) {
    http_response_code(409); // Conflict
    echo json_encode(['status' => 'error', 'message' => 'Ja existeix un registre d\'aquest represaliat a la base de dades']);
    exit;
}

// Inicializar un array para los errores
$errors = [];

// Validación de los datos recibidos
$data_empresonament_raw = $data['data_empresonament'] ?? '';
if (!empty($data_empresonament_raw)) {
    $data_empresonamentFormat = convertirDataFormatMysql($data_empresonament_raw, 3);

    if (!$data_empresonamentFormat) {
        $errors[] = ValidacioErrors::dataNoValida('data empresonament');
    }
} else {
    $data_empresonamentFormat = null;
}

$data_llibertat_raw = $data['data_llibertat'] ?? '';
if (!empty($data_llibertat_raw)) {
    $data_llibertatFormat = convertirDataFormatMysql($data_llibertat_raw, 3);

    if (!$data_llibertatFormat) {
        $errors[] = ValidacioErrors::dataNoValida('data llibertat');
    }
} else {
    $data_llibertatFormat = null;
}

// Si hay errores, devolver una respuesta con los errores
if (!empty($errors)) {
    Response::error(
        MissatgesAPI::error('validacio'),
        $errors,
        400
    );
}

// Si no hay errores, crear las variables PHP y preparar la consulta PDO
$trasllats = !empty($data['trasllats']) ? $data['trasllats'] : NULL;
$llocTrasllat = !empty($data['lloc_trasllat']) ? $data['lloc_trasllat'] : NULL;
$dataTrasllat = !empty($data['data_trasllat']) ? $data['data_trasllat'] : NULL;
$llibertat = !empty($data['llibertat']) ? $data['llibertat'] : NULL;
$modalitat = isset($data['modalitat']) ? $data['modalitat'] : NULL;
$vicissituds = !empty($data['vicissituds']) ? $data['vicissituds'] : NULL;
$observacions = !empty($data['observacions']) ? $data['observacions'] : NULL;

// Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
try {

    global $conn;
    /** @var PDO $conn */

    // Crear la consulta SQL
    $sql = "INSERT INTO db_detinguts_model (
        idPersona, data_empresonament, trasllats, lloc_trasllat, 
        data_trasllat, llibertat, data_llibertat, modalitat, 
        vicissituds, observacions
    ) VALUES (
        :idPersona, :data_empresonament, :trasllats, :lloc_trasllat, 
        :data_trasllat, :llibertat, :data_llibertat, :modalitat, 
        :vicissituds, :observacions
    )";

    // Preparar la consulta
    $stmt = $conn->prepare($sql);

    // Enlazar los parámetros con los valores de las variables PHP
    $stmt->bindParam(':idPersona', $idPersona, PDO::PARAM_INT);
    $stmt->bindParam(':data_empresonament', $data_empresonamentFormat, PDO::PARAM_STR);
    $stmt->bindParam(':trasllats', $trasllats, PDO::PARAM_INT);
    $stmt->bindParam(':lloc_trasllat', $lloc_trasllat, PDO::PARAM_STR);
    $stmt->bindParam(':data_trasllat', $data_trasllat, PDO::PARAM_STR);
    $stmt->bindParam(':llibertat', $llibertat, PDO::PARAM_INT);
    $stmt->bindParam(':data_llibertat', $data_llibertatFormat, PDO::PARAM_STR);
    $stmt->bindParam(':modalitat', $modalitat, PDO::PARAM_INT);
    $stmt->bindParam(':vicissituds', $vicissituds, PDO::PARAM_STR);
    $stmt->bindParam(':observacions', $observacions, PDO::PARAM_STR);

    // Ejecutar la consulta
    $stmt->execute();

    // Recuperar el ID del registro creado
    $id = $conn->lastInsertId();

    // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis
    $detalls = "Creació fitxa grup repressió detinguts presó model";
    $tipusOperacio = "INSERT";

    Audit::registrarCanvi(
        $conn,
        $userId,                      // ID del usuario que hace el cambio
        $tipusOperacio,             // Tipus operacio
        $detalls,                       // Descripción de la operación
        Tables::DB_PRESO_MODEL,  // Nombre de la tabla afectada
        $id                           // ID del registro modificada
    );

    // Respuesta de éxito
    Response::success(
        MissatgesAPI::success('create'),
        ['id' => $id],
        200
    );
} catch (PDOException $e) {
    // En caso de error en la conexión o ejecución de la consulta
    Response::error(
        MissatgesAPI::error('errorBD'),
        [$e->getMessage()],
        500
    );
}
