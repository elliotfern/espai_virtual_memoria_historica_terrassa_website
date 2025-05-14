<?php

// Leer los datos de entrada
$input = file_get_contents('php://input');

// Verificar si los datos están vacíos
if (empty($input)) {
    http_response_code(400); // Bad Request
    echo json_encode(["status" => "error", "message" => "No se recibieron datos"]);
    exit;
}

// Decodificar los datos JSON
$data = json_decode($input, true);

// Verificar si los datos se decodificaron correctamente
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400); // Bad Request
    echo json_encode(["status" => "error", "message" => "Error al decodificar los datos JSON: " . json_last_error_msg()]);
    exit;
}

$errors = [];
if (empty($data['arxiu'])) {
    $errors['arxiu'] = 'El campo arxiu es obligatorio.';
}
if (empty($data['codi'])) {
    $errors['codi'] = 'El campo codi es obligatorio.';
}
if (empty($data['ciutat'])) {
    $errors['ciutat'] = 'El campo ciutat es obligatorio.';
}

if (empty($data['web'])) {
    $errors['web'] = 'El campo web es obligatorio.';
}

if (!empty($errors)) {
    http_response_code(400); // Bad Request
    echo json_encode(["status" => "error", "message" => "S'han produït errors en la validació", "errors" => $errors]);
    exit;
}

// Si no hay errores, crear las variables PHP y preparar la consulta PDO
$arxiu = $data['arxiu'];
$codi = $data['codi'];
$ciutat = $data['ciutat'];
$descripcio = $data['descripcio'];
$web = $data['web'];

// Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
try {

    global $conn;
    /** @var PDO $conn */

    // Crear la consulta SQL
    $sql = "INSERT INTO aux_bibliografia_arxius_codis (
            arxiu, codi, ciutat, descripcio, web
        ) VALUES (
            :arxiu, :codi, :ciutat, :descripcio, :web
        )";

    // Preparar la consulta
    $stmt = $conn->prepare($sql);

    // Enlazar los parámetros con los valores de las variables PHP
    $stmt->bindParam(':arxiu', $arxiu, PDO::PARAM_STR);
    $stmt->bindParam(':codi', $codi, PDO::PARAM_STR);
    $stmt->bindParam(':ciutat', $ciutat, PDO::PARAM_INT);
    $stmt->bindParam(':descripcio', $descripcio, PDO::PARAM_STR);
    $stmt->bindParam(':web', $web, PDO::PARAM_STR);

    // Ejecutar la consulta
    $stmt->execute();

    // Respuesta de éxito
    echo json_encode(["status" => "success", "message" => "Les dades s'han actualitzat correctament a la base de dades."]);
} catch (PDOException $e) {
    // En caso de error en la conexión o ejecución de la consulta
    http_response_code(500); // Internal Server Error
    echo json_encode(["status" => "error", "message" => "S'ha produit un error a la base de dades: " . $e->getMessage()]);
}
