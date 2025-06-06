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

// TAULES AUXILIARS
// 1) POST municipi
// ruta POST => "/api/auxiliars/post/municipi"
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

    // Verificar si el municipi ya existe en la base de datos
    global $conn;
    /** @var PDO $conn */
    $sql = "SELECT COUNT(*) FROM aux_dades_municipis WHERE ciutat = :ciutat";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':ciutat', $data['ciutat'], PDO::PARAM_STR);
    $stmt->execute();
    $municipiExists = $stmt->fetchColumn();

    if ($municipiExists > 0) {
        Response::error(
            MissatgesAPI::error('duplicat'),
            [],
            409
        );
        exit;
    }

    // Si no hay errores, crear las variables PHP y preparar la consulta PDO
    $ciutat = $data['ciutat'];
    $ciutat_ca = !empty($data['ciutat_ca']) ? $data['ciutat_ca'] : NULL;
    $comarca = !empty($data['comarca']) ? $data['comarca'] : NULL;
    $provincia = !empty($data['provincia']) ? $data['provincia'] : NULL;
    $comunitat = !empty($data['comunitat']) ? $data['comunitat'] : NULL;
    $estat = !empty($data['estat']) ? $data['estat'] : NULL;


    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "INSERT INTO aux_dades_municipis (
        ciutat, ciutat_ca, comarca, provincia, comunitat, estat 
        ) VALUES (
            :ciutat, :ciutat_ca, :comarca, :provincia, :comunitat, :estat 
        )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':ciutat', $ciutat, PDO::PARAM_STR);
        $stmt->bindParam(':ciutat_ca', $ciutat_ca, PDO::PARAM_STR);
        $stmt->bindParam(':comarca', $comarca, PDO::PARAM_INT);
        $stmt->bindParam(':provincia', $provincia, PDO::PARAM_INT);
        $stmt->bindParam(':comunitat', $comunitat, PDO::PARAM_INT);
        $stmt->bindParam(':estat', $estat, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $id = $conn->lastInsertId();
        $tipusOperacio = "INSERT";
        $detalls =  "Creació nou municipi: " . $ciutat;

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_DADES_MUNICIPIS,  // Nombre de la tabla afectada
            $id                           // ID del registro modificado
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
    // 2) POST ofici
    // ruta POST => "/api/auxiliars/post/ofici"
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

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "INSERT INTO aux_oficis (
        ofici_cat, ofici_es, ofici_en
        ) VALUES (
            :ofici_cat, :ofici_es, :ofici_en
        )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':ofici_cat', $ofici_cat, PDO::PARAM_STR);
        $stmt->bindParam(':ofici_es', $ofici_es, PDO::PARAM_STR);
        $stmt->bindParam(':ofici_en', $ofici_en, PDO::PARAM_STR);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $id = $conn->lastInsertId();
        $tipusOperacio = "INSERT";
        $detalls =  "Creació nou ofici: " . $ofici_cat;

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
    // 3) POST tipologia_espai
    // ruta POST => "/api/auxiliars/post/tipologia_espai"
} else if ($slug === "tipologia_espai") {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['tipologia_espai_ca'])) {
        $errors[] =  ValidacioErrors::requerit('tipologia espai');
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
    $tipologia_espai_ca = $data['tipologia_espai_ca'];

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "INSERT INTO aux_tipologia_espais  (
         tipologia_espai_ca
        ) VALUES (
            :tipologia_espai_ca
        )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':tipologia_espai_ca', $tipologia_espai_ca, PDO::PARAM_STR);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $id = $conn->lastInsertId();

        // Recuperar el ID del registro creado
        $tipusOperacio = "INSERT";
        $detalls =  "Creació nova tipologia d'espai: " . $tipologia_espai_ca;

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_TIPOLOGIA_ESPAIS,  // Nombre de la tabla afectada
            $id                           // ID del registro modificado
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
    // 4) POST causa_mort
    // ruta POST => "/api/auxiliars/post/causa_mort"
} else if ($slug === "causa_mort") {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['causa_defuncio_ca'])) {
        $errors[] =  ValidacioErrors::requerit('causa de defunció');
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
    $causa_defuncio_ca = $data['causa_defuncio_ca'];

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "INSERT INTO aux_causa_defuncio (
            causa_defuncio_ca
        ) VALUES (
            :causa_defuncio_ca
        )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':causa_defuncio_ca', $causa_defuncio_ca, PDO::PARAM_STR);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $id = $conn->lastInsertId();

        // Recuperar el ID del registro creado
        $tipusOperacio = "INSERT";
        $detalls =  "Creació nova causa de defunció " . $causa_defuncio_ca;

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_CAUSA_DEFUNCIO,  // Nombre de la tabla afectada
            $id                           // ID del registro modificado
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
    // 5) POST CARREC EMPRESA
    // ruta POST => "/api/auxiliars/post/carrec_empresa" 
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

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "INSERT INTO aux_ofici_carrec (
            carrec_cat, carrec_eng, carrec_cast
        ) VALUES (
            :carrec_cat, :carrec_eng, :carrec_cast
        )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':carrec_cat', $carrec_cat, PDO::PARAM_STR);
        $stmt->bindParam(':carrec_eng', $carrec_eng, PDO::PARAM_STR);
        $stmt->bindParam(':carrec_cast', $carrec_cast, PDO::PARAM_STR);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $id = $conn->lastInsertId();

        // Recuperar el ID del registro creado
        $tipusOperacio = "INSERT";
        $detalls =  "Creació nou càrrec: " . $carrec_cat;

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

    // 6) POST SUB-SECTOR ECONOMIC
    // ruta POST => "/api/auxiliars/post/sub_sector_economic"
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

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "INSERT INTO aux_sub_sector_economic (
            sub_sector_cat, sub_sector_eng, idSector, sub_sector_cast
        ) VALUES (
            :sub_sector_cat, :sub_sector_eng, :idSector, :sub_sector_cast
        )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':sub_sector_cat', $sub_sector_cat, PDO::PARAM_STR);
        $stmt->bindParam(':sub_sector_eng', $sub_sector_eng, PDO::PARAM_STR);
        $stmt->bindParam(':sub_sector_cast', $sub_sector_cast, PDO::PARAM_STR);
        $stmt->bindParam(':idSector', $idSector, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $id = $conn->lastInsertId();

        // Recuperar el ID del registro creado
        $tipusOperacio = "INSERT";
        $detalls =  "Creació nou sub-sector econòmic " . $sub_sector_cat;

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
    // 6) POST PARTIT POLITIC
    // ruta POST => "/api/auxiliars/post/partit_politic"
} elseif ($slug === "partit_politic") {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['partit_politic'])) {
        $errors[] =  ValidacioErrors::requerit('partit polític');
    }

    if (empty($data['sigles'])) {
        $errors[] =  ValidacioErrors::requerit('sigles');
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
    $partit_politic = $data['partit_politic'];
    $sigles = $data['sigles'];

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "INSERT INTO aux_filiacio_politica (
            partit_politic, sigles
        ) VALUES (
            :partit_politic, :sigles
        )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':partit_politic', $partit_politic, PDO::PARAM_STR);
        $stmt->bindParam(':sigles', $sigles, PDO::PARAM_STR);
        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $id = $conn->lastInsertId();
        $detallsOperacio = "Creació partit polític: " . $partit_politic;
        $tipusOperacio = "INSERT";

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detallsOperacio,                       // Descripción de la operación
            Tables::AUX_FILIACIO_POLITICA,  // Nombre de la tabla afectada
            $id                           // ID del registro modificado
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
    // 6) POST SINDICAT
    // ruta POST => "/api/auxiliars/post/sindicat"
} elseif ($slug === "sindicat") {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['sindicat'])) {
        $errors[] =  ValidacioErrors::requerit('sindicat');
    }

    if (empty($data['sigles'])) {
        $errors[] =  ValidacioErrors::requerit('sigles');
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
    $sindicat = $data['sindicat'];
    $sigles = $data['sigles'];

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "INSERT INTO aux_filiacio_sindical (
            sindicat, sigles
        ) VALUES (
            :sindicat, :sigles
        )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':sindicat', $sindicat, PDO::PARAM_STR);
        $stmt->bindParam(':sigles', $sigles, PDO::PARAM_STR);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $id = $conn->lastInsertId();

        // Recuperar el ID del registro creado
        $tipusOperacio = "INSERT";
        $detalls =  "Creació nova sindicat: " . $sindicat;

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_FILIACIO_SINDICAL,  // Nombre de la tabla afectada
            $id                           // ID del registro modificado
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

    // 7) POST COMARCA
    // ruta POST => "/api/auxiliars/post/comarca"
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

    // Verificar si la comarca ya existe en la base de datos
    global $conn;
    /** @var PDO $conn */
    $sql = "SELECT COUNT(*) FROM aux_dades_municipis_comarca WHERE comarca = :comarca";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':comarca', $data['comarca'], PDO::PARAM_STR);
    $stmt->execute();
    $comarcaExists = $stmt->fetchColumn();

    if ($comarcaExists > 0) {
        Response::error(
            MissatgesAPI::error('duplicat'),
            [],
            409
        );
        exit;
    }

    // Si no hay errores, crear las variables PHP y preparar la consulta PDO
    $comarca = $data['comarca'];
    $comarca_ca = !empty($data['comarca_ca']) ? $data['comarca_ca'] : NULL;

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "INSERT INTO aux_dades_municipis_comarca (
            comarca, comarca_ca
        ) VALUES (
            :comarca, :comarca_ca
        )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':comarca', $comarca, PDO::PARAM_STR);
        $stmt->bindParam(':comarca_ca', $comarca_ca, PDO::PARAM_STR);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $id = $conn->lastInsertId();

        // Recuperar el ID del registro creado
        $tipusOperacio = "INSERT";
        $detalls =  "Creació nova comarca " . $comarca;

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_DADES_MUNICIPIS_COMARCA,  // Nombre de la tabla afectada
            $id                           // ID del registro modificado
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

    // 7) POST PROVINCIA
    // ruta POST => "/api/auxiliars/post/provincia"
} else if ($slug === "provincia") {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['provincia'])) {
        $errors[] = 'El camp provincia és obligatori.';
    }

    // Si hay errores, devolver una respuesta con los errores
    if (!empty($errors)) {
        Response::error(
            MissatgesAPI::error('validacio'),
            $errors,
            400
        );
    }

    // Verificar si la provincia ya existe en la base de datos
    global $conn;
    /** @var PDO $conn */
    $sql = "SELECT COUNT(*) FROM aux_dades_municipis_provincia WHERE provincia = :provincia";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':provincia', $data['provincia'], PDO::PARAM_STR);
    $stmt->execute();
    $provinciaExists = $stmt->fetchColumn();

    if ($provinciaExists > 0) {
        Response::error(
            MissatgesAPI::error('duplicat'),
            [],
            409
        );
        exit;
    }

    // Si no hay errores, crear las variables PHP y preparar la consulta PDO
    $provincia_ca = !empty($data['provincia_ca']) ? $data['provincia_ca'] : NULL;
    $provincia = $data['provincia'];

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "INSERT INTO aux_dades_municipis_provincia (
            provincia, provincia_ca
        ) VALUES (
            :provincia, :provincia_ca
        )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':provincia', $provincia, PDO::PARAM_STR);
        $stmt->bindParam(':provincia_ca', $provincia_ca, PDO::PARAM_STR);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $id = $conn->lastInsertId();

        // Recuperar el ID del registro creado
        $tipusOperacio = "INSERT";
        $detalls =  "Creació nova provincia: " . $provincia;

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_DADES_MUNICIPIS_PROVINCIA,  // Nombre de la tabla afectada
            $id                           // ID del registro modificado
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

    // 7) POST COMUNITAT AUTONOMA
    // ruta POST => "/api/auxiliars/post/comunitat"
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

    // Verificar si la comunitat ya existe en la base de datos
    global $conn;
    /** @var PDO $conn */
    $sql = "SELECT COUNT(*) FROM aux_dades_municipis_comunitat WHERE comunitat = :comunitat";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':comunitat', $data['comunitat'], PDO::PARAM_STR);
    $stmt->execute();
    $comunitatExists = $stmt->fetchColumn();

    if ($comunitatExists > 0) {
        Response::error(
            MissatgesAPI::error('duplicat'),
            [],
            409
        );
        exit;
    }

    // Si no hay errores, crear las variables PHP y preparar la consulta PDO
    $comunitat = $data['comunitat'];
    $comunitat_ca = !empty($data['comunitat_ca']) ? $data['comunitat_ca'] : NULL;

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "INSERT INTO aux_dades_municipis_comunitat (
            comunitat, comunitat_ca
        ) VALUES (
            :comunitat, :comunitat_ca
        )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':comunitat', $comunitat, PDO::PARAM_STR);
        $stmt->bindParam(':comunitat_ca', $comunitat_ca, PDO::PARAM_STR);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $id = $conn->lastInsertId();

        // Recuperar el ID del registro creado
        $tipusOperacio = "INSERT";
        $detalls =  "Creació nova comunitat autònoma: " . $comunitat;

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_DADES_MUNICIPIS_COMUNITAT,  // Nombre de la tabla afectada
            $id                           // ID del registro modificado
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

    // 8) POST ESTAT
    // ruta POST => "/api/auxiliars/post/estat"
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

    // Verificar si l'estat ja existeix a la base de dades
    global $conn;
    /** @var PDO $conn */
    $sql = "SELECT COUNT(*) FROM aux_dades_municipis_estat WHERE estat = :estat";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':estat', $data['estat'], PDO::PARAM_STR);
    $stmt->execute();
    $estatExists = $stmt->fetchColumn();

    if ($estatExists > 0) {
        Response::error(
            MissatgesAPI::error('duplicat'),
            [],
            409
        );
        exit;
    }

    // Si no hay errores, crear las variables PHP y preparar la consulta PDO
    $estat = $data['estat'];
    $estat_ca = !empty($data['estat_ca']) ? $data['estat_ca'] : NULL;

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "INSERT INTO aux_dades_municipis_estat (
            estat, estat_ca
        ) VALUES (
            :estat, :estat_ca
        )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':estat', $estat, PDO::PARAM_STR);
        $stmt->bindParam(':estat_ca', $estat_ca, PDO::PARAM_STR);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $id = $conn->lastInsertId();

        // Recuperar el ID del registro creado
        $tipusOperacio = "INSERT";
        $detalls =  "Creació nou estat: " . $estat;

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_DADES_MUNICIPIS_ESTAT,  // Nombre de la tabla afectada
            $id                           // ID del registro modificado
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
    // 8) POST AVATAR USUARI
    // ruta POST => "/api/auxiliars/post/usuariAvatar"
} else if ($slug === "usuariAvatar") {

    // Verificar si se recibió un archivo
    if (empty($_FILES['fileToUpload']) || $_FILES['fileToUpload']['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'No se subió ningún archivo o hubo un error en la carga.',
            'error_code' => $_FILES['fileToUpload']['error'] ?? 'Sin información'
        ]);
        exit();
    }

    // Configuración de rutas
    $servidorMedia = '/home/epgylzqu/memoriaterrassa.cat/public/img/';
    $servidorTemp = '/home/epgylzqu/memoriaterrassa.cat/public/tmp/';

    // Verificar y sanitizar el tipo de imagen
    $tipus = isset($_POST['tipus']) ? (int)$_POST['tipus'] : 0;
    $allowed_types = [
        2 => 'usuaris-avatars',
    ];
    $typeName = $allowed_types[$tipus] ?? 'tmp';

    // Crear el directorio de destino si no existe
    $target_dir = rtrim($servidorMedia, '/') . '/' . $typeName . '/';
    if (!file_exists($target_dir)) {
        if (!mkdir($target_dir, 0777, true)) {
            echo json_encode(['error' => "No se pudo crear el directorio $target_dir."]);
            exit();
        }
    }

    // Verificar permisos de escritura
    if (!is_writable($target_dir)) {
        echo json_encode(['error' => "El directorio $target_dir no tiene permisos de escritura."]);
        exit();
    }

    // Validar el archivo
    $file = $_FILES['fileToUpload'];
    $allowed_mime_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $max_file_size = 2 * 1024 * 1024; // 2 MB

    if ($file['size'] > $max_file_size || !in_array($file['type'], $allowed_mime_types)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'El archivo es demasiado grande o no es un tipo de imagen permitido.',
        ]);
        exit();
    }

    // Generar un nombre único para el archivo
    $uniqueName = basename($file['name']);
    $targetFile = $target_dir . $uniqueName;

    // Mover el archivo al servidor
    if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Hubo un problema al mover el archivo al servidor.',
        ]);
        exit();
    }

    // Insertar datos en la base de datos
    // id 	nomArxiu 	nomImatge 	tipus 	dateCreated 	dateModified 	
    try {
        $nomArxiu = pathinfo($uniqueName, PATHINFO_FILENAME);

        $nom = !empty($_POST['nom']) ? data_input($_POST['nom']) : ($hasError = true);
        $nomImatge = !empty($_POST['nomImatge']) ? data_input($_POST['nomImatge']) : ($hasError = true);
        $dateCreated = date('Y-m-d');

        // Usar una conexión global para PDO
        global $conn;
        /** @var PDO $conn */
        $sql = "INSERT INTO aux_imatges (nomArxiu, tipus, nomImatge, dateCreated) 
            VALUES (:nomArxiu, :tipus, :nomImatge, :dateCreated)";

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(":nomArxiu", $nomArxiu, PDO::PARAM_STR);
        $stmt->bindParam(":tipus", $tipus, PDO::PARAM_INT);
        $stmt->bindParam(":nomImatge", $nomImatge, PDO::PARAM_STR);
        $stmt->bindParam(":dateCreated", $dateCreated, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo json_encode([
                'status' => 'success',
                'message' => 'El archivo se ha subido y registrado correctamente.',
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Hubo un problema al insertar en la base de datos.',
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error al interactuar con la base de datos.',
            'error' => $e->getMessage(),
        ]);
    }
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

    $nom = isset($data['nom']) && trim($data['nom']) !== '' ? data_input($data['nom']) : null;
    $email = isset($data['email']) && trim($data['email']) !== '' ? data_input($data['email']) : null;
    $biografia_cat = isset($data['biografia_cat']) ? data_input($data['biografia_cat']) : null;
    $user_type = isset($data['user_type']) && trim($data['user_type']) !== '' ? (int) $data['user_type'] : null;
    $password = isset($data['password']) && trim($data['password']) !== '' ? data_input($data['password']) : null;
    $avatar = isset($data['avatar']) ? (int) $data['avatar'] : null;

    // Verificar campos obligatorios
    if ($nom === null || $email === null || $user_type === null || $password === null) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Falten dades obligatòries']);
        exit();
    }

    // Hashear el password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT, ['cost' => 10]);

    global $conn;
    /** @var PDO $conn */
    $query = "INSERT INTO auth_users (nom, email, biografia_cat, user_type, password, avatar)
          VALUES (:nom, :email, :biografia_cat, :user_type, :password, :avatar)";

    try {
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':biografia_cat', $biografia_cat, PDO::PARAM_STR);
        $stmt->bindParam(':user_type', $user_type, PDO::PARAM_INT);
        $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        $stmt->bindParam(':avatar', $avatar, PDO::PARAM_INT);

        $stmt->execute();

        header("Content-Type: application/json");
        echo json_encode(['status' => 'success', 'message' => 'Usuari creat correctament']);
    } catch (PDOException $e) {

        // Si no hay resultados, devolver un mensaje de error
        header("Content-Type: application/json");
        echo json_encode(['status' => 'error', 'message' => 'Error en la transmissió de les dades.']);
    }

    // 8) POST CATEGORIA REPRESSIO
    // ruta POST => "/api/auxiliars/post/categoriaRepressio"
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

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "INSERT INTO aux_categoria (
                categoria_cat,
                categoria_cast,
                categoria_eng,
                categoria_fr,
                categoria_it,
                categoria_pt
            ) 
            VALUES (
                'valor_categoria_cat', 
                'valor_categoria_cast', 
                'valor_categoria_eng', 
                'valor_categoria_fr', 
                'valor_categoria_it', 
                'valor_categoria_pt'
            )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':categoria_cat', $categoria_cat, PDO::PARAM_STR);
        $stmt->bindParam(':categoria_cast', $categoria_cast, PDO::PARAM_STR);
        $stmt->bindParam(':categoria_eng', $categoria_eng, PDO::PARAM_STR);
        $stmt->bindParam(':categoria_fr', $categoria_fr, PDO::PARAM_STR);
        $stmt->bindParam(':categoria_it', $categoria_it, PDO::PARAM_STR);
        $stmt->bindParam(':categoria_pt', $categoria_pt, PDO::PARAM_STR);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $id = $conn->lastInsertId();

        // Recuperar el ID del registro creado
        $tipusOperacio = "INSERT";
        $detalls =  "Creació nova categoria repressió: " . $categoria_cat;

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
}
