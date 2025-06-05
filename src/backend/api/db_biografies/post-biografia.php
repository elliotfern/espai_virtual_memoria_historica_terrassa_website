<?php

use App\Config\Tables;
use App\Config\Audit;
use App\Config\DatabaseConnection;

$conn = DatabaseConnection::getConnection();

if (!$conn) {
    die("No se pudo establecer conexión a la base de datos.");
}

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
    echo json_encode(["error" => "Método no permitido. Se requiere POST."]);
    exit;
}

$userId = getAuthenticatedUserId();
if (!$userId) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

// BIOGRAFIA

// 1) POST Biografia
// ruta POST => "/api/cronologia/put/
$inputData = file_get_contents('php://input');
$data = json_decode($inputData, true);

// Inicializar un array para los errores
$errors = [];

/* Validación de los datos recibidos
if (empty($data['biografiaCa'])) {
    $errors[] = 'El camp biografia és obligatori.';
}
*/
// Si hay errores, devolver una respuesta con los errores
if (!empty($errors)) {
    http_response_code(400); // Bad Request
    echo json_encode(["status" => "error", "message" => "S'han produït errors en la validació", "errors" => $errors]);
    exit;
}

// Si no hay errores, crear las variables PHP y preparar la consulta PDO

$biografiaCa = !empty($data['biografiaCa']) ? sanitizeHtml($data['biografiaCa']) : NULL;
$biografiaEs = !empty($data['biografiaEs']) ? sanitizeHtml($data['biografiaEs']) : NULL;
$biografiaEn = !empty($data['biografiaEn']) ? sanitizeHtml($data['biografiaEn']) : NULL;

$idRepresaliat = $data['idRepresaliat'];

// Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
try {

    global $conn;
    /** @var PDO $conn */

    // Crear la consulta SQL
    $sql = "INSERT INTO db_biografies 
    (biografiaCa, biografiaEs, biografiaEn, idRepresaliat) VALUES (:biografiaCa, :biografiaEs, :biografiaEn, :idRepresaliat)";

    // Preparar la consulta
    $stmt = $conn->prepare($sql);

    // Enlazar los parámetros con los valores de las variables PHP
    $stmt->bindParam(':biografiaCa', $biografiaCa, PDO::PARAM_STR);
    $stmt->bindParam(':biografiaEs', $biografiaEs, PDO::PARAM_STR);
    $stmt->bindParam(':biografiaEn', $biografiaEn, PDO::PARAM_STR);
    $stmt->bindParam(':idRepresaliat', $idRepresaliat, PDO::PARAM_INT);

    // Ejecutar la consulta
    $stmt->execute();

    // Recuperar el ID del registro creado
    $id = $conn->lastInsertId();

    // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis
    $tipusOperacio = "Creació biografia";
    $detalls = "INSERT";

    Audit::registrarCanvi(
        $conn,
        $userId,                      // ID del usuario que hace el cambio
        $tipusOperacio,             // Tipus operacio
        $detalls,                       // Descripción de la operación
        Tables::DB_BIOGRAFIES,  // Nombre de la tabla afectada
        $id                           // ID del registro modificada
    );

    // Respuesta de éxito
    echo json_encode(["status" => "success", "message" => "Les dades s'han actualitzat correctament a la base de dades."]);
} catch (PDOException $e) {
    // En caso de error en la conexión o ejecución de la consulta
    http_response_code(500); // Internal Server Error
    echo json_encode(["status" => "error", "message" => "S'ha produit un error 1 a la base de dades: "]);
}
