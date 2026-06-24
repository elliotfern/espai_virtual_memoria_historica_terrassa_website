<?php

use App\Config\DatabaseConnection;
use App\Utils\Mailer;

session_start();

// Conexión a BD
$conn = DatabaseConnection::getConnection();
if (!$conn) {
    throw new RuntimeException("No es pot establir connexió amb la base de dades.");
}

/**
 * Limpia un mensaje proveniente de un <textarea> para almacenarlo de forma segura.
 */
function sanitizeHtml(?string $raw, int $maxLength = 5000, int $maxLines = 400): string
{
    $s = (string)($raw ?? '');

    if (!mb_check_encoding($s, 'UTF-8')) {
        $s = @mb_convert_encoding($s, 'UTF-8', 'auto');
        if ($s === false) $s = '';
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

header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");

$allowedOrigin = DOMAIN;
checkReferer($allowedOrigin);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

$usuariId = getAuthenticatedUserId();
if (!$usuariId) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

$inputData = file_get_contents('php://input');
$data = json_decode($inputData, true) ?? [];

$errors = [];

if (empty($data['missatge_id']) || !ctype_digit((string)$data['missatge_id'])) {
    $errors[] = "Identificador de missatge no vàlid.";
} else {
    $missatgeId = (int)$data['missatge_id'];
}

if (empty($data['subject'])) {
    $subjectBase = "Resposta al teu missatge a Memòria Terrassa";
} else {
    $subjectBase = trim((string)$data['subject']);
    if (mb_strlen($subjectBase, 'UTF-8') > 255) {
        $errors[] = "L'assumpte és massa llarg (màx. 255 caràcters).";
    }
}

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

try {
    // 1) Recuperar el missatge original
    $stmt = $conn->prepare("
        SELECT nomCognoms, email, token_assumpte
        FROM db_form_contacte
        WHERE id = :id
    ");
    $stmt->execute([':id' => $missatgeId]);
    $original = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$original) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => "No s'ha trobat el missatge original."]);
        exit;
    }

    $nomCognoms       = (string) $original['nomCognoms'];
    $emailDestinatari = (string) $original['email'];
    $tokenAssumpte    = $original['token_assumpte'] ?? null;

    if (!filter_var($emailDestinatari, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'El correu del destinatari no és vàlid.']);
        exit;
    }

    if (empty($tokenAssumpte)) {
        $tokenAssumpte = 'MT-' . str_pad((string)$missatgeId, 6, '0', STR_PAD_LEFT);
        $stmtUpdateToken = $conn->prepare("UPDATE db_form_contacte SET token_assumpte = :token WHERE id = :id");
        $stmtUpdateToken->execute([':token' => $tokenAssumpte, ':id' => $missatgeId]);
    }

    $subject = '[' . $tokenAssumpte . '] ' . $subjectBase;
    if (mb_strlen($subject, 'UTF-8') > 255) {
        $subject = mb_substr($subject, 0, 255, 'UTF-8');
    }

    // 2) Insertar la resposta
    $stmt = $conn->prepare("
        INSERT INTO db_form_contacte_respostes (
            missatge_id, usuari_id, resposta_subject,
            resposta_text, email_destinatari, data_resposta
        ) VALUES (
            :missatge_id, :usuari_id, :resposta_subject,
            :resposta_text, :email_destinatari, NOW()
        )
    ");
    $stmt->execute([
        ':missatge_id'       => $missatgeId,
        ':usuari_id'         => $usuariId,
        ':resposta_subject'  => $subject,
        ':resposta_text'     => $respostaText,
        ':email_destinatari' => $emailDestinatari,
    ]);

    $respostaId = (int) $conn->lastInsertId();

    // 3) Enviar el correu
    $respostaHtml = nl2br(htmlspecialchars($respostaText, ENT_QUOTES, 'UTF-8'));

    $htmlBody = '
        <!DOCTYPE html>
        <html lang="ca">
        <head>
        <meta charset="UTF-8">
        <style>
            body { margin:0; padding:0; font-family:Arial,sans-serif; background-color:#f6f4eb; color:#333; }
            .wrapper { width:100%; padding:20px 0; background-color:#f6f4eb; }
            .container { max-width:600px; margin:0 auto; background:#fff; border-radius:8px; box-shadow:0 0 10px rgba(0,0,0,0.05); padding:24px 24px 30px; }
            .header { text-align:center; margin-bottom:20px; }
            .logo { max-width:220px; height:auto; display:block; margin:0 auto; }
            .footer { margin-top:30px; font-size:12px; color:#999; text-align:center; }
            .divider { border:0; border-top:1px solid #e0e0e0; margin:20px 0; }
        </style>
        </head>
        <body>
        <div class="wrapper">
            <div class="container">
                <div class="header">
                    <img src="https://media.memoriaterrassa.cat/assets_web/logo_web.png" alt="Memòria Terrassa" class="logo" />
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

    $mailer = new Mailer();
    $sent = $mailer->send(
        to: $emailDestinatari,
        toName: $nomCognoms,
        subject: $subject,
        htmlBody: $htmlBody,
        plainText: $respostaText,
        replyTo: 'email@memoriaterrassa.cat',
    );

    if (!$sent) {
        throw new \RuntimeException('Mailer::send() devolvió false.');
    }

    // 4) Actualitzar estat del missatge original a 2 (Resposta enviada)
    $updateStmt = $conn->prepare("UPDATE db_form_contacte SET estat = 2 WHERE id = :id");
    $updateStmt->execute([':id' => $missatgeId]);

    echo json_encode([
        'status'      => 'success',
        'message'     => 'Resposta enviada correctament.',
        'resposta_id' => $respostaId,
    ]);
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => "S'ha produït un error al servidor.",
        'detail'  => $e->getMessage(),
    ]);
}
