<?php

use App\Config\DatabaseConnection;

$conn = DatabaseConnection::getConnection();

if (!$conn) {
    die("No se pudo establecer conexión a la base de datos.");
}

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
    echo json_encode(["error" => "Método no permitido. Se requiere PUT."]);
    exit;
}


// DB_FONTS DOCUMENTALS
// 1) POST esdeveniment
// ruta POST => "/api/cronologia/post/
$inputData = file_get_contents('php://input');
$data = json_decode($inputData, true);

// Inicializar un array para los errores
$errors = [];

// Validación de los datos recibidos
if (empty($data['any'])) {
    $errors[] = 'El camp any és obligatori.';
}

if (empty($data['mes'])) {
    $errors[] = 'El camp mes és obligatori.';
}

if (empty($data['area'])) {
    $errors[] = 'El camp area inici és obligatori.';
}

if (empty($data['tema'])) {
    $errors[] = 'El camp tema inici és obligatori.';
}

if (empty($data['textCa'])) {
    $errors[] = 'El camp text inici és obligatori.';
}

// Si hay errores, devolver una respuesta con los errores
if (!empty($errors)) {
    http_response_code(400); // Bad Request
    echo json_encode(["status" => "error", "message" => "S'han produït errors en la validació", "errors" => $errors]);
    exit;
}

// Si no hay errores, crear las variables PHP y preparar la consulta PDO
$any = !empty($data['any']) ? $data['any'] : NULL;
$mes = !empty($data['mes']) ? $data['mes'] : NULL;
$diaInici = !empty($data['diaInici']) ? $data['diaInici'] : NULL;
$diaFi = !empty($data['diaFi']) ? $data['diaFi'] : NULL;
$mesFi = !empty($data['mesFi']) ? $data['mesFi'] : NULL;
$area = !empty($data['area']) ? $data['area'] : NULL;
$tema = !empty($data['tema']) ? $data['tema'] : NULL;
$textCa = !empty($data['textCa']) ? $data['textCa'] : NULL;

// Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
try {

    global $conn;
    /** @var PDO $conn */

    // Crear la consulta SQL
    $sql = "INSERT INTO db_cronologia (
            any, mes, diaInici, diaFi, mesFi, area, tema, textCa
        ) VALUES (
            :any, :mes, :diaInici, :diaFi, :mesFi, :area, :tema, :textCa
        )";

    // Preparar la consulta
    $stmt = $conn->prepare($sql);

    // Enlazar los parámetros con los valores de las variables PHP
    $stmt->bindParam(':any', $any, PDO::PARAM_INT);
    $stmt->bindParam(':mes', $mes, PDO::PARAM_INT);
    $stmt->bindParam(':diaInici', $diaInici, PDO::PARAM_INT);
    $stmt->bindParam(':diaFi', $diaFi, PDO::PARAM_INT);
    $stmt->bindParam(':mesFi', $mesFi, PDO::PARAM_INT);
    $stmt->bindParam(':area', $area, PDO::PARAM_STR);
    $stmt->bindParam(':tema', $tema, PDO::PARAM_STR);
    $stmt->bindParam(':textCa', $textCa, PDO::PARAM_STR);

    // Ejecutar la consulta
    $stmt->execute();


    // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

    $dataHoraCanvi = date('Y-m-d H:i:s');
    $tipusOperacio = "Insert Nou esdeveniment cronologia";
    $idUser = $data['userId'] ?? null;
    $lastInsertId = NULL;

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
    echo json_encode(["status" => "error", "message" => "S'ha produit un error 1 a la base de dades: "]);
}
