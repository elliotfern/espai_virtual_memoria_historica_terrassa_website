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
    echo json_encode(["error" => "Método no permitido. Se requiere PUT."]);
    exit;
}

// DB_DADES PERSONALS
// 1) POST municipi
// ruta POST => "/api/auxiliars/post/?type=municipi"
if (isset($_GET['type']) && $_GET['type'] == 'municipi') {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['ciutat'])) {
        $errors[] = 'El camp ciutat és obligatori.';
    }


    // Si hay errores, devolver una respuesta con los errores
    if (!empty($errors)) {
        http_response_code(400); // Bad Request
        echo json_encode(["status" => "error", "message" => "S'han produït errors en la validació", "errors" => $errors]);
        exit;
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
        http_response_code(409); // Conflict
        echo json_encode(["status" => "error", "message" => "El municipi ja existeix a la base de dades."]);
        exit;
    }

    // Si no hay errores, crear las variables PHP y preparar la consulta PDO
    $ciutat = !empty($data['ciutat']) ? $data['ciutat'] : NULL;
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
        ciutat, comarca, provincia, comunitat, estat 
        ) VALUES (
            :ciutat, :comarca, :provincia, :comunitat, :estat 
        )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(
            ':ciutat',
            $ciutat,
            PDO::PARAM_STR
        );
        $stmt->bindParam(':comarca', $comarca, PDO::PARAM_INT);
        $stmt->bindParam(':provincia', $provincia, PDO::PARAM_INT);
        $stmt->bindParam(':comunitat', $comunitat, PDO::PARAM_INT);
        $stmt->bindParam(':estat', $estat, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $lastInsertId = $conn->lastInsertId();

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        $dataHoraCanvi = date('Y-m-d H:i:s');
        $tipusOperacio = "Insert Nou municipi";
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
    // 2) POST ofici
    // ruta POST => "/api/auxiliars/post/?type=ofici"
} elseif (isset($_GET['type']) && $_GET['type'] == 'ofici') {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['ofici_cat'])) {
        $errors[] = 'El camp ciutat és obligatori.';
    }


    // Si hay errores, devolver una respuesta con los errores
    if (!empty($errors)) {
        http_response_code(400); // Bad Request
        echo json_encode(["status" => "error", "message" => "S'han produït errors en la validació", "errors" => $errors]);
        exit;
    }

    // Si no hay errores, crear las variables PHP y preparar la consulta PDO
    $ofici_cat = !empty($data['ofici_cat']) ? $data['ofici_cat'] : NULL;

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "INSERT INTO aux_oficis (
        ofici_cat
        ) VALUES (
            :ofici_cat
        )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':ofici_cat', $ofici_cat, PDO::PARAM_STR);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $lastInsertId = $conn->lastInsertId();

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        $dataHoraCanvi = date('Y-m-d H:i:s');
        $tipusOperacio = "Insert Nou ofici";
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
    // 3) POST tipologia_espai
    // ruta POST => "/api/auxiliars/post/?type=tipologia_espai"
} elseif (isset($_GET['type']) && $_GET['type'] == 'tipologia_espai') {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['tipologia_espai_ca'])) {
        $errors[] = 'El camp ciutat és obligatori.';
    }


    // Si hay errores, devolver una respuesta con los errores
    if (!empty($errors)) {
        http_response_code(400); // Bad Request
        echo json_encode(["status" => "error", "message" => "S'han produït errors en la validació", "errors" => $errors]);
        exit;
    }

    // Si no hay errores, crear las variables PHP y preparar la consulta PDO
    $tipologia_espai_ca = !empty($data['tipologia_espai_ca']) ? $data['tipologia_espai_ca'] : NULL;

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
        $lastInsertId = $conn->lastInsertId();

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        $dataHoraCanvi = date('Y-m-d H:i:s');
        $tipusOperacio = "Insert Nova tipologia espai";
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
    // 4) POST causa_mort
    // ruta POST => "/api/auxiliars/post/?type=causa_mort"
} elseif (isset($_GET['type']) && $_GET['type'] == 'causa_mort') {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['causa_defuncio_ca'])) {
        $errors[] = 'El camp ciutat és obligatori.';
    }


    // Si hay errores, devolver una respuesta con los errores
    if (!empty($errors)) {
        http_response_code(400); // Bad Request
        echo json_encode(["status" => "error", "message" => "S'han produït errors en la validació", "errors" => $errors]);
        exit;
    }

    // Si no hay errores, crear las variables PHP y preparar la consulta PDO
    $causa_defuncio_ca = !empty($data['causa_defuncio_ca']) ? $data['causa_defuncio_ca'] : NULL;

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
        $lastInsertId = $conn->lastInsertId();

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        $dataHoraCanvi = date('Y-m-d H:i:s');
        $tipusOperacio = "Insert Nova causa defunció";
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
    // 5) POST CARREC EMPRESA
    // ruta POST => "/api/auxiliars/post/?type=carrec_empresa"
} elseif (isset($_GET['type']) && $_GET['type'] == 'carrec_empresa') {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['carrec_cat'])) {
        $errors[] = 'El camp ciutat és obligatori.';
    }


    // Si hay errores, devolver una respuesta con los errores
    if (!empty($errors)) {
        http_response_code(400); // Bad Request
        echo json_encode(["status" => "error", "message" => "S'han produït errors en la validació", "errors" => $errors]);
        exit;
    }

    // Si no hay errores, crear las variables PHP y preparar la consulta PDO
    $carrec_cat = !empty($data['carrec_cat']) ? $data['carrec_cat'] : NULL;

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "INSERT INTO aux_ofici_carrec (
            carrec_cat
        ) VALUES (
            :carrec_cat
        )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':carrec_cat', $carrec_cat, PDO::PARAM_STR);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $lastInsertId = $conn->lastInsertId();

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        $dataHoraCanvi = date('Y-m-d H:i:s');
        $tipusOperacio = "Insert Nou càrrec-ofici";
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
    // 6) POST SUB-SECTOR ECONOMIC
    // ruta POST => "/api/auxiliars/post/?type=sub_sector_economic"
} elseif (isset($_GET['type']) && $_GET['type'] == 'sub_sector_economic') {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['sub_sector_cat'])) {
        $errors[] = 'El camp ciutat és obligatori.';
    }


    // Si hay errores, devolver una respuesta con los errores
    if (!empty($errors)) {
        http_response_code(400); // Bad Request
        echo json_encode(["status" => "error", "message" => "S'han produït errors en la validació", "errors" => $errors]);
        exit;
    }

    // Si no hay errores, crear las variables PHP y preparar la consulta PDO
    $sub_sector_cat = !empty($data['sub_sector_cat']) ? $data['sub_sector_cat'] : NULL;

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "INSERT INTO aux_sub_sector_economic (
            sub_sector_cat
        ) VALUES (
            :sub_sector_cat
        )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':sub_sector_cat', $sub_sector_cat, PDO::PARAM_STR);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $lastInsertId = $conn->lastInsertId();

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        $dataHoraCanvi = date('Y-m-d H:i:s');
        $tipusOperacio = "Insert Nou sub-sector ecònòmic";
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
    // 6) POST PARTIT POLITIC
    // ruta POST => "/api/auxiliars/post/?type=partit_politic"
} elseif (isset($_GET['type']) && $_GET['type'] == 'partit_politic') {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['partit_politic'])) {
        $errors[] = 'El camp ciutat és obligatori.';
    }

    if (empty($data['sigles'])) {
        $errors[] = 'El camp ciutat és obligatori.';
    }

    // Si hay errores, devolver una respuesta con los errores
    if (!empty($errors)) {
        http_response_code(400); // Bad Request
        echo json_encode(["status" => "error", "message" => "S'han produït errors en la validació", "errors" => $errors]);
        exit;
    }

    // Si no hay errores, crear las variables PHP y preparar la consulta PDO
    $partit_politic = !empty($data['partit_politic']) ? $data['partit_politic'] : NULL;
    $sigles = !empty($data['sigles']) ? $data['sigles'] : NULL;

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
        $lastInsertId = $conn->lastInsertId();

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        $dataHoraCanvi = date('Y-m-d H:i:s');
        $tipusOperacio = "Insert Nou partit polític";
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
    // 6) POST SINDICAT
    // ruta POST => "/api/auxiliars/post/?type=sindicat"
} elseif (isset($_GET['type']) && $_GET['type'] == 'sindicat') {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['sindicat'])) {
        $errors[] = 'El camp ciutat és obligatori.';
    }

    if (empty($data['sigles'])) {
        $errors[] = 'El camp ciutat és obligatori.';
    }


    // Si hay errores, devolver una respuesta con los errores
    if (!empty($errors)) {
        http_response_code(400); // Bad Request
        echo json_encode(["status" => "error", "message" => "S'han produït errors en la validació", "errors" => $errors]);
        exit;
    }

    // Si no hay errores, crear las variables PHP y preparar la consulta PDO
    $sindicat = !empty($data['sindicat']) ? $data['sindicat'] : NULL;
    $sigles = !empty($data['sigles']) ? $data['sigles'] : NULL;

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
        $lastInsertId = $conn->lastInsertId();

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        $dataHoraCanvi = date('Y-m-d H:i:s');
        $tipusOperacio = "Insert Nou sindicat";
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

    // 7) POST COMARCA
    // ruta POST => "/api/auxiliars/post/?type=comarca"
} elseif (isset($_GET['type']) && $_GET['type'] == 'comarca') {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['comarca'])) {
        $errors[] = 'El camp comarca és obligatori.';
    }

    // Si hay errores, devolver una respuesta con los errores
    if (!empty($errors)) {
        http_response_code(400); // Bad Request
        echo json_encode(["status" => "error", "message" => "S'han produït errors en la validació", "errors" => $errors]);
        exit;
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
        http_response_code(409); // Conflict
        echo json_encode(["status" => "error", "message" => "La comarca ja existeix a la base de dades."]);
        exit;
    }

    // Si no hay errores, crear las variables PHP y preparar la consulta PDO
    $comarca = !empty($data['comarca']) ? $data['comarca'] : NULL;

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "INSERT INTO aux_dades_municipis_comarca (
            comarca
        ) VALUES (
            :comarca
        )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':comarca', $comarca, PDO::PARAM_STR);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $lastInsertId = "";

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        $dataHoraCanvi = date('Y-m-d H:i:s');
        $tipusOperacio = "Insert Nova comarca";
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

    // 7) POST PROVINCIA
    // ruta POST => "/api/auxiliars/post/?type=provincia"
} elseif (isset($_GET['type']) && $_GET['type'] == 'provincia') {
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
        http_response_code(400); // Bad Request
        echo json_encode(["status" => "error", "message" => "S'han produït errors en la validació", "errors" => $errors]);
        exit;
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
        http_response_code(409); // Conflict
        echo json_encode(["status" => "error", "message" => "La provincia ja existeix a la base de dades."]);
        exit;
    }

    // Si no hay errores, crear las variables PHP y preparar la consulta PDO
    $provincia = !empty($data['provincia']) ? $data['provincia'] : NULL;

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "INSERT INTO aux_dades_municipis_provincia (
            provincia
        ) VALUES (
            :provincia
        )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':provincia', $provincia, PDO::PARAM_STR);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $lastInsertId = "";

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        $dataHoraCanvi = date('Y-m-d H:i:s');
        $tipusOperacio = "Insert Nova provincia";
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

    // 7) POST COMUNITAT AUTONOMA
    // ruta POST => "/api/auxiliars/post/?type=comunitat"
} elseif (isset($_GET['type']) && $_GET['type'] == 'comunitat') {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['comunitat'])) {
        $errors[] = 'El camp comunitat és obligatori.';
    }

    // Si hay errores, devolver una respuesta con los errores
    if (!empty($errors)) {
        http_response_code(400); // Bad Request
        echo json_encode(["status" => "error", "message" => "S'han produït errors en la validació", "errors" => $errors]);
        exit;
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
        http_response_code(409); // Conflict
        echo json_encode(["status" => "error", "message" => "La comunitat ja existeix a la base de dades."]);
        exit;
    }

    // Si no hay errores, crear las variables PHP y preparar la consulta PDO
    $comunitat = !empty($data['comunitat']) ? $data['comunitat'] : NULL;

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "INSERT INTO aux_dades_municipis_comunitat (
            comunitat
        ) VALUES (
            :comunitat
        )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':comunitat', $comunitat, PDO::PARAM_STR);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $lastInsertId = "";

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        $dataHoraCanvi = date('Y-m-d H:i:s');
        $tipusOperacio = "Insert Nova Comunitat autonoma";
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

    // 8) POST ESTAT
    // ruta POST => "/api/auxiliars/post/?type=estat"
} elseif (isset($_GET['type']) && $_GET['type'] == 'estat') {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['estat'])) {
        $errors[] = 'El camp estat és obligatori.';
    }

    // Si hay errores, devolver una respuesta con los errores
    if (!empty($errors)) {
        http_response_code(400); // Bad Request
        echo json_encode(["status" => "error", "message" => "S'han produït errors en la validació", "errors" => $errors]);
        exit;
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
        http_response_code(409); // Conflict
        echo json_encode(["status" => "error", "message" => "L'estat ja existeix a la base de dades."]);
        exit;
    }

    // Si no hay errores, crear las variables PHP y preparar la consulta PDO
    $estat = !empty($data['estat']) ? $data['estat'] : NULL;

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "INSERT INTO aux_dades_municipis_estat (
            estat
        ) VALUES (
            :estat
        )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':estat', $estat, PDO::PARAM_STR);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $lastInsertId = "";

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        $dataHoraCanvi = date('Y-m-d H:i:s');
        $tipusOperacio = "Insert Nou estat";
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
}
