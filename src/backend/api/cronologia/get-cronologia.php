<?php

use App\Config\DatabaseConnection;

$conn = DatabaseConnection::getConnection();

if (!$conn) {
    die("No se pudo establecer conexión a la base de datos.");
}

// Configuración de cabeceras para aceptar JSON y responder JSON
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: https://memoriaterrassa.cat");
header("Access-Control-Allow-Methods: GET");

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
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405); // Método no permitido
    echo json_encode(["error" => "Método no permitido. Se requiere GET."]);
    exit;
}


// DB_FONTS DOCUMENTALS
// 1) POST esdeveniment
// ruta POST => "/api/cronologia/post/
header("Content-Type: application/json");

// Parámetros de la consulta
$area = $_GET['area'] ?? 'tots';
$tema = $_GET['tema'] ?? 'tots';
$pagina = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
$limite = 20; // Número de eventos por página
$offset = ($pagina - 1) * $limite;

global $conn;

// Obtener el parámetro del año
$any = $_GET['any'] ?? 'tots';

$sql = "SELECT c.id, c.any, m.mesCa AS mes, m.ordre AS mesOrdre, c.diaInici, c.diaFi, c.mesFi, c.tema, c.area, c.textCa 
    FROM db_cronologia AS c
    LEFT JOIN aux_cronologia_mes AS m ON c.mes = m.id
    WHERE 1=1";

if ($area !== 'tots') {
    $sql .= " AND c.area = :area";
}
if ($tema !== 'tots') {
    $sql .= " AND c.tema = :tema";
}
if ($any !== 'tots') {
    $sql .= " AND c.any = :any";
}

$sql .= " ORDER BY c.any ASC, mesOrdre ASC, c.diaInici ASC LIMIT :limite OFFSET :offset";

$stmt = $conn->prepare($sql);

// Asignar parámetros a la consulta
if ($area !== 'tots') {
    $stmt->bindParam(':area', $area, PDO::PARAM_INT);
}
if ($tema !== 'tots') {
    $stmt->bindParam(':tema', $tema, PDO::PARAM_INT);
}
if ($any !== 'tots') {
    $stmt->bindParam(':any', $any, PDO::PARAM_INT);
}
$stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();
$eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// =============================
// Cálculo del total de eventos
// =============================
$countSql = "SELECT COUNT(*) FROM db_cronologia WHERE 1=1";

if ($area !== 'tots') {
    $countSql .= " AND area = :area";
}
if ($tema !== 'tots') {
    $countSql .= " AND tema = :tema";
}
if ($any !== 'tots') {
    $countSql .= " AND any = :any";
}

$countStmt = $conn->prepare($countSql);

// Asignar parámetros a la consulta de conteo
if ($area !== 'tots') {
    $countStmt->bindParam(':area', $area, PDO::PARAM_INT);
}
if ($tema !== 'tots') {
    $countStmt->bindParam(':tema', $tema, PDO::PARAM_INT);
}
if ($any !== 'tots') {
    $countStmt->bindParam(':any', $any, PDO::PARAM_INT);
}

$countStmt->execute();
$totalEventos = $countStmt->fetchColumn();
$totalPaginas = ceil($totalEventos / $limite);

// =============================
// Respuesta JSON
// =============================
echo json_encode([
    'eventos' => $eventos,
    'totalEventos' => $totalEventos,
    'totalPaginas' => $totalPaginas
]);
