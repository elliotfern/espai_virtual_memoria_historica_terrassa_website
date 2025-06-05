<?php

use App\Config\Tables;
use App\Config\Audit;
use App\Config\DatabaseConnection;

$conn = DatabaseConnection::getConnection();

if (!$conn) {
    die("No se pudo establecer conexión a la base de datos.");
}

// Configuración de cabeceras para aceptar JSON y responder JSON
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: PUT");

// Definir el dominio permitido
$allowedOrigin = DOMAIN;

// Llamar a la función para verificar el referer
checkReferer($allowedOrigin);

// Verificar que el método de la solicitud sea GET
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

$userId = getAuthenticatedUserId();
if (!$userId) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

$inputData = file_get_contents('php://input');
$data = json_decode($inputData, true);


// Inicializar un array para los errores
$errors = [];

// Validación de los datos recibidos
if (!$data['idPersona'] || !$data['id']) {
    $errors[] = 'Falta ID i IDPersona';
}

// Validación de los datos recibidos
if (empty($data['situacio'])) {
    $errors[] = "El camp 'situacio' és obligatori";
}

if (empty($data['data_alliberament'])) {
    $errors[] = "El camp 'data de l'alliberament o mort' és obligatori";
}

if (empty($data['lloc_mort_alliberament'])) {
    $errors[] = "El camp 'lloc de la mort o alliberament' obligatori";
}

$data_alliberamentRaw = $data['data_alliberament'] ?? '';
if (!empty($data_alliberamentRaw)) {
    $data_alliberamentFormat = convertirDataFormatMysql($data_alliberamentRaw, 1);

    if (!$data_alliberamentFormat) {
        $errors[] = "El format de data no és vàlid. Format esperat: DD/MM/YYYY, amb anys entre 1936 i 1939";
    }
} else {
    $data_alliberamentFormat = null;
}

// Si hay errores, devolver una respuesta con los errores
if (!empty($errors)) {
    http_response_code(400); // Bad Request
    echo json_encode(["status" => "error", "message" => $errors]);
    exit;
}

// Si no hay errores, crear las variables PHP y preparar la consulta PDO
$situacio = !empty($data['situacio']) ? $data['situacio'] : NULL;
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
$idPersona = $data['idPersona'];
$id = $data['id'];

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
        deportacio_nom_matricula_subcamp = :deportacio_nom_matricula_subcamp,
        idPersona = :idPersona
    WHERE id = :id";

    // Preparar la consulta
    $stmt = $conn->prepare($sql);

    // Enlazar los parámetros con los valores de las variables PHP
    $stmt->bindParam(':situacio', $situacio, PDO::PARAM_INT);
    $stmt->bindParam(':data_alliberament', $data_alliberamentFormat, PDO::PARAM_STR);
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
    if (isset($id)) {
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    }

    // Ejecutar la consulta
    $stmt->execute();

    // Recuperar el ID del registro creado
    $id = $conn->lastInsertId();

    // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

    $detalls = "Modificació fitxa grup repressió deportats";
    $tipusOperacio = "UPDATE";

    Audit::registrarCanvi(
        $conn,
        $userId,                      // ID del usuario que hace el cambio
        $tipusOperacio,             // Tipus operacio
        $detalls,                       // Descripción de la operación
        Tables::DB_DEPORTATS,  // Nombre de la tabla afectada
        $id                           // ID del registro modificada
    );

    // Respuesta de éxito
    echo json_encode(["status" => "success", "message" => "Les dades s'han actualitzat correctament a la base de dades."]);
} catch (PDOException $e) {
    // En caso de error en la conexión o ejecución de la consulta
    http_response_code(500); // Internal Server Error
    echo json_encode(["status" => "error", "message" => "S'ha produit un error a la base de dades: " . $e->getMessage()]);
}
