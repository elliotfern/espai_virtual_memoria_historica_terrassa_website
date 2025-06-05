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
if (empty($data['nom'])) {
    $errors[] =  ValidacioErrors::requerit('nom');
}

if (empty($data['cognom1'])) {
    $errors[] = ValidacioErrors::requerit('primer cognom');
}

if (empty($data['categoria'])) {
    $errors[] = ValidacioErrors::requerit('categoria');
}

if (empty($data['sexe'])) {
    $errors[] = ValidacioErrors::requerit('sexe');
}

if (empty($data['tipologia_lloc_defuncio'])) {
    $errors[] = ValidacioErrors::requerit('tipologia del lloc de defunció');
}

if (empty($data['causa_defuncio'])) {
    $errors[] = ValidacioErrors::requerit('causa de defunció');
}

if (empty($data['municipi_residencia'])) {
    $errors[] = ValidacioErrors::requerit('municipi de residència');
}

if (empty($data['estat_civil'])) {
    $errors[] = ValidacioErrors::requerit('estat civil');
}

if (empty($data['estudis'])) {
    $errors[] = ValidacioErrors::requerit('estudis');
}

if (empty($data['ofici'])) {
    $errors[] = ValidacioErrors::requerit('ofici');
}

if (empty($data['filiacio_politica'])) {
    $errors[] = ValidacioErrors::requerit('filiació política');
}

if (empty($data['filiacio_sindical'])) {
    $errors[] = ValidacioErrors::requerit('filiació sindical');
}

if (empty($data['autor'])) {
    $errors[] = ValidacioErrors::requerit('autor');
}

if (empty($data['completat'])) {
    $errors[] = ValidacioErrors::requerit('completat');
}

$data_naixementRaw = $data['data_naixement'] ?? '';
if (!empty($data_naixementRaw)) {
    $data_naixementFormat = convertirDataFormatMysql($data_naixementRaw, 3);

    if (!$data_naixementFormat) {
        $errors[] = ValidacioErrors::dataNoValida('data de naixement');
    }
} else {
    $data_naixementFormat = null;
}


$data_defuncioRaw = $data['data_defuncio'] ?? '';
if (!empty($data_defuncioRaw)) {
    $data_defuncioFormat = convertirDataFormatMysql($data_defuncioRaw, 3);

    if (!$data_defuncioFormat) {
        $errors[] = ValidacioErrors::dataNoValida('data de defunció');
    }
} else {
    $data_defuncioFormat = null;
}

// Si hay errores, devolver una respuesta con los errores
if (!empty($errors)) {
    Response::error(
        MissatgesAPI::error('validacio'),
        $errors,
        400
    );
}

// Si no hay errores, crear las variables PHP y preparar la consulta PDO
$nom = $data['nom'] ?? null;
$cognom1 = $data['cognom1'] ?? null;
$cognom2 = $data['cognom2'] ?? null;
$categoria = $data['categoria'] ?? null;
$sexe = $data['sexe'] ?? null;
$municipi_naixement = $data['municipi_naixement'] ?? null;
$municipi_defuncio = $data['municipi_defuncio'] ?? null;
$tipologia_lloc_defuncio = $data['tipologia_lloc_defuncio'] ?? null;
$causa_defuncio = $data['causa_defuncio'] ?? null;
$municipi_residencia = $data['municipi_residencia'] ?? null;
$adreca = $data['adreca'] ?? null;
$estat_civil = $data['estat_civil'] ?? null;
$estudis = $data['estudis'] ?? null;
$ofici = $data['ofici'] ?? null;
$empresa = $data['empresa'] ?? null;
$sector = $data['sector'] ?? null;
$sub_sector = $data['sub_sector'] ?? null;
$carrec_empresa = $data['carrec_empresa'] ?? null;
$filiacio_politica = $data['filiacio_politica'] ?? null;
$filiacio_sindical = $data['filiacio_sindical'] ?? null;
$activitat_durant_guerra = $data['activitat_durant_guerra'] ?? null;
$observacions = $data['observacions'] ?? null;
$autor = $data['autor'] ?? null;
$completat = $data['completat'] ?? 1;
$visibilitat = $data['visibilitat'] ?? 1;

$data_actualitzacio = date('Y-m-d');
$id = $data['id'];

// Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
try {

    global $conn;
    /** @var PDO $conn */

    // Crear la consulta SQL
    $sql = "UPDATE db_dades_personals SET
        nom = :nom,
        cognom1 = :cognom1,
        cognom2 = :cognom2,
        categoria = :categoria,
        sexe = :sexe,
        data_naixement = :data_naixement,
        data_defuncio = :data_defuncio,
        municipi_naixement = :municipi_naixement,
        municipi_defuncio = :municipi_defuncio,
        tipologia_lloc_defuncio = :tipologia_lloc_defuncio,
        causa_defuncio = :causa_defuncio,
        municipi_residencia = :municipi_residencia,
        adreca = :adreca,
        estat_civil = :estat_civil,
        estudis = :estudis,
        ofici = :ofici,
        empresa = :empresa,
        sector = :sector,
        sub_sector = :sub_sector,
        carrec_empresa = :carrec_empresa,
        filiacio_politica = :filiacio_politica,
        filiacio_sindical = :filiacio_sindical,
        activitat_durant_guerra = :activitat_durant_guerra,
        observacions = :observacions,
        autor = :autor,
        data_actualitzacio = :data_actualitzacio,
        completat = :completat,
        visibilitat = :visibilitat
    WHERE id = :id";

    // Preparar la consulta
    $stmt = $conn->prepare($sql);

    // Enlazar los parámetros con los valores de las variables PHP
    $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
    $stmt->bindParam(':cognom1', $cognom1, PDO::PARAM_STR);
    $stmt->bindParam(':cognom2', $cognom2, PDO::PARAM_STR);
    $stmt->bindParam(':categoria', $categoria, PDO::PARAM_STR);
    $stmt->bindParam(':sexe', $sexe, PDO::PARAM_INT);
    $stmt->bindParam(':data_naixement', $data_naixementFormat, PDO::PARAM_STR);
    $stmt->bindParam(':data_defuncio', $data_defuncioFormat, PDO::PARAM_STR);
    $stmt->bindParam(':municipi_naixement', $municipi_naixement, PDO::PARAM_INT);
    $stmt->bindParam(':municipi_defuncio', $municipi_defuncio, PDO::PARAM_INT);
    $stmt->bindParam(':tipologia_lloc_defuncio', $tipologia_lloc_defuncio, PDO::PARAM_INT);
    $stmt->bindParam(':causa_defuncio', $causa_defuncio, PDO::PARAM_INT);
    $stmt->bindParam(':municipi_residencia', $municipi_residencia, PDO::PARAM_INT);
    $stmt->bindParam(':adreca', $adreca, PDO::PARAM_STR);
    $stmt->bindParam(':estat_civil', $estat_civil, PDO::PARAM_INT);
    $stmt->bindParam(':estudis', $estudis, PDO::PARAM_INT);
    $stmt->bindParam(':ofici', $ofici, PDO::PARAM_INT);
    $stmt->bindParam(':empresa', $empresa, PDO::PARAM_STR);
    $stmt->bindParam(':sector', $sector, PDO::PARAM_INT);
    $stmt->bindParam(':sub_sector', $sub_sector, PDO::PARAM_INT);
    $stmt->bindParam(':carrec_empresa', $carrec_empresa, PDO::PARAM_INT);
    $stmt->bindParam(':filiacio_politica', $filiacio_politica, PDO::PARAM_STR);
    $stmt->bindParam(':filiacio_sindical', $filiacio_sindical, PDO::PARAM_STR);
    $stmt->bindParam(':activitat_durant_guerra', $activitat_durant_guerra, PDO::PARAM_STR);
    $stmt->bindParam(':observacions', $observacions, PDO::PARAM_STR);
    $stmt->bindParam(':autor', $autor, PDO::PARAM_INT);
    $stmt->bindParam(':data_actualitzacio', $data_actualitzacio, PDO::PARAM_STR);
    $stmt->bindParam(':completat', $completat, PDO::PARAM_INT);
    $stmt->bindParam(':visibilitat', $visibilitat, PDO::PARAM_INT);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    // Supón que el ID a modificar lo pasas en el JSON también
    if (isset($data['id'])) {
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    }

    // Ejecutar la consulta
    $stmt->execute();

    // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis
    $detalls = "Modificació Fitxa represaliat: " . $nom . " " . $cognom1 . " " . $cognom2;
    $tipusOperacio = "UPDATE";

    Audit::registrarCanvi(
        $conn,
        $userId,                      // ID del usuario que hace el cambio
        $tipusOperacio,             // Tipus operacio
        $detalls,                       // Descripción de la operación
        Tables::DB_DADES_PERSONALS,  // Nombre de la tabla afectada
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
