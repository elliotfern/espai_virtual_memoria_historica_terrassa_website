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
$data_exili = !empty($data['data_exili']) ? $data['data_exili'] : NULL;
$lloc_partida = !empty($data['lloc_partida']) ? $data['lloc_partida'] : NULL;
$lloc_pas_frontera = !empty($data['lloc_pas_frontera']) ? $data['lloc_pas_frontera'] : NULL;
$amb_qui_passa_frontera = !empty($data['amb_qui_passa_frontera']) ? $data['amb_qui_passa_frontera'] : NULL;
$primer_desti_exili = !empty($data['primer_desti_exili']) ? $data['primer_desti_exili'] : NULL;
$primer_desti_data = !empty($data['primer_desti_data']) ? $data['primer_desti_data'] : NULL;
$tipologia_primer_desti = !empty($data['tipologia_primer_desti']) ? $data['tipologia_primer_desti'] : NULL;
$dades_lloc_primer_desti = !empty($data['dades_lloc_primer_desti']) ? $data['dades_lloc_primer_desti'] : NULL;
$periple_recorregut = !empty($data['periple_recorregut']) ? $data['periple_recorregut'] : NULL;
$deportat = !empty($data['deportat']) ? $data['deportat'] : NULL;
$ultim_desti_exili = !empty($data['ultim_desti_exili']) ? $data['ultim_desti_exili'] : NULL;
$tipologia_ultim_desti = !empty($data['tipologia_ultim_desti']) ? $data['tipologia_ultim_desti'] : NULL;
$participacio_resistencia = !empty($data['participacio_resistencia']) ? $data['participacio_resistencia'] : NULL;
$dades_resistencia = !empty($data['dades_resistencia']) ? $data['dades_resistencia'] : NULL;
$activitat_politica_exili = !empty($data['activitat_politica_exili']) ? $data['activitat_politica_exili'] : NULL;
$activitat_sindical_exili = !empty($data['activitat_sindical_exili']) ? $data['activitat_sindical_exili'] : NULL;
$situacio_legal_espanya = !empty($data['situacio_legal_espanya']) ? $data['situacio_legal_espanya'] : NULL;
$idPersona = !empty($data['idPersona']) ? $data['idPersona'] : NULL;

// Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
try {

    global $conn;
    /** @var PDO $conn */

    // Crear la consulta SQL
    $sql = "UPDATE db_exiliats SET 
            data_exili = :data_exili,
            lloc_partida = :lloc_partida,
            lloc_pas_frontera = :lloc_pas_frontera,
            amb_qui_passa_frontera = :amb_qui_passa_frontera,
            primer_desti_exili = :primer_desti_exili,
            primer_desti_data = :primer_desti_data,
            tipologia_primer_desti = :tipologia_primer_desti,
            dades_lloc_primer_desti = :dades_lloc_primer_desti,
            periple_recorregut = :periple_recorregut,
            deportat = :deportat,
            ultim_desti_exili = :ultim_desti_exili,
            tipologia_ultim_desti = :tipologia_ultim_desti,
            participacio_resistencia = :participacio_resistencia,
            dades_resistencia = :dades_resistencia,
            activitat_politica_exili = :activitat_politica_exili,
            activitat_sindical_exili = :activitat_sindical_exili,
            situacio_legal_espanya = :situacio_legal_espanya
        WHERE idPersona = :idPersona";

    // Preparar la consulta
    $stmt = $conn->prepare($sql);

    // Enlazar los parámetros con los valores de las variables PHP
    $stmt->bindParam(':data_exili', $data_exili, PDO::PARAM_STR);
    $stmt->bindParam(':lloc_partida', $lloc_partida, PDO::PARAM_STR);
    $stmt->bindParam(':lloc_pas_frontera', $lloc_pas_frontera, PDO::PARAM_INT);
    $stmt->bindParam(':amb_qui_passa_frontera', $amb_qui_passa_frontera, PDO::PARAM_STR);
    $stmt->bindParam(':primer_desti_exili', $primer_desti_exili, PDO::PARAM_INT);
    $stmt->bindParam(':primer_desti_data', $primer_desti_data, PDO::PARAM_STR);
    $stmt->bindParam(':tipologia_primer_desti', $tipologia_primer_desti, PDO::PARAM_INT);
    $stmt->bindParam(':dades_lloc_primer_desti', $dades_lloc_primer_desti, PDO::PARAM_STR);
    $stmt->bindParam(':periple_recorregut', $periple_recorregut, PDO::PARAM_STR);
    $stmt->bindParam(':deportat', $deportat, PDO::PARAM_STR);
    $stmt->bindParam(':ultim_desti_exili', $ultim_desti_exili, PDO::PARAM_INT);
    $stmt->bindParam(':tipologia_ultim_desti', $tipologia_ultim_desti, PDO::PARAM_INT);
    $stmt->bindParam(':participacio_resistencia', $participacio_resistencia, PDO::PARAM_INT);
    $stmt->bindParam(':dades_resistencia', $dades_resistencia, PDO::PARAM_STR);
    $stmt->bindParam(':activitat_politica_exili', $activitat_politica_exili, PDO::PARAM_STR);
    $stmt->bindParam(':activitat_sindical_exili', $activitat_sindical_exili, PDO::PARAM_STR);
    $stmt->bindParam(':situacio_legal_espanya', $situacio_legal_espanya, PDO::PARAM_STR);
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
    $tipusOperacio = "Update Dades exiliats";
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
