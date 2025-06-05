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
if (empty($data['id'])) {
    $errors[] = 'El camp id és obligatori.';
}

if (empty($data['nom'])) {
    $errors[] = 'El camp nom és obligatori.';
}

if (empty($data['cognom1'])) {
    $errors[] = 'El camp cognom1 és obligatori.';
}

if (empty($data['relacio_parentiu'])) {
    $errors[] = 'El camp relacio_parentiu és obligatori.';
}

if (empty($data['idParent'])) {
    $errors[] = 'El camp idParent és obligatori.';
}

// Si hay errores, devolver una respuesta con los errores
if (!empty($errors)) {
    http_response_code(400); // Bad Request
    echo json_encode(["status" => "error", "message" => "S'han produït errors en la validació", "errors" => $errors]);
    exit;
}

// Si no hay errores, crear las variables PHP y preparar la consulta PDO
$id = !empty($data['id']) ? $data['id'] : NULL;
$nom = !empty($data['nom']) ? $data['nom'] : NULL;
$cognom1 = !empty($data['cognom1']) ? $data['cognom1'] : NULL;
$cognom2 = !empty($data['cognom2']) ? $data['cognom2'] : NULL;
$anyNaixement = !empty($data['anyNaixement']) ? $data['anyNaixement'] : NULL;
$relacio_parentiu = !empty($data['relacio_parentiu']) ? $data['relacio_parentiu'] : NULL;
$idParent = !empty($data['idParent']) ? $data['idParent'] : NULL;

// Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
try {

    global $conn;
    /** @var PDO $conn */

    // Crear la consulta SQL
    $sql = "UPDATE aux_familiars SET 
        nom = :nom,
        cognom1 = :cognom1,
        cognom2 = :cognom2,
        anyNaixement = :anyNaixement,
        relacio_parentiu = :relacio_parentiu,
        idParent = :idParent
        WHERE id = :id";

    // Preparar la consulta
    $stmt = $conn->prepare($sql);

    // Enlazar los parámetros con los valores de las variables PHP
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
    $stmt->bindParam(':cognom1', $cognom1, PDO::PARAM_STR);
    $stmt->bindParam(':cognom2', $cognom2, PDO::PARAM_STR);
    $stmt->bindParam(':anyNaixement', $anyNaixement, PDO::PARAM_STR);
    $stmt->bindParam(':relacio_parentiu', $relacio_parentiu, PDO::PARAM_INT);
    $stmt->bindParam(':idParent', $idParent, PDO::PARAM_INT);

    // Supón que el ID a modificar lo pasas en el JSON también
    if (isset($data['idPersona'])) {
        $stmt->bindParam(':idPersona', $data['idPersona'], PDO::PARAM_INT);
    }

    // Ejecutar la consulta
    $stmt->execute();

    $nomComplet = $nom . " " . $cognom1 . " " . $cognom2;

    // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis
    $tipusOperacio = "UPDATE";
    $detalls = "Modificació de familiar: " . $nomComplet;

    Audit::registrarCanvi(
        $conn,
        $userId,                      // ID del usuario que hace el cambio
        $tipusOperacio,             // Tipus operacio
        $detalls,                       // Descripción de la operación
        Tables::AUX_FAMILIARS,  // Nombre de la tabla afectada
        $id                           // ID del registro modificada
    );

    // Respuesta de éxito
    echo json_encode(["status" => "success", "message" => "Les dades s'han actualitzat correctament a la base de dades."]);
} catch (PDOException $e) {
    // En caso de error en la conexión o ejecución de la consulta
    http_response_code(500); // Internal Server Error
    echo json_encode(["status" => "error", "message" => "S'ha produit un error a la base de dades: " . $e->getMessage()]);
}
