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
header("Access-Control-Allow-Methods: POST");

// Definir el dominio permitido
$allowedOrigin = DOMAIN;

// Llamar a la función para verificar el referer
checkReferer($allowedOrigin);

// Verificar que el método de la solicitud sea GET
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
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

$idPersona = $data['idPersona'];

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
$copia_exp = !empty($data['copia_exp']) ? $data['copia_exp'] : NULL;
$tipus_procediment = !empty($data['tipus_procediment']) ? $data['tipus_procediment'] : NULL;
$tipus_judici = !empty($data['tipus_judici']) ? $data['tipus_judici'] : NULL;
$num_causa = !empty($data['num_causa']) ? $data['num_causa'] : NULL;
$secretari_instructor = !empty($data['secretari_instructor']) ? $data['secretari_instructor'] : NULL;
$jutjat = !empty($data['jutjat']) ? $data['jutjat'] : NULL;
$any_inicial = !empty($data['any_inicial']) ? $data['any_inicial'] : NULL;
$any_final = !empty($data['any_final']) ? $data['any_final'] : NULL;
$lloc_consell_guerra = !empty($data['lloc_consell_guerra']) ? $data['lloc_consell_guerra'] : NULL;
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
    $sql = "INSERT INTO db_processats (
    idPersona, tipus_procediment, tipus_judici, num_causa,
    data_inici_proces, secretari_instructor, jutjat, any_inicial,
    any_final, consell_guerra_data, lloc_consell_guerra, ponent, tribunal_vocals, acusacio, acusacio_2, testimoni_acusacio,
    sentencia_data, sentencia, pena, commutacio, observacions, anyDetingut, data_detencio, lloc_detencio, num_registre, copia_exp
        ) VALUES (
    :idPersona, :tipus_procediment, :tipus_judici, :num_causa,
    :data_inici_proces, :secretari_instructor, :jutjat, :any_inicial,
    :any_final, :consell_guerra_data, :lloc_consell_guerra, :ponent, :tribunal_vocals, :acusacio, :acusacio_2, :testimoni_acusacio,
    :sentencia_data, :sentencia, :pena, :commutacio, :observacions, :anyDetingut, :data_detencio, :lloc_detencio, :num_registre, :copia_exp
)";

    // Preparar la consulta
    $stmt = $conn->prepare($sql);

    // Enlazar los parámetros con los valores de las variables PHP
    $stmt->bindParam(':idPersona', $idPersona, PDO::PARAM_INT);
    $stmt->bindParam(':data_detencio', $data_detencioFormat, PDO::PARAM_STR);
    $stmt->bindParam(':lloc_detencio', $lloc_detencio, PDO::PARAM_STR);
    $stmt->bindParam(':tipus_procediment', $tipus_procediment, PDO::PARAM_INT);
    $stmt->bindParam(':tipus_judici', $tipus_judici, PDO::PARAM_INT);
    $stmt->bindParam(':num_causa', $num_causa, PDO::PARAM_STR);
    $stmt->bindParam(':data_inici_proces', $data_inici_procesFormat, PDO::PARAM_STR);
    $stmt->bindParam(':secretari_instructor', $secretari_instructor, PDO::PARAM_STR);
    $stmt->bindParam(':jutjat', $jutjat, PDO::PARAM_INT);
    $stmt->bindParam(':any_inicial', $any_inicial, PDO::PARAM_STR);
    $stmt->bindParam(':any_final', $any_final, PDO::PARAM_STR);
    $stmt->bindParam(':consell_guerra_data', $consell_guerra_dataFormat, PDO::PARAM_STR);
    $stmt->bindParam(':lloc_consell_guerra', $lloc_consell_guerra, PDO::PARAM_INT);
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
    $stmt->bindParam(':copia_exp', $copia_exp, PDO::PARAM_INT);

    // Ejecutar la consulta
    $stmt->execute();

    // Recuperar el ID del registro creado
    $id = $conn->lastInsertId();

    $sqlJutges = "INSERT INTO db_processats_jutges_instructors 
        (processat_id, jutge_id) 
        VALUES (:processat_id, :jutge_id)";

    $stmtJutges = $conn->prepare($sqlJutges);

    if (!empty($data['jutges_instructors']) && is_array($data['jutges_instructors'])) {
        foreach ($data['jutges_instructors'] as $jutgeId) {
            $stmtJutges->execute([
                ':processat_id' => $id,
                ':jutge_id' => $jutgeId
            ]);
        }
    }

    if (!empty($data['secretaris_instructors']) && is_array($data['secretaris_instructors'])) {

        $sqlInsertSecretaris = "
        INSERT INTO db_processats_secretaris_instructors
        (processat_id, secretari_id)
        VALUES (:processat_id, :secretari_id)
        ";

        $stmtInsSec = $conn->prepare($sqlInsertSecretaris);

        foreach ($data['secretaris_instructors'] as $secretariId) {
            $stmtInsSec->execute([
                ':processat_id' => $id,
                ':secretari_id' => $secretariId
            ]);
        }
    }

    if (!empty($data['presidents_tribunals']) && is_array($data['presidents_tribunals'])) {

        $sqlInsert = "
        INSERT INTO db_processats_presidents_tribunal
        (processat_id, president_id)
        VALUES (:processat_id, :president_id)
        ";

        $stmtIns = $conn->prepare($sqlInsert);

        foreach ($data['presidents_tribunals'] as $presidentId) {
            $stmtIns->execute([
                ':processat_id' => $id,
                ':president_id' => $presidentId
            ]);
        }
    }

    if (!empty($data['defensors']) && is_array($data['defensors'])) {

        $sql = "INSERT INTO db_processats_defensors (processat_id, defensor_id)
            VALUES (:processat_id, :defensor_id)";

        $stmt = $conn->prepare($sql);

        foreach ($data['defensors'] as $idDefensor) {
            $stmt->execute([
                ':processat_id' => $id,
                ':defensor_id' => $idDefensor
            ]);
        }
    }

    if (!empty($data['fiscals']) && is_array($data['fiscals'])) {

        $sqlInsert = "
        INSERT INTO db_processats_fiscals (processat_id, fiscal_id)
        VALUES (:processat_id, :fiscal_id)
    ";

        $stmtIns = $conn->prepare($sqlInsert);

        foreach ($data['fiscals'] as $fiscalId) {
            $stmtIns->execute([
                ':processat_id' => $id,
                ':fiscal_id' => $fiscalId
            ]);
        }
    }

    // PONENTS
    if (!empty($data['ponents']) && is_array($data['ponents'])) {

        $sqlInsert = "INSERT INTO db_processats_ponents 
    (processat_id, ponent_id)
    VALUES (:processat_id, :ponent_id)";

        $stmtIns = $conn->prepare($sqlInsert);

        foreach ($data['ponents'] as $ponentId) {
            $stmtIns->execute([
                ':processat_id' => $id,
                ':ponent_id' => $ponentId
            ]);
        }
    }

    // VOCALS DE TRIBUNALS
    if (!empty($data['tribunals_vocals']) && is_array($data['tribunals_vocals'])) {

        $sqlInsert = "INSERT INTO db_processats_tribunal_vocals 
    (processat_id, vocal_id)
    VALUES (:processat_id, :vocal_id)";

        $stmtIns = $conn->prepare($sqlInsert);

        foreach ($data['tribunals_vocals'] as $vocalId) {
            $stmtIns->execute([
                ':processat_id' => $id,
                ':vocal_id' => $vocalId
            ]);
        }
    }

    // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis
    $detalls = "Creació fitxa repressió processats/empresonats";
    $tipusOperacio = "INSERT";

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
        MissatgesAPI::success('create'),
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
