<?php

use App\Config\Tables;
use App\Config\Audit;

// Configuración de cabeceras para aceptar JSON y responder JSON
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: https://memoriaterrassa.cat");
header("Access-Control-Allow-Methods: POST");

// Dominio permitido (modifica con tu dominio)
$allowed_origin = "https://memoriaterrassa.cat";

// Verificar el encabezado 'Origin'
if (isset($_SERVER['HTTP_ORIGIN'])) {
    if ($_SERVER['HTTP_ORIGIN'] !== $allowed_origin) {
        http_response_code(403); // Respuesta 403 Forbidden
        echo json_encode(["error" => "Acceso denegado. Origen no permitido."]);
        exit;
    }
}

// Verificar que el método HTTP sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Método no permitido
    echo json_encode(["error" => "Método no permitido. Se requiere PUT."]);
    exit;
}

$userId = getAuthenticatedUserId();
if (!$userId) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

$inputData = file_get_contents('php://input');
$data = json_decode($inputData, true);

$idPersona = $data['idPersona'] ?? null;

// Comprobación directa en la misma sección del PUT
global $conn;
/** @var PDO $conn */
$stmtCheck = $conn->prepare("SELECT COUNT(*) FROM db_cost_huma_morts_front WHERE idPersona = :idPersona");
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
if (empty($data['circumstancia_mort'])) {
    $errors[] = "El camp 'circumstancia_mort' és obligatori";
}

if (empty($data['condicio'])) {
    $errors[] = "El camp 'condicio' és obligatori";
}

if (empty($data['bandol'])) {
    $errors[] = "El camp 'bandol' és obligatori";
}

$desaparegut_dataRaw = $data['desaparegut_data'] ?? '';
if (!empty($desaparegut_dataRaw)) {
    $desaparegut_dataFormat = convertirDataFormatMysql($desaparegut_dataRaw, 1);

    if (!$desaparegut_dataFormat) {
        $errors[] = "El format de data no és vàlid. Format esperat: DD/MM/YYYY, amb anys entre 1936 i 1939";
    }
} else {
    $desaparegut_dataFormat = null;
}

$desaparegut_data_aparicioRaw = $data['desaparegut_data_aparicio'] ?? '';
if (!empty($desaparegut_data_aparicioRaw)) {
    $desaparegut_data_aparicioFormat = convertirDataFormatMysql($desaparegut_data_aparicioRaw, 1);

    if (!$desaparegut_data_aparicioFormat) {
        $errors[] = "El format de data no és vàlid. Format esperat: DD/MM/YYYY, amb anys entre 1936 i 1939.";
    }
} else {
    $desaparegut_data_aparicioFormat = null;
}

// Si hay errores, devolver una respuesta con los errores
if (!empty($errors)) {
    http_response_code(400); // Bad Request
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "message" => $errors]);
    exit;
}

// Si no hay errores, crear las variables PHP y preparar la consulta PDO
$condicio = $data['condicio'];
$bandol = $data['bandol'];
$any_lleva = !empty($data['any_lleva']) ? $data['any_lleva'] : NULL;
$unitat_inicial = !empty($data['unitat_inicial']) ? $data['unitat_inicial'] : NULL;
$cos = !empty($data['cos']) ? $data['cos'] : NULL;
$unitat_final = !empty($data['unitat_final']) ? $data['unitat_final'] : NULL;
$graduacio_final = !empty($data['graduacio_final']) ? $data['graduacio_final'] : NULL;
$periple_militar = !empty($data['periple_militar']) ? $data['periple_militar'] : NULL;
$circumstancia_mort = $data['circumstancia_mort'];
$desaparegut_lloc = !empty($data['desaparegut_lloc']) ? $data['desaparegut_lloc'] : NULL;
$desaparegut_lloc_aparicio = !empty($data['desaparegut_lloc_aparicio']) ? $data['desaparegut_lloc_aparicio'] : NULL;

// Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
try {

    global $conn;
    /** @var PDO $conn */

    // Crear la consulta SQL
    $sql = "INSERT INTO db_cost_huma_morts_front (
        idPersona,
        condicio,
        bandol,
        any_lleva,
        unitat_inicial,
        cos,
        unitat_final,
        graduacio_final,
        periple_militar,
        circumstancia_mort,
        desaparegut_data,
        desaparegut_lloc,
        desaparegut_data_aparicio,
        desaparegut_lloc_aparicio
    ) VALUES (
        :idPersona,
        :condicio,
        :bandol,
        :any_lleva,
        :unitat_inicial,
        :cos,
        :unitat_final,
        :graduacio_final,
        :periple_militar,
        :circumstancia_mort,
        :desaparegut_data,
        :desaparegut_lloc,
        :desaparegut_data_aparicio,
        :desaparegut_lloc_aparicio
    )";

    // Preparar la consulta
    $stmt = $conn->prepare($sql);

    // Enlazar los parámetros con los valores de las variables PHP
    $stmt->bindParam(':idPersona', $idPersona, PDO::PARAM_INT);
    $stmt->bindParam(':condicio', $condicio, PDO::PARAM_INT);
    $stmt->bindParam(':bandol', $bandol, PDO::PARAM_INT);
    $stmt->bindParam(':any_lleva', $any_lleva, PDO::PARAM_STR);
    $stmt->bindParam(':unitat_inicial', $unitat_inicial, PDO::PARAM_STR);
    $stmt->bindParam(':cos', $cos, PDO::PARAM_INT);
    $stmt->bindParam(':unitat_final', $unitat_final, PDO::PARAM_STR);
    $stmt->bindParam(':graduacio_final', $graduacio_final, PDO::PARAM_STR);
    $stmt->bindParam(':periple_militar', $periple_militar, PDO::PARAM_STR);
    $stmt->bindParam(':circumstancia_mort', $circumstancia_mort, PDO::PARAM_INT);
    $stmt->bindParam(':desaparegut_data', $desaparegut_dataFormat, PDO::PARAM_STR);
    $stmt->bindParam(':desaparegut_lloc', $desaparegut_lloc, PDO::PARAM_INT);
    $stmt->bindParam(':desaparegut_data_aparicio', $desaparegut_data_aparicioFormat, PDO::PARAM_STR);
    $stmt->bindParam(':desaparegut_lloc_aparicio', $desaparegut_lloc_aparicio, PDO::PARAM_INT);

    // Ejecutar la consulta
    $stmt->execute();

    // Recuperar el ID del registro creado
    $id = $conn->lastInsertId();

    // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis
    $detalls = "Creació fitxa repressió cost humà morts al front";
    $tipusOperacio = "INSERT";

    Audit::registrarCanvi(
        $conn,
        $userId,                      // ID del usuario que hace el cambio
        $tipusOperacio,             // Tipus operacio
        $detalls,                       // Descripción de la operación
        Tables::DB_COST_HUMA_MORTS_FRONT,  // Nombre de la tabla afectada
        $id                           // ID del registro modificada
    );

    // Respuesta de éxito
    echo json_encode(["status" => "success", "message" => "Les dades s'han desat correctament a la base de dades."]);
} catch (PDOException $e) {
    // En caso de error en la conexión o ejecución de la consulta
    http_response_code(500); // Internal Server Error
    echo json_encode(["status" => "error", "message" => "S'ha produit un error a la base de dades: " . $e->getMessage()]);
}
