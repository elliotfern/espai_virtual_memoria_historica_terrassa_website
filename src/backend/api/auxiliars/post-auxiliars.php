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
    $ofici_fr = !empty($data['ofici_fr']) ? $data['ofici_fr'] : NULL;
    $ofici_it = !empty($data['ofici_it']) ? $data['ofici_it'] : NULL;
    $ofici_pt = !empty($data['ofici_pt']) ? $data['ofici_pt'] : NULL;

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "INSERT INTO aux_oficis (
            ofici_cat,
            ofici_es,
            ofici_en,
            ofici_fr,
            ofici_it,
            ofici_pt
        ) VALUES (
            :ofici_cat,
            :ofici_es,
            :ofici_en,
            :ofici_fr,
            :ofici_it,
            :ofici_pt
        )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':ofici_cat', $ofici_cat, PDO::PARAM_STR);
        $stmt->bindParam(':ofici_es', $ofici_es, PDO::PARAM_STR);
        $stmt->bindParam(':ofici_en', $ofici_en, PDO::PARAM_STR);
        $stmt->bindParam(':ofici_fr',  $ofici_fr,  PDO::PARAM_STR);
        $stmt->bindParam(':ofici_it',  $ofici_it,  PDO::PARAM_STR);
        $stmt->bindParam(':ofici_pt',  $ofici_pt,  PDO::PARAM_STR);

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
    $causa_defuncio_es = !empty($data['causa_defuncio_es']) ? $data['causa_defuncio_es'] : NULL;
    $causa_defuncio_en = !empty($data['causa_defuncio_en']) ? $data['causa_defuncio_en'] : NULL;
    $causa_defuncio_fr = !empty($data['causa_defuncio_fr']) ? $data['causa_defuncio_fr'] : NULL;
    $causa_defuncio_pt = !empty($data['causa_defuncio_pt']) ? $data['causa_defuncio_pt'] : NULL;
    $causa_defuncio_it = !empty($data['causa_defuncio_it']) ? $data['causa_defuncio_it'] : NULL;
    $cat                = !empty($data['cat']) ? $data['cat'] : NULL;

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "INSERT INTO aux_causa_defuncio (
            causa_defuncio_ca,
            causa_defuncio_es,
            causa_defuncio_en,
            causa_defuncio_fr,
            causa_defuncio_pt,
            causa_defuncio_it,
            cat
        ) VALUES (
            :causa_defuncio_ca,
            :causa_defuncio_es,
            :causa_defuncio_en,
            :causa_defuncio_fr,
            :causa_defuncio_pt,
            :causa_defuncio_it,
            :cat
        )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':causa_defuncio_ca', $causa_defuncio_ca, PDO::PARAM_STR);
        $stmt->bindParam(':causa_defuncio_es', $causa_defuncio_es, PDO::PARAM_STR);
        $stmt->bindParam(':causa_defuncio_en', $causa_defuncio_en, PDO::PARAM_STR);
        $stmt->bindParam(':causa_defuncio_fr', $causa_defuncio_fr, PDO::PARAM_STR);
        $stmt->bindParam(':causa_defuncio_pt', $causa_defuncio_pt, PDO::PARAM_STR);
        $stmt->bindParam(':causa_defuncio_it', $causa_defuncio_it, PDO::PARAM_STR);
        $stmt->bindParam(':cat', $cat, PDO::PARAM_STR);

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
    $carrec_es = !empty($data['carrec_es']) ? $data['carrec_es'] : NULL;
    $carrec_en = !empty($data['carrec_en']) ? $data['carrec_en'] : NULL;
    $carrec_fr = !empty($data['carrec_fr']) ? $data['carrec_fr'] : NULL;
    $carrec_pt = !empty($data['carrec_pt']) ? $data['carrec_pt'] : NULL;
    $carrec_it = !empty($data['carrec_it']) ? $data['carrec_it'] : NULL;

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "INSERT INTO aux_ofici_carrec (
            carrec_cat, carrec_en, carrec_es, carrec_fr, carrec_it, carrec_pt
        ) VALUES (
            :carrec_cat, :carrec_en, :carrec_es, :carrec_fr, :carrec_it, :carrec_pt
        )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':carrec_cat', $carrec_cat, PDO::PARAM_STR);
        $stmt->bindParam(':carrec_en', $carrec_en, PDO::PARAM_STR);
        $stmt->bindParam(':carrec_es', $carrec_es, PDO::PARAM_STR);
        $stmt->bindParam(':carrec_fr', $carrec_fr, PDO::PARAM_STR);
        $stmt->bindParam(':carrec_it', $carrec_it, PDO::PARAM_STR);
        $stmt->bindParam(':carrec_pt', $carrec_pt, PDO::PARAM_STR);

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
    $sub_sector_es = !empty($data['sub_sector_es']) ? $data['sub_sector_es'] : NULL;
    $sub_sector_en = !empty($data['sub_sector_en']) ? $data['sub_sector_en'] : NULL;
    $sub_sector_it = !empty($data['sub_sector_it']) ? $data['sub_sector_it'] : NULL;
    $sub_sector_fr = !empty($data['sub_sector_fr']) ? $data['sub_sector_fr'] : NULL;
    $sub_sector_pt = !empty($data['sub_sector_pt']) ? $data['sub_sector_pt'] : NULL;
    $idSector = !empty($data['idSector']) ? $data['idSector'] : NULL;


    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "INSERT INTO aux_sub_sector_economic (
            sub_sector_cat, sub_sector_en, idSector, sub_sector_es, sub_sector_it, sub_sector_fr, sub_sector_pt
        ) VALUES (
            :sub_sector_cat, :sub_sector_en, :idSector, :sub_sector_es, :sub_sector_it, :sub_sector_fr, :sub_sector_pt
        )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':sub_sector_cat', $sub_sector_cat, PDO::PARAM_STR);
        $stmt->bindParam(':sub_sector_en', $sub_sector_en, PDO::PARAM_STR);
        $stmt->bindParam(':sub_sector_es', $sub_sector_es, PDO::PARAM_STR);
        $stmt->bindParam(':sub_sector_it', $sub_sector_it, PDO::PARAM_STR);
        $stmt->bindParam(':sub_sector_fr', $sub_sector_fr, PDO::PARAM_STR);
        $stmt->bindParam(':sub_sector_pt', $sub_sector_pt, PDO::PARAM_STR);
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

    if (empty($data['grup'])) {
        $errors[] =  ValidacioErrors::requerit('Grup repressió');
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
    $grup = $data['grup'];
    $categoria_cast = !empty($data['categoria_es']) ? $data['categoria_es'] : NULL;
    $categoria_eng = !empty($data['categoria_en']) ? $data['categoria_en'] : NULL;
    $categoria_it = !empty($data['categoria_it']) ? $data['categoria_it'] : NULL;
    $categoria_fr = !empty($data['categoria_fr']) ? $data['categoria_fr'] : NULL;
    $categoria_pt = !empty($data['categoria_pt']) ? $data['categoria_pt'] : NULL;
    $categoria_pt = !empty($data['categoria_pt']) ? $data['categoria_pt'] : NULL;
    $categoria_pt = !empty($data['categoria_pt']) ? $data['categoria_pt'] : NULL;

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "INSERT INTO aux_categoria (
                categoria_cat,
                categoria_es,
                categoria_en,
                categoria_fr,
                categoria_it,
                categoria_pt,
                grup
            ) 
            VALUES (
                'valor_categoria_cat', 
                'valor_categoria_es', 
                'valor_categoria_en', 
                'valor_categoria_fr', 
                'valor_categoria_it', 
                'valor_categoria_pt',
                'grup'
            )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':categoria_cat', $categoria_cat, PDO::PARAM_STR);
        $stmt->bindParam(':categoria_es', $categoria_es, PDO::PARAM_STR);
        $stmt->bindParam(':categoria_en', $categoria_en, PDO::PARAM_STR);
        $stmt->bindParam(':categoria_fr', $categoria_fr, PDO::PARAM_STR);
        $stmt->bindParam(':categoria_it', $categoria_it, PDO::PARAM_STR);
        $stmt->bindParam(':categoria_pt', $categoria_pt, PDO::PARAM_STR);
        $stmt->bindParam(':grup', $grup, PDO::PARAM_INT);

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

    // RUTA POST : Acusació judicial
    // POST => https://memoriaterrassa.cat/api/auxiliars/post/acusacio   
} else if ($slug === "acusacio") {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Verificar si se recibieron datos
    if ($data === null) {
        // Error al decodificar JSON
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['error' => 'Error decoding JSON data']);
        exit();
    }

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['acusacio_ca'])) {
        $errors[] =  ValidacioErrors::requerit('Acusació judicial català');
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
    $acusacio_ca = $data['acusacio_ca'];
    $acusacio_es = !empty($data['acusacio_es']) ? $data['acusacio_es'] : NULL;
    $acusacio_en = !empty($data['acusacio_en']) ? $data['acusacio_en'] : NULL;
    $acusacio_fr = !empty($data['acusacio_fr']) ? $data['acusacio_fr'] : NULL;
    $acusacio_pt = !empty($data['acusacio_pt']) ? $data['acusacio_pt'] : NULL;
    $acusacio_it = !empty($data['acusacio_it']) ? $data['acusacio_it'] : NULL;

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "INSERT INTO aux_acusacions (
                acusacio_ca,
                acusacio_es,
                acusacio_en,
                acusacio_fr,
                acusacio_pt,
                acusacio_it
            ) 
            VALUES (
                :acusacio_ca, 
                :acusacio_es, 
                :acusacio_en, 
                :acusacio_fr, 
                :acusacio_pt, 
                :acusacio_it
            )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':acusacio_ca', $acusacio_ca, PDO::PARAM_STR);
        $stmt->bindParam(':acusacio_es', $acusacio_es, PDO::PARAM_STR);
        $stmt->bindParam(':acusacio_en', $acusacio_en, PDO::PARAM_STR);
        $stmt->bindParam(':acusacio_fr', $acusacio_fr, PDO::PARAM_STR);
        $stmt->bindParam(':acusacio_pt', $acusacio_pt, PDO::PARAM_STR);
        $stmt->bindParam(':acusacio_it', $acusacio_it, PDO::PARAM_STR);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $id = $conn->lastInsertId();

        // Recuperar el ID del registro creado
        $tipusOperacio = "INSERT";
        $detalls =  "Creació nova acusació judicial: " . $acusacio_ca;

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_ACUSACIONS,  // Nombre de la tabla afectada
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

    // RUTA POST : Bàndol guerra civil
    // POST => https://memoriaterrassa.cat/api/auxiliars/post/bandol   
} else if ($slug === "bandol") {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Verificar si se recibieron datos
    if ($data === null) {
        // Error al decodificar JSON
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['error' => 'Error decoding JSON data']);
        exit();
    }

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['bandol_ca'])) {
        $errors[] =  ValidacioErrors::requerit('Bàndol català');
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
    $bandol_ca = $data['bandol_ca'];
    $bandol_es = !empty($data['bandol_es']) ? $data['bandol_es'] : NULL;
    $bandol_en = !empty($data['bandol_en']) ? $data['bandol_en'] : NULL;
    $bandol_it = !empty($data['bandol_it']) ? $data['bandol_it'] : NULL;
    $bandol_fr = !empty($data['bandol_fr']) ? $data['bandol_fr'] : NULL;
    $bandol_pt = !empty($data['bandol_pt']) ? $data['bandol_pt'] : NULL;

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "INSERT INTO aux_bandol (
                bandol_ca,
                bandol_es,
                bandol_en,
                bandol_it,
                bandol_fr,
                bandol_pt
            ) 
            VALUES (
                ':bandol_ca', 
                ':bandol_es', 
                ':bandol_en', 
                ':bandol_it', 
                ':bandol_fr', 
                ':bandol_pt'
            )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':bandol_ca', $bandol_ca, PDO::PARAM_STR);
        $stmt->bindParam(':bandol_es', $bandol_es, PDO::PARAM_STR);
        $stmt->bindParam(':bandol_en', $bandol_en, PDO::PARAM_STR);
        $stmt->bindParam(':bandol_fr', $bandol_fr, PDO::PARAM_STR);
        $stmt->bindParam(':bandol_pt', $bandol_pt, PDO::PARAM_STR);
        $stmt->bindParam(':bandol_it', $bandol_it, PDO::PARAM_STR);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $id = $conn->lastInsertId();

        // Recuperar el ID del registro creado
        $tipusOperacio = "INSERT";
        $detalls =  "Creació nou bàndol guerra civil: " . $bandol_ca;

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_BANDOL,  // Nombre de la tabla afectada
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

    // 4) POST condicio_militar
    // ruta POST => "/api/auxiliars/post/condicio_militar"
} else if ($slug === "condicio_militar") {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['condicio_ca'])) {
        $errors[] =  ValidacioErrors::requerit('condició militar');
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
    $condicio_ca = $data['condicio_ca'];
    $condicio_es = !empty($data['condicio_es']) ? $data['condicio_es'] : NULL;
    $condicio_en = !empty($data['condicio_en']) ? $data['condicio_en'] : NULL;
    $condicio_fr = !empty($data['condicio_fr']) ? $data['condicio_fr'] : NULL;
    $condicio_it = !empty($data['condicio_it']) ? $data['condicio_it'] : NULL;
    $condicio_pt = !empty($data['condicio_pt']) ? $data['condicio_pt'] : NULL;

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "INSERT INTO aux_condicio (
            condicio_ca,
            condicio_es,
            condicio_en,
            condicio_fr,
            condicio_it,
            condicio_pt
        ) VALUES (
            :condicio_ca,
            :condicio_es,
            :condicio_en,
            :condicio_fr,
            :condicio_it,
            :condicio_pt
        )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':condicio_ca', $condicio_ca, PDO::PARAM_STR);
        $stmt->bindParam(':condicio_es', $condicio_es, PDO::PARAM_STR);
        $stmt->bindParam(':condicio_en', $condicio_en, PDO::PARAM_STR);
        $stmt->bindParam(':condicio_fr', $condicio_fr, PDO::PARAM_STR);
        $stmt->bindParam(':condicio_it', $condicio_it, PDO::PARAM_STR);
        $stmt->bindParam(':condicio_pt', $condicio_pt, PDO::PARAM_STR);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $id = $conn->lastInsertId();

        // Recuperar el ID del registro creado
        $tipusOperacio = "INSERT";
        $detalls =  "Creació nova condició militar: " . $condicio_ca;

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_CONDICIO,  // Nombre de la tabla afectada
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

    // 4) POST cos militar
    // ruta POST => "/api/auxiliars/post/cos_militar"
} else if ($slug === "cos_militar") {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['cos_militar_ca'])) {
        $errors[] =  ValidacioErrors::requerit('cos militar català');
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
    $cos_militar_ca = $data['cos_militar_ca'];
    $cos_militar_es = !empty($data['cos_militar_es']) ? $data['cos_militar_es'] : NULL;
    $cos_militar_en = !empty($data['cos_militar_en']) ? $data['cos_militar_en'] : NULL;
    $cos_militar_fr = !empty($data['cos_militar_fr']) ? $data['cos_militar_fr'] : NULL;
    $cos_militar_it = !empty($data['cos_militar_it']) ? $data['cos_militar_it'] : NULL;
    $cos_militar_pt = !empty($data['cos_militar_pt']) ? $data['cos_militar_pt'] : NULL;

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "INSERT INTO aux_cossos_militars (
            cos_militar_ca,
            cos_militar_es,
            cos_militar_en,
            cos_militar_fr,
            cos_militar_it,
            cos_militar_pt
        ) VALUES (
            :cos_militar_ca,
            :cos_militar_es,
            :cos_militar_en,
            :cos_militar_fr,
            :cos_militar_it,
            :cos_militar_pt
        )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':cos_militar_ca', $cos_militar_ca, PDO::PARAM_STR);
        $stmt->bindParam(':cos_militar_es', $cos_militar_es, PDO::PARAM_STR);
        $stmt->bindParam(':cos_militar_en', $cos_militar_en, PDO::PARAM_STR);
        $stmt->bindParam(':cos_militar_fr', $cos_militar_fr, PDO::PARAM_STR);
        $stmt->bindParam(':cos_militar_it', $cos_militar_it, PDO::PARAM_STR);
        $stmt->bindParam(':cos_militar_pt', $cos_militar_pt, PDO::PARAM_STR);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $id = $conn->lastInsertId();

        // Recuperar el ID del registro creado
        $tipusOperacio = "INSERT";
        $detalls =  "Creació nou cos militar: " . $cos_militar_ca;

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_COSSOS_MILITARS,  // Nombre de la tabla afectada
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

    // 4) POST espai execucio
    // ruta POST => "/api/auxiliars/post/espai"
} else if ($slug === "espai") {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['espai_cat'])) {
        $errors[] =  ValidacioErrors::requerit('espai català');
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
    $espai_cat = $data['espai_cat'];
    $municipi = !empty($data['municipi']) ? $data['municipi'] : NULL;
    $espai_es = !empty($data['espai_es']) ? $data['espai_es'] : NULL;
    $espai_en = !empty($data['espai_en']) ? $data['espai_en'] : NULL;
    $espai_fr = !empty($data['espai_fr']) ? $data['espai_fr'] : NULL;
    $espai_it = !empty($data['espai_it']) ? $data['espai_it'] : NULL;
    $espai_pt = !empty($data['espai_pt']) ? $data['espai_pt'] : NULL;
    $descripcio_espai = !empty($data['descripcio_espai']) ? $data['descripcio_espai'] : NULL;

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "INSERT INTO aux_espai (
            espai_cat,
            espai_es,
            espai_en,
            espai_fr,
            espai_it,
            espai_pt,
            descripcio_espai,
            municipi
        ) VALUES (
            :espai_cat,
            :espai_es,
            :espai_en,
            :espai_fr,
            :espai_it,
            :espai_pt,
            :descripcio_espai,
            :municipi
        )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':espai_cat', $espai_cat, PDO::PARAM_STR);
        $stmt->bindParam(':espai_es', $espai_es, PDO::PARAM_STR);
        $stmt->bindParam(':espai_en', $espai_en, PDO::PARAM_STR);
        $stmt->bindParam(':espai_fr', $espai_fr, PDO::PARAM_STR);
        $stmt->bindParam(':espai_it', $espai_it, PDO::PARAM_STR);
        $stmt->bindParam(':espai_pt', $espai_pt, PDO::PARAM_STR);
        $stmt->bindParam(':descripcio_espai', $descripcio_espai, PDO::PARAM_STR);
        $stmt->bindParam(':municipi', $municipi, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $id = $conn->lastInsertId();

        // Recuperar el ID del registro creado
        $tipusOperacio = "INSERT";
        $detalls =  "Creació de nou espai: " . $espai_cat;

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_ESPAI,  // Nombre de la tabla afectada
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

    // 4) POST Nivell d'estudis
    // ruta POST => "/api/auxiliars/post/nivell_estudis"
} else if ($slug === "nivell_estudis") {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['estudi_cat'])) {
        $errors[] =  ValidacioErrors::requerit('nivell estudis català');
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
    $estudi_cat = $data['estudi_cat'];
    $estudi_es  = !empty($data['estudi_es'])  ? $data['estudi_es']  : NULL;
    $estudi_en  = !empty($data['estudi_en'])  ? $data['estudi_en']  : NULL;
    $estudi_it  = !empty($data['estudi_it'])  ? $data['estudi_it']  : NULL;
    $estudi_fr  = !empty($data['estudi_fr'])  ? $data['estudi_fr']  : NULL;
    $estudi_pt  = !empty($data['estudi_pt'])  ? $data['estudi_pt']  : NULL;

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "INSERT INTO aux_estudis (
                estudi_cat,
                estudi_es,
                estudi_en,
                estudi_it,
                estudi_fr,
                estudi_pt
            ) VALUES (
                :estudi_cat,
                :estudi_es,
                :estudi_en,
                :estudi_it,
                :estudi_fr,
                :estudi_pt
            )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':estudi_cat', $estudi_cat, PDO::PARAM_STR);
        $stmt->bindParam(':estudi_es',  $estudi_es,  PDO::PARAM_STR);
        $stmt->bindParam(':estudi_en',  $estudi_en,  PDO::PARAM_STR);
        $stmt->bindParam(':estudi_it',  $estudi_it,  PDO::PARAM_STR);
        $stmt->bindParam(':estudi_fr',  $estudi_fr,  PDO::PARAM_STR);
        $stmt->bindParam(':estudi_pt',  $estudi_pt,  PDO::PARAM_STR);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $id = $conn->lastInsertId();

        // Recuperar el ID del registro creado
        $tipusOperacio = "INSERT";
        $detalls =  "Creació de nou nivell d'estudis: " . $estudi_cat;

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_ESTUDIS,  // Nombre de la tabla afectada
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

    // 4) POST estat civil
    // ruta POST => "/api/auxiliars/post/estat_civil"
} else if ($slug === "estat_civil") {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['estat_cat'])) {
        $errors[] =  ValidacioErrors::requerit('estat civil català');
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
    $estat_cat = $data['estat_cat'];
    $estat_es  = !empty($data['estat_es']) ? $data['estat_es'] : NULL;
    $estat_en  = !empty($data['estat_en']) ? $data['estat_en'] : NULL;
    $estat_fr  = !empty($data['estat_fr']) ? $data['estat_fr'] : NULL;
    $estat_it  = !empty($data['estat_it']) ? $data['estat_it'] : NULL;
    $estat_pt  = !empty($data['estat_pt']) ? $data['estat_pt'] : NULL;

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "INSERT INTO aux_estat_civil  (
                estat_cat,
                estat_es,
                estat_en,
                estat_fr,
                estat_it,
                estat_pt
            ) VALUES (
                :estat_cat,
                :estat_es,
                :estat_en,
                :estat_fr,
                :estat_it,
                :estat_pt
            )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':estat_cat', $estat_cat, PDO::PARAM_STR);
        $stmt->bindParam(':estat_es', $estat_es, PDO::PARAM_STR);
        $stmt->bindParam(':estat_en', $estat_en, PDO::PARAM_STR);
        $stmt->bindParam(':estat_fr', $estat_fr, PDO::PARAM_STR);
        $stmt->bindParam(':estat_it', $estat_it, PDO::PARAM_STR);
        $stmt->bindParam(':estat_pt', $estat_pt, PDO::PARAM_STR);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $id = $conn->lastInsertId();

        // Recuperar el ID del registro creado
        $tipusOperacio = "INSERT";
        $detalls =  "Creació de nou estat civil: " . $estat_cat;

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_ESTAT_CIVIL,  // Nombre de la tabla afectada
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

    // 4) POST sector econòmic
    // ruta POST => "/api/auxiliars/post/sector_economic"
} else if ($slug === "sector_economic") {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['sector_cat'])) {
        $errors[] =  ValidacioErrors::requerit('sector econòmic en català');
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
    $sector_cat = $data['sector_cat'];
    $sector_es  = !empty($data['sector_es']) ? $data['sector_es'] : NULL;
    $sector_en  = !empty($data['sector_en']) ? $data['sector_en'] : NULL;
    $sector_fr  = !empty($data['sector_fr']) ? $data['sector_fr'] : NULL;
    $sector_it  = !empty($data['sector_it']) ? $data['sector_it'] : NULL;
    $sector_pt  = !empty($data['sector_pt']) ? $data['sector_pt'] : NULL;

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "INSERT INTO aux_sector_economic (
            sector_cat,
            sector_es,
            sector_en,
            sector_fr,
            sector_it,
            sector_pt
        ) VALUES (
            :sector_cat,
            :sector_es,
            :sector_en,
            :sector_fr,
            :sector_it,
            :sector_pt
        )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':sector_cat', $sector_cat, PDO::PARAM_STR);
        $stmt->bindParam(':sector_es', $sector_es, PDO::PARAM_STR);
        $stmt->bindParam(':sector_en', $sector_en, PDO::PARAM_STR);
        $stmt->bindParam(':sector_fr', $sector_fr, PDO::PARAM_STR);
        $stmt->bindParam(':sector_it', $sector_it, PDO::PARAM_STR);
        $stmt->bindParam(':sector_pt', $sector_pt, PDO::PARAM_STR);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $id = $conn->lastInsertId();

        // Recuperar el ID del registro creado
        $tipusOperacio = "INSERT";
        $detalls =  "Creació de nou sector econòmic: " . $sector_cat;

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_SECTOR_ECONOMIC,  // Nombre de la tabla afectada
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

    // 4) POST Empresa
    // ruta POST => "/api/auxiliars/post/empresa"
} else if ($slug === "empresa") {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['empresa_ca'])) {
        $errors[] =  ValidacioErrors::requerit('empresa en català');
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
    $empresa_ca = $data['empresa_ca'];
    $empresa_es  = !empty($data['empresa_es']) ? $data['empresa_es'] : NULL;
    $empresa_en  = !empty($data['empresa_en']) ? $data['empresa_en'] : NULL;
    $empresa_fr  = !empty($data['empresa_fr']) ? $data['empresa_fr'] : NULL;
    $empresa_it  = !empty($data['empresa_it']) ? $data['empresa_it'] : NULL;
    $empresa_pt  = !empty($data['empresa_pt']) ? $data['empresa_pt'] : NULL;

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "INSERT INTO aux_empreses (
            empresa_ca,
            empresa_es,
            empresa_en,
            empresa_fr,
            empresa_it,
            empresa_pt
        ) VALUES (
            :empresa_ca,
            :empresa_es,
            :empresa_en,
            :empresa_fr,
            :empresa_it,
            :empresa_pt
        )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':empresa_ca', $empresa_ca, PDO::PARAM_STR);
        $stmt->bindParam(':empresa_es', $empresa_es, PDO::PARAM_STR);
        $stmt->bindParam(':empresa_en', $empresa_en, PDO::PARAM_STR);
        $stmt->bindParam(':empresa_fr', $empresa_fr, PDO::PARAM_STR);
        $stmt->bindParam(':empresa_it', $empresa_it, PDO::PARAM_STR);
        $stmt->bindParam(':empresa_pt', $empresa_pt, PDO::PARAM_STR);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $id = $conn->lastInsertId();

        // Recuperar el ID del registro creado
        $tipusOperacio = "INSERT";
        $detalls =  "Creació de nova empresa: " . $empresa_ca;

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_EMPRESES,  // Nombre de la tabla afectada
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

    // 4) POST TIpus procediment judicial
    // ruta POST => "/api/auxiliars/post/procedimentJudicial"
} else if ($slug === "procedimentJudicial") {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['procediment_ca'])) {
        $errors[] =  ValidacioErrors::requerit('procediment judicial en català');
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
    $procediment_ca = $data['procediment_ca'];
    $procediment_es  = !empty($data['procediment_es']) ? $data['procediment_es'] : NULL;
    $procediment_en  = !empty($data['procediment_en']) ? $data['procediment_en'] : NULL;

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "INSERT INTO aux_procediment_judicial (
            procediment_ca,
            procediment_es,
            procediment_en
        ) VALUES (
            :procediment_ca,
            :procediment_es,
            :procediment_en
        )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':procediment_ca', $procediment_ca, PDO::PARAM_STR);
        $stmt->bindParam(':procediment_es', $procediment_es, PDO::PARAM_STR);
        $stmt->bindParam(':procediment_en', $procediment_en, PDO::PARAM_STR);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $id = $conn->lastInsertId();

        // Recuperar el ID del registro creado
        $tipusOperacio = "INSERT";
        $detalls =  "Creació de nou tipus de procediment judicial: " . $procediment_ca;

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_PROCEDIMENT_JUDICIAL,  // Nombre de la tabla afectada
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

    // 4) POST Tipus judici
    // ruta POST => "/api/auxiliars/post/tipusJudici"
} else if ($slug === "tipusJudici") {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['tipusJudici_ca'])) {
        $errors[] =  ValidacioErrors::requerit('tipus judici en català');
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
    $tipusJudici_ca  = $data['tipusJudici_ca'];
    $tipusJudici_es  = !empty($data['tipusJudici_es']) ? $data['tipusJudici_es'] : NULL;
    $tipusJudici_en  = !empty($data['tipusJudici_en']) ? $data['tipusJudici_en'] : NULL;
    $tipusJudici_fr  = !empty($data['tipusJudici_fr']) ? $data['tipusJudici_fr'] : NULL;
    $tipusJudici_it  = !empty($data['tipusJudici_it']) ? $data['tipusJudici_it'] : NULL;
    $tipusJudici_pt  = !empty($data['tipusJudici_pt']) ? $data['tipusJudici_pt'] : NULL;

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "INSERT INTO aux_tipus_judici (
            tipusJudici_ca,
            tipusJudici_es,
            tipusJudici_en,
            tipusJudici_fr,
            tipusJudici_it,
            tipusJudici_pt
        ) VALUES (

            :tipusJudici_ca,
            :tipusJudici_es,
            :tipusJudici_en,
            :tipusJudici_fr,
            :tipusJudici_it,
            :tipusJudici_pt
        )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':tipusJudici_ca', $tipusJudici_ca, PDO::PARAM_STR);
        $stmt->bindParam(':tipusJudici_es', $tipusJudici_es, PDO::PARAM_STR);
        $stmt->bindParam(':tipusJudici_en', $tipusJudici_en, PDO::PARAM_STR);
        $stmt->bindParam(':tipusJudici_fr', $tipusJudici_fr, PDO::PARAM_STR);
        $stmt->bindParam(':tipusJudici_it', $tipusJudici_it, PDO::PARAM_STR);
        $stmt->bindParam(':tipusJudici_pt', $tipusJudici_pt, PDO::PARAM_STR);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $id = $conn->lastInsertId();

        // Recuperar el ID del registro creado
        $tipusOperacio = "INSERT";
        $detalls =  "Creació de nou tipus de judici: " . $tipusJudici_ca;

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_TIPUS_JUDICI,  // Nombre de la tabla afectada
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

    // 4) POST Sentències
    // ruta POST => "/api/auxiliars/post/sentencia"
} else if ($slug === "sentencia") {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['sentencia_ca'])) {
        $errors[] =  ValidacioErrors::requerit('sentència en català');
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
    $sentencia_ca = $data['sentencia_ca'];
    $sentencia_es = !empty($data['sentencia_es']) ? $data['sentencia_es'] : NULL;
    $sentencia_en = !empty($data['sentencia_en']) ? $data['sentencia_en'] : NULL;
    $sentencia_fr = !empty($data['sentencia_fr']) ? $data['sentencia_fr'] : NULL;
    $sentencia_it = !empty($data['sentencia_it']) ? $data['sentencia_it'] : NULL;
    $sentencia_pt = !empty($data['sentencia_pt']) ? $data['sentencia_pt'] : NULL;

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "INSERT INTO aux_sentencies (
            sentencia_ca,
            sentencia_es,
            sentencia_en,
            sentencia_fr,
            sentencia_it,
            sentencia_pt
        ) VALUES (
            :sentencia_ca,
            :sentencia_es,
            :sentencia_en,
            :sentencia_fr,
            :sentencia_it,
            :sentencia_pt
        )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':sentencia_ca', $sentencia_ca, PDO::PARAM_STR);
        $stmt->bindParam(':sentencia_es', $sentencia_es, PDO::PARAM_STR);
        $stmt->bindParam(':sentencia_en', $sentencia_en, PDO::PARAM_STR);
        $stmt->bindParam(':sentencia_fr', $sentencia_fr, PDO::PARAM_STR);
        $stmt->bindParam(':sentencia_it', $sentencia_it, PDO::PARAM_STR);
        $stmt->bindParam(':sentencia_pt', $sentencia_pt, PDO::PARAM_STR);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $id = $conn->lastInsertId();

        // Recuperar el ID del registro creado
        $tipusOperacio = "INSERT";
        $detalls =  "Creació de nova sentència: " . $sentencia_ca;

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_SENTENCIES,  // Nombre de la tabla afectada
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

    // 4) POST Pena
    // ruta POST => "/api/auxiliars/post/pena"
} else if ($slug === "pena") {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['pena_ca'])) {
        $errors[] =  ValidacioErrors::requerit('pena en català');
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
    $pena_ca = $data['pena_ca'];
    $pena_es = !empty($data['pena_es']) ? $data['pena_es'] : NULL;
    $pena_en = !empty($data['pena_en']) ? $data['pena_en'] : NULL;
    $pena_it = !empty($data['pena_it']) ? $data['pena_it'] : NULL;
    $pena_fr = !empty($data['pena_fr']) ? $data['pena_fr'] : NULL;
    $pena_pt = !empty($data['pena_pt']) ? $data['pena_pt'] : NULL;

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "INSERT INTO aux_penes (
            pena_ca,
            pena_es,
            pena_en,
            pena_it,
            pena_fr,
            pena_pt
        ) VALUES (
            :pena_ca,
            :pena_es,
            :pena_en,
            :pena_it,
            :pena_fr,
            :pena_pt
        )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':pena_ca', $pena_ca, PDO::PARAM_STR);
        $stmt->bindParam(':pena_es', $pena_es, PDO::PARAM_STR);
        $stmt->bindParam(':pena_en', $pena_en, PDO::PARAM_STR);
        $stmt->bindParam(':pena_it', $pena_it, PDO::PARAM_STR);
        $stmt->bindParam(':pena_fr', $pena_fr, PDO::PARAM_STR);
        $stmt->bindParam(':pena_pt', $pena_pt, PDO::PARAM_STR);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $id = $conn->lastInsertId();

        // Recuperar el ID del registro creado
        $tipusOperacio = "INSERT";
        $detalls =  "Creació de nova pena: " . $pena_ca;

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_PENES,  // Nombre de la tabla afectada
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

    // 4) POST Jutjats
    // ruta POST => "/api/auxiliars/post/jutjat"
} else if ($slug === "jutjat") {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['jutjat_ca'])) {
        $errors[] =  ValidacioErrors::requerit('seu judicial en català');
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
    $jutjat_ca = $data['jutjat_ca'];
    $jutjat_es = !empty($data['jutjat_es']) ? $data['jutjat_es'] : NULL;
    $jutjat_en = !empty($data['jutjat_en']) ? $data['jutjat_en'] : NULL;
    $jutjat_fr = !empty($data['jutjat_fr']) ? $data['jutjat_fr'] : NULL;
    $jutjat_it = !empty($data['jutjat_it']) ? $data['jutjat_it'] : NULL;
    $jutjat_pt = !empty($data['jutjat_pt']) ? $data['jutjat_pt'] : NULL;

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "INSERT INTO aux_jutjats (
                jutjat_ca,
                jutjat_es,
                jutjat_en,
                jutjat_fr,
                jutjat_it,
                jutjat_pt
            ) VALUES (
                :jutjat_ca,
                :jutjat_es,
                :jutjat_en,
                :jutjat_fr,
                :jutjat_it,
                :jutjat_pt
            )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':jutjat_ca', $jutjat_ca, PDO::PARAM_STR);
        $stmt->bindParam(':jutjat_es', $jutjat_es, PDO::PARAM_STR);
        $stmt->bindParam(':jutjat_en', $jutjat_en, PDO::PARAM_STR);
        $stmt->bindParam(':jutjat_fr', $jutjat_fr, PDO::PARAM_STR);
        $stmt->bindParam(':jutjat_it', $jutjat_it, PDO::PARAM_STR);
        $stmt->bindParam(':jutjat_pt', $jutjat_pt, PDO::PARAM_STR);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $id = $conn->lastInsertId();

        // Recuperar el ID del registro creado
        $tipusOperacio = "INSERT";
        $detalls =  "Creació de nova seu judicial: " . $jutjat_ca;

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_JUTJATS,  // Nombre de la tabla afectada
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

    // 4) POST Modalitats preso
    // ruta POST => "/api/auxiliars/post/modalitatPreso"
} else if ($slug === "modalitatPreso") {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['modalitat_ca'])) {
        $errors[] =  ValidacioErrors::requerit('modalitat de presó en català');
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
    $modalitat_ca = $data['modalitat_ca'];
    $modalitat_es = !empty($data['modalitat_es']) ? $data['modalitat_es'] : NULL;
    $modalitat_en = !empty($data['modalitat_en']) ? $data['modalitat_en'] : NULL;
    $modalitat_fr = !empty($data['modalitat_fr']) ? $data['modalitat_fr'] : NULL;
    $modalitat_it = !empty($data['modalitat_it']) ? $data['modalitat_it'] : NULL;
    $modalitat_pt = !empty($data['modalitat_pt']) ? $data['modalitat_pt'] : NULL;

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        $sql = "INSERT INTO aux_modalitat_preso (
                modalitat_ca,
                modalitat_es,
                modalitat_en,
                modalitat_fr,
                modalitat_it,
                modalitat_pt
            ) VALUES (
                :modalitat_ca,
                :modalitat_es,
                :modalitat_en,
                :modalitat_fr,
                :modalitat_it,
                :modalitat_pt
            )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':modalitat_ca', $modalitat_ca, PDO::PARAM_STR);
        $stmt->bindParam(':modalitat_es', $modalitat_es, PDO::PARAM_STR);
        $stmt->bindParam(':modalitat_en', $modalitat_en, PDO::PARAM_STR);
        $stmt->bindParam(':modalitat_fr', $modalitat_fr, PDO::PARAM_STR);
        $stmt->bindParam(':modalitat_it', $modalitat_it, PDO::PARAM_STR);
        $stmt->bindParam(':modalitat_pt', $modalitat_pt, PDO::PARAM_STR);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $id = $conn->lastInsertId();

        // Recuperar el ID del registro creado
        $tipusOperacio = "INSERT";
        $detalls =  "Creació de nova modalitat de presó: " . $modalitat_ca;

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_MODALITAT_PRESO,  // Nombre de la tabla afectada
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

    // 4) POST Motius de detencio/empresonament
    // ruta POST => "/api/auxiliars/post/motiuEmpresonament"
} else if ($slug === "motiuEmpresonament") {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['motiuEmpresonament_ca'])) {
        $errors[] =  ValidacioErrors::requerit('motiu empresonament en català');
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
    $motiuEmpresonament_ca = $data['motiuEmpresonament_ca'];
    $motiuEmpresonament_es = !empty($data['motiuEmpresonament_es']) ? $data['motiuEmpresonament_es'] : NULL;
    $motiuEmpresonament_en = !empty($data['motiuEmpresonament_en']) ? $data['motiuEmpresonament_en'] : NULL;
    $motiuEmpresonament_fr = !empty($data['motiuEmpresonament_fr']) ? $data['motiuEmpresonament_fr'] : NULL;
    $motiuEmpresonament_it = !empty($data['motiuEmpresonament_it']) ? $data['motiuEmpresonament_it'] : NULL;
    $motiuEmpresonament_pt = !empty($data['motiuEmpresonament_pt']) ? $data['motiuEmpresonament_pt'] : NULL;

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        $sql = "INSERT INTO aux_motius_empresonament (
                motiuEmpresonament_ca,
                motiuEmpresonament_es,
                motiuEmpresonament_en,
                motiuEmpresonament_fr,
                motiuEmpresonament_it,
                motiuEmpresonament_pt
            ) VALUES (
                :motiuEmpresonament_ca,
                :motiuEmpresonament_es,
                :motiuEmpresonament_en,
                :motiuEmpresonament_fr,
                :motiuEmpresonament_it,
                :motiuEmpresonament_pt
            )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':motiuEmpresonament_ca', $motiuEmpresonament_ca, PDO::PARAM_STR);
        $stmt->bindParam(':motiuEmpresonament_es', $motiuEmpresonament_es, PDO::PARAM_STR);
        $stmt->bindParam(':motiuEmpresonament_en', $motiuEmpresonament_en, PDO::PARAM_STR);
        $stmt->bindParam(':motiuEmpresonament_fr', $motiuEmpresonament_fr, PDO::PARAM_STR);
        $stmt->bindParam(':motiuEmpresonament_it', $motiuEmpresonament_it, PDO::PARAM_STR);
        $stmt->bindParam(':motiuEmpresonament_pt', $motiuEmpresonament_pt, PDO::PARAM_STR);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $id = $conn->lastInsertId();

        // Recuperar el ID del registro creado
        $tipusOperacio = "INSERT";
        $detalls =  "Creació de nou motiu d'empresonament: " . $motiuEmpresonament_ca;

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_MOTIUS_EMPRESONANENT,  // Nombre de la tabla afectada
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

    // 4) POST Grups de repressió
    // ruta POST => "/api/auxiliars/post/grupRepressio"
} else if ($slug === "grupRepressio") {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['nom_institucio'])) {
        $errors[] =  ValidacioErrors::requerit('nom institució en català');
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
    $carrec = !empty($data['carrec']) ? $data['carrec'] : NULL;
    $nom_institucio = $data['nom_institucio'];
    $grup_institucio = !empty($data['grup_institucio']) ? $data['grup_institucio'] : NULL;

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        $sql = "INSERT INTO aux_sistema_repressiu (
            carrec,
            nom_institucio,
            grup_institucio
        ) VALUES (
            :carrec,
            :nom_institucio,
            :grup_institucio
        )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':carrec', $carrec, PDO::PARAM_STR);
        $stmt->bindParam(':nom_institucio', $nom_institucio, PDO::PARAM_STR);
        $stmt->bindParam(':grup_institucio', $grup_institucio, PDO::PARAM_INT); // suponiendo que es int

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $id = $conn->lastInsertId();

        // Recuperar el ID del registro creado
        $tipusOperacio = "INSERT";
        $detalls =  "Creació de nou grup de repressió: " . $nom_institucio;

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_SISTEMA_REPRESSIU,  // Nombre de la tabla afectada
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

    // 4) POST Presons
    // ruta POST => "/api/auxiliars/post/preso"
} else if ($slug === "preso") {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['nom_preso'])) {
        $errors[] =  ValidacioErrors::requerit('nom presó en català');
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
    $nom_preso        = $data['nom_preso'];
    $municipi_preso   = !empty($data['municipi_preso']) ? $data['municipi_preso'] : null;
    $nom_preso_es     = !empty($data['nom_preso_es']) ? $data['nom_preso_es'] : null;
    $nom_preso_en     = !empty($data['nom_preso_en']) ? $data['nom_preso_en'] : null;
    $nom_preso_fr     = !empty($data['nom_preso_fr']) ? $data['nom_preso_fr'] : null;
    $nom_preso_it     = !empty($data['nom_preso_it']) ? $data['nom_preso_it'] : null;
    $nom_preso_pt     = !empty($data['nom_preso_pt']) ? $data['nom_preso_pt'] : null;

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        $sql = "INSERT INTO aux_presons (
            nom_preso,
            municipi_preso,
            nom_preso_es,
            nom_preso_en,
            nom_preso_fr,
            nom_preso_it,
            nom_preso_pt
        ) VALUES (
            :nom_preso,
            :municipi_preso,
            :nom_preso_es,
            :nom_preso_en,
            :nom_preso_fr,
            :nom_preso_it,
            :nom_preso_pt
        )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':nom_preso', $nom_preso, PDO::PARAM_STR);
        $stmt->bindParam(':municipi_preso', $municipi_preso, PDO::PARAM_INT);
        $stmt->bindParam(':nom_preso_es', $nom_preso_es, PDO::PARAM_STR);
        $stmt->bindParam(':nom_preso_en', $nom_preso_en, PDO::PARAM_STR);
        $stmt->bindParam(':nom_preso_fr', $nom_preso_fr, PDO::PARAM_STR);
        $stmt->bindParam(':nom_preso_it', $nom_preso_it, PDO::PARAM_STR);
        $stmt->bindParam(':nom_preso_pt', $nom_preso_pt, PDO::PARAM_STR);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $id = $conn->lastInsertId();

        // Recuperar el ID del registro creado
        $tipusOperacio = "INSERT";
        $detalls =  "Creació de nova presó: " . $nom_preso;

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_PRESONS,  // Nombre de la tabla afectada
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

    // 4) POST Presons / camps detencio
    // ruta POST => "/api/auxiliars/post/presoDetencio"
} else if ($slug === "presoDetencio") {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['nom'])) {
        $errors[] =  ValidacioErrors::requerit('nom presó en català');
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
    $tipus     = !empty($data['tipus']) ? $data['tipus'] : null;
    $nom       = $data['nom'];
    $municipi  = !empty($data['municipi']) ? $data['municipi'] : null;

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        $sql = "INSERT INTO aux_deportacio_preso (
            tipus,
            nom,
            municipi
        ) VALUES (
            :tipus,
            :nom,
            :municipi
        )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':tipus', $tipus, PDO::PARAM_INT);
        $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindParam(':municipi', $municipi, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $id = $conn->lastInsertId();

        // Recuperar el ID del registro creado
        $tipusOperacio = "INSERT";
        $detalls =  "Creació de nova presó/camp de detenció: " . $nom;

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_PRESONS_CAMPS,  // Nombre de la tabla afectada
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
