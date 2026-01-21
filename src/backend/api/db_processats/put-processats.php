<?php

use App\Config\Tables;
use App\Config\Audit;
use App\Config\DatabaseConnection;
use App\Utils\MissatgesAPI;
use App\Utils\Response;
use App\Utils\ValidacioErrors;

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

// Verificar que el método de la solicitud sea PUT
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

if (!$data['idPersona']) {
    http_response_code(400); // Bad Request
    echo json_encode(["status" => 'error', 'message' => 'Falta IDPersona']);
    exit;
}

// Inicializar un array para los errores
$errors = [];

// Validación de los datos recibidos
if (empty($data['tipus_procediment'])) {
    $errors[] =  ValidacioErrors::requerit('tipus de procediment');
}

$data_inici_proces_raw = $data['data_inici_proces'] ?? '';
if (!empty($data_inici_proces_raw)) {
    $data_inici_procesFormat = convertirDataFormatMysql($data_inici_proces_raw, 3);

    if (!$data_inici_procesFormat) {
        $errors[] = ValidacioErrors::dataNoValida('data inici procediment judicial');
    }
} else {
    $data_inici_procesFormat = null;
}

$consell_guerra_data_raw = $data['consell_guerra_data'] ?? '';
if (!empty($consell_guerra_data_raw)) {
    $consell_guerra_dataFormat = convertirDataFormatMysql($consell_guerra_data_raw, 3);

    if (!$consell_guerra_dataFormat) {
        $errors[] = ValidacioErrors::dataNoValida('data consell de guerra');
    }
} else {
    $consell_guerra_dataFormat = null;
}

// Si hay errores, devolver una respuesta con los errores
if (!empty($errors)) {
    Response::error(
        MissatgesAPI::error('validacio'),
        $errors,
        400
    );
}

$sentencia_data_raw = $data['sentencia_data'] ?? '';
if (!empty($sentencia_data_raw)) {
    $sentencia_dataFormat = convertirDataFormatMysql($sentencia_data_raw, 3);

    if (!$sentencia_dataFormat) {
        $errors[] = ValidacioErrors::dataNoValida('data sentència');
    }
} else {
    $sentencia_dataFormat = null;
}

$data_detencio_raw = $data['data_detencio'] ?? '';
if (!empty($data_detencio_raw)) {
    $data_detencioFormat = convertirDataFormatMysql($data_detencio_raw, 3);

    if (!$data_detencioFormat) {
        $errors[] = ValidacioErrors::dataNoValida('data detenció');
    }
} else {
    $data_detencioFormat = null;
}

// Si no hay errores, crear las variables PHP y preparar la consulta PDO
$idPersona = $data['idPersona'];
$id = $data['id'];
$copia_exp = !empty($data['copia_exp']) ? $data['copia_exp'] : NULL;
$tipus_procediment = !empty($data['tipus_procediment']) ? $data['tipus_procediment'] : NULL;
$tipus_judici = !empty($data['tipus_judici']) ? $data['tipus_judici'] : NULL;
$num_causa = !empty($data['num_causa']) ? $data['num_causa'] : NULL;
$jutge_instructor = !empty($data['jutge_instructor']) ? $data['jutge_instructor'] : NULL;
$secretari_instructor = !empty($data['secretari_instructor']) ? $data['secretari_instructor'] : NULL;
$jutjat = !empty($data['jutjat']) ? $data['jutjat'] : NULL;
$any_inicial = !empty($data['any_inicial']) ? $data['any_inicial'] : NULL;
$any_final = !empty($data['any_final']) ? $data['any_final'] : NULL;
$lloc_consell_guerra = !empty($data['lloc_consell_guerra']) ? $data['lloc_consell_guerra'] : NULL;
$president_tribunal = !empty($data['president_tribunal']) ? $data['president_tribunal'] : NULL;
$defensor = !empty($data['defensor']) ? $data['defensor'] : NULL;
$fiscal = !empty($data['fiscal']) ? $data['fiscal'] : NULL;
$ponent = !empty($data['ponent']) ? $data['ponent'] : NULL;
$tribunal_vocals = !empty($data['tribunal_vocals']) ? $data['tribunal_vocals'] : NULL;
$acusacio = !empty($data['acusacio']) ? $data['acusacio'] : NULL;
$acusacio_2 = !empty($data['acusacio_2']) ? $data['acusacio_2'] : NULL;
$testimoni_acusacio = !empty($data['testimoni_acusacio']) ? $data['testimoni_acusacio'] : NULL;
$sentencia = !empty($data['sentencia']) ? $data['sentencia'] : NULL;
$pena = !empty($data['pena']) ? $data['pena'] : NULL;
$commutacio = !empty($data['commutacio']) ? $data['commutacio'] : NULL;
$observacions = !empty($data['observacions']) ? $data['observacions'] : NULL;
$anyDetingut = !empty($data['anyDetingut']) ? $data['anyDetingut'] : NULL;
$lloc_detencio = !empty($data['lloc_detencio']) ? $data['lloc_detencio'] : NULL;
$num_registre = !empty($data['num_registre']) ? $data['num_registre'] : NULL;

// Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
try {

    global $conn;
    /** @var PDO $conn */

    // Crear la consulta SQL
    $sql = "UPDATE db_processats
        SET
            idPersona = :idPersona,
            copia_exp = :copia_exp,
            tipus_procediment = :tipus_procediment,
            tipus_judici = :tipus_judici,
            num_causa = :num_causa,
            data_inici_proces = :data_inici_proces,
            jutge_instructor = :jutge_instructor,
            secretari_instructor = :secretari_instructor,
            jutjat = :jutjat,
            any_inicial = :any_inicial,
            any_final = :any_final,
            consell_guerra_data = :consell_guerra_data,
            lloc_consell_guerra = :lloc_consell_guerra,
            president_tribunal = :president_tribunal,
            defensor = :defensor,
            fiscal = :fiscal,
            ponent = :ponent,
            tribunal_vocals = :tribunal_vocals,
            acusacio = :acusacio,
            acusacio_2 = :acusacio_2,
            testimoni_acusacio = :testimoni_acusacio,
            sentencia_data = :sentencia_data,
            sentencia = :sentencia,
            pena = :pena,
            commutacio = :commutacio,
            observacions = :observacions,
            anyDetingut = :anyDetingut,
            lloc_detencio = :lloc_detencio,
            data_detencio = :data_detencio,
            num_registre = :num_registre
        WHERE id = :id;";

    // Preparar la consulta
    $stmt = $conn->prepare($sql);

    // Enlazar los parámetros con los valores de las variables PHP
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':idPersona', $idPersona, PDO::PARAM_INT);
    $stmt->bindParam(':data_detencio', $data_detencioFormat, PDO::PARAM_STR);
    $stmt->bindParam(':lloc_detencio', $lloc_detencio, PDO::PARAM_STR);
    $stmt->bindParam(':copia_exp', $copia_exp, PDO::PARAM_INT);
    $stmt->bindParam(':tipus_procediment', $tipus_procediment, PDO::PARAM_INT);
    $stmt->bindParam(':tipus_judici', $tipus_judici, PDO::PARAM_INT);
    $stmt->bindParam(':num_causa', $num_causa, PDO::PARAM_STR);
    $stmt->bindParam(':data_inici_proces', $data_inici_procesFormat, PDO::PARAM_STR);
    $stmt->bindParam(':jutge_instructor', $jutge_instructor, PDO::PARAM_STR);
    $stmt->bindParam(':secretari_instructor', $secretari_instructor, PDO::PARAM_STR);
    $stmt->bindParam(':jutjat', $jutjat, PDO::PARAM_INT);
    $stmt->bindParam(':any_inicial', $any_inicial, PDO::PARAM_STR);
    $stmt->bindParam(':any_final', $any_final, PDO::PARAM_STR);
    $stmt->bindParam(':consell_guerra_data', $consell_guerra_dataFormat, PDO::PARAM_STR);
    $stmt->bindParam(':lloc_consell_guerra', $lloc_consell_guerra, PDO::PARAM_INT);
    $stmt->bindParam(':president_tribunal', $president_tribunal, PDO::PARAM_STR);
    $stmt->bindParam(':defensor', $defensor, PDO::PARAM_STR);
    $stmt->bindParam(':fiscal', $fiscal, PDO::PARAM_STR);
    $stmt->bindParam(':ponent', $ponent, PDO::PARAM_STR);
    $stmt->bindParam(':tribunal_vocals', $tribunal_vocals, PDO::PARAM_STR);
    $stmt->bindParam(':acusacio', $acusacio, PDO::PARAM_INT);
    $stmt->bindParam(':acusacio_2', $acusacio_2, PDO::PARAM_INT);
    $stmt->bindParam(':testimoni_acusacio', $testimoni_acusacio, PDO::PARAM_STR);
    $stmt->bindParam(':sentencia_data', $sentencia_dataFormat, PDO::PARAM_STR);
    $stmt->bindParam(':sentencia', $sentencia, PDO::PARAM_INT);
    $stmt->bindParam(':pena', $pena, PDO::PARAM_INT);
    $stmt->bindParam(':commutacio', $commutacio, PDO::PARAM_STR);
    $stmt->bindParam(':observacions', $observacions, PDO::PARAM_STR);
    $stmt->bindParam(':anyDetingut', $anyDetingut, PDO::PARAM_STR);
    $stmt->bindParam(':num_registre', $num_registre, PDO::PARAM_STR);

    // Ejecutar la consulta
    $stmt->execute();

    // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis
    $detalls = "Modificació fitxa repressió processats/empresonats";
    $tipusOperacio = "UPDATE";

    Audit::registrarCanvi(
        $conn,
        $userId,                      // ID del usuario que hace el cambio
        $tipusOperacio,             // Tipus operacio
        $detalls,                       // Descripción de la operación
        Tables::DB_PROCESSATS,  // Nombre de la tabla afectada
        $id                           // ID del registro modificada
    );

    // Respuesta de éxito
    Response::success(
        MissatgesAPI::success('update'),
        ['id' => $id],
        200
    );
} catch (PDOException $e) {
    // En caso de error en la conexión o ejecución de la consulta
    Response::error(
        MissatgesAPI::error('errorBD'),
        [$e->getMessage()],
        500
    );
}
