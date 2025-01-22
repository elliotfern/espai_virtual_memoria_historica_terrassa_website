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

// Verificar que el método HTTP sea PUT
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
if (empty($data['nom'])) {
    $errors[] = 'El camp nom és obligatori.';
}

if (empty($data['cognom1'])) {
    $errors[] = 'El camp cognom1 és obligatori.';
}

if (empty($data['categoria'])) {
    $errors[] = 'El camp categoria és obligatori.';
}

if (empty($data['sexe'])) {
    $errors[] = 'El camp sexe és obligatori.';
}

if (empty($data['data_naixement'])) {
    $errors[] = 'El camp data_naixement és obligatori.';
}

if (empty($data['tipologia_lloc_defuncio'])) {
    $errors[] = 'El camp tipologia_lloc_defuncio és obligatori.';
}

if (empty($data['causa_defuncio'])) {
    $errors[] = 'El camp causa_defuncio és obligatori.';
}

if (empty($data['municipi_residencia'])) {
    $errors[] = 'El camp municipi_residencia és obligatori.';
}

if (empty($data['estat_civil'])) {
    $errors[] = 'El camp estat_civil és obligatori.';
}

if (empty($data['estudis'])) {
    $errors[] = 'El camp estudis és obligatori.';
}

if (empty($data['ofici'])) {
    $errors[] = 'El camp ofici és obligatori.';
}

if (empty($data['filiacio_politica'])) {
    $errors[] = 'El camp filiacio_politica és obligatori.';
}

if (empty($data['filiacio_sindical'])) {
    $errors[] = 'El camp filiacio_sindical és obligatori.';
}

if (empty($data['autor'])) {
    $errors[] = 'El camp autor és obligatori.';
}

if (empty($data['completat'])) {
    $errors[] = 'El camp completat és obligatori.';
}

// Si hay errores, devolver una respuesta con los errores
if (!empty($errors)) {
    http_response_code(400); // Bad Request
    echo json_encode(["status" => "error", "message" => "S'ha produit un error a la base de dades", "errors" => $errors]);
    exit;
}

// Si no hay errores, crear las variables PHP y preparar la consulta PDO
$nom = $data['nom'] ?? null;
$cognom1 = $data['cognom1'] ?? null;
$cognom2 = $data['cognom2'] ?? null;
$categoria = $data['categoria'] ?? null;
$sexe = $data['sexe'] ?? null;
$data_naixement = $data['data_naixement'] ?? null;
$data_defuncio = $data['data_defuncio'] ?? null;
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
$data_creacio =  date('Y-m-d');
$data_actualitzacio = date('Y-m-d');
$completat = $data['completat'] ?? 1;

// Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
try {

    global $conn;
    /** @var PDO $conn */

    // Crear la consulta SQL
    $sql = "INSERT INTO db_dades_personals (
    nom, cognom1, cognom2, categoria, sexe, data_naixement, data_defuncio,
    municipi_naixement, municipi_defuncio, tipologia_lloc_defuncio, causa_defuncio,
    municipi_residencia, adreca, estat_civil, estudis, ofici, empresa, sector,
    sub_sector, carrec_empresa, filiacio_politica, filiacio_sindical, activitat_durant_guerra,
    observacions, autor, data_creacio, data_actualitzacio, completat
    ) VALUES (
    :nom, :cognom1, :cognom2, :categoria, :sexe, :data_naixement, :data_defuncio,
    :municipi_naixement, :municipi_defuncio, :tipologia_lloc_defuncio, :causa_defuncio,
    :municipi_residencia, :adreca, :estat_civil, :estudis, :ofici, :empresa, :sector,
    :sub_sector, :carrec_empresa, :filiacio_politica, :filiacio_sindical, :activitat_durant_guerra,
    :observacions, :autor, :data_creacio, :data_actualitzacio, :completat
    )";

    // Preparar la consulta
    $stmt = $conn->prepare($sql);

    // Enlazar los parámetros con los valores de las variables PHP
    $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
    $stmt->bindParam(':cognom1', $cognom1, PDO::PARAM_STR);
    $stmt->bindParam(':cognom2', $cognom2, PDO::PARAM_STR);
    $stmt->bindParam(':categoria', $categoria, PDO::PARAM_STR);
    $stmt->bindParam(':sexe', $sexe, PDO::PARAM_INT);
    $stmt->bindParam(':data_naixement', $data_naixement, PDO::PARAM_STR);
    $stmt->bindParam(':data_defuncio', $data_defuncio, PDO::PARAM_STR);
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
    $stmt->bindParam(':data_creacio', $data_creacio, PDO::PARAM_STR);
    $stmt->bindParam(':data_actualitzacio', $data_actualitzacio, PDO::PARAM_STR);
    $stmt->bindParam(':completat', $comnpletat, PDO::PARAM_INT);

    // Ejecutar la consulta
    $stmt->execute();

    // Recuperar el ID del registro creado
    $lastInsertId = $conn->lastInsertId();

    // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

    $dataHoraCanvi = date('Y-m-d H:i:s');
    $tipusOperacio = "Insert Fitxa persona";
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
    echo json_encode(["status" => "success", "message" => "Les dades s'han desat correctament a la base de dades."]);
} catch (PDOException $e) {
    // En caso de error en la conexión o ejecución de la consulta
    http_response_code(500); // Internal Server Error
    echo json_encode(["status" => "error", "message" => "S'ha produit un error a la base de dades: " . $e->getMessage()]);
}
