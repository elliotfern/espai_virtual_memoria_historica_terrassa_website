<?php

use App\Config\Tables;
use App\Config\Audit;

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
if (empty($data['cirscumstancies_mort'])) {
    $errors[] = "El camp 'circumstàncies de la mort' és obligatori";
}

$dataCadaverRaw = $data['data_trobada_cadaver'] ?? '';
if (!empty($dataCadaverRaw)) {
    $dataCadaverFormat = convertirDataFormatMysql($dataCadaverRaw, 2);

    if (!$dataCadaverFormat) {
        $errors[] = "El format de data no és vàlid. Format esperat: DD/MM/YYYY, amb anys entre 1936 i 1979.";
    }
} else {
    $dataCadaverFormat = null;
}

$dataDetencioRaw = $data['data_detencio'] ?? '';
if (!empty($dataDetencioRaw)) {
    $dataDetencioFormat = convertirDataFormatMysql($dataDetencioRaw, 2);

    if (!$dataDetencioFormat) {
        $errors[] = "El format de data no és vàlid. Format esperat: DD/MM/YYYY, amb anys entre 1936 i 1979.";
    }
} else {
    $dataDetencioFormat = null;
}

$dataBombardeigRaw = $data['data_bombardeig'] ?? '';
if (!empty($dataBombardeigRaw)) {
    $dataBombardeigFormat = convertirDataFormatMysql($dataBombardeigRaw, 1);

    if (!$dataBombardeigFormat) {
        $errors[] = "El format de data no és vàlid. Format esperat: DD/MM/YYYY, amb anys entre 1936 i 1939.";
    }
} else {
    $dataBombardeigFormat = null;
}

if (!$data['idPersona'] || !$data['id']) {
    $errors[] = 'Falta ID i IDPersona';
}

// Si hay errores, devolver una respuesta con los errores
if (!empty($errors)) {
    http_response_code(400); // Bad Request
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "message" => $errors]);
    exit;
}


// Si no hay errores, crear las variables PHP y preparar la consulta PDO
$idPersona = $data['idPersona'];
$id = $data['id'];
$cirscumstancies_mort = $data['cirscumstancies_mort'];
$lloc_trobada_cadaver = !empty($data['lloc_trobada_cadaver']) ? $data['lloc_trobada_cadaver'] : NULL;
$lloc_detencio = !empty($data['lloc_detencio']) ? $data['lloc_detencio'] : NULL;
$municipi_bombardeig = !empty($data['municipi_bombardeig']) ? $data['municipi_bombardeig'] : NULL;
$lloc_bombardeig = !empty($data['lloc_bombardeig']) ? $data['lloc_bombardeig'] : NULL;
$responsable_bombardeig = !empty($data['responsable_bombardeig']) ? $data['responsable_bombardeig'] : NULL;
$qui_detencio = !empty($data['qui_detencio']) ? $data['qui_detencio'] : NULL;
$qui_executa_afusellat = !empty($data['qui_executa_afusellat']) ? $data['qui_executa_afusellat'] : NULL;
$qui_ordena_afusellat = !empty($data['qui_ordena_afusellat']) ? $data['qui_ordena_afusellat'] : NULL;

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
            responsable_bombardeig = :responsable_bombardeig,
            qui_detencio = :qui_detencio,
            qui_executa_afusellat = :qui_executa_afusellat,
            qui_ordena_afusellat = :qui_ordena_afusellat,
            idPersona = :idPersona
            WHERE id = :id";

    // Preparar la consulta
    $stmt = $conn->prepare($sql);

    // Enlazar los parámetros con los valores de las variables PHP
    $stmt->bindParam(':idPersona', $idPersona, PDO::PARAM_INT);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':cirscumstancies_mort', $cirscumstancies_mort, PDO::PARAM_INT);
    $stmt->bindParam(':data_trobada_cadaver', $dataCadaverFormat, PDO::PARAM_STR);
    $stmt->bindParam(':lloc_trobada_cadaver', $lloc_trobada_cadaver, PDO::PARAM_INT);
    $stmt->bindParam(':data_detencio', $dataDetencioFormat, PDO::PARAM_STR);
    $stmt->bindParam(':lloc_detencio', $lloc_detencio, PDO::PARAM_INT);
    $stmt->bindParam(':data_bombardeig', $dataBombardeigFormat, PDO::PARAM_STR);
    $stmt->bindParam(':municipi_bombardeig', $municipi_bombardeig, PDO::PARAM_INT);
    $stmt->bindParam(':lloc_bombardeig', $lloc_bombardeig, PDO::PARAM_INT);
    $stmt->bindParam(':responsable_bombardeig', $responsable_bombardeig, PDO::PARAM_INT);
    $stmt->bindParam(':qui_detencio', $qui_detencio, PDO::PARAM_STR);
    $stmt->bindParam(':qui_executa_afusellat', $qui_executa_afusellat, PDO::PARAM_STR);
    $stmt->bindParam(':qui_ordena_afusellat', $qui_ordena_afusellat, PDO::PARAM_STR);

    // Supón que el ID a modificar lo pasas en el JSON también
    if (isset($id)) {
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    }

    // Ejecutar la consulta
    $stmt->execute();

    // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis
    $detalls = "Modificació fitxa repressió cost humà morts civils";
    $tipusOperacio = "UPDATE";

    Audit::registrarCanvi(
        $conn,
        $userId,                      // ID del usuario que hace el cambio
        $tipusOperacio,             // Tipus operacio
        $detalls,                       // Descripción de la operación
        Tables::DB_COST_HUMA_MORTS_CIVILS,  // Nombre de la tabla afectada
        $id                           // ID del registro modificada
    );

    // Respuesta de éxito
    echo json_encode(["status" => "success", "message" => "Les dades s'han actualitzat correctament a la base de dades."]);
} catch (PDOException $e) {
    // En caso de error en la conexión o ejecución de la consulta
    http_response_code(500); // Internal Server Error
    echo json_encode(["status" => "error", "message" => "S'ha produit un error a la base de dades: " . $e->getMessage()]);
}
