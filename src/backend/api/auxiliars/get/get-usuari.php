<?php

// Configuración de cabeceras para aceptar JSON y responder JSON
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET");

// Definir el dominio permitido
$allowedOrigin = "https://memoriaterrassa.cat";

// Llamar a la función para verificar el referer
checkReferer($allowedOrigin);

// Verificar que el método de la solicitud sea GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

$id = $routeParams[0];

global $conn;
/** @var PDO $conn */
$query = "SELECT u.nom, u.email, u.biografia_cat, u.user_type, u.id
                FROM auth_users AS u
                WHERE u.id = :id";
try {
    // Preparar la consulta
    $stmt = $conn->prepare($query);

    $stmt->bindParam(':id', $id, PDO::PARAM_STR);

    // Ejecutar la consulta
    $stmt->execute();

    // Obtener los resultados
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si hay resultados
    if ($row) {
        // Si hay resultados, devolver los datos en formato JSON
        header("Content-Type: application/json");
        echo json_encode($row);
    } else {
        // Si no hay resultados, devolver un mensaje de error
        header("Content-Type: application/json");
        echo json_encode(['status' => 'error', 'message' => 'No se encontraron registros.']);
    }
} catch (PDOException $e) {
    // En caso de error en la consulta, devolver el error en formato JSON
    header("Content-Type: application/json");
    echo json_encode(['status' => 'error', 'message' => 'Error en la consulta: ' . $e->getMessage()]);
}
