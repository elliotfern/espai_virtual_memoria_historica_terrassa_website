<?php

// DB_FONTS DOCUMENTALS
// 1) POST llibre
// ruta POST => "/api/fonts_documentals/post/?type=llibre"
if (isset($_GET['type']) && $_GET['type'] == 'bibliografia') {
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

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "INSERT INTO aux_bibliografia_llibres (
            llibre, idRepresaliat, pagina
        ) VALUES (
            :llibre, :idRepresaliat, :pagina
        )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':llibre', $llibre, PDO::PARAM_INT);
        $stmt->bindParam(':idRepresaliat', $idRepresaliat, PDO::PARAM_INT);
        $stmt->bindParam(':pagina', $pagina, PDO::PARAM_STR);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $lastInsertId = $idRepresaliat;

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        $dataHoraCanvi = date('Y-m-d H:i:s');
        $tipusOperacio = "Insert Nova bibliografia";
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

    // 3) POST arxivistica
    // ruta POST => "/api/auxiliars/post/?type=arxivistica"
} elseif (isset($_GET['type']) && $_GET['type'] == 'arxivistica') {
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

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "INSERT INTO aux_bibliografia_arxius (
            referencia, codi, idRepresaliat
        ) VALUES (
            :referencia, :codi, :idRepresaliat 
        )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':referencia', $referencia, PDO::PARAM_STR);
        $stmt->bindParam(':codi', $codi, PDO::PARAM_INT);
        $stmt->bindParam(':idRepresaliat', $idRepresaliat, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        $dataHoraCanvi = date('Y-m-d H:i:s');
        $tipusOperacio = "Insert Nova arxivistica";
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
        $stmt->bindParam(':idPersonaFitxa', $idRepresaliat, PDO::PARAM_INT);
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
    // POST ARXIU
} elseif (isset($_GET['type']) && $_GET['type'] == 'arxiu') {

    // Leer los datos de entrada
    $input = file_get_contents('php://input');

    // Verificar si los datos están vacíos
    if (empty($input)) {
        http_response_code(400); // Bad Request
        echo json_encode(["status" => "error", "message" => "No se recibieron datos"]);
        exit;
    }

    // Decodificar los datos JSON
    $data = json_decode($input, true);

    // Verificar si los datos se decodificaron correctamente
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400); // Bad Request
        echo json_encode(["status" => "error", "message" => "Error al decodificar los datos JSON: " . json_last_error_msg()]);
        exit;
    }

    $errors = [];
    if (empty($data['arxiu'])) {
        $errors['arxiu'] = 'El campo arxiu es obligatorio.';
    }
    if (empty($data['codi'])) {
        $errors['codi'] = 'El campo codi es obligatorio.';
    }
    if (empty($data['ciutat'])) {
        $errors['ciutat'] = 'El campo ciutat es obligatorio.';
    }
    if (empty($data['descripcio'])) {
        $errors['descripcio'] = 'El campo descripcio es obligatorio.';
    }
    if (empty($data['web'])) {
        $errors['web'] = 'El campo web es obligatorio.';
    }

    if (!empty($errors)) {
        http_response_code(400); // Bad Request
        echo json_encode(["status" => "error", "message" => "S'han produït errors en la validació", "errors" => $errors]);
        exit;
    }

    // Si no hay errores, crear las variables PHP y preparar la consulta PDO
    $arxiu = $data['arxiu'];
    $codi = $data['codi'];
    $ciutat = $data['ciutat'];
    $descripcio = $data['descripcio'];
    $web = $data['web'];

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "INSERT INTO aux_bibliografia_arxius_codis (
            arxiu, codi, ciutat, descripcio, web
        ) VALUES (
            :arxiu, :codi, :ciutat, :descripcio, :web
        )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':arxiu', $arxiu, PDO::PARAM_STR);
        $stmt->bindParam(':codi', $codi, PDO::PARAM_STR);
        $stmt->bindParam(':ciutat', $ciutat, PDO::PARAM_INT);
        $stmt->bindParam(':descripcio', $descripcio, PDO::PARAM_STR);
        $stmt->bindParam(':web', $web, PDO::PARAM_STR);

        // Ejecutar la consulta
        $stmt->execute();

        // Respuesta de éxito
        echo json_encode(["status" => "success", "message" => "Les dades s'han actualitzat correctament a la base de dades."]);
    } catch (PDOException $e) {
        // En caso de error en la conexión o ejecución de la consulta
        http_response_code(500); // Internal Server Error
        echo json_encode(["status" => "error", "message" => "S'ha produit un error a la base de dades: " . $e->getMessage()]);
    }
} else {
    // En caso de error en la conexión o ejecución de la consulta
    http_response_code(500); // Internal Server Error
    echo json_encode(["status" => "error", "message" => "S'ha produit un error a la base de dades: " . $e->getMessage()]);
}
