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
if (!isset($data['condicio'])) {
    $errors[] = "El campo 'condicio' es obligatorio y debe ser 1 o 2.";
}

if (!isset($data['bandol'])) {
    $errors[] = "El campo 'bandol' es obligatorio y debe ser 1 o 2.";
}

if (!isset($data['cos']) || !is_numeric($data['cos'])) {
    $errors[] = "El campo 'cos' es obligatorio y debe ser un número válido (1, 2 o 3).";
}

if (!isset($data['circumstancia_mort'])) {
    $errors[] = "El campo 'circumstancia_mort' es obligatorio y debe ser 1 o 2.";
}


// Si hay errores, devolver una respuesta con los errores
if (!empty($errors)) {
    http_response_code(400); // Bad Request
    echo json_encode(["errors" => $errors]);
    exit;
}

// Si no hay errores, crear las variables PHP y preparar la consulta PDO
$condicio = !empty($data['condicio']) ? $data['condicio'] : NULL;
$bandol = !empty($data['bandol']) ? $data['bandol'] : NULL;
$any_lleva = !empty($data['any_lleva']) ? $data['any_lleva'] : NULL;
$unitat_inicial = !empty($data['unitat_inicial']) ? $data['unitat_inicial'] : NULL;
$cos = !empty($data['cos']) ? $data['cos'] : NULL;
$unitat_final = !empty($data['unitat_final']) ? $data['unitat_final'] : NULL;
$graduacio_final = !empty($data['graduacio_final']) ? $data['graduacio_final'] : NULL;
$periple_militar = !empty($data['periple_militar']) ? $data['periple_militar'] : NULL;
$circumstancia_mort = !empty($data['circumstancia_mort']) ? $data['circumstancia_mort'] : NULL;
$desaparegut_data = !empty($data['desaparegut_data']) ? $data['desaparegut_data'] : NULL;
$desaparegut_lloc = !empty($data['desaparegut_lloc']) ? $data['desaparegut_lloc'] : NULL;
$desaparegut_data_aparicio = !empty($data['desaparegut_data_aparicio']) ? $data['desaparegut_data_aparicio'] : NULL;
$desaparegut_lloc_aparicio = !empty($data['desaparegut_lloc_aparicio']) ? $data['desaparegut_lloc_aparicio'] : NULL;
$idPersona = !empty($data['idPersona']) ? $data['idPersona'] : NULL;

// Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
try {

    global $conn;
    /** @var PDO $conn */

    // Crear la consulta SQL
    $sql = "UPDATE db_cost_huma_morts_front SET 
            condicio = :condicio,
            bandol = :bandol,
            any_lleva = :any_lleva,
            unitat_inicial = :unitat_inicial,
            cos = :cos,
            unitat_final = :unitat_final,
            graduacio_final = :graduacio_final,
            periple_militar = :periple_militar,
            circumstancia_mort = :circumstancia_mort,
            desaparegut_data = :desaparegut_data,
            desaparegut_lloc = :desaparegut_lloc,
            desaparegut_data_aparicio = :desaparegut_data_aparicio,
            desaparegut_lloc_aparicio = :desaparegut_lloc_aparicio
        WHERE idPersona = :idPersona";

    // Preparar la consulta
    $stmt = $conn->prepare($sql);

    // Enlazar los parámetros con los valores de las variables PHP
    $stmt->bindParam(':condicio', $condicio, PDO::PARAM_INT);
    $stmt->bindParam(':bandol', $bandol, PDO::PARAM_INT);
    $stmt->bindParam(':any_lleva', $any_lleva, PDO::PARAM_STR);
    $stmt->bindParam(':unitat_inicial', $unitat_inicial, PDO::PARAM_STR);
    $stmt->bindParam(':cos', $cos, PDO::PARAM_INT);
    $stmt->bindParam(':unitat_final', $unitat_final, PDO::PARAM_STR);
    $stmt->bindParam(':graduacio_final', $graduacio_final, PDO::PARAM_STR);
    $stmt->bindParam(':periple_militar', $periple_militar, PDO::PARAM_STR);
    $stmt->bindParam(':circumstancia_mort', $circumstancia_mort, PDO::PARAM_INT);
    $stmt->bindParam(':desaparegut_data', $desaparegut_data, PDO::PARAM_STR);
    $stmt->bindParam(':desaparegut_lloc', $desaparegut_lloc, PDO::PARAM_INT);
    $stmt->bindParam(':desaparegut_data_aparicio', $desaparegut_data_aparicio, PDO::PARAM_STR);
    $stmt->bindParam(':desaparegut_lloc_aparicio', $desaparegut_lloc_aparicio, PDO::PARAM_INT);

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
    $tipusOperacio = "Update Dades cost humà en combat";
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
