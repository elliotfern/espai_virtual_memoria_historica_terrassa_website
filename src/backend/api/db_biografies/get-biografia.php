<?php

use App\Config\DatabaseConnection;

$conn = DatabaseConnection::getConnection();

if (!$conn) {
    die("No se pudo establecer conexión a la base de datos.");
}

$slug = $routeParams[0];

// Configuración de cabeceras para aceptar JSON y responder JSON
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET");

// Definir el dominio permitido
$allowedOrigin = DOMAIN;

// Llamar a la función para verificar el referer
checkReferer($allowedOrigin);

// Verificar que el método de la solicitud sea GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// GET : Fitxa repressialiat > relació de biografies
// URL: https://memoriaterrassa.cat/api/biografia/get/fitxaBiografia?id=${id}
if ($slug === "fitxaBiografia") {
    $id = $_GET['id'] ?? null;

    $query = "SELECT 
            b.id, b.idRepresaliat, b.biografiaCa, b.biografiaEs, b.biografiaEn, b.biografiaIt, b.biografiaPt, b.biografiaFr
            FROM db_biografies AS b
            WHERE b.idRepresaliat = :idRepresaliat";

    $result = getData2($query, ['idRepresaliat' => $id], false);
    echo json_encode($result);
}
