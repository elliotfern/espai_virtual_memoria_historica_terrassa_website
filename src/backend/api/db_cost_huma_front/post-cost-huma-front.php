<?php

use App\Config\Tables;
use App\Config\Audit;

// Cabeceras
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: https://memoriaterrassa.cat");
header("Access-Control-Allow-Methods: POST");

$allowed_origin = "https://memoriaterrassa.cat";
if (isset($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN'] !== $allowed_origin) {
    http_response_code(403);
    echo json_encode(["error" => "Acceso denegado. Origen no permitido."]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido. Se requiere POST."]);
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

// --- Helpers de normalización (como en PUT) ---
function intOrNull($v): ?int
{
    if ($v === '' || $v === null) return null;
    if ($v === '0' || $v === 0) return null;
    if (is_numeric($v)) return (int)$v;
    return null;
}
function strOrNull($v): ?string
{
    if ($v === null) return null;
    $v = is_string($v) ? trim($v) : $v;
    return ($v === '' ? null : (string)$v);
}

// idPersona obligatorio y normalizado
$idPersona = intOrNull($data['idPersona'] ?? null);
if ($idPersona === null) {
    http_response_code(400);
    echo json_encode(["status" => 'error', 'message' => 'Falta IDPersona']);
    exit;
}

// Evitar duplicados por idPersona
global $conn;
/** @var PDO $conn */
$stmtCheck = $conn->prepare("SELECT COUNT(*) FROM db_cost_huma_morts_front WHERE idPersona = :idPersona");
$stmtCheck->bindValue(':idPersona', $idPersona, PDO::PARAM_INT);
$stmtCheck->execute();
if ($stmtCheck->fetchColumn() > 0) {
    http_response_code(409);
    echo json_encode(['status' => 'error', 'message' => 'Ja existeix un registre d\'aquest represaliat a la base de dades']);
    exit;
}

// Validaciones y normalizaciones
$errors = [];

// Fechas
$desaparegut_dataRaw = $data['desaparegut_data'] ?? '';
if ($desaparegut_dataRaw !== '') {
    $desaparegut_dataFormat = convertirDataFormatMysql($desaparegut_dataRaw, 1);
    if (!$desaparegut_dataFormat) {
        $errors[] = "El format de data no és vàlid. Format esperat: DD/MM/YYYY, amb anys entre 1936 i 1939";
    }
} else {
    $desaparegut_dataFormat = null;
}

$desaparegut_data_aparicioRaw = $data['desaparegut_data_aparicio'] ?? '';
if ($desaparegut_data_aparicioRaw !== '') {
    $desaparegut_data_aparicioFormat = convertirDataFormatMysql($desaparegut_data_aparicioRaw, 1);
    if (!$desaparegut_data_aparicioFormat) {
        $errors[] = "El format de data no és vàlid. Format esperat: DD/MM/YYYY, amb anys entre 1936 i 1939.";
    }
} else {
    $desaparegut_data_aparicioFormat = null;
}

// INTs normalizados
$condicio                  = intOrNull($data['condicio'] ?? null);
$bandol                    = intOrNull($data['bandol'] ?? null);
$cos                       = intOrNull($data['cos'] ?? null);
$circumstancia_mort        = intOrNull($data['circumstancia_mort'] ?? null);
$desaparegut_lloc          = intOrNull($data['desaparegut_lloc'] ?? null);
$desaparegut_lloc_aparicio = intOrNull($data['desaparegut_lloc_aparicio'] ?? null);
$reaparegut                = intOrNull($data['reaparegut'] ?? null);

// Campos obligatorios (si alguno puede ser opcional, elimina su validación)
if ($circumstancia_mort === null) $errors[] = "El camp 'circumstancia_mort' és obligatori";
if ($condicio === null)           $errors[] = "El camp 'condicio' és obligatori";
if ($bandol === null)             $errors[] = "El camp 'bandol' és obligatori";

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => $errors]);
    exit;
}

// Strings opcionales
$any_lleva             = strOrNull($data['any_lleva'] ?? null);
$unitat_inicial        = strOrNull($data['unitat_inicial'] ?? null);
$unitat_final          = strOrNull($data['unitat_final'] ?? null);
$graduacio_final       = strOrNull($data['graduacio_final'] ?? null);
$periple_militar       = strOrNull($data['periple_militar'] ?? null);
$aparegut_observacions = strOrNull($data['aparegut_observacions'] ?? null);

try {
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
        desaparegut_lloc_aparicio,
        aparegut_observacions,
        reaparegut
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
        :desaparegut_lloc_aparicio,
        :aparegut_observacions,
        :reaparegut
    )";

    $stmt = $conn->prepare($sql);

    // INTs: bind NULL vs INT
    $stmt->bindValue(':idPersona',          $idPersona,          PDO::PARAM_INT);
    $stmt->bindValue(':condicio',           $condicio,           $condicio === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
    $stmt->bindValue(':bandol',             $bandol,             $bandol === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
    $stmt->bindValue(':cos',                $cos,                $cos === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
    $stmt->bindValue(':circumstancia_mort', $circumstancia_mort, $circumstancia_mort === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
    $stmt->bindValue(':desaparegut_lloc',   $desaparegut_lloc,   $desaparegut_lloc === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
    $stmt->bindValue(':desaparegut_lloc_aparicio', $desaparegut_lloc_aparicio, $desaparegut_lloc_aparicio === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
    $stmt->bindValue(':reaparegut',         $reaparegut,         $reaparegut === null ? PDO::PARAM_NULL : PDO::PARAM_INT);

    // Strings / fechas: bind NULL vs STR
    $stmt->bindValue(':any_lleva',              $any_lleva,              $any_lleva === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt->bindValue(':unitat_inicial',         $unitat_inicial,         $unitat_inicial === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt->bindValue(':unitat_final',           $unitat_final,           $unitat_final === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt->bindValue(':graduacio_final',        $graduacio_final,        $graduacio_final === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt->bindValue(':periple_militar',        $periple_militar,        $periple_militar === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt->bindValue(':aparegut_observacions',  $aparegut_observacions,  $aparegut_observacions === null ? PDO::PARAM_NULL : PDO::PARAM_STR);

    $stmt->bindValue(':desaparegut_data',          $desaparegut_dataFormat,          $desaparegut_dataFormat === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt->bindValue(':desaparegut_data_aparicio', $desaparegut_data_aparicioFormat, $desaparegut_data_aparicioFormat === null ? PDO::PARAM_NULL : PDO::PARAM_STR);

    $stmt->execute();

    $id = $conn->lastInsertId();

    // Audit
    $detalls = "Creació fitxa repressió cost humà morts al front";
    $tipusOperacio = "INSERT";
    Audit::registrarCanvi(
        $conn,
        $userId,
        $tipusOperacio,
        $detalls,
        Tables::DB_COST_HUMA_MORTS_FRONT,
        $id
    );

    echo json_encode(["status" => "success", "message" => "Les dades s'han desat correctament a la base de dades."]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "S'ha produit un error a la base de dades: " . $e->getMessage()]);
}
