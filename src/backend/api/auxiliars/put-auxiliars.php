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

$slug = $routeParams[0];
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

// 1) PUT municipi
// ruta PUT => "/api/auxiliars/put/municipi"
if ($slug === "municipi") {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['ciutat'])) {
        $errors[] =  ValidacioErrors::requerit('ciutat');
    }

    if (empty($data['estat'])) {
        $errors[] =  ValidacioErrors::requerit('estat');
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
    $ciutat = $data['ciutat'];
    $ciutat_ca = !empty($data['ciutat_ca']) ? $data['ciutat_ca'] : NULL;
    $comarca = !empty($data['comarca']) ? $data['comarca'] : NULL;
    $provincia = !empty($data['provincia']) ? $data['provincia'] : NULL;
    $comunitat = !empty($data['comunitat']) ? $data['comunitat'] : NULL;
    $estat = !empty($data['estat']) ? $data['estat'] : NULL;
    $id = !empty($data['id']) ? $data['id'] : NULL;


    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "UPDATE aux_dades_municipis SET
            ciutat = :ciutat,
            ciutat_ca = :ciutat_ca,
            comarca = :comarca,
            provincia = :provincia,
            comunitat = :comunitat,
            estat = :estat
        WHERE id = :id";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':ciutat', $ciutat, PDO::PARAM_STR);
        $stmt->bindParam(':ciutat_ca', $ciutat_ca, PDO::PARAM_STR);
        $stmt->bindParam(':comarca', $comarca, PDO::PARAM_INT);
        $stmt->bindParam(':provincia', $provincia, PDO::PARAM_INT);
        $stmt->bindParam(':comunitat', $comunitat, PDO::PARAM_INT);
        $stmt->bindParam(':estat', $estat, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis
        $detalls = "Modificació municipi: " . $ciutat;
        $tipusOperacio = "UPDATE";

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_DADES_MUNICIPIS,  // Nombre de la tabla afectada
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

    // 3) PUT: Update Usuari
    // RUTA PUT => "/api/auxiliars/put/usuari"
} else if ($slug === "usuari") {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Verificar si se recibieron datos
    if ($data === null) {
        // Error al decodificar JSON
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['error' => 'Error decoding JSON data']);
        exit();
    }

    // Ahora puedes acceder a los datos como un array asociativo
    $hasError = false; // Inicializamos la variable $hasError como false

    $nom               = !empty($data['nom']) ? data_input($data['nom']) : ($hasError = true);
    $email        = !empty($data['email']) ? data_input($data['email']) : ($hasError = true);
    $biografia_cat         = !empty($data['biografia_cat']) ? data_input($data['biografia_cat']) : ($hasError = false);
    $user_type          = !empty($data['user_type']) ? data_input($data['user_type']) : ($hasError = true);
    $avatar            = !empty($data['avatar']) ? data_input($data['avatar']) : ($hasError = false);
    $id                  = !empty($data['id']) ? data_input($data['id']) : ($hasError = true);

    // Si hay algún error de validación
    if ($hasError) {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['status' => 'error', 'message' => 'Falten dades obligatòries']);
        exit();
    }

    global $conn;
    /** @var PDO $conn */

    // Construcción dinámica del query dependiendo de si se actualiza la contraseña o no
    $query = "UPDATE auth_users SET nom = :nom, email = :email, biografia_cat = :biografia_cat, user_type = :user_type, avatar = :avatar";
    $params = [
        ':nom' => $nom,
        ':email' => $email,
        ':biografia_cat' => $biografia_cat,
        ':user_type' => $user_type,
        ':avatar' => $avatar,
    ];

    // Si el password viene lleno, lo incluimos
    if (!empty($data['password'])) {
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT, ['cost' => 10]);
        $query .= ", password = :password";
        $params[':password'] = $hashedPassword;
    }

    $query .= " WHERE id = :id";
    $params[':id'] = $id;

    try {
        $stmt = $conn->prepare($query);
        $stmt->execute($params);

        echo json_encode(['status' => 'success', 'message' => 'Usuari actualitzat correctament']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error en l\'actualització de les dades.']);
    }

    // 4) PUT Partit politic
    // ruta PUT => "/api/auxiliars/put/partitPolitic"
} else if ($slug === "partitPolitic") {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['partit_politic'])) {
        $errors[] =  ValidacioErrors::requerit('partit polític');
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
    $partit_politic = !empty($data['partit_politic']) ? $data['partit_politic'] : NULL;
    $sigles = !empty($data['sigles']) ? $data['sigles'] : NULL;
    $id = !empty($data['id']) ? $data['id'] : NULL;


    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "UPDATE aux_filiacio_politica SET
            partit_politic = :partit_politic,
            sigles = :sigles
        WHERE id = :id";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':partit_politic', $partit_politic, PDO::PARAM_STR);
        $stmt->bindParam(':sigles', $sigles, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis
        $detalls = "Modificació partit polític: " . $partit_politic;
        $tipusOperacio = "UPDATE";

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_FILIACIO_POLITICA,  // Nombre de la tabla afectada
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

    // 5) PUT Sindicat
    // ruta PUT => "/api/auxiliars/put/sindicat"
} else if ($slug === "sindicat") {

    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['sindicat'])) {
        $errors[] =  ValidacioErrors::requerit('sindicat');
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
    $sindicat = !empty($data['sindicat']) ? $data['sindicat'] : NULL;
    $sigles = !empty($data['sigles']) ? $data['sigles'] : NULL;
    $id = !empty($data['id']) ? $data['id'] : NULL;

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "UPDATE aux_filiacio_sindical SET
            sindicat = :sindicat,
            sigles = :sigles
        WHERE id = :id";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':sindicat', $sindicat, PDO::PARAM_STR);
        $stmt->bindParam(':sigles', $sigles, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis
        $detalls = "Modificació sindicat: " . $sindicat;
        $tipusOperacio = "UPDATE";

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_FILIACIO_SINDICAL,  // Nombre de la tabla afectada
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

    // 6) PUT Comarca
    // ruta PUT => "/api/auxiliars/put/comarca"
} else if ($slug === "comarca") {

    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['comarca'])) {
        $errors[] =  ValidacioErrors::requerit('comarca');
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
    $comarca = $data['comarca'];
    $comarca_ca = !empty($data['comarca_ca']) ? $data['comarca_ca'] : NULL;
    $id = $data['id'];

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "UPDATE aux_dades_municipis_comarca SET
            comarca = :comarca,
            comarca_ca = :comarca_ca
        WHERE id = :id";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':comarca', $comarca, PDO::PARAM_STR);
        $stmt->bindParam(':comarca_ca', $comarca_ca, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis
        $detalls = "Modificació comarca: " . $comarca;
        $tipusOperacio = "UPDATE";

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_DADES_MUNICIPIS_COMARCA,  // Nombre de la tabla afectada
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

    // 6) PUT Provincia
    // ruta PUT => "/api/auxiliars/put/provincia"
} else if ($slug === "provincia") {

    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['provincia'])) {
        $errors[] =  ValidacioErrors::requerit('provincia');
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
    $provincia = $data['provincia'];
    $provincia_ca = !empty($data['provincia_ca']) ? $data['provincia_ca'] : NULL;
    $id = $data['id'];

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "UPDATE aux_dades_municipis_provincia SET
            provincia = :provincia, provincia_ca = :provincia_ca
        WHERE id = :id";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':provincia', $provincia, PDO::PARAM_STR);
        $stmt->bindParam(':provincia_ca', $provincia_ca, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis
        $detalls = "Modificació província: " . $provincia;
        $tipusOperacio = "UPDATE";

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_DADES_MUNICIPIS_PROVINCIA,  // Nombre de la tabla afectada
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

    // 6) PUT Comunitat autonoma - regio
    // ruta PUT => "/api/auxiliars/put/comunitat"
} else if ($slug === "comunitat") {

    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['comunitat'])) {
        $errors[] =  ValidacioErrors::requerit('comunitat');
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
    $comunitat = $data['comunitat'];
    $comunitat_ca = !empty($data['comunitat_ca']) ? $data['comunitat_ca'] : NULL;
    $id = $data['id'];

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "UPDATE aux_dades_municipis_comunitat SET
            comunitat = :comunitat, comunitat_ca = :comunitat_ca
        WHERE id = :id";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':comunitat', $comunitat, PDO::PARAM_STR);
        $stmt->bindParam(':comunitat_ca', $comunitat_ca, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis
        $detalls = "Modificació comunitat: " . $comunitat;
        $tipusOperacio = "UPDATE";

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_DADES_MUNICIPIS_COMUNITAT,  // Nombre de la tabla afectada
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

    // 7) PUT Estat
    // ruta PUT => "/api/auxiliars/put/estat"
} else if ($slug === "estat") {

    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['estat'])) {
        $errors[] =  ValidacioErrors::requerit('estat');
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
    $estat = $data['estat'];
    $estat_ca = !empty($data['estat_ca']) ? $data['estat_ca'] : NULL;
    $id = $data['id'];

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "UPDATE aux_dades_municipis_estat SET
            estat = :estat, estat_ca = :estat_ca
        WHERE id = :id";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':estat', $estat, PDO::PARAM_STR);
        $stmt->bindParam(':estat_ca', $estat_ca, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis
        $detalls = "Modificació estat: " . $estat;
        $tipusOperacio = "UPDATE";

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_DADES_MUNICIPIS_ESTAT,  // Nombre de la tabla afectada
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

    // 5) PUT CARREC EMPRESA
    // ruta PUT => "/api/auxiliars/pust/carrec_empresa" 
} else if ($slug === "carrec_empresa") {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['carrec_cat'])) {
        $errors[] =  ValidacioErrors::requerit('càrrec empresa');
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
    $carrec_cat = $data['carrec_cat'];
    $carrec_cast = !empty($data['carrec_cast']) ? $data['carrec_cast'] : NULL;
    $carrec_eng = !empty($data['carrec_eng']) ? $data['carrec_eng'] : NULL;
    $id = $data['id'];

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "UPDATE aux_ofici_carrec SET 
            carrec_cat = :carrec_cat,
            carrec_eng = :carrec_eng,
            carrec_cast = :carrec_cast
        WHERE id = :id";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':carrec_cat', $carrec_cat, PDO::PARAM_STR);
        $stmt->bindParam(':carrec_eng', $carrec_eng, PDO::PARAM_STR);
        $stmt->bindParam(':carrec_cast', $carrec_cast, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $tipusOperacio = "UPDATE";
        $detalls =  "Modificació càrrec: " . $carrec_cat;

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_OFICI_CARREC,  // Nombre de la tabla afectada
            $id                           // ID del registro modificado
        );

        // Respuesta de éxito
        Response::success(
            MissatgesAPI::success('update'),
            ['id' => $id],
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }

    // 2) PUT ofici
    // ruta PUT => "/api/auxiliars/put/ofici"
} else if ($slug === "ofici") {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['ofici_cat'])) {
        $errors[] =  ValidacioErrors::requerit('ofici');
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
    $ofici_cat = $data['ofici_cat'];
    $ofici_es = !empty($data['ofici_es']) ? $data['ofici_es'] : NULL;
    $ofici_en = !empty($data['ofici_en']) ? $data['ofici_en'] : NULL;
    $id = $data['id'];

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "UPDATE aux_oficis SET 
            ofici_cat = :ofici_cat,
            ofici_es = :ofici_es,
            ofici_en = :ofici_en
        WHERE id = :id";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':ofici_cat', $ofici_cat, PDO::PARAM_STR);
        $stmt->bindParam(':ofici_es', $ofici_es, PDO::PARAM_STR);
        $stmt->bindParam(':ofici_en', $ofici_en, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $tipusOperacio = "UPDATR";
        $detalls =  "Modificació ofici: " . $ofici_cat;

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_OFICIS,  // Nombre de la tabla afectada
            $id                           // ID del registro modificado
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

    // 6) PUT SUB-SECTOR ECONOMIC
    // ruta PUT => "/api/auxiliars/put/sub_sector_economic"
} else if ($slug === "sub_sector_economic") {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['sub_sector_cat'])) {
        $errors[] =  ValidacioErrors::requerit('sub-sector econòmic');
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
    $sub_sector_cat = $data['sub_sector_cat'];
    $sub_sector_cast = !empty($data['sub_sector_cast']) ? $data['sub_sector_cast'] : NULL;
    $sub_sector_eng = !empty($data['sub_sector_eng']) ? $data['sub_sector_eng'] : NULL;
    $idSector = !empty($data['idSector']) ? $data['idSector'] : NULL;
    $id = $data['id'];

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        $sql = "UPDATE aux_sub_sector_economic SET 
            sub_sector_cat = :sub_sector_cat,
            sub_sector_eng = :sub_sector_eng,
            idSector = :idSector,
            sub_sector_cast = :sub_sector_cast
        WHERE id = :id";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':sub_sector_cat', $sub_sector_cat, PDO::PARAM_STR);
        $stmt->bindParam(':sub_sector_eng', $sub_sector_eng, PDO::PARAM_STR);
        $stmt->bindParam(':sub_sector_cast', $sub_sector_cast, PDO::PARAM_STR);
        $stmt->bindParam(':idSector', $idSector, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $tipusOperacio = "UPDATE";
        $detalls =  "Modificació sub-sector econòmic " . $sub_sector_cat;

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_SUB_SECTOR_ECONOMIC,  // Nombre de la tabla afectada
            $id                           // ID del registro modificado
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

    // 8) PUT CATEGORIA REPRESSIO
    // ruta PUT => "/api/auxiliars/put/categoriaRepressio"
} else if ($slug === "categoriaRepressio") {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['categoria_cat'])) {
        $errors[] =  ValidacioErrors::requerit('Categoria repressió català');
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
    $categoria_cat = $data['categoria_cat'];
    $categoria_cast = !empty($data['categoria_cast']) ? $data['categoria_cast'] : NULL;
    $categoria_eng = !empty($data['categoria_eng']) ? $data['categoria_eng'] : NULL;
    $categoria_it = !empty($data['categoria_it']) ? $data['categoria_it'] : NULL;
    $categoria_fr = !empty($data['categoria_fr']) ? $data['categoria_fr'] : NULL;
    $categoria_pt = !empty($data['categoria_pt']) ? $data['categoria_pt'] : NULL;
    $id = $data['id'];

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "UPDATE aux_categoria SET
            categoria_cat = :categoria_cat,
            categoria_cast = :categoria_cast,
            categoria_eng = :categoria_eng,
            categoria_fr = :categoria_fr,
            categoria_it = :categoria_it,
            categoria_pt = :categoria_pt
        WHERE id = :id";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':categoria_cat', $categoria_cat, PDO::PARAM_STR);
        $stmt->bindParam(':categoria_cast', $categoria_cast, PDO::PARAM_STR);
        $stmt->bindParam(':categoria_eng', $categoria_eng, PDO::PARAM_STR);
        $stmt->bindParam(':categoria_fr', $categoria_fr, PDO::PARAM_STR);
        $stmt->bindParam(':categoria_it', $categoria_it, PDO::PARAM_STR);
        $stmt->bindParam(':categoria_pt', $categoria_pt, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $tipusOperacio = "UPDATE";
        $detalls =  "Modificació categoria repressió: " . $categoria_cat;

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_CATEGORIA,  // Nombre de la tabla afectada
            $id                           // ID del registro modificado
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
} else {
    Response::error(
        MissatgesAPI::error('errorEndPoint'),
        [],
        500
    );
}
