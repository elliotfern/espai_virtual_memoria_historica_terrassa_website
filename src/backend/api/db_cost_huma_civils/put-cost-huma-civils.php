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

$inputData = file_get_contents('php://input');
$data = json_decode($inputData, true);

// Inicializar un array para los errores
$errors = [];

// Validación de los datos recibidos


// Si hay errores, devolver una respuesta con los errores
if (!empty($errors)) {
    http_response_code(400); // Bad Request
    echo json_encode(["errors" => $errors]);
    exit;
}

// Si no hay errores, crear las variables PHP y preparar la consulta PDO
$idPersona = !empty($data['idPersona']) ? $data['idPersona'] : NULL;
$cirscumstancies_mort = !empty($data['cirscumstancies_mort']) ? $data['cirscumstancies_mort'] : NULL;
$data_trobada_cadaver = !empty($data['data_trobada_cadaver']) ? $data['data_trobada_cadaver'] : NULL;
$lloc_trobada_cadaver = !empty($data['lloc_trobada_cadaver']) ? $data['lloc_trobada_cadaver'] : NULL;
$data_detencio = !empty($data['data_detencio']) ? $data['data_detencio'] : NULL;
$lloc_detencio = !empty($data['lloc_detencio']) ? $data['lloc_detencio'] : NULL;
$data_bombardeig = !empty($data['data_bombardeig']) ? $data['data_bombardeig'] : NULL;
$municipi_bombardeig = !empty($data['municipi_bombardeig']) ? $data['municipi_bombardeig'] : NULL;
$lloc_bombardeig = !empty($data['lloc_bombardeig']) ? $data['lloc_bombardeig'] : NULL;
$responsable_bombardeig = !empty($data['responsable_bombardeig']) ? $data['responsable_bombardeig'] : NULL;

// Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
try {

    global $conn;
    /** @var PDO $conn */

    // Crear la consulta SQL
    $sql = "UPDATE db_cost_huma_morts_civils SET 
            cirscumstancies_mort = :cirscumstancies_mort,
            data_trobada_cadaver = :data_trobada_cadaver,
            lloc_trobada_cadaver = :lloc_trobada_cadaver,
            data_detencio = :data_detencio,
            lloc_detencio = :lloc_detencio,
            data_bombardeig = :data_bombardeig,
            municipi_bombardeig = :municipi_bombardeig,
            lloc_bombardeig = :lloc_bombardeig,
            responsable_bombardeig = :responsable_bombardeig
            WHERE idPersona = :idPersona";

    // Preparar la consulta
    $stmt = $conn->prepare($sql);

    // Enlazar los parámetros con los valores de las variables PHP
    $stmt->bindParam(':idPersona', $idPersona, PDO::PARAM_INT);
    $stmt->bindParam(':cirscumstancies_mort', $cirscumstancies_mort, PDO::PARAM_INT);
    $stmt->bindParam(':data_trobada_cadaver', $data_trobada_cadaver, PDO::PARAM_STR);
    $stmt->bindParam(':lloc_trobada_cadaver', $lloc_trobada_cadaver, PDO::PARAM_INT);
    $stmt->bindParam(':data_detencio', $data_detencio, PDO::PARAM_STR);
    $stmt->bindParam(':lloc_detencio', $lloc_detencio, PDO::PARAM_INT);
    $stmt->bindParam(':data_bombardeig', $data_bombardeig, PDO::PARAM_STR);
    $stmt->bindParam(':municipi_bombardeig', $municipi_bombardeig, PDO::PARAM_INT);
    $stmt->bindParam(':lloc_bombardeig', $lloc_bombardeig, PDO::PARAM_INT);
    $stmt->bindParam(':responsable_bombardeig', $responsable_bombardeig, PDO::PARAM_INT);

    // Supón que el ID a modificar lo pasas en el JSON también
    if (isset($data['idPersona'])) {
        $stmt->bindParam(':idPersona', $data['idPersona'], PDO::PARAM_INT);
    }

    // Ejecutar la consulta
    $stmt->execute();

    // Recuperar el ID del registro creado
    $lastInsertId = $idPersona;

    // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

    $dataHoraCanvi = date('Y-m-d H:i:s');
    $tipusOperacio = "Update Dades cost humà civils";
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
