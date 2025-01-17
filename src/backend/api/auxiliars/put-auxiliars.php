<?php
// Configuración de cabeceras para aceptar JSON y responder JSON
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: https://memoriaterrassa.cat");
header("Access-Control-Allow-Methods: PUT");

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
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405); // Método no permitido
    echo json_encode(["error" => "Método no permitido. Se requiere PUT."]);
    exit;
}

// DB_DADES PERSONALS
// 1) PUT municipi
// ruta PUT => "/api/auxiliars/put/?type=municipi"
if (isset($_GET['type']) && $_GET['type'] == 'municipi') {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['ciutat'])) {
        $errors[] = 'El camp ciutat és obligatori.';
    }


    // Si hay errores, devolver una respuesta con los errores
    if (!empty($errors)) {
        http_response_code(400); // Bad Request
        echo json_encode(["status" => "error", "message" => "S'han produït errors en la validació", "errors" => $errors]);
        exit;
    }

    // Si no hay errores, crear las variables PHP y preparar la consulta PDO
    $ciutat = !empty($data['ciutat']) ? $data['ciutat'] : NULL;
    $comarca = !empty($data['comarca']) ? $data['comarca'] : NULL;
    $provincia = !empty($data['provincia']) ? $data['provincia'] : NULL;
    $comunitat = !empty($data['comunitat']) ? $data['comunitat'] : NULL;
    $estat = !empty($data['estat']) ? $data['estat'] : NULL;
    $id = !empty($data['id']) ? $data['id'] : NULL;


    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "UPDATE aux_dades_municipis SET
            ciutat = :ciutat,
            comarca = :comarca,
            provincia = :provincia,
            comunitat = :comunitat,
            estat = :estat
        WHERE id = :id";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':ciutat', $ciutat, PDO::PARAM_STR);
        $stmt->bindParam(':comarca', $comarca, PDO::PARAM_INT);
        $stmt->bindParam(':provincia', $provincia, PDO::PARAM_INT);
        $stmt->bindParam(':comunitat', $comunitat, PDO::PARAM_INT);
        $stmt->bindParam(':estat', $estat, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $lastInsertId = $conn->lastInsertId();

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        $dataHoraCanvi = date('Y-m-d H:i:s');
        $tipusOperacio = "Update municipi";
        $idUser = $data['userId'] ?? null;

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
}
