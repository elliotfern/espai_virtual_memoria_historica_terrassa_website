<?php

$inputData = file_get_contents('php://input');
$data = json_decode($inputData, true);

// Inicializar un array para los errores
$errors = [];

// Validación de los datos recibidos
if (empty($data['llibre'])) {
    $errors[] = 'El camp llibre és obligatori.';
}

if (empty($data['autor'])) {
    $errors[] = 'El camp autor és obligatori.';
}

// Si hay errores, devolver una respuesta con los errores
if (!empty($errors)) {
    http_response_code(400); // Bad Request
    echo json_encode(["status" => "error", "message" => "S'han produït errors en la validació", "errors" => $errors]);
    exit;
}

// Si no hay errores, crear las variables PHP y preparar la consulta PDO
$llibre = !empty($data['llibre']) ? $data['llibre'] : NULL;
$autor = !empty($data['autor']) ? $data['autor'] : NULL;
$editorial = !empty($data['editorial']) ? $data['editorial'] : NULL;
$ciutat = !empty($data['ciutat']) ? $data['ciutat'] : NULL;
$any = !empty($data['any']) ? $data['any'] : NULL;
$volum = !empty($data['volum']) ? $data['volum'] : NULL;

// Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
try {

    global $conn;
    /** @var PDO $conn */

    // Crear la consulta SQL
    $sql = "INSERT INTO aux_bibliografia_llibre_detalls (
            llibre, autor, editorial, ciutat, any, volum
        ) VALUES (
            :llibre, :autor, :editorial, :ciutat, :any, :volum
        )";

    // Preparar la consulta
    $stmt = $conn->prepare($sql);

    // Enlazar los parámetros con los valores de las variables PHP
    $stmt->bindParam(':llibre', $llibre, PDO::PARAM_STR);
    $stmt->bindParam(':autor', $autor, PDO::PARAM_STR);
    $stmt->bindParam(':editorial', $editorial, PDO::PARAM_STR);
    $stmt->bindParam(':ciutat', $ciutat, PDO::PARAM_INT);
    $stmt->bindParam(':any', $any, PDO::PARAM_STR);
    $stmt->bindParam(':volum', $volum, PDO::PARAM_STR);

    // Ejecutar la consulta
    $stmt->execute();


    // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

    $dataHoraCanvi = date('Y-m-d H:i:s');
    $tipusOperacio = "Insert Nou llibre";
    $idUser = $data['userId'] ?? null;
    $lastInsertId = NULL;

    // Crear la consulta SQL
    $sql2 = "INSERT INTO control_registre_canvis (
        idUser, idPersonaFitxa, tipusOperacio, dataHoraCanvi
        ) VALUES (
        :idUser, :idPersonaFitxa, :tipusOperacio, :dataHoraCanvi
        )";

    // Preparar la consulta
    $stmt = $conn->prepare($sql2);

    // Enlazar los parámetros con los valores de las variables PHP
    $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
    $stmt->bindParam(':idPersonaFitxa', $lastInsertId, PDO::PARAM_INT);
    $stmt->bindParam(':dataHoraCanvi', $dataHoraCanvi, PDO::PARAM_STR);
    $stmt->bindParam(':tipusOperacio', $tipusOperacio, PDO::PARAM_STR);

    // Ejecutar la consulta
    $stmt->execute();

    // Respuesta de éxito
    echo json_encode(["status" => "success", "message" => "Les dades s'han actualitzat correctament a la base de dades."]);
} catch (PDOException $e) {
    // En caso de error en la conexión o ejecución de la consulta
    http_response_code(500); // Internal Server Error
    echo json_encode(["status" => "error", "message" => "S'ha produit un error a la base de dades: " . $e->getMessage()]);
}
