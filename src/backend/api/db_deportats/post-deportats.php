<?php

use App\Config\Tables;
use App\Config\Audit;
use App\Config\DatabaseConnection;

/** Helpers para binds */
function bindDateOrNull(PDOStatement $stmt, string $param, ?string $val): void
{
    $stmt->bindValue($param, $val, $val !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
}
function bindIntOrNull(PDOStatement $stmt, string $param, $val): void
{
    $stmt->bindValue($param, $val, $val !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
}
function bindStrOrNull(PDOStatement $stmt, string $param, $val): void
{
    $stmt->bindValue($param, $val, $val !== null && $val !== '' ? PDO::PARAM_STR : PDO::PARAM_NULL);
}

$conn = DatabaseConnection::getConnection();
if (!$conn) {
    http_response_code(500);
    die("No se pudo establecer conexión a la base de datos.");
}

/** CORS / headers */
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Origin: " . DOMAIN);

// Preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Referer (si procede)
checkReferer(DOMAIN);

// Método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Auth
$userId = getAuthenticatedUserId();
if (!$userId) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

// Input
$inputData = file_get_contents('php://input');
$data = json_decode($inputData, true) ?? [];

// idPersona obligatorio
if (empty($data['idPersona'])) {
    http_response_code(400);
    echo json_encode(["status" => 'error', 'message' => 'Falta idPersona']);
    exit;
}
$idPersona = (int)$data['idPersona'];

// Duplicados por idPersona
$stmtCheck = $conn->prepare("SELECT COUNT(*) FROM db_deportats WHERE idPersona = :idPersona");
$stmtCheck->execute([':idPersona' => $idPersona]);
if ($stmtCheck->fetchColumn() > 0) {
    http_response_code(409);
    echo json_encode(['status' => 'error', 'message' => "Ja existeix un registre d'aquest represaliat a la base de dades"]);
    exit;
}

// Errores de validación
$errors = [];

// Campos obligatorios (ajusta si alguno puede ser NULL)
if (empty($data['situacio'])) {
    $errors[] = "El camp 'situacio' és obligatori";
}
if (empty($data['lloc_mort_alliberament'])) {
    $errors[] = "El camp 'lloc de la mort o alliberament' és obligatori";
}

// Fechas que aceptan vacío => NULL
$data_alliberamentRaw = $data['data_alliberament'] ?? '';
if ($data_alliberamentRaw !== '') {
    $data_alliberamentFormat = convertirDataFormatMysql($data_alliberamentRaw, 3);
    if (!$data_alliberamentFormat) $errors[] = "Data alliberament: format no vàlid. Esperat: DD/MM/YYYY";
} else {
    $data_alliberamentFormat = null;
}

$situacioFranca_sortidaRaw = $data['situacioFranca_sortida'] ?? '';
if ($situacioFranca_sortidaRaw !== '') {
    $situacioFranca_sortidaFormat = convertirDataFormatMysql($situacioFranca_sortidaRaw, 3);
    if (!$situacioFranca_sortidaFormat) $errors[] = "Data sortida presó: format no vàlid. Esperat: DD/MM/YYYY";
} else {
    $situacioFranca_sortidaFormat = null;
}

$deportacio_data_entradaRaw = $data['deportacio_data_entrada'] ?? '';
if ($deportacio_data_entradaRaw !== '') {
    $deportacio_data_entradaFormat = convertirDataFormatMysql($deportacio_data_entradaRaw, 3);
    if (!$deportacio_data_entradaFormat) $errors[] = "Data entrada (deportacio): format no vàlid. Esperat: DD/MM/YYYY";
} else {
    $deportacio_data_entradaFormat = null;
}

$deportacio_data_entrada_subcampRaw = $data['deportacio_data_entrada_subcamp'] ?? '';
if ($deportacio_data_entrada_subcampRaw !== '') {
    $deportacio_data_entrada_subcampFormat = convertirDataFormatMysql($deportacio_data_entrada_subcampRaw, 3);
    if (!$deportacio_data_entrada_subcampFormat) $errors[] = "Data entrada subcamp: format no vàlid. Esperat: DD/MM/YYYY";
} else {
    $deportacio_data_entrada_subcampFormat = null;
}

$presoClasificacioData1Raw = $data['presoClasificacioData1'] ?? '';
if ($presoClasificacioData1Raw !== '') {
    $presoClasificacioData1Format = convertirDataFormatMysql($presoClasificacioData1Raw, 3);
    if (!$presoClasificacioData1Format) $errors[] = "Data presó (1): format no vàlid. Esperat: DD/MM/YYYY";
} else {
    $presoClasificacioData1Format = null;
}

$presoClasificacioData2Raw = $data['presoClasificacioData2'] ?? '';
if ($presoClasificacioData2Raw !== '') {
    $presoClasificacioData2Format = convertirDataFormatMysql($presoClasificacioData2Raw, 3);
    if (!$presoClasificacioData2Format) $errors[] = "Data presó (2): format no vàlid. Esperat: DD/MM/YYYY";
} else {
    $presoClasificacioData2Format = null;
}

// Si hay errores
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => $errors]);
    exit;
}

// Escalares / nulos
$situacio = $data['situacio'] ?? null;
$lloc_mort_alliberament = $data['lloc_mort_alliberament'] ?? null;
$situacioFranca = $data['situacioFranca'] ?? null;
$situacioFranca_num_matricula = $data['situacioFranca_num_matricula'] ?? null;
$situacioFrancaObservacions = $data['situacioFrancaObservacions'] ?? null;
$presoClasificacio1 = $data['presoClasificacio1'] ?? null;
$presoClasificacio2 = $data['presoClasificacio2'] ?? null;
$deportacio_camp = $data['deportacio_camp'] ?? null;
$deportacio_subcamp = $data['deportacio_subcamp'] ?? null;
$deportacio_num_matricula = $data['deportacio_num_matricula'] ?? null;
$deportacio_nom_matricula_subcamp = $data['deportacio_nom_matricula_subcamp'] ?? null;
$deportacio_observacions = $data['deportacio_observacions'] ?? null;

try {
    /** @var PDO $conn */
    $sql = "INSERT INTO db_deportats (
        idPersona,
        situacio,
        data_alliberament,
        lloc_mort_alliberament,
        situacioFranca,
        situacioFranca_sortida,
        situacioFranca_num_matricula,
        situacioFrancaObservacions,
        presoClasificacio1,
        presoClasificacioData1,
        presoClasificacio2,
        presoClasificacioData2,
        deportacio_camp,
        deportacio_data_entrada,
        deportacio_num_matricula,
        deportacio_subcamp,
        deportacio_data_entrada_subcamp,
        deportacio_nom_matricula_subcamp,
        deportacio_observacions
    ) VALUES (
        :idPersona,
        :situacio,
        :data_alliberament,
        :lloc_mort_alliberament,
        :situacioFranca,
        :situacioFranca_sortida,
        :situacioFranca_num_matricula,
        :situacioFrancaObservacions,
        :presoClasificacio1,
        :presoClasificacioData1,
        :presoClasificacio2,
        :presoClasificacioData2,
        :deportacio_camp,
        :deportacio_data_entrada,
        :deportacio_num_matricula,
        :deportacio_subcamp,
        :deportacio_data_entrada_subcamp,
        :deportacio_nom_matricula_subcamp,
        :deportacio_observacions
    )";

    $stmt = $conn->prepare($sql);

    // INT obligatorios
    $stmt->bindValue(':idPersona', $idPersona, PDO::PARAM_INT);

    // INT (permiten NULL si llega null)
    bindIntOrNull($stmt, ':situacio', $situacio);
    bindIntOrNull($stmt, ':lloc_mort_alliberament', $lloc_mort_alliberament);
    bindIntOrNull($stmt, ':situacioFranca', $situacioFranca);
    bindIntOrNull($stmt, ':presoClasificacio1', $presoClasificacio1);
    bindIntOrNull($stmt, ':presoClasificacio2', $presoClasificacio2);
    bindIntOrNull($stmt, ':deportacio_camp', $deportacio_camp);
    bindIntOrNull($stmt, ':deportacio_subcamp', $deportacio_subcamp);

    // TEXT / VARCHAR
    bindStrOrNull($stmt, ':situacioFranca_num_matricula', $situacioFranca_num_matricula);
    bindStrOrNull($stmt, ':situacioFrancaObservacions', $situacioFrancaObservacions);
    bindStrOrNull($stmt, ':deportacio_num_matricula', $deportacio_num_matricula);
    bindStrOrNull($stmt, ':deportacio_nom_matricula_subcamp', $deportacio_nom_matricula_subcamp);
    bindStrOrNull($stmt, ':deportacio_observacions', $deportacio_observacions);

    // FECHAS (string o NULL)
    bindDateOrNull($stmt, ':data_alliberament', $data_alliberamentFormat);
    bindDateOrNull($stmt, ':situacioFranca_sortida', $situacioFranca_sortidaFormat);
    bindDateOrNull($stmt, ':presoClasificacioData1', $presoClasificacioData1Format);
    bindDateOrNull($stmt, ':presoClasificacioData2', $presoClasificacioData2Format);
    bindDateOrNull($stmt, ':deportacio_data_entrada', $deportacio_data_entradaFormat);
    bindDateOrNull($stmt, ':deportacio_data_entrada_subcamp', $deportacio_data_entrada_subcampFormat);

    $stmt->execute();

    $idInsert = $conn->lastInsertId();

    Audit::registrarCanvi(
        $conn,
        $userId,
        "INSERT",
        "Creació fitxa grup repressió deportats",
        Tables::DB_DEPORTATS,
        $idInsert
    );

    echo json_encode(["status" => "success", "message" => "Les dades s'han creat correctament a la base de dades.", "id" => $idInsert]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "S'ha produït un error a la base de dades: " . $e->getMessage()]);
}
