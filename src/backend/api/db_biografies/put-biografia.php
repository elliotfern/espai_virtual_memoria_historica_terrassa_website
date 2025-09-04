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

// Verificar que el método HTTP sea PUT
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
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

// BIOGRAFIA

// 1) POST Biografia
// ruta POST => "/api/cronologia/put/
$inputData = file_get_contents('php://input');
$data = json_decode($inputData, true);

// Inicializar un array para los errores
$errors = [];

/* Validación de los datos recibidos
if (empty($data['biografiaCa'])) {
    $errors[] = 'El biografia any és obligatori.';
}
*/

if (!is_numeric($data['id']) || !is_numeric($data['idRepresaliat'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "ID invàlid."]);
    exit;
}

$id = $data['id'];
$idRepresaliat = $data['idRepresaliat'];

$sqlCheck = "SELECT COUNT(*) FROM `" . Tables::DB_BIOGRAFIES . "` WHERE id = :id";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bindParam(':id', $id, PDO::PARAM_INT);
$stmtCheck->execute();

if ($stmtCheck->fetchColumn() == 0) {
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "No s'ha trobat la biografia amb aquest ID."]);
    exit;
}

// —— Helpers ——
// Devuelve texto “plano” para validar si hay contenido real (quita etiquetas, &nbsp;, espacios…)
function plainTextFromHtml(?string $html): string
{
    if ($html === null) return '';
    $s = html_entity_decode((string)$html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $s = strip_tags($s);
    // elimina NBSP y comprime espacios
    $s = preg_replace('/\x{A0}/u', ' ', $s);
    $s = preg_replace('/\s+/u', ' ', $s);
    return trim($s);
}

// —— Recoger campos —— 
$rawCa = $data['biografiaCa'] ?? null;
$rawEs = $data['biografiaEs'] ?? null;
$rawEn = $data['biografiaEn'] ?? null;

$errors = [];

// —— Validación: al menos una biografía con texto real (CA o ES) ——
$hasCa = plainTextFromHtml($rawCa) !== '';
$hasEs = plainTextFromHtml($rawEs) !== '';
if (!$hasCa && !$hasEs) {
    $errors[] = "Cal escriure almenys una biografia (català o castellà).";
}

// —— Validación: idRepresaliat válido y existente ——
if ($idRepresaliat <= 0) {
    $errors[] = "Falta un idRepresaliat vàlid.";
} else {
    $stmt = $conn->prepare("SELECT 1 FROM db_dades_personals WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $idRepresaliat]);
    if (!$stmt->fetchColumn()) {
        $errors[] = "No existeix el represaliat indicat (idRepresaliat=$idRepresaliat).";
    }
}

// —— Si hay errores, corta aquí ——
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        "status"  => "error",
        "message" => "S'han produït errors en la validació",
        "errors"  => $errors
    ]);
    exit;
}

// Si no hay errores, crear las variables PHP y preparar la consulta PDO
$biografiaCa = $hasCa ? sanitizeTrixHtml($rawCa) : null;
$biografiaEs = $hasEs ? sanitizeTrixHtml($rawEs) : null;
$biografiaEn = plainTextFromHtml($rawEn) !== '' ? sanitizeTrixHtml($rawEn) : null;


// Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
try {

    global $conn;
    /** @var PDO $conn */

    // Crear la consulta SQL
    $sql = "UPDATE `" . Tables::DB_BIOGRAFIES . "` SET 
        biografiaCa = :biografiaCa,
        biografiaEs = :biografiaEs,
        biografiaEn = :biografiaEn,
        idRepresaliat = :idRepresaliat
        WHERE id = :id";

    // Preparar la consulta
    $stmt = $conn->prepare($sql);

    // Enlazar los parámetros con los valores de las variables PHP
    $stmt->bindParam(':biografiaCa', $biografiaCa, PDO::PARAM_STR);
    $stmt->bindParam(':biografiaEs', $biografiaEs, PDO::PARAM_STR);
    $stmt->bindParam(':biografiaEn', $biografiaEn, PDO::PARAM_STR);
    $stmt->bindParam(':idRepresaliat', $idRepresaliat, PDO::PARAM_INT);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    // Ejecutar la consulta
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        echo json_encode(["status" => "error", "message" => "No s'han fet canvis a la base de dades."]);
        exit;
    }

    // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis
    $tipusOperacio = "UPDATE";
    $detalls = "Modificació de biografia";

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
    echo json_encode(["status" => "error", "message" => "S'ha produit un error a la base de dades:" . $e->getMessage()]);
}
