<?php

use App\Config\DatabaseConnection;

$conn = DatabaseConnection::getConnection();

header("Content-Type: application/json");

if (!$conn) {
    http_response_code(500);
    echo json_encode(["error" => "Error conexión DB"]);
    exit;
}

// =========================================================
// CONFIG
// =========================================================

$jsonUrl = "https://memoriaterrassa.cat/dades.json";

// =========================================================
// FETCH JSON
// =========================================================

$jsonContent = @file_get_contents($jsonUrl);

if (!$jsonContent) {
    http_response_code(500);
    echo json_encode(["error" => "No se pudo cargar el JSON"]);
    exit;
}

$data = json_decode($jsonContent, true);

if (!$data || !is_array($data)) {
    http_response_code(500);
    echo json_encode(["error" => "JSON inválido"]);
    exit;
}

// =========================================================
// PREPARE STATEMENTS
// =========================================================

$sqlInsert = "INSERT INTO db_cronologia (
    any, mes, diaInici, diaFi, mesFi, area, tema, textCa
) VALUES (
    :any, :mes, :diaInici, :diaFi, :mesFi, :area, :tema, :textCa
)";

$stmtInsert = $conn->prepare($sqlInsert);

// evitar duplicados
$sqlCheck = "SELECT id FROM db_cronologia 
             WHERE any = :any AND textCa = :textCa 
             LIMIT 1";

$stmtCheck = $conn->prepare($sqlCheck);

// =========================================================
// IMPORT
// =========================================================

$inserted = 0;
$skipped = 0;
$errors = [];

try {

    $conn->beginTransaction();

    foreach ($data as $i => $event) {

        try {

            $any = $event['any'] ?? null;
            $mes = $event['mes'] ?? null;
            $diaInici = $event['diaInici'] ?? null;
            $diaFi = $event['diaFi'] ?? null;
            $mesFi = $event['mesFi'] ?? null;
            $area = $event['area'] ?? null;
            $tema = $event['tema'] ?? null;
            $textCa = $event['textCa'] ?? null;

            // mínimo obligatorio
            if (!$any || !$textCa) {
                $skipped++;
                continue;
            }

            // =========================
            // CHECK DUPLICADO
            // =========================
            $stmtCheck->execute([
                ':any' => $any,
                ':textCa' => $textCa
            ]);

            if ($stmtCheck->fetch()) {
                $skipped++;
                continue;
            }

            // =========================
            // INSERT
            // =========================
            $stmtInsert->bindValue(':any', $any, PDO::PARAM_INT);
            $stmtInsert->bindValue(':mes', $mes, $mes === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmtInsert->bindValue(':diaInici', $diaInici, $diaInici === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmtInsert->bindValue(':diaFi', $diaFi, $diaFi === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmtInsert->bindValue(':mesFi', $mesFi, $mesFi === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmtInsert->bindValue(':area', $area, $area === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmtInsert->bindValue(':tema', $tema, $tema === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmtInsert->bindValue(':textCa', $textCa, PDO::PARAM_STR);

            $stmtInsert->execute();

            $inserted++;
        } catch (Exception $e) {
            $errors[] = "Error fila $i: " . $e->getMessage();
        }
    }

    $conn->commit();
} catch (Exception $e) {
    $conn->rollBack();

    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Error en transacción",
        "detail" => $e->getMessage()
    ]);
    exit;
}

// =========================================================
// RESPONSE
// =========================================================

echo json_encode([
    "status" => "success",
    "total" => count($data),
    "inserted" => $inserted,
    "skipped" => $skipped,
    "errors_count" => count($errors),
    "errors_sample" => array_slice($errors, 0, 10)
]);
