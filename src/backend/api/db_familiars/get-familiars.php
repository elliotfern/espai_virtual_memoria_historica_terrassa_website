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

// GET : Fitxa repressialiat > relació de familiars
// URL: https://memoriaterrassa.cat/api/familiars/get/fitxaFamiliars?id=${id}
if ($slug === "fitxaFamiliars") {
    $id = $_GET['id'] ?? null;

    $query = "SELECT 
            f.id, CONCAT_WS(' ', f.nom, f.cognom1, f.cognom2) AS nom_complet,
            f.anyNaixement,
            f.idParent,
            r.relacio_parentiu
            FROM aux_familiars AS f
            LEFT JOIN aux_familiars_relacio AS r ON f.relacio_parentiu = r.id
            WHERE f.idParent = :idParent";

    $result = getData2($query, ['idParent' => $id], false);
    echo json_encode($result);
}
