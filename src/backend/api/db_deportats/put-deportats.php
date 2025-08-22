<?php

use App\Config\Tables;
use App\Config\Audit;
use App\Config\DatabaseConnection;

/** Helpers */
function bindDateOrNull(PDOStatement $stmt, string $param, ?string $val): void
{
    $stmt->bindValue($param, $val, $val !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
}
function bindIntOrNull(PDOStatement $stmt, string $param, $val): void
{
    $stmt->bindValue($param, $val, $val !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
}

$conn = DatabaseConnection::getConnection();
if (!$conn) {
    http_response_code(500);
    die("No se pudo establecer conexión a la base de datos.");
}

/** CORS / headers */
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Origin: " . DOMAIN);

// Preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Referer check (si lo usas)
checkReferer(DOMAIN);

// Método
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
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

// Errores
$errors = [];

// Validaciones mínimas
if (empty($data['id']) || empty($data['idPersona'])) {
    $errors[] = 'Falta id i idPersona';
}
if (empty($data['situacio'])) {
    $errors[] = "El camp 'situacio' és obligatori";
}
if (empty($data['lloc_mort_alliberament'])) {
    $errors[] = "El camp 'lloc de la mort o alliberament' és obligatori";
}

// Fechas (permiten vacío => NULL)
$data_alliberamentRaw = $data['data_alliberament'] ?? '';
if ($data_alliberamentRaw !== '') {
    $data_alliberamentFormat = convertirDataFormatMysql($data_alliberamentRaw, 3);
    if (!$data_alliberamentFormat) {
        $errors[] = "Data alliberament: el format no és vàlid. Esperat: DD/MM/YYYY";
    }
} else {
    $data_alliberamentFormat = null;
}

$situacioFranca_sortidaRaw = $data['situacioFranca_sortida'] ?? '';
if ($situacioFranca_sortidaRaw !== '') {
    $situacioFranca_sortidaFormat = convertirDataFormatMysql($situacioFranca_sortidaRaw, 3);
    if (!$situacioFranca_sortidaFormat) {
        $errors[] = "Data sortida presó (situacioFranca_sortida): format no vàlid. Esperat: DD/MM/YYYY";
    }
} else {
    $situacioFranca_sortidaFormat = null;
}

$deportacio_data_entradaRaw = $data['deportacio_data_entrada'] ?? '';
if ($deportacio_data_entradaRaw !== '') {
    $deportacio_data_entradaFormat = convertirDataFormatMysql($deportacio_data_entradaRaw, 3);
    if (!$deportacio_data_entradaFormat) {
        $errors[] = "Data entrada (deportacio): format no vàlid. Esperat: DD/MM/YYYY";
    }
} else {
    $deportacio_data_entradaFormat = null;
}

$deportacio_data_entrada_subcampRaw = $data['deportacio_data_entrada_subcamp'] ?? '';
if ($deportacio_data_entrada_subcampRaw !== '') {
    $deportacio_data_entrada_subcampFormat = convertirDataFormatMysql($deportacio_data_entrada_subcampRaw, 3);
    if (!$deportacio_data_entrada_subcampFormat) {
        $errors[] = "Data entrada subcamp: format no vàlid. Esperat: DD/MM/YYYY";
    }
} else {
    $deportacio_data_entrada_subcampFormat = null;
}

$presoClasificacioData1Raw = $data['presoClasificacioData1'] ?? '';
if ($presoClasificacioData1Raw !== '') {
    $presoClasificacioData1Format = convertirDataFormatMysql($presoClasificacioData1Raw, 3);
    if (!$presoClasificacioData1Format) {
        $errors[] = "Data presó (presoClasificacioData1): format no vàlid. Esperat: DD/MM/YYYY";
    }
} else {
    $presoClasificacioData1Format = null;
}

$presoClasificacioData2Raw = $data['presoClasificacioData2'] ?? '';
if ($presoClasificacioData2Raw !== '') {
    $presoClasificacioData2Format = convertirDataFormatMysql($presoClasificacioData2Raw, 3);
    if (!$presoClasificacioData2Format) {
        $errors[] = "Data presó (presoClasificacioData2): format no vàlid. Esperat: DD/MM/YYYY";
    }
} else {
    $presoClasificacioData2Format = null;
}

$presoClasificacioDataEntrada2Raw = $data['presoClasificacioDataEntrada2'] ?? '';
if ($presoClasificacioDataEntrada2Raw !== '') {
    $presoClasificacioDataEntrada2Format = convertirDataFormatMysql($presoClasificacioDataEntrada2Raw, 3);
    if (!$presoClasificacioDataEntrada2Format) $errors[] = "Data entrada presó (2): format no vàlid. Esperat: DD/MM/YYYY";
} else {
    $presoClasificacioDataEntrada2Format = null;
}

$presoClasificacioDataEntrada1Raw = $data['presoClasificacioDataEntrada1'] ?? '';
if ($presoClasificacioDataEntrada1Raw !== '') {
    $presoClasificacioDataEntrada1Format = convertirDataFormatMysql($presoClasificacioDataEntrada1Raw, 3);
    if (!$presoClasificacioDataEntrada1Format) $errors[] = "Data entrada presó (1): format no vàlid. Esperat: DD/MM/YYYY";
} else {
    $presoClasificacioDataEntrada1Format = null;
}

// Si hay errores
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => $errors]);
    exit;
}

// Scalars / nulos
$id = (int)$data['id'];
$idPersona = (int)$data['idPersona'];

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
$presoClasificacioMatr1 = $data['presoClasificacioMatr1'] ?? null;
$presoClasificacioMatr2 = $data['presoClasificacioMatr2'] ?? null;


try {
    /** @var PDO $conn */
    $sql = "UPDATE db_deportats SET
        idPersona = :idPersona,
        situacio = :situacio,
        data_alliberament = :data_alliberament,
        lloc_mort_alliberament = :lloc_mort_alliberament,
        situacioFranca = :situacioFranca,
        situacioFranca_sortida = :situacioFranca_sortida,
        situacioFranca_num_matricula = :situacioFranca_num_matricula,
        situacioFrancaObservacions = :situacioFrancaObservacions,
        presoClasificacio1 = :presoClasificacio1,
        presoClasificacioData1 = :presoClasificacioData1,
        presoClasificacio2 = :presoClasificacio2,
        presoClasificacioData2 = :presoClasificacioData2,
        deportacio_camp = :deportacio_camp,
        deportacio_data_entrada = :deportacio_data_entrada,
        deportacio_num_matricula = :deportacio_num_matricula,
        deportacio_subcamp = :deportacio_subcamp,
        deportacio_data_entrada_subcamp = :deportacio_data_entrada_subcamp,
        deportacio_nom_matricula_subcamp = :deportacio_nom_matricula_subcamp,
        deportacio_observacions = :deportacio_observacions,
        presoClasificacioDataEntrada1 = :presoClasificacioDataEntrada1,
        presoClasificacioDataEntrada2 = :presoClasificacioDataEntrada2,
        presoClasificacioMatr1 = :presoClasificacioMatr1,
        presoClasificacioMatr2 = :presoClasificacioMatr2
    WHERE id = :id";

    $stmt = $conn->prepare($sql);

    // INT obligatorios
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->bindValue(':idPersona', $idPersona, PDO::PARAM_INT);

    // INT (permiten NULL?)
    bindIntOrNull($stmt, ':situacio', $situacio);
    bindIntOrNull($stmt, ':lloc_mort_alliberament', $lloc_mort_alliberament);
    bindIntOrNull($stmt, ':situacioFranca', $situacioFranca);
    bindIntOrNull($stmt, ':presoClasificacio1', $presoClasificacio1);
    bindIntOrNull($stmt, ':presoClasificacio2', $presoClasificacio2);
    bindIntOrNull($stmt, ':deportacio_camp', $deportacio_camp);
    bindIntOrNull($stmt, ':deportacio_subcamp', $deportacio_subcamp);

    // TEXT / VARCHAR
    $stmt->bindValue(':situacioFranca_num_matricula', $situacioFranca_num_matricula, $situacioFranca_num_matricula !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
    $stmt->bindValue(':situacioFrancaObservacions', $situacioFrancaObservacions, $situacioFrancaObservacions !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
    $stmt->bindValue(':deportacio_num_matricula', $deportacio_num_matricula, $deportacio_num_matricula !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
    $stmt->bindValue(':deportacio_nom_matricula_subcamp', $deportacio_nom_matricula_subcamp, $deportacio_nom_matricula_subcamp !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
    $stmt->bindValue(':deportacio_observacions', $deportacio_observacions, $deportacio_observacions !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
    $stmt->bindValue(':presoClasificacioMatr1', $presoClasificacioMatr1, $presoClasificacioMatr1 !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
    $stmt->bindValue(':presoClasificacioMatr2', $presoClasificacioMatr2, $presoClasificacioMatr2 !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);

    // FECHAS (string o NULL)
    bindDateOrNull($stmt, ':data_alliberament', $data_alliberamentFormat);
    bindDateOrNull($stmt, ':situacioFranca_sortida', $situacioFranca_sortidaFormat);
    bindDateOrNull($stmt, ':presoClasificacioData1', $presoClasificacioData1Format);
    bindDateOrNull($stmt, ':presoClasificacioData2', $presoClasificacioData2Format);
    bindDateOrNull($stmt, ':deportacio_data_entrada', $deportacio_data_entradaFormat);
    bindDateOrNull($stmt, ':deportacio_data_entrada_subcamp', $deportacio_data_entrada_subcampFormat);
    bindDateOrNull($stmt, ':presoClasificacioDataEntrada1', $presoClasificacioDataEntrada1Format);
    bindDateOrNull($stmt, ':presoClasificacioDataEntrada2', $presoClasificacioDataEntrada2Format);

    $stmt->execute();

    // Auditoría: usar el ID actualizado (no lastInsertId en UPDATE)
    Audit::registrarCanvi(
        $conn,
        $userId,
        "UPDATE",
        "Modificació fitxa grup repressió deportats",
        Tables::DB_DEPORTATS,
        $id
    );

    echo json_encode(["status" => "success", "message" => "Les dades s'han actualitzat correctament a la base de dades."]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "S'ha produït un error a la base de dades: " . $e->getMessage()]);
}
