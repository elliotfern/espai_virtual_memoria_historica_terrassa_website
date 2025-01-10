<?php
// Configuración de cabeceras para aceptar JSON y responder JSON
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: https://memoriaterrassa.cat");
header("Access-Control-Allow-Methods: POST");

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

// Verificar que el método HTTP sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Método no permitido
    echo json_encode(["error" => "Método no permitido. Se requiere POST."]);
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
    $sql = "INSERT INTO db_cost_huma_morts_civils (
        idPersona,
        cirscumstancies_mort,
        data_trobada_cadaver,
        lloc_trobada_cadaver,
        data_detencio,
        lloc_detencio,
        data_bombardeig,
        municipi_bombardeig,
        lloc_bombardeig,
        responsable_bombardeig
    ) VALUES (
        :idPersona,
        :cirscumstancies_mort,
        :data_trobada_cadaver,
        :lloc_trobada_cadaver,
        :data_detencio,
        :lloc_detencio,
        :data_bombardeig,
        :municipi_bombardeig,
        :lloc_bombardeig,
        :responsable_bombardeig
    )";

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

    // Ejecutar la consulta
    $stmt->execute();

    // Respuesta de éxito
    echo json_encode(["status" => "success", "message" => "Les dades s'han desat correctament a la base de dades."]);
} catch (PDOException $e) {
    // En caso de error en la conexión o ejecución de la consulta
    http_response_code(500); // Internal Server Error
    echo json_encode(["status" => "error", "message" => "S'ha produit un error a la base de dades: " . $e->getMessage()]);
}
