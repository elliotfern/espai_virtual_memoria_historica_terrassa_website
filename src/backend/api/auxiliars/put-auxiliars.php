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
    $user_type          = !empty($data['user_type']) ? data_input($data['user_type']) : ($hasError = true);
    $avatar            = !empty($data['avatar']) ? data_input($data['avatar']) : ($hasError = false);
    $slug              = !empty($data['slug']) ? data_input($data['slug']) : ($hasError = false);
    $grup              = !empty($data['grup']) ? data_input($data['grup']) : ($hasError = false);
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
    $query = "UPDATE auth_users SET nom = :nom, email = :email, user_type = :user_type, avatar = :avatar, slug = :slug, grup = :grup";
    $params = [
        ':nom' => $nom,
        ':email' => $email,
        ':user_type' => $user_type,
        ':avatar' => $avatar,
        ':slug' => $slug,
        ':grup' => $grup
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
} else if ($slug === "usuari-biografia") {

    // —— Body JSON ——
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        Response::error(MissatgesAPI::error('jsonInvalid'), ['Body JSON invàlid'], 400);
    }

    $errors = [];

    // —— Helpers ——
    // Devuelve texto “plano” para validar si hay contenido real (quita etiquetas, &nbsp;, espacios…)
    function plainTextFromHtml(?string $html): string
    {
        if ($html === null) return '';
        $s = html_entity_decode((string)$html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $s = strip_tags($s);
        // elimina NBSP y comprime espacios
        $s = preg_replace('/\x{A0}/u', ' ', $s);
        $s = preg_replace('/\s+/u', ' ', $s);
        return trim($s);
    }

    // ⚠️ Debes tener implementada esta función igual que en el POST
    // - Permitir solo etiquetas/atributos de Trix que consideres seguros
    // - Normalizar estilos, eliminar scripts/eventos, etc.
    if (!function_exists('sanitizeTrixHtml')) {
        function sanitizeTrixHtml(string $html): string
        {
            // Placeholder mínimo por si acaso (reemplaza por tu implementación real)
            return $html;
        }
    }

    /** ---------- Identificador del registro ---------- **/
    $id_user = $data['id_user'] ?? null;  // recomendado (una fila por usuario)
    $id_pk   = $data['id'] ?? null;       // alternativo (PK auto-inc)

    if (($id_user === null || $id_user === '') && ($id_pk === null || $id_pk === '')) {
        $errors[] = ValidacioErrors::requerit('id_user o id');
    } else {
        if ($id_user !== null && $id_user !== '' && !is_numeric($id_user)) {
            $errors[] = ValidacioErrors::invalid('id_user');
        }
        if ($id_pk !== null && $id_pk !== '' && !is_numeric($id_pk)) {
            $errors[] = ValidacioErrors::invalid('id');
        }
    }

    // Si viene id_user, valida rango como en tu POST (0..10)
    if ($id_user !== null && $id_user !== '') {
        $id_user = (int)$id_user;
        if ($id_user < 0 || $id_user > 15) {
            $errors[] = ValidacioErrors::invalid('id_user');
        }
    }

    /** ---------- Campos actualizables ---------- **/
    // Para PUT parcial: si la clave NO viene, usamos NULL (para COALESCE conservar el valor actual)
    $hasAnyUpdatable = false;
    $getOpt = function (array $src, string $key) use (&$hasAnyUpdatable) {
        if (array_key_exists($key, $src)) {
            $hasAnyUpdatable = true;
            return $src[$key];
        }
        return null; // no enviado → null ⇒ COALESCE conserva
    };

    // ——— bio_curta_* (≤255)
    $len255 = function ($v, string $label) use (&$errors) {
        if ($v === null) return null; // no enviado
        $s = trim((string)$v);
        if (mb_strlen($s, 'UTF-8') > 255) {
            $errors[] = ValidacioErrors::massaLlarg($label, 255);
        }
        return $s; // puede ser '' para “vaciar”
    };
    $bio_curta_ca = $len255($getOpt($data, 'bio_curta_ca'), 'bio_curta_ca');
    $bio_curta_es = $len255($getOpt($data, 'bio_curta_es'), 'bio_curta_es');
    $bio_curta_en = $len255($getOpt($data, 'bio_curta_en'), 'bio_curta_en');
    $bio_curta_it = $len255($getOpt($data, 'bio_curta_it'), 'bio_curta_it');
    $bio_curta_fr = $len255($getOpt($data, 'bio_curta_fr'), 'bio_curta_fr');
    $bio_curta_pt = $len255($getOpt($data, 'bio_curta_pt'), 'bio_curta_pt');

    // ——— bio_* (Trix/HTML)
    // Si viene el campo:
    //   - Sanitizamos para guardar
    //   - Permitimos '' para “vaciar” contenido
    // Si NO viene, lo dejamos en NULL para que COALESCE conserve
    $normTrix = function ($v) {
        if ($v === null) return null;                // no enviado
        $raw = (string)$v;
        $plain = plainTextFromHtml($raw);
        if ($plain === '') {
            return '';                                // enviado “vacío” → vaciar
        }
        return sanitizeTrixHtml($raw);               // enviado con contenido → sanitizado
    };

    $bio_ca = $normTrix($getOpt($data, 'bio_ca'));
    $bio_es = $normTrix($getOpt($data, 'bio_es'));
    $bio_en = $normTrix($getOpt($data, 'bio_en'));
    $bio_fr = $normTrix($getOpt($data, 'bio_fr'));
    $bio_it = $normTrix($getOpt($data, 'bio_it'));
    $bio_pt = $normTrix($getOpt($data, 'bio_pt'));

    // —— Regla de negocio: si ENVIAS biografías largas, al menos CA o ES con “texto real” ——
    // (Si no envías ninguna de las dos, no aplicamos esta validación porque no estás cambiándolas)
    $sentCa = array_key_exists('bio_ca', $data);
    $sentEs = array_key_exists('bio_es', $data);
    if ($sentCa || $sentEs) {
        $plainCa = $sentCa ? plainTextFromHtml((string)($data['bio_ca'] ?? '')) : '';
        $plainEs = $sentEs ? plainTextFromHtml((string)($data['bio_es'] ?? '')) : '';
        if ($plainCa === '' && $plainEs === '') {
            $errors[] = "Cal escriure almenys una biografia (català o castellà).";
        }
    }

    // Si no se envía ningún campo actualizable → error
    if (!$hasAnyUpdatable) {
        $errors[] = ValidacioErrors::requerit('almenys un camp per actualitzar');
    }

    // Errores de validación
    if (!empty($errors)) {
        Response::error(MissatgesAPI::error('validacio'), $errors, 400);
    }

    /** ---------- UPDATE ---------- **/
    try {
        /** @var PDO $conn */
        global $conn;
        if (!isset($conn) || !($conn instanceof PDO)) {
            $conn = DatabaseConnection::getConnection();
        }

        $sql = "UPDATE auth_users_i18n SET
                bio_curta_ca = COALESCE(:bio_curta_ca, bio_curta_ca),
                bio_curta_es = COALESCE(:bio_curta_es, bio_curta_es),
                bio_curta_en = COALESCE(:bio_curta_en, bio_curta_en),
                bio_curta_it = COALESCE(:bio_curta_it, bio_curta_it),
                bio_curta_fr = COALESCE(:bio_curta_fr, bio_curta_fr),
                bio_curta_pt = COALESCE(:bio_curta_pt, bio_curta_pt),
                bio_ca       = COALESCE(:bio_ca,       bio_ca),
                bio_es       = COALESCE(:bio_es,       bio_es),
                bio_en       = COALESCE(:bio_en,       bio_en),
                bio_fr       = COALESCE(:bio_fr,       bio_fr),
                bio_it       = COALESCE(:bio_it,       bio_it),
                bio_pt       = COALESCE(:bio_pt,       bio_pt)
            WHERE " . (
            ($id_user !== null && $id_user !== '') ? "id_user = :id_user" : "id = :id_pk"
        );

        $stmt = $conn->prepare($sql);

        // Bind de los “curta” (NULL → conservar; ''/texto → actualizar)
        $bindStrNull = function (PDOStatement $st, string $param, $val): void {
            $st->bindValue($param, $val, $val === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        };
        $bindStrNull($stmt, ':bio_curta_ca', $bio_curta_ca);
        $bindStrNull($stmt, ':bio_curta_es', $bio_curta_es);
        $bindStrNull($stmt, ':bio_curta_en', $bio_curta_en);
        $bindStrNull($stmt, ':bio_curta_it', $bio_curta_it);
        $bindStrNull($stmt, ':bio_curta_fr', $bio_curta_fr);
        $bindStrNull($stmt, ':bio_curta_pt', $bio_curta_pt);

        // Bind de las Trix/HTML ya sanitizadas
        $bindStrNull($stmt, ':bio_ca', $bio_ca);
        $bindStrNull($stmt, ':bio_es', $bio_es);
        $bindStrNull($stmt, ':bio_en', $bio_en);
        $bindStrNull($stmt, ':bio_fr', $bio_fr);
        $bindStrNull($stmt, ':bio_it', $bio_it);
        $bindStrNull($stmt, ':bio_pt', $bio_pt);

        if ($id_user !== null && $id_user !== '') {
            $stmt->bindValue(':id_user', (int)$id_user, PDO::PARAM_INT);
        } else {
            $stmt->bindValue(':id_pk', (int)$id_pk, PDO::PARAM_INT);
        }

        $stmt->execute();
        $rows = $stmt->rowCount();

        // Audit
        $userId = $GLOBALS['userId'] ?? null;
        if (class_exists('Audit')) {
            $detalls = "Actualització biografia d'usuari (i18n)";
            $tipusOperacio = "UPDATE";
            $tableName = defined('Tables::DB_AUTH_USERS_I18N') ? Tables::DB_AUTH_USERS_I18N : 'auth_users_i18n';
            $auditId = $id_pk ?? $id_user;

            Audit::registrarCanvi(
                $conn,
                (int)($userId ?? 0),
                $tipusOperacio,
                $detalls,
                $tableName,
                (int)$auditId
            );
        }

        if ($rows === 0) {
            // No cambios (mismos valores) o no existe
            Response::success(MissatgesAPI::success('noCanvis'), ['updated' => 0], 200);
            exit;
        }

        Response::success(
            MissatgesAPI::success('update'),
            [
                'updated' => $rows,
                'where'   => ($id_user !== null && $id_user !== '') ? ['id_user' => (int)$id_user] : ['id' => (int)$id_pk]
            ],
            200
        );
    } catch (PDOException $e) {
        Response::error(MissatgesAPI::error('errorBD'), [$e->getMessage()], 500);
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
    $carrec_es = !empty($data['carrec_es']) ? $data['carrec_es'] : NULL;
    $carrec_en = !empty($data['carrec_en']) ? $data['carrec_en'] : NULL;
    $carrec_fr = !empty($data['carrec_fr']) ? $data['carrec_fr'] : NULL;
    $carrec_pt = !empty($data['carrec_pt']) ? $data['carrec_pt'] : NULL;
    $carrec_it = !empty($data['carrec_it']) ? $data['carrec_it'] : NULL;
    $id = $data['id'];

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "UPDATE aux_ofici_carrec SET 
            carrec_cat = :carrec_cat,
            carrec_en = :carrec_en,
            carrec_es = :carrec_es,
            carrec_pt = :carrec_pt,
            carrec_fr = :carrec_fr,
            carrec_it = :carrec_it
        WHERE id = :id";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':carrec_cat', $carrec_cat, PDO::PARAM_STR);
        $stmt->bindParam(':carrec_en', $carrec_en, PDO::PARAM_STR);
        $stmt->bindParam(':carrec_es', $carrec_es, PDO::PARAM_STR);
        $stmt->bindParam(':carrec_fr', $carrec_fr, PDO::PARAM_STR);
        $stmt->bindParam(':carrec_it', $carrec_it, PDO::PARAM_STR);
        $stmt->bindParam(':carrec_pt', $carrec_pt, PDO::PARAM_STR);
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
    if (empty($data['ofici_ca'])) {
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
    $ofici_ca = $data['ofici_ca'];
    $ofici_es = !empty($data['ofici_es']) ? $data['ofici_es'] : NULL;
    $ofici_en = !empty($data['ofici_en']) ? $data['ofici_en'] : NULL;
    $ofici_fr  = !empty($data['ofici_fr'])  ? $data['ofici_fr']  : NULL;
    $ofici_it  = !empty($data['ofici_it'])  ? $data['ofici_it']  : NULL;
    $ofici_pt  = !empty($data['ofici_pt'])  ? $data['ofici_pt']  : NULL;
    $id = $data['id'];

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "UPDATE aux_oficis SET 
            ofici_ca = :ofici_ca,
            ofici_es = :ofici_es,
            ofici_en = :ofici_en
            ofici_fr =:ofici_fr,
            ofici_it =:ofici_it,
            ofici_pt =:ofici_pt
        WHERE id = :id";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':ofici_ca', $ofici_ca, PDO::PARAM_STR);
        $stmt->bindParam(':ofici_es', $ofici_es, PDO::PARAM_STR);
        $stmt->bindParam(':ofici_en', $ofici_en, PDO::PARAM_STR);
        $stmt->bindParam(':ofici_fr',  $ofici_fr,  PDO::PARAM_STR);
        $stmt->bindParam(':ofici_it',  $ofici_it,  PDO::PARAM_STR);
        $stmt->bindParam(':ofici_pt',  $ofici_pt,  PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $tipusOperacio = "UPDATR";
        $detalls =  "Modificació ofici: " . $ofici_ca;

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
    $sub_sector_es = !empty($data['sub_sector_es']) ? $data['sub_sector_es'] : NULL;
    $sub_sector_en = !empty($data['sub_sector_en']) ? $data['sub_sector_en'] : NULL;
    $sub_sector_it = !empty($data['sub_sector_it']) ? $data['sub_sector_it'] : NULL;
    $sub_sector_fr = !empty($data['sub_sector_fr']) ? $data['sub_sector_fr'] : NULL;
    $sub_sector_pt = !empty($data['sub_sector_pt']) ? $data['sub_sector_pt'] : NULL;
    $idSector = !empty($data['idSector']) ? $data['idSector'] : NULL;
    $id = $data['id'];

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        $sql = "UPDATE aux_sub_sector_economic SET 
            sub_sector_cat = :sub_sector_cat,
            sub_sector_en = :sub_sector_en,
            idSector = :idSector,
            sub_sector_es = :sub_sector_es,
            sub_sector_pt = :sub_sector_pt,
            sub_sector_fr = :sub_sector_fr,
            sub_sector_it = :sub_sector_it
        WHERE id = :id";

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
    $categoria_es = !empty($data['categoria_es']) ? $data['categoria_es'] : NULL;
    $categoria_en = !empty($data['categoria_en']) ? $data['categoria_en'] : NULL;
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
            categoria_es = :categoria_es,
            categoria_en = :categoria_en,
            categoria_fr = :categoria_fr,
            categoria_it = :categoria_it,
            categoria_pt = :categoria_pt,
            grup = :grup
        WHERE id = :id";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':categoria_cat', $categoria_cat, PDO::PARAM_STR);
        $stmt->bindParam(':categoria_es', $categoria_es, PDO::PARAM_STR);
        $stmt->bindParam(':categoria_en', $categoria_en, PDO::PARAM_STR);
        $stmt->bindParam(':categoria_fr', $categoria_fr, PDO::PARAM_STR);
        $stmt->bindParam(':categoria_it', $categoria_it, PDO::PARAM_STR);
        $stmt->bindParam(':categoria_pt', $categoria_pt, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':grup', $grup, PDO::PARAM_INT);

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

    // RUTA PUT : Acusació judicial
    // PUTT => https://memoriaterrassa.cat/api/auxiliars/put/acusacio   
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

    if (empty($data['id'])) {
        $errors[] =  ValidacioErrors::requerit('Id del registre');
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
    $id = $data['id'];

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "UPDATE aux_acusacions SET
            acusacio_ca = :acusacio_ca,
            acusacio_es = :acusacio_es,
            acusacio_en = :acusacio_en,
            acusacio_fr = :acusacio_fr,
            acusacio_pt = :acusacio_pt,
            acusacio_it = :acusacio_it
        WHERE id = :id";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':acusacio_ca', $acusacio_ca, PDO::PARAM_STR);
        $stmt->bindParam(':acusacio_es', $acusacio_es, PDO::PARAM_STR);
        $stmt->bindParam(':acusacio_en', $acusacio_en, PDO::PARAM_STR);
        $stmt->bindParam(':acusacio_fr', $acusacio_fr, PDO::PARAM_STR);
        $stmt->bindParam(':acusacio_pt', $acusacio_pt, PDO::PARAM_STR);
        $stmt->bindParam(':acusacio_it', $acusacio_it, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $tipusOperacio = "UPDATE";
        $detalls =  "Modificació acusació judicial: " . $acusacio_ca;

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

    // RUTA PUT : Bàndol guerra civil
    // PUT => https://memoriaterrassa.cat/api/auxiliars/put/bandol   
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
    $id =  $data['id'];

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "UPDATE aux_bandol SET
            bandol_ca = :bandol_ca,
            bandol_es = :bandol_es,
            bandol_en = :bandol_en,
            bandol_it = :bandol_it,
            bandol_fr = :bandol_fr,
            bandol_pt = :bandol_pt
        WHERE id = :id";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':bandol_ca', $bandol_ca, PDO::PARAM_STR);
        $stmt->bindParam(':bandol_es', $bandol_es, PDO::PARAM_STR);
        $stmt->bindParam(':bandol_en', $bandol_en, PDO::PARAM_STR);
        $stmt->bindParam(':bandol_fr', $bandol_fr, PDO::PARAM_STR);
        $stmt->bindParam(':bandol_pt', $bandol_pt, PDO::PARAM_STR);
        $stmt->bindParam(':bandol_it', $bandol_it, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $tipusOperacio = "UPDATE";
        $detalls =  "Modificació bàndol guerra civil: " . $bandol_ca;

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

    // 4) PUT causa_mort
    // ruta PUT  => "/api/auxiliars/put/causa_mort"
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
    $id                = $data['id'];

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "UPDATE aux_causa_defuncio SET
            causa_defuncio_ca = :causa_defuncio_ca,
            causa_defuncio_es = :causa_defuncio_es,
            causa_defuncio_en = :causa_defuncio_en,
            causa_defuncio_fr = :causa_defuncio_fr,
            causa_defuncio_pt = :causa_defuncio_pt,
            causa_defuncio_it = :causa_defuncio_it,
            cat = :cat
        WHERE id = :id";

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
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $tipusOperacio = "UPDATE";
        $detalls =  "Modificació causa de defunció " . $causa_defuncio_ca;

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

    // 4) PUT condicio_militar
    // ruta PUT => "/api/auxiliars/put/condicio_militar"
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
    $id = $data['id'];

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "UPDATE aux_condicio SET
            condicio_ca = :condicio_ca,
            condicio_es = :condicio_es,
            condicio_en = :condicio_en,
            condicio_fr = :condicio_fr,
            condicio_it = :condicio_it,
            condicio_pt = :condicio_pt
        WHERE id = :id";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':condicio_ca', $condicio_ca, PDO::PARAM_STR);
        $stmt->bindParam(':condicio_es', $condicio_es, PDO::PARAM_STR);
        $stmt->bindParam(':condicio_en', $condicio_en, PDO::PARAM_STR);
        $stmt->bindParam(':condicio_fr', $condicio_fr, PDO::PARAM_STR);
        $stmt->bindParam(':condicio_it', $condicio_it, PDO::PARAM_STR);
        $stmt->bindParam(':condicio_pt', $condicio_pt, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $tipusOperacio = "UPDATE";
        $detalls =  "Modificació condició militar: " . $condicio_ca;

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

    // 4) PUT cos militar
    // ruta PUT => "/api/auxiliars/put/cos_militar"
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
    $id = $data['id'];

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "UPDATE aux_cossos_militars SET
            cos_militar_ca = :cos_militar_ca,
            cos_militar_es = :cos_militar_es,
            cos_militar_en = :cos_militar_en,
            cos_militar_fr = :cos_militar_fr,
            cos_militar_it = :cos_militar_it,
            cos_militar_pt = :cos_militar_pt
        WHERE id = :id";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':cos_militar_ca', $cos_militar_ca, PDO::PARAM_STR);
        $stmt->bindParam(':cos_militar_es', $cos_militar_es, PDO::PARAM_STR);
        $stmt->bindParam(':cos_militar_en', $cos_militar_en, PDO::PARAM_STR);
        $stmt->bindParam(':cos_militar_fr', $cos_militar_fr, PDO::PARAM_STR);
        $stmt->bindParam(':cos_militar_it', $cos_militar_it, PDO::PARAM_STR);
        $stmt->bindParam(':cos_militar_pt', $cos_militar_pt, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();


        // Recuperar el ID del registro creado
        $tipusOperacio = "UPDATE";
        $detalls =  "Modificació cos militar: " . $cos_militar_ca;

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

    // 4) PUT espai execucio
    // ruta PUT => "/api/auxiliars/put/espai"
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
    $espai_es = !empty($data['espai_es']) ? $data['espai_es'] : NULL;
    $espai_en = !empty($data['espai_en']) ? $data['espai_en'] : NULL;
    $espai_fr = !empty($data['espai_fr']) ? $data['espai_fr'] : NULL;
    $espai_it = !empty($data['espai_it']) ? $data['espai_it'] : NULL;
    $espai_pt = !empty($data['espai_pt']) ? $data['espai_pt'] : NULL;
    $descripcio_espai = !empty($data['descripcio_espai']) ? $data['descripcio_espai'] : NULL;
    $municipi = !empty($data['municipi']) ? $data['municipi'] : NULL;
    $id = $data['id'];

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "UPDATE aux_espai SET
            espai_cat = :espai_cat,
            espai_es = :espai_es,
            espai_en = :espai_en,
            espai_fr = :espai_fr,
            espai_it = :espai_it,
            espai_pt = :espai_pt,
            descripcio_espai = :descripcio_espai,
            municipi = :municipi
        WHERE id = :id";

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
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();


        // Recuperar el ID del registro creado
        $tipusOperacio = "UPDATE";
        $detalls =  "Modificació espai: " . $espai_cat;

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

    // 4) PUT estat civil
    // ruta PUT => "/api/auxiliars/put/estat_civil"
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
    $id  = $data['id'];

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "UPDATE aux_estat_civil SET
            estat_cat = :estat_cat,
            estat_es  = :estat_es,
            estat_en  = :estat_en,
            estat_fr  = :estat_fr,
            estat_it  = :estat_it,
            estat_pt  = :estat_pt
        WHERE id = :id";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':estat_cat', $estat_cat, PDO::PARAM_STR);
        $stmt->bindParam(':estat_es', $estat_es, PDO::PARAM_STR);
        $stmt->bindParam(':estat_en', $estat_en, PDO::PARAM_STR);
        $stmt->bindParam(':estat_fr', $estat_fr, PDO::PARAM_STR);
        $stmt->bindParam(':estat_it', $estat_it, PDO::PARAM_STR);
        $stmt->bindParam(':estat_pt', $estat_pt, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $tipusOperacio = "UPDATE";
        $detalls =  "Modificació d'estat civil: " . $estat_cat;

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

    // 4) PUT Nivell d'estudis
    // ruta PUT => "/api/auxiliars/put/nivell_estudis"
} else if ($slug === "nivell_estudis") {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['estudi_ca'])) {
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
    $estudi_ca = $data['estudi_ca'];
    $estudi_es  = !empty($data['estudi_es'])  ? $data['estudi_es']  : NULL;
    $estudi_en  = !empty($data['estudi_en'])  ? $data['estudi_en']  : NULL;
    $estudi_it  = !empty($data['estudi_it'])  ? $data['estudi_it']  : NULL;
    $estudi_fr  = !empty($data['estudi_fr'])  ? $data['estudi_fr']  : NULL;
    $estudi_pt  = !empty($data['estudi_pt'])  ? $data['estudi_pt']  : NULL;
    $id  = $data['id'];

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "UPDATE aux_estudis SET
            estudi_ca = :estudi_ca,
            estudi_es  = :estudi_es,
            estudi_en  = :estudi_en,
            estudi_it  = :estudi_it,
            estudi_fr  = :estudi_fr,
            estudi_pt  = :estudi_pt
        WHERE id = :id";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':estudi_ca', $estudi_ca, PDO::PARAM_STR);
        $stmt->bindParam(':estudi_es',  $estudi_es,  PDO::PARAM_STR);
        $stmt->bindParam(':estudi_en',  $estudi_en,  PDO::PARAM_STR);
        $stmt->bindParam(':estudi_it',  $estudi_it,  PDO::PARAM_STR);
        $stmt->bindParam(':estudi_fr',  $estudi_fr,  PDO::PARAM_STR);
        $stmt->bindParam(':estudi_pt',  $estudi_pt,  PDO::PARAM_STR);
        $stmt->bindParam(':id',         $id,         PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $tipusOperacio = "UPDATE";
        $detalls =  "Modificació nivell d'estudis: " . $estudi_ca;

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

    // 4) PUT sector econòmic
    // ruta PUT => "/api/auxiliars/put/sector_economic"
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
    $id = $data['id'];

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "UPDATE aux_sector_economic SET 
            sector_cat = :sector_cat,
            sector_es  = :sector_es,
            sector_en  = :sector_en,
            sector_fr  = :sector_fr,
            sector_it  = :sector_it,
            sector_pt  = :sector_pt
        WHERE id = :id";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':sector_cat', $sector_cat, PDO::PARAM_STR);
        $stmt->bindParam(':sector_es', $sector_es, PDO::PARAM_STR);
        $stmt->bindParam(':sector_en', $sector_en, PDO::PARAM_STR);
        $stmt->bindParam(':sector_fr', $sector_fr, PDO::PARAM_STR);
        $stmt->bindParam(':sector_it', $sector_it, PDO::PARAM_STR);
        $stmt->bindParam(':sector_pt', $sector_pt, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $tipusOperacio = "UPDATE";
        $detalls =  "Modificació de sector econòmic: " . $sector_cat;

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

    // 4) PUT Empresa
    // ruta PUT => "/api/auxiliars/post/empresa"
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
    $id = $data['id'];

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "UPDATE aux_empreses SET
            empresa_ca = :empresa_ca,
            empresa_es = :empresa_es,
            empresa_en = :empresa_en,
            empresa_fr = :empresa_fr,
            empresa_it = :empresa_it,
            empresa_pt = :empresa_pt
        WHERE id = :id";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':empresa_ca', $empresa_ca, PDO::PARAM_STR);
        $stmt->bindParam(':empresa_es', $empresa_es, PDO::PARAM_STR);
        $stmt->bindParam(':empresa_en', $empresa_en, PDO::PARAM_STR);
        $stmt->bindParam(':empresa_fr', $empresa_fr, PDO::PARAM_STR);
        $stmt->bindParam(':empresa_it', $empresa_it, PDO::PARAM_STR);
        $stmt->bindParam(':empresa_pt', $empresa_pt, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $tipusOperacio = "UPDATE";
        $detalls =  "Modificació empresa: " . $empresa_ca;

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

    // 4) PUT TIpus procediment judicial
    // ruta PUT => "/api/auxiliars/post/procedimentJudicial"
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
    $id = $data['id'];

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "UPDATE aux_procediment_judicial
            SET
                procediment_ca = :procediment_ca,
                procediment_es = :procediment_es,
                procediment_en = :procediment_en
            WHERE id = :id";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':procediment_ca', $procediment_ca, PDO::PARAM_STR);
        $stmt->bindParam(':procediment_es', $procediment_es, PDO::PARAM_STR);
        $stmt->bindParam(':procediment_en', $procediment_en, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $tipusOperacio = "UPDATE";
        $detalls =  "Modificació tipus de procediment judicial: " . $procediment_ca;

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

    // 4) PUT Tipus judici
    // ruta PUT => "/api/auxiliars/post/tipusJudici"
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
    $id  = $data['id'];
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
        $sql = "UPDATE aux_tipus_judici SET
            tipusJudici_ca = :tipusJudici_ca,
            tipusJudici_es = :tipusJudici_es,
            tipusJudici_en = :tipusJudici_en,
            tipusJudici_fr = :tipusJudici_fr,
            tipusJudici_it = :tipusJudici_it,
            tipusJudici_pt = :tipusJudici_pt
        WHERE id = :id";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':tipusJudici_ca', $tipusJudici_ca, PDO::PARAM_STR);
        $stmt->bindParam(':tipusJudici_es', $tipusJudici_es, PDO::PARAM_STR);
        $stmt->bindParam(':tipusJudici_en', $tipusJudici_en, PDO::PARAM_STR);
        $stmt->bindParam(':tipusJudici_fr', $tipusJudici_fr, PDO::PARAM_STR);
        $stmt->bindParam(':tipusJudici_it', $tipusJudici_it, PDO::PARAM_STR);
        $stmt->bindParam(':tipusJudici_pt', $tipusJudici_pt, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $tipusOperacio = "UPDATE";
        $detalls =  "Modificació tipus de judici: " . $tipusJudici_ca;

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

    // 4) PUT Sentències
    // ruta PUT => "/api/auxiliars/put/sentencia"
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
    $id = $data['id'];

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "UPDATE aux_sentencies SET
            sentencia_ca = :sentencia_ca,
            sentencia_es = :sentencia_es,
            sentencia_en = :sentencia_en,
            sentencia_fr = :sentencia_fr,
            sentencia_it = :sentencia_it,
            sentencia_pt = :sentencia_pt
        WHERE id = :id";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':sentencia_ca', $sentencia_ca, PDO::PARAM_STR);
        $stmt->bindParam(':sentencia_es', $sentencia_es, PDO::PARAM_STR);
        $stmt->bindParam(':sentencia_en', $sentencia_en, PDO::PARAM_STR);
        $stmt->bindParam(':sentencia_fr', $sentencia_fr, PDO::PARAM_STR);
        $stmt->bindParam(':sentencia_it', $sentencia_it, PDO::PARAM_STR);
        $stmt->bindParam(':sentencia_pt', $sentencia_pt, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $tipusOperacio = "UPDATE";
        $detalls =  "Modificació sentència: " . $sentencia_ca;

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

    // 4) PUT Pena
    // ruta PUT => "/api/auxiliars/put/pena"
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
    $id = $data['id'];

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        // Crear la consulta SQL
        $sql = "UPDATE aux_penes SET
            pena_ca = :pena_ca,
            pena_es = :pena_es,
            pena_en = :pena_en,
            pena_it = :pena_it,
            pena_fr = :pena_fr,
            pena_pt = :pena_pt
        WHERE id = :id";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':pena_ca', $pena_ca, PDO::PARAM_STR);
        $stmt->bindParam(':pena_es', $pena_es, PDO::PARAM_STR);
        $stmt->bindParam(':pena_en', $pena_en, PDO::PARAM_STR);
        $stmt->bindParam(':pena_it', $pena_it, PDO::PARAM_STR);
        $stmt->bindParam(':pena_fr', $pena_fr, PDO::PARAM_STR);
        $stmt->bindParam(':pena_pt', $pena_pt, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $tipusOperacio = "UPDATE";
        $detalls =  "Modificació pena: " . $pena_ca;

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


    // 4) PUT Jutjats
    // ruta PUT => "/api/auxiliars/put/jutjat"
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
    $id = $data['id'];
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
        $sql = "UPDATE aux_jutjats SET
            jutjat_ca = :jutjat_ca,
            jutjat_es = :jutjat_es,
            jutjat_en = :jutjat_en,
            jutjat_fr = :jutjat_fr,
            jutjat_it = :jutjat_it,
            jutjat_pt = :jutjat_pt
        WHERE id = :id";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':jutjat_ca', $jutjat_ca, PDO::PARAM_STR);
        $stmt->bindParam(':jutjat_es', $jutjat_es, PDO::PARAM_STR);
        $stmt->bindParam(':jutjat_en', $jutjat_en, PDO::PARAM_STR);
        $stmt->bindParam(':jutjat_fr', $jutjat_fr, PDO::PARAM_STR);
        $stmt->bindParam(':jutjat_it', $jutjat_it, PDO::PARAM_STR);
        $stmt->bindParam(':jutjat_pt', $jutjat_pt, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $tipusOperacio = "UPDATE";
        $detalls =  "Modificació seu judicial: " . $jutjat_ca;

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

    // 4) PUT Modalitats preso
    // ruta PUT => "/api/auxiliars/put/modalitatPreso"
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
    $id = $data['id'];

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        $sql = "UPDATE aux_modalitat_preso SET
            modalitat_ca = :modalitat_ca,
            modalitat_es = :modalitat_es,
            modalitat_en = :modalitat_en,
            modalitat_fr = :modalitat_fr,
            modalitat_it = :modalitat_it,
            modalitat_pt = :modalitat_pt
        WHERE id = :id";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':modalitat_ca', $modalitat_ca, PDO::PARAM_STR);
        $stmt->bindParam(':modalitat_es', $modalitat_es, PDO::PARAM_STR);
        $stmt->bindParam(':modalitat_en', $modalitat_en, PDO::PARAM_STR);
        $stmt->bindParam(':modalitat_fr', $modalitat_fr, PDO::PARAM_STR);
        $stmt->bindParam(':modalitat_it', $modalitat_it, PDO::PARAM_STR);
        $stmt->bindParam(':modalitat_pt', $modalitat_pt, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $tipusOperacio = "UPDATE";
        $detalls =  "Modificació modalitat de presó: " . $modalitat_ca;

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

    // 4) PUT Motius de detencio/empresonament
    // ruta PUT => "/api/auxiliars/put/motiuEmpresonament"
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
    $id = $data['id'];

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        $sql = "UPDATE aux_motius_empresonament SET
            motiuEmpresonament_ca = :motiuEmpresonament_ca,
            motiuEmpresonament_es = :motiuEmpresonament_es,
            motiuEmpresonament_en = :motiuEmpresonament_en,
            motiuEmpresonament_fr = :motiuEmpresonament_fr,
            motiuEmpresonament_it = :motiuEmpresonament_it,
            motiuEmpresonament_pt = :motiuEmpresonament_pt
        WHERE id = :id";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':motiuEmpresonament_ca', $motiuEmpresonament_ca, PDO::PARAM_STR);
        $stmt->bindParam(':motiuEmpresonament_es', $motiuEmpresonament_es, PDO::PARAM_STR);
        $stmt->bindParam(':motiuEmpresonament_en', $motiuEmpresonament_en, PDO::PARAM_STR);
        $stmt->bindParam(':motiuEmpresonament_fr', $motiuEmpresonament_fr, PDO::PARAM_STR);
        $stmt->bindParam(':motiuEmpresonament_it', $motiuEmpresonament_it, PDO::PARAM_STR);
        $stmt->bindParam(':motiuEmpresonament_pt', $motiuEmpresonament_pt, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $tipusOperacio = "UPDATE";
        $detalls =  "Modificació motiu d'empresonament: " . $motiuEmpresonament_ca;

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

    // 4) PUT Grups de repressió
    // ruta PUT => "/api/auxiliars/put/grupRepressio"
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
    $id = $data['id'];

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        $sql = "UPDATE aux_sistema_repressiu
        SET carrec = :carrec,
            nom_institucio = :nom_institucio,
            grup_institucio = :grup_institucio
        WHERE id = :id";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':carrec', $carrec, PDO::PARAM_STR);
        $stmt->bindParam(':nom_institucio', $nom_institucio, PDO::PARAM_STR);
        $stmt->bindParam(':grup_institucio', $grup_institucio, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $tipusOperacio = "UPDATE";
        $detalls =  "Modificació grup de repressió: " . $nom_institucio;

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

    // 4) PUT Presons
    // ruta PUT => "/api/auxiliars/put/preso"
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
    $id = $data['id'];

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        $sql = "UPDATE aux_presons SET
            nom_preso = :nom_preso,
            municipi_preso = :municipi_preso,
            nom_preso_es = :nom_preso_es,
            nom_preso_en = :nom_preso_en,
            nom_preso_fr = :nom_preso_fr,
            nom_preso_it = :nom_preso_it,
            nom_preso_pt = :nom_preso_pt
        WHERE id = :id";

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
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $tipusOperacio = "UPDATE";
        $detalls =  "Modificació de nova presó: " . $nom_preso;

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

    // 4) PUT Presons / camps detencio
    // ruta PUT => "/api/auxiliars/post/presoDetencio"
} else if ($slug === "presoDetencio") {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['nom'])) {
        $errors[] =  ValidacioErrors::requerit('nom presó en català');
    }

    // Validación de los datos recibidos
    if (empty($data['id'])) {
        $errors[] =  ValidacioErrors::requerit('id');
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
    $id       = $data['id'];
    $municipi  = !empty($data['municipi']) ? $data['municipi'] : null;

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        $sql = "UPDATE aux_deportacio_preso
        SET 
            tipus = :tipus,
            nom = :nom,
            municipi = :municipi
        WHERE id = :id";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':tipus', $tipus, PDO::PARAM_INT);
        $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindParam(':municipi', $municipi, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $tipusOperacio = "UPDATE";
        $detalls =  "Modificació de presó/camp de detenció: " . $nom;

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

    // 4) PUT Camp concentracio
    // ruta PUT => "/api/auxiliars/put/campConcentracio"
} else if ($slug === "campConcentracio") {
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Inicializar un array para los errores
    $errors = [];

    // Validación de los datos recibidos
    if (empty($data['nom'])) {
        $errors[] =  ValidacioErrors::requerit('nom camp en català');
    }

    // Validación de los datos recibidos
    if (empty($data['id'])) {
        $errors[] =  ValidacioErrors::requerit('id');
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
    $id       = $data['id'];
    $municipi  = !empty($data['municipi']) ? $data['municipi'] : null;

    // Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
    try {

        global $conn;
        /** @var PDO $conn */

        $sql = "UPDATE aux_camps_concentracio
        SET 
            tipus = :tipus,
            nom = :nom,
            municipi = :municipi
        WHERE id = :id";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':tipus', $tipus, PDO::PARAM_INT);
        $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindParam(':municipi', $municipi, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Recuperar el ID del registro creado
        $tipusOperacio = "UPDATE";
        $detalls =  "Modificació camp de concentracio: " . $nom;

        // Si la inserció té èxit, cal registrar la inserció en la base de control de canvis

        Audit::registrarCanvi(
            $conn,
            $userId,                      // ID del usuario que hace el cambio
            $tipusOperacio,             // Tipus operacio
            $detalls,                       // Descripción de la operación
            Tables::AUX_CAMPS_CONCENTRACIO,  // Nombre de la tabla afectada
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

    // Fi endpoints   
} else {
    Response::error(
        MissatgesAPI::error('errorEndPoint'),
        [],
        500
    );
}
