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

if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "JSON invàlid", "errors" => ["Format de dades incorrecte"]]);
    exit;
}


global $conn;
/** @var PDO $conn */

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
$idRepresaliat = $data['idRepresaliat'];

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

// —— Sanitizar para guardar (solo si pasó validación) ——
$biografiaCa = $hasCa ? sanitizeTrixHtml($rawCa) : null;
$biografiaEs = $hasEs ? sanitizeTrixHtml($rawEs) : null;
$biografiaEn = plainTextFromHtml($rawEn) !== '' ? sanitizeTrixHtml($rawEn) : null;

// —— Duplicado: ya hay biografia para ese represaliat ——
$check = $conn->prepare("SELECT id FROM db_biografies WHERE idRepresaliat = :id LIMIT 1");
$check->execute([':id' => $idRepresaliat]);
$existingId = $check->fetchColumn();

if ($existingId) {
    http_response_code(409); // Conflict
    echo json_encode([
        "status"  => "error",
        "message" => "Ja existeix una biografia per a aquest represaliat.",
        "errors"  => ["existing_id" => (int)$existingId]
    ]);
    exit;
}

try {
    // Insert
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "INSERT INTO db_biografies (biografiaCa, biografiaEs, biografiaEn, idRepresaliat)
            VALUES (:biografiaCa, :biografiaEs, :biografiaEn, :idRepresaliat)";
    $stmt = $conn->prepare($sql);

    // Usa PARAM_NULL si el valor es null; PARAM_STR si hay texto
    $stmt->bindValue(':biografiaCa', $biografiaCa, $biografiaCa === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt->bindValue(':biografiaEs', $biografiaEs, $biografiaEs === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt->bindValue(':biografiaEn', $biografiaEn, $biografiaEn === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt->bindValue(':idRepresaliat', $idRepresaliat, PDO::PARAM_INT);

    $stmt->execute();
    $id = (int)$conn->lastInsertId();

    // Auditoría
    $tipusOperacio = "Creació biografia";
    $detalls = "INSERT";
    // Asumiendo que $userId ya está disponible en tu contexto
    Audit::registrarCanvi(
        $conn,
        $userId,
        $tipusOperacio,
        $detalls,
        Tables::DB_BIOGRAFIES,
        $id
    );

    echo json_encode(["status" => "success", "message" => "Les dades s'han actualitzat correctament a la base de dades.", "id" => (int)$id]);
} catch (PDOException $e) {

    $code = $e->errorInfo[1] ?? null; // MySQL error code
    if ($code === 1062) {
        http_response_code(409);
        echo json_encode([
            "status"  => "error",
            "message" => "Ja existeix una biografia per a aquest represaliat."
        ]);
        exit;
    }
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "S'ha produit un error a la base de dades."]);
}
