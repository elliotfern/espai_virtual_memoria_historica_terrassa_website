<?php
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

try {
    global $conn;
    $query = "SELECT DISTINCT c.any
    FROM db_cronologia AS c
    ORDER BY c.any ASC";

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $eventos = $stmt->fetchAll();

    echo json_encode($eventos);
} catch (PDOException $e) {
    echo json_encode(["error" => "Error en la base de datos: " . $e->getMessage()]);
}
