<?php

use App\Config\Tables;
use App\Config\Audit;
use App\Config\DatabaseConnection;

$conn = DatabaseConnection::getConnection();

if (!$conn) {
    die("No se pudo establecer conexión a la base de datos.");
}


$slug = $routeParams[0];

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

// DB_FONTS DOCUMENTALS
// 1) PUT ref_bibliografica > serveix per desar una referencia bibliografica de fitxa
// Ruta PUT => "/api/fonts_documentals/put/ref_bibliografica"
if ($slug === 'ref_bibliografica') {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['llibre'])) {
        $errors[] = 'El camp llibre és obligatori.';
    }

    // Si hay errores, devolver una respuesta con los errores
    if (!empty($errors)) {
        http_response_code(400); // Bad Request
        echo json_encode(["status" => "error", "message" => "S'han produït errors en la validació", "errors" => $errors]);
        exit;
    }

    // Si no hay errores, crear las variables PHP y preparar la consulta PDO
    $llibre = !empty($data['llibre']) ? $data['llibre'] : NULL;
    $idRepresaliat = !empty($data['idRepresaliat']) ? $data['idRepresaliat'] : NULL;
    $pagina = !empty($data['pagina']) ? $data['pagina'] : NULL;
    $id = !empty($data['id']) ? $data['id'] : NULL;

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "UPDATE aux_bibliografia_llibres
        SET 
            llibre = :llibre,
            idRepresaliat = :idRepresaliat,
            pagina = :pagina
        WHERE id = :id";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':llibre', $llibre, PDO::PARAM_INT);
        $stmt->bindParam(':idRepresaliat', $idRepresaliat, PDO::PARAM_INT);
        $stmt->bindParam(':pagina', $pagina, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis
        $detalls = "Modificació referència bibliogràfica";
        $tipusOperacio = "UPDATE";

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_BIBLIOGRAFIA_LLIBRES,  // Nombre de la tabla afectada
            $id                           // ID del registro modificada
        );

        // Respuesta de éxito
        echo json_encode(["status" => "success", "message" => "Les dades s'han actualitzat correctament a la base de dades."]);
    } catch (PDOException $e) {
        // En caso de error en la conexión o ejecución de la consulta
        http_response_code(500); // Internal Server Error
        echo json_encode(["status" => "error", "message" => "S'ha produit un error a la base de dades: "]);
    }

    // 3) PUT ref_arxivistica fitxa represaliat
    // ruta PUT => "/api/fonts_documentals/post/ref_arxivistica"
} else if ($slug === 'ref_arxivistica') {

    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['referencia'])) {
        $errors[] = 'El camp referencia és obligatori.';
    }

    // Si hay errores, devolver una respuesta con los errores
    if (!empty($errors)) {
        http_response_code(400); // Bad Request
        echo json_encode(["status" => "error", "message" => "S'han produït errors en la validació", "errors" => $errors]);
        exit;
    }

    // Si no hay errores, crear las variables PHP y preparar la consulta PDO
    $referencia = !empty($data['referencia']) ? $data['referencia'] : NULL;
    $idRepresaliat = !empty($data['idRepresaliat']) ? $data['idRepresaliat'] : NULL;
    $codi = !empty($data['codi']) ? $data['codi'] : NULL;
    $id  = !empty($data['id']) ? $data['id'] : NULL;

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "UPDATE aux_bibliografia_arxius SET
            referencia = :referencia,
            codi = :codi,
            idRepresaliat = :idRepresaliat
        WHERE id = :id";


        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':referencia', $referencia, PDO::PARAM_STR);
        $stmt->bindParam(':codi', $codi, PDO::PARAM_INT);
        $stmt->bindParam(':idRepresaliat', $idRepresaliat, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis
        $detalls = "Modificació referència arxivística";
        $tipusOperacio = "UPDATE";

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_BIBLIOGRAFIA_ARXIUS,  // Nombre de la tabla afectada
            $id                           // ID del registro modificada
        );

        // Respuesta de éxito
        echo json_encode(["status" => "success", "message" => "Les dades s'han actualitzat correctament a la base de dades."]);
    } catch (PDOException $e) {
        // En caso de error en la conexión o ejecución de la consulta
        http_response_code(500); // Internal Server Error
        echo json_encode(["status" => "error", "message" => "S'ha produit un error a la base de dades"]);
    }
    // 4) PUT modificació llibre
    // ruta POST => "/api/fonts_documentals/put/llibre"
} else if ($slug === 'llibre') {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['llibre'])) {
        $errors[] = 'El camp llibre és obligatori.';
    }

    if (empty($data['autor'])) {
        $errors[] = 'El camp autor és obligatori.';
    }

    // Si hay errores, devolver una respuesta con los errores
    if (!empty($errors)) {
        http_response_code(400); // Bad Request
        echo json_encode(["status" => "error", "message" => "S'han produït errors en la validació", "errors" => $errors]);
        exit;
    }

    // Si no hay errores, crear las variables PHP y preparar la consulta PDO
    $llibre = !empty($data['llibre']) ? $data['llibre'] : NULL;
    $autor = !empty($data['autor']) ? $data['autor'] : NULL;
    $editorial = !empty($data['editorial']) ? $data['editorial'] : NULL;
    $ciutat = !empty($data['ciutat']) ? $data['ciutat'] : NULL;
    $any = !empty($data['any']) ? $data['any'] : NULL;
    $volum = !empty($data['volum']) ? $data['volum'] : NULL;
    $id = !empty($data['id']) ? $data['id'] : NULL;

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "UPDATE aux_bibliografia_llibre_detalls SET
            llibre = :llibre,
            autor = :autor,
            editorial = :editorial,
            ciutat = :ciutat,
            any = :any,
            volum = :volum
        WHERE id = :id";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':llibre', $llibre, PDO::PARAM_STR);
        $stmt->bindParam(':autor', $autor, PDO::PARAM_STR);
        $stmt->bindParam(':editorial', $editorial, PDO::PARAM_STR);
        $stmt->bindParam(':ciutat', $ciutat, PDO::PARAM_INT);
        $stmt->bindParam(':any', $any, PDO::PARAM_STR);
        $stmt->bindParam(':volum', $volum, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis
        $detalls = "Modificació llibre: " . $llibre;
        $tipusOperacio = "UPDATE";

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_BIBLIOGRAFIA_LLIBRE_DETALLS,  // Nombre de la tabla afectada
            $id                           // ID del registro modificada
        );

        // Respuesta de éxito
        echo json_encode(["status" => "success", "message" => "Les dades s'han actualitzat correctament a la base de dades."]);
    } catch (PDOException $e) {
        // En caso de error en la conexión o ejecución de la consulta
        http_response_code(500); // Internal Server Error
        echo json_encode(["status" => "error", "message" => "S'ha produit un error a la base de dades"]);
    }

    // 4) PUT modificació arxiu
    // ruta POST => "/api/fonts_documentals/put/arxiu"
} else if ($slug === 'arxiu') {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['arxiu'])) {
        $errors[] = 'El camp arxiu és obligatori.';
    }

    if (empty($data['codi'])) {
        $errors[] = 'El camp codi és obligatori.';
    }

    // Si hay errores, devolver una respuesta con los errores
    if (!empty($errors)) {
        http_response_code(400); // Bad Request
        echo json_encode(["status" => "error", "message" => "S'han produït errors en la validació", "errors" => $errors]);
        exit;
    }

    // Si no hay errores, crear las variables PHP y preparar la consulta PDO
    $arxiu = $data['llibre'];
    $descripcio = !empty($data['descripcio']) ? $data['descripcio'] : NULL;
    $codi = $data['codi'];
    $ciutat = !empty($data['ciutat']) ? $data['ciutat'] : NULL;
    $web = !empty($data['web']) ? $data['web'] : NULL;
    $id = $data['id'];

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "UPDATE aux_bibliografia_arxius_codis SET
        arxiu = :arxiu,
        descripcio = :descripcio,
        codi = ;codi,
        web = :web,
        ciutat = :ciutat
        WHERE id = :id";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':arxiu', $arxiu, PDO::PARAM_STR);
        $stmt->bindParam(':descripcio', $descripcio, PDO::PARAM_STR);
        $stmt->bindParam(':codi', $codi, PDO::PARAM_STR);
        $stmt->bindParam(':ciutat', $ciutat, PDO::PARAM_INT);
        $stmt->bindParam(':web', $web, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis
        $detalls = "Modificació arxiu: " . $arxiu;
        $tipusOperacio = "UPDATE";

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_BIBLIOGRAFIA_ARXIUS_CODIS,  // Nombre de la tabla afectada
            $id                           // ID del registro modificada
        );

        // Respuesta de éxito
        echo json_encode(["status" => "success", "message" => "Les dades s'han actualitzat correctament a la base de dades."]);
    } catch (PDOException $e) {
        // En caso de error en la conexión o ejecución de la consulta
        http_response_code(500); // Internal Server Error
        echo json_encode(["status" => "error", "message" => "S'ha produit un error a la base de dades"]);
    }
}
