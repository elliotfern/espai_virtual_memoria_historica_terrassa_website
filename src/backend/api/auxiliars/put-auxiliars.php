<?php
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

// DB_DADES PERSONALS
// 1) PUT municipi
// ruta PUT => "/api/auxiliars/put/municipi"
if ($slug === "municipi") {
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

    // Si no hay errores, crear las variables PHP y preparar la consulta PDO
    $ciutat = !empty($data['ciutat']) ? $data['ciutat'] : NULL;
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
            comarca = :comarca,
            provincia = :provincia,
            comunitat = :comunitat,
            estat = :estat
        WHERE id = :id";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':ciutat', $ciutat, PDO::PARAM_STR);
        $stmt->bindParam(':comarca', $comarca, PDO::PARAM_INT);
        $stmt->bindParam(':provincia', $provincia, PDO::PARAM_INT);
        $stmt->bindParam(':comunitat', $comunitat, PDO::PARAM_INT);
        $stmt->bindParam(':estat', $estat, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $lastInsertId = $conn->lastInsertId();

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        $dataHoraCanvi = date('Y-m-d H:i:s');
        $tipusOperacio = "Update municipi";
        $idUser = $userId;

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
}
