<?php

$slug = $routeParams[0];

use Firebase\JWT\JWT;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Config\DatabaseConnection;
use App\Utils\MissatgesAPI;

$conn = DatabaseConnection::getConnection();

if ($conn === null) {
    http_response_code(500);
    echo json_encode(['error' => 'No se pudo conectar a la base de datos']);
    exit;
}

if (!$conn) {
    die("No se pudo establecer conexión a la base de datos.");
}

// Cargar variables de entorno desde .env
$jwtSecret = $_ENV['TOKEN'];
$emailPass = $_ENV['EMAIL_PASS'];

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


// POST : procés de login a l'àrea privada
// URL: https://memoriaterrassa.cat/api/auth/post/login
if ($slug === "login") {
    // Obtener el cuerpo de la solicitud
    $data = json_decode(file_get_contents('php://input'), true); // Decodifica el JSON

    // Asegúrate de que las variables están definidas
    $username = isset($data['userName']) ? $data['userName'] : null;
    $password = isset($data['password']) ? $data['password'] : null;

    if (empty($username) || empty($password)) {
        $response['status'] = 'error';
        $response['message'] = 'El camp email i password són obligatoris.';
        echo json_encode($response);
        exit;
    }

    $query = "SELECT u.id, u.email, u.password, u.user_type, u.nom, i.nomArxiu
              FROM auth_users AS u
              INNER JOIN aux_imatges AS i ON u.avatar = i.id
              WHERE u.email = :email";
    $stmt = $conn->prepare($query);
    $stmt->execute(['email' => $username]);

    if ($stmt->rowCount() === 0) {
        $response['status'] = 'error';
        $response['message'] = 'Usuari no trobat.';
        echo json_encode($response);
        exit;
    }

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $hash = $row['password'];
    $id = $row['id'];
    $userType = $row['user_type'];
    $nom = $row['nom'];
    $avatar = $row['nomArxiu'];

    if (password_verify($password, $hash) && in_array($userType, [1, 2, 3, 4])) {
        session_start();
        $idUser = $id;

        $key = $jwtSecret;
        $algorithm = "HS256";  // Elige el algoritmo adecuado para tu aplicación
        $payload = array(
            "user_id" =>  $id,
            "username" => $nom,
            "user_type" => $userType,
            "avatar" => $avatar,
            'iat' => time(),
            'exp' => time() + 604800,
            "kid" => "key_api"
        );

        // Encode headers in the JWT string
        $jwt = JWT::encode($payload, $key, $algorithm);

        // Preparar la respuesta
        $response = array(
            "status" => "success",
            "message" => MissatgesAPI::success('loginOk'),
        );

        $cookie_options = array(
            'expires' => time() + 604800,
            'path' => '/',
            'domain' => 'memoriaterrassa.cat',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        );

        // Establecer las cookies
        setcookie('token', $jwt, $cookie_options);

        // Si la inserció té èxit, cal registrar acces usuari en la base de control de acces
        $dataAcces = date('Y-m-d H:i:s');
        $idUser = $idUser;
        $tipusOperacio = 1;

        // Crear la consulta SQL
        $sql2 = "INSERT INTO auth_users_control_acces (
        idUser, dataAcces, tipusOperacio
        ) VALUES (
        :idUser, :dataAcces, :tipusOperacio
        )";

        // Preparar la consulta
        $stmt = $conn->prepare($sql2);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
        $stmt->bindParam(':tipusOperacio', $tipusOperacio, PDO::PARAM_INT);
        $stmt->bindParam(':dataAcces', $dataAcces, PDO::PARAM_STR);

        // Ejecutar la consulta
        $stmt->execute();

        // Si la inserció té èxit, cal registrar acces usuari en la base de control de acces
        $dataAcces = date('Y-m-d H:i:s');
        $idUser = $id;
        $tipusOperacio = 2;

        // Crear la consulta SQL
        $sql2 = "INSERT INTO auth_users_control_acces (
                idUser, dataAcces, tipusOperacio
                ) VALUES (
                :idUser, :dataAcces, :tipusOperacio
                )";

        // Preparar la consulta
        $stmt2 = $conn->prepare($sql2);

        // Enlazar los parámetros con los valores de las variables PHP
        $stmt2->bindParam(':idUser', $idUser, PDO::PARAM_INT);
        $stmt2->bindParam(':tipusOperacio', $tipusOperacio, PDO::PARAM_INT);
        $stmt2->bindParam(':dataAcces', $dataAcces, PDO::PARAM_STR);

        // Ejecutar la consulta
        $stmt2->execute();

        // Devolver la respuesta JSON
        echo json_encode($response);

        exit;
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Usuari no autoritzat o contrasenya incorrecta.';
        echo json_encode($response);
        exit;
    }

    // POST : procés de recuperació contrasenya
    // URL: https://memoriaterrassa.cat/api/auth/post/recuperacioPassword
} else if ($slug === "recuperacioPassword") {
    $data = json_decode(file_get_contents("php://input"), true);
    $email = trim($data['email'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Aquest correu no és vàlid']);
        exit;
    }

    global $conn;
    /** @var PDO $conn */

    // Verificar si el usuario existe
    $stmt = $conn->prepare("SELECT id FROM auth_users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $token = bin2hex(random_bytes(32));
        $tokenHash = password_hash($token, PASSWORD_DEFAULT);
        $expires = date('Y-m-d H:i:s', time() + 900); // 1 hora

        // Eliminar tokens antiguos (opcional pero recomendado)
        $conn->prepare("DELETE FROM auth_users_password_resets WHERE email = ?")->execute([$email]);

        // Insertar el nuevo token
        $stmt = $conn->prepare("INSERT INTO auth_users_password_resets (email, token_hash, expires_at) VALUES (?, ?, ?)");
        $stmt->execute([$email, $tokenHash, $expires]);

        // 5. Preparar y enviar email
        $resetLink = "https://memoriaterrassa.cat/restabliment-contrasenya?token=$token&email=" . urlencode($email);
        // Enviar el correo con PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'hl121.lucushost.org';
            $mail->SMTPAuth = true;
            $mail->Username = 'email@memoriaterrassa.cat';
            $mail->Password = $emailPass;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';

            $mail->setFrom('no-reply@memoriaterrassa.cat', 'Memòria Terrassa');
            $mail->addAddress($email);
            $mail->Subject = 'Restabliment de contrasenya';
            $mail->isHTML(true);
            $mail->Subject = 'Recuperació de contrasenya';

            $mail->Body = '
                <!DOCTYPE html>
                <html lang="ca">
                <head>
                <meta charset="UTF-8">
                <style>
                    body {
                    font-family: Arial, sans-serif;
                    background-color: #f4f4f4;
                    color: #333;
                    padding: 20px;
                    }
                    .container {
                    max-width: 600px;
                    margin: auto;
                    background: #ffffff;
                    border-radius: 8px;
                    box-shadow: 0 0 10px rgba(0,0,0,0.05);
                    padding: 30px;
                    }
                    .button {
                    display: inline-block;
                    background-color: #007bff;
                    color: #ffffff;
                    padding: 12px 20px;
                    text-decoration: none;
                    border-radius: 5px;
                    margin-top: 20px;
                    }
                    .footer {
                    margin-top: 30px;
                    font-size: 12px;
                    color: #999;
                    }
                </style>
                </head>
                <body>
                <div class="container">
                    <h2>Recuperació de contrasenya</h2>
                    <p>Hola,</p>
                    <p>Hem rebut una sol·licitud per restablir la contrasenya del teu compte.</p>
                    <p>Fes clic al següent botó per continuar amb el procés:</p>
                    <a class="button" href="' . htmlspecialchars($resetLink) . '">Restablir contrasenya</a>
                    <p>Si no has demanat aquest canvi, pots ignorar aquest missatge.</p>

                    <div class="footer">
                    Aquest missatge s\'ha enviat automàticament. Si tens cap dubte, contacta amb nosaltres.
                    </div>
                </div>
                </body>
                </html>';

            $mail->send();
        } catch (Exception $e) {
            // Log en tu servidor si quieres depurar
        }
    }

    // Siempre la misma respuesta por seguridad
    echo json_encode([
        'status' => 'ok',
        'message' => 'Si el correu introduït és correcte, rebràs un enllaç de recuperació a la teva bústia.'
    ]);

    // POST : procés de creació de nova contrasenya
    // URL: https://memoriaterrassa.cat/api/auth/post/restablimentPassword
} else if ($slug === "restablimentPassword") {

    header('Content-Type: application/json');

    $data = json_decode(file_get_contents("php://input"), true);
    $password = trim($data['password'] ?? '');
    $token = trim($data['token'] ?? '');
    $email = trim($data['email'] ?? '');

    if (strlen($password) < 6 || empty($token) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Dades no vàlides']);
        exit;
    }

    global $conn;
    /** @var PDO $conn */

    // Verificar token en la base de datos
    $stmt = $conn->prepare("SELECT token_hash, expires_at FROM auth_users_password_resets WHERE email = ? ORDER BY id DESC LIMIT 1");
    $stmt->execute([$email]);
    $reset = $stmt->fetch();

    if (!$reset || strtotime($reset['expires_at']) < time()) {
        echo json_encode(['status' => 'error', 'message' => 'Token caducat o invàlid']);
        exit;
    }

    if (!password_verify($token, $reset['token_hash'])) {
        echo json_encode(['status' => 'error', 'message' => 'Token incorrecte']);
        exit;
    }

    // Actualizar la contraseña en la tabla de usuarios
    $passwordHash = password_hash($password, PASSWORD_DEFAULT, ['cost' => 10]);
    $stmt = $conn->prepare("UPDATE auth_users SET password = ? WHERE email = ?");
    $stmt->execute([$passwordHash, $email]);

    // Eliminar el token usado
    $conn->prepare("DELETE FROM auth_users_password_resets WHERE email = ?")->execute([$email]);

    echo json_encode(['status' => 'ok', 'message' => 'Contrasenya actualitzada correctament. Seràs redigirit a la pàgina d\'accès a la intranet en uns segons.']);
} else {
    echo json_encode(['error' => 'No hi ha cap consulta disponible']);
}
