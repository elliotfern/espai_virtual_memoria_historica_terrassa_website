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
    echo json_encode(["status" => "error", "message" => "S'han produït errors en la validació", "errors" => $errors]);
    exit;
}

// Si no hay errores, crear las variables PHP y preparar la consulta PDO
$situacio = !empty($data['situacio']) ? $data['situacio'] : NULL;
$data_alliberament = !empty($data['data_alliberament']) ? $data['data_alliberament'] : NULL;
$lloc_mort_alliberament = !empty($data['lloc_mort_alliberament']) ? $data['lloc_mort_alliberament'] : NULL;
$preso_tipus = !empty($data['preso_tipus']) ? $data['preso_tipus'] : NULL;
$preso_nom = !empty($data['preso_nom']) ? $data['preso_nom'] : NULL;
$preso_data_sortida = !empty($data['preso_data_sortida']) ? $data['preso_data_sortida'] : NULL;
$preso_localitat = !empty($data['preso_localitat']) ? $data['preso_localitat'] : NULL;
$preso_num_matricula = !empty($data['preso_num_matricula']) ? $data['preso_num_matricula'] : NULL;
$deportacio_nom_camp = !empty($data['deportacio_nom_camp']) ? $data['deportacio_nom_camp'] : NULL;
$deportacio_data_entrada = !empty($data['deportacio_data_entrada']) ? $data['deportacio_data_entrada'] : NULL;
$deportacio_num_matricula = !empty($data['deportacio_num_matricula']) ? $data['deportacio_num_matricula'] : NULL;
$deportacio_nom_subcamp = !empty($data['deportacio_nom_subcamp']) ? $data['deportacio_nom_subcamp'] : NULL;
$deportacio_data_entrada_subcamp = !empty($data['deportacio_data_entrada_subcamp']) ? $data['deportacio_data_entrada_subcamp'] : NULL;
$deportacio_nom_matricula_subcamp = !empty($data['deportacio_nom_matricula_subcamp']) ? $data['deportacio_nom_matricula_subcamp'] : NULL;
$idPersona = !empty($data['idPersona']) ? $data['idPersona'] : NULL;

// Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
try {

    global $conn;
    /** @var PDO $conn */

    // Crear la consulta SQL
    $sql = "UPDATE db_deportats SET 
        situacio = :situacio,
        data_alliberament = :data_alliberament,
        lloc_mort_alliberament = :lloc_mort_alliberament,
        preso_tipus = :preso_tipus,
        preso_nom = :preso_nom,
        preso_data_sortida = :preso_data_sortida,
        preso_localitat = :preso_localitat,
        preso_num_matricula = :preso_num_matricula,
        deportacio_nom_camp = :deportacio_nom_camp,
        deportacio_data_entrada = :deportacio_data_entrada,
        deportacio_num_matricula = :deportacio_num_matricula,
        deportacio_nom_subcamp = :deportacio_nom_subcamp,
        deportacio_data_entrada_subcamp = :deportacio_data_entrada_subcamp,
        deportacio_nom_matricula_subcamp = :deportacio_nom_matricula_subcamp
    WHERE idPersona = :idPersona";

    // Preparar la consulta
    $stmt = $conn->prepare($sql);

    // Enlazar los parámetros con los valores de las variables PHP
    $stmt->bindParam(':situacio', $situacio, PDO::PARAM_INT);
    $stmt->bindParam(':data_alliberament', $data_alliberament, PDO::PARAM_STR);
    $stmt->bindParam(':lloc_mort_alliberament', $lloc_mort_alliberament, PDO::PARAM_INT);
    $stmt->bindParam(':preso_tipus', $preso_tipus, PDO::PARAM_INT);
    $stmt->bindParam(':preso_nom', $preso_nom, PDO::PARAM_STR);
    $stmt->bindParam(':preso_data_sortida', $preso_data_sortida, PDO::PARAM_STR);
    $stmt->bindParam(':preso_localitat', $preso_localitat, PDO::PARAM_INT);
    $stmt->bindParam(':preso_num_matricula', $preso_num_matricula, PDO::PARAM_STR);
    $stmt->bindParam(':deportacio_nom_camp', $deportacio_nom_camp, PDO::PARAM_STR);
    $stmt->bindParam(':deportacio_data_entrada', $deportacio_data_entrada, PDO::PARAM_STR);
    $stmt->bindParam(':deportacio_num_matricula', $deportacio_num_matricula, PDO::PARAM_STR);
    $stmt->bindParam(':deportacio_nom_subcamp', $deportacio_nom_subcamp, PDO::PARAM_STR);
    $stmt->bindParam(':deportacio_data_entrada_subcamp', $deportacio_data_entrada_subcamp, PDO::PARAM_STR);
    $stmt->bindParam(':deportacio_nom_matricula_subcamp', $deportacio_nom_matricula_subcamp, PDO::PARAM_STR);
    $stmt->bindParam(':idPersona', $idPersona, PDO::PARAM_INT);

    // Supón que el ID a modificar lo pasas en el JSON también
    if (isset($data['idPersona'])) {
        $stmt->bindParam(':idPersona', $data['idPersona'], PDO::PARAM_INT);
    }

    // Ejecutar la consulta
    $stmt->execute();

    // Recuperar el ID del registro creado
    $lastInsertId = !empty($data['id']) ? $data['id'] : NULL;

    // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

    $dataHoraCanvi = date('Y-m-d H:i:s');
    $tipusOperacio = "Update Dades deportats";
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
