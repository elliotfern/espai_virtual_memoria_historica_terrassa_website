<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Config\DatabaseConnection;

$emailPass = $_ENV['EMAIL_PASS'];

$conn = DatabaseConnection::getConnection();

if (!$conn) {
    throw new Exception("No es pot establir connexió amb la base de dades.");
}


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


// POST FORMULARI DE CONTACTE
// RUTA POST => /api/form_contacte/post
$inputData = file_get_contents('php://input');
$data = json_decode($inputData, true);

if (!empty($data['extra_field'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Detecció de SPAM."]);
    exit;
}

// mesures anti-spam
$minSeconds = 5; // tiempo mínimo esperado en segundos
$currentTimestamp = time();
$formTimestamp = isset($data['form_timestamp']) ? (int) $data['form_timestamp'] : 0;

if (($currentTimestamp - $formTimestamp) < $minSeconds) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'S\'ha detectat un enviament massa ràpid.',
    ]);
    exit;
}

$ip = $_SERVER['REMOTE_ADDR'];
$limite = 1;
$intervaloMinutos = 30;

$sql = "SELECT COUNT(*) as total 
        FROM db_form_contacte
        WHERE form_ip = :form_ip 
          AND dataEnviament >= NOW() - INTERVAL :minutes MINUTE";
$stmt = $conn->prepare($sql);
$stmt->execute([
    ':form_ip' => $ip,
    ':minutes' => $intervaloMinutos
]);

$result = $stmt->fetch();

if ($result['total'] >= $limite) {
    http_response_code(429); // Too Many Requests
    echo json_encode([
        'status' => 'error',
        'message' => "Has superat el límit d'enviaments. Torna-ho a provar més tard."
    ]);
    exit;
}

// Inicializar un array para los errores
$errors = [];

// Validación de los datos recibidos
if (empty($data['nomCognoms'])) {
    $errors[] = "El camp 'Nom i Cognoms' és obligatori.";
} else {
    $nomCognoms = strip_tags(trim($data['nomCognoms']));
}

if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = "El correu electrònic no és vàlid.";
} else {
    $email = filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL);
}

if (!empty($data['telefon'])) {
    $telefon = trim($data['telefon']);

    // Validar que solo contenga números, espacios, + o -
    if (!preg_match('/^[\d\+\-\s]{6,20}$/', $telefon)) {
        $errors[] = "El telèfon introduït no és vàlid.";
    } else {
        $telefon = preg_replace('/[^\d\+\-\s]/', '', $telefon); // Sanear
    }
} else {
    $telefon = null;
}


if (empty($data['missatge'])) {
    $errors[] = "El missatge és obligatori";
} else {
    $missatge = sanitizeHtml($data['missatge']);
}

// Si hay errores, devolver una respuesta con los errores
if (!empty($errors)) {
    http_response_code(400); // Bad Request
    echo json_encode(["status" => "error", "message" => "S'han produït errors en la validació", "errors" => $errors]);
    exit;
}

// Si no hay errores, crear las variables PHP y preparar la consulta PDO

$form_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$form_user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
$dataEnviament = date('Y-m-d H:i:s');

// 	id 	nomCognoms 	email 	telefon 	missatge 	form_ip 	form_user_agent 	dataEnviament 	
// Conectar a la base de datos con PDO (asegúrate de modificar los detalles de la conexión)
try {

    global $conn;
    /** @var PDO $conn */

    // Crear la consulta SQL
    $sql = "INSERT INTO db_form_contacte (
                nomCognoms,
                email,
                telefon,
                missatge,
                form_ip,
                form_user_agent,
                dataEnviament
            ) VALUES (
                :nomCognoms,
                :email,
                :telefon,
                :missatge,
                :form_ip,
                :form_user_agent,
                :dataEnviament
            )";

    // Preparar la consulta
    $stmt = $conn->prepare($sql);

    // Enlazar los parámetros con los valores de las variables PHP
    $stmt->bindParam(':nomCognoms', $nomCognoms, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':telefon', $telefon, PDO::PARAM_STR);
    $stmt->bindParam(':missatge', $missatge, PDO::PARAM_STR);
    $stmt->bindParam(':form_ip', $form_ip, PDO::PARAM_STR);
    $stmt->bindParam(':form_user_agent', $form_user_agent, PDO::PARAM_STR);
    $stmt->bindParam(':dataEnviament', $dataEnviament, PDO::PARAM_STR);

    // Ejecutar la consulta
    $stmt->execute();

    // 5. Preparar y enviar email
    $resetLink = "https://memoriaterrassa.cat/gestio";

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
        $mail->addAddress('elliot@hispantic.com');
        $mail->addAddress('manel.marquez@gmail.com');
        $mail->Subject = 'Rebut un formulari de contacte al web';
        $mail->isHTML(true);

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
                    <h2>Nou formulari de contacte</h2>
                    <p>Hola,</p>
                    <p>Hem rebut un nou formulari de contacte</p>
                    <p>Fes clic al següent botó per veure el missatge:</p>
                    <a class="button" href="' . htmlspecialchars($resetLink) . '">Intranet web</a>

                    <div class="footer">
                    Aquest missatge s\'ha enviat automàticament.
                    </div>
                </div>
                </body>
                </html>';

        $mail->send();
    } catch (Exception $e) {
        // Log en tu servidor si quieres depurar
    }

    // Respuesta de éxito
    echo json_encode(["status" => "success", "message" => "Les dades s'han actualitzat correctament a la base de dades."]);
} catch (PDOException $e) {
    // En caso de error en la conexión o ejecución de la consulta
    http_response_code(500); // Internal Server Error
    echo json_encode(["status" => "error", "message" => "S'ha produit un error a la base de dades: " . $e->getMessage()]);
}
