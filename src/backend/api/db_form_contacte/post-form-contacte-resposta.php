<?php

use App\Config\DatabaseConnection;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Si no lo haces en un bootstrap común:
session_start();

$brevoApiKey = $_ENV['BREVO_API_KEY'] ?? '';
if (empty($brevoApiKey)) {
    // Lo ideal es configurar esto en tu .env
    // y no dejarlo hardcodeado.
}

// Conexión a BD
$conn = DatabaseConnection::getConnection();
if (!$conn) {
    throw new Exception("No es pot establir connexió amb la base de dades.");
}

/**
 * Limpia un mensaje proveniente de un <textarea> para almacenarlo de forma segura.
 * Reutilizo tu misma función sanitizeHtml.
 */
function sanitizeHtml(?string $raw, int $maxLength = 5000, int $maxLines = 400): string
{
    $s = (string)($raw ?? '');

    if (!mb_check_encoding($s, 'UTF-8')) {
        $s = @mb_convert_encoding($s, 'UTF-8', 'auto');
        if ($s === false) {
            $s = '';
        }
    }

    $s = preg_replace('/^\xEF\xBB\xBF/u', '', $s);
    $s = str_replace("\0", '', $s);
    $s = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $s);

    $s = str_replace(["\r\n", "\r"], "\n", $s);
    $s = strip_tags($s);

    $lines = array_map(static fn($l) => trim($l, " \t"), explode("\n", $s));
    $s = preg_replace("/\n{3,}/", "\n\n", trim(implode("\n", $lines)));

    $arr = explode("\n", $s);
    if (count($arr) > $maxLines) {
        $arr = array_slice($arr, 0, $maxLines);
    }
    $s = implode("\n", $arr);

    if (mb_strlen($s, 'UTF-8') > $maxLength) {
        $s = mb_substr($s, 0, $maxLength, 'UTF-8');
    }

    return $s;
}

// Configuración cabeceras
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");

$allowedOrigin = DOMAIN;
checkReferer($allowedOrigin);

// Solo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

// Comprobar usuario logueado en intranet
$usuariId = getAuthenticatedUserId();
if (!$usuariId) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

// Leer JSON
$inputData = file_get_contents('php://input');
$data = json_decode($inputData, true) ?? [];

// Validación básica
$errors = [];

// missatge_id
if (empty($data['missatge_id']) || !ctype_digit((string)$data['missatge_id'])) {
    $errors[] = "Identificador de missatge no vàlid.";
} else {
    $missatgeId = (int)$data['missatge_id'];
}

// subject
if (empty($data['subject'])) {
    // Puedes poner un subject por defecto si no lo envían
    $subject = "Resposta al teu missatge a Memòria Terrassa";
} else {
    $subject = trim((string)$data['subject']);
    if (mb_strlen($subject, 'UTF-8') > 255) {
        $errors[] = "L'assumpte és massa llarg (màx. 255 caràcters).";
    }
}

// resposta_text
if (empty($data['resposta_text'])) {
    $errors[] = "El missatge de resposta és obligatori.";
} else {
    $respostaText = sanitizeHtml($data['resposta_text'], 10000, 1000);
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => "S'han produït errors en la validació.",
        'errors'  => $errors,
    ]);
    exit;
}

// Config SMTP Brevo desde .env
$brevoHost     = $_ENV['BREVO_SMTP_HOST']     ?? 'smtp-relay.brevo.com';
$brevoPort     = (int)($_ENV['BREVO_SMTP_PORT'] ?? 587);
$brevoUser     = $_ENV['BREVO_SMTP_USER']     ?? '';
$brevoPass     = $_ENV['BREVO_SMTP_PASS']     ?? '';
$brevoFrom     = $_ENV['BREVO_SMTP_FROM']     ?? 'email@memoriaterrassa.cat';
$brevoFromName = $_ENV['BREVO_SMTP_FROM_NAME'] ?? 'Espai Virtual de la Memòria Història de Terrassa';

if (empty($brevoUser) || empty($brevoPass)) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Configuració SMTP de Brevo no disponible.',
    ]);
    exit;
}

try {
    /** @var PDO $conn */

    // 1) Recuperar el missatge original (email + nomCognoms)
    $sql = "SELECT nomCognoms, email 
            FROM db_form_contacte
            WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $missatgeId]);
    $original = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$original) {
        http_response_code(404);
        echo json_encode([
            'status'  => 'error',
            'message' => 'No s\'ha trobat el missatge original.',
        ]);
        exit;
    }

    $nomCognoms       = (string)$original['nomCognoms'];
    $emailDestinatari = (string)$original['email'];

    if (!filter_var($emailDestinatari, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode([
            'status'  => 'error',
            'message' => 'El correu del destinatari no és vàlid.',
        ]);
        exit;
    }

    // 2) Insertar la resposta a la taula db_form_contacte_respostes
    $sql = "INSERT INTO db_form_contacte_respostes (
                missatge_id,
                usuari_id,
                resposta_subject,
                resposta_text,
                email_destinatari,
                data_resposta
            )
            VALUES (
                :missatge_id,
                :usuari_id,
                :resposta_subject,
                :resposta_text,
                :email_destinatari,
                NOW()
            )";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':missatge_id'      => $missatgeId,
        ':usuari_id'        => $usuariId,
        ':resposta_subject' => $subject,
        ':resposta_text'    => $respostaText,
        ':email_destinatari' => $emailDestinatari,
    ]);

    $respostaId = (int)$conn->lastInsertId();

    // 3) Enviar el correu via SMTP de Brevo + PHPMailer
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = $brevoHost;
        $mail->SMTPAuth   = true;
        $mail->Username   = $brevoUser;
        $mail->Password   = $brevoPass;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $brevoPort;
        $mail->CharSet    = 'UTF-8';
        $mail->Encoding   = 'base64';

        $mail->setFrom($brevoFrom, $brevoFromName);
        $mail->addAddress($emailDestinatari, $nomCognoms);

        $mail->Subject = $subject;
        $mail->isHTML(true);

        // HTML: convertimos saltos de línea a <br>
        $respostaHtml = nl2br(htmlspecialchars($respostaText, ENT_QUOTES, 'UTF-8'));

        $mail->Body = '
            <!DOCTYPE html>
            <html lang="ca">
            <head>
            <meta charset="UTF-8">
            <style>
                body {
                    margin: 0;
                    padding: 0;
                    font-family: Arial, sans-serif;
                    background-color: #f6f4eb;
                    color: #333333;
                }
                .wrapper {
                    width: 100%;
                    padding: 20px 0;
                    background-color: #f6f4eb;
                }
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    background: #ffffff;
                    border-radius: 8px;
                    box-shadow: 0 0 10px rgba(0,0,0,0.05);
                    padding: 24px 24px 30px 24px;
                }
                .header {
                    text-align: center;
                    margin-bottom: 20px;
                }
                .logo {
                    max-width: 220px;
                    height: auto;
                    display: block;
                    margin: 0 auto;
                }
                .footer {
                    margin-top: 30px;
                    font-size: 12px;
                    color: #999999;
                    text-align: center;
                }
                .divider {
                    border: 0;
                    border-top: 1px solid #e0e0e0;
                    margin: 20px 0;
                }
            </style>
            </head>
            <body>
            <div class="wrapper">
                <div class="container">
                    <div class="header">
                        <img 
                            src="https://media.memoriaterrassa.cat/assets_web/logo_web.png" 
                            alt="Memòria Terrassa" 
                            class="logo"
                        />
                    </div>

                    <p>Hola ' . htmlspecialchars($nomCognoms, ENT_QUOTES, 'UTF-8') . ',</p>
                    <p>Et responem al missatge que vas enviar al web de Memòria Terrassa:</p>

                    <hr class="divider" />

                    <p>' . $respostaHtml . '</p>

                    <hr class="divider" />

                    <div class="footer">
                        Aquest correu s\'ha enviat automàticament des del web memoriaterrassa.cat.
                    </div>
                </div>
            </div>
            </body>
            </html>';


        $mail->AltBody = $respostaText;

        $mail->send();

        echo json_encode([
            'status'      => 'success',
            'message'     => 'Resposta enviada correctament.',
            'resposta_id' => $respostaId,
        ]);
    } catch (Exception $e) {
        // Aquí podrías, si quieres, actualizar la fila de resposta con info de error_enviament
        // pero tu tabla actual no tiene esa columna; de momento solo devolvemos error.
        http_response_code(502);
        echo json_encode([
            'status'      => 'error',
            'message'     => 'Error en enviar el correu de resposta.',
            'detail'      => $e->getMessage(),
            'resposta_id' => $respostaId,
        ]);
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'S\'ha produït un error al servidor.',
        'detail'  => $e->getMessage(),
    ]);
}
