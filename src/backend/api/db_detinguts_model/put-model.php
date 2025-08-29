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

// inici
$inputData = file_get_contents('php://input');
$data = json_decode($inputData, true);

if (!$data['idPersona']) {
    http_response_code(400); // Bad Request
    echo json_encode(["status" => 'error', 'message' => 'Falta IDPersona']);
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

$data_trasllat_raw = $data['data_trasllat'] ?? '';
if (!empty($data_trasllat_raw)) {
    $data_trasllatFormat = convertirDataFormatMysql($data_trasllat_raw, 3);

    if (!$data_trasllatFormat) {
        $errors[] = ValidacioErrors::dataNoValida('data trasllat');
    }
} else {
    $data_trasllatFormat = null;
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
$lloc_trasllat = !empty($data['lloc_trasllat']) ? $data['lloc_trasllat'] : NULL;
$llibertat = !empty($data['llibertat']) ? $data['llibertat'] : NULL;
$modalitat = isset($data['modalitat']) ? $data['modalitat'] : NULL;
$vicissituds = !empty($data['vicissituds']) ? $data['vicissituds'] : NULL;
$observacions = !empty($data['observacions']) ? $data['observacions'] : NULL;
$idPersona = $data['idPersona'];
$id = $data['id'];

// Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
try {

    global $conn;
    /** @var PDO $conn */

    // Crear la consulta SQL
    $sql = "UPDATE db_detinguts_model SET
        data_empresonament = :data_empresonament,
        trasllats = :trasllats,
        lloc_trasllat = :lloc_trasllat,
        data_trasllat = :data_trasllat,
        llibertat = :llibertat,
        data_llibertat = :data_llibertat,
        modalitat = :modalitat,
        vicissituds = :vicissituds,
        observacions = :observacions,
        idPersona = :idPersona
    WHERE id = :id";

    // Preparar la consulta
    $stmt = $conn->prepare($sql);

    // Enlazar los parámetros con los valores de las variables PHP
    $stmt->bindParam(':idPersona', $idPersona, PDO::PARAM_INT);
    $stmt->bindParam(':data_empresonament', $data_empresonamentFormat, PDO::PARAM_STR);
    $stmt->bindParam(':trasllats', $trasllats, PDO::PARAM_INT);
    $stmt->bindParam(':lloc_trasllat', $lloc_trasllat, PDO::PARAM_STR);
    $stmt->bindParam(':data_trasllat', $data_trasllatFormat, PDO::PARAM_STR);
    $stmt->bindParam(':llibertat', $llibertat, PDO::PARAM_INT);
    $stmt->bindParam(':data_llibertat', $data_llibertatFormat, PDO::PARAM_STR);
    $stmt->bindParam(':modalitat', $modalitat, PDO::PARAM_INT);
    $stmt->bindParam(':vicissituds', $vicissituds, PDO::PARAM_STR);
    $stmt->bindParam(':observacions', $observacions, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    // Ejecutar la consulta
    $stmt->execute();

    // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis
    $detalls = "Modificació fitxa grup repressió detinguts presó model";
    $tipusOperacio = "UPDATE";

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
        MissatgesAPI::success('update'),
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
