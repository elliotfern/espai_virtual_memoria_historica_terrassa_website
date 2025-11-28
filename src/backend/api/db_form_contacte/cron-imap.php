<?php

use App\Config\DatabaseConnection;

// ------ Configuración IMAP ------
$emailPass = $_ENV['EMAIL_PASS'];

// Ajusta estos valores a tu servidor de correo real
$hostname = '{hl121.lucushost.org:993/imap/ssl}INBOX';
$username = 'email@memoriaterrassa.cat';
$password = $emailPass;


// --------------------------------

try {
    /** @var PDO $pdo */
    $pdo = DatabaseConnection::getConnection();
    if (!$pdo) {
        throw new RuntimeException("No es pot establir connexió amb la base de dades.");
    }
} catch (Throwable $e) {
    echo "ERROR BD: " . $e->getMessage() . PHP_EOL;
    exit(1);
}

// Comprobar que la extensión IMAP está cargada
if (!function_exists('imap_open')) {
    echo "ERROR: L'extensió IMAP de PHP no està habilitada." . PHP_EOL;
    exit(1);
}

// Conectar al buzón
$inbox = @imap_open($hostname, $username, $password);
if (!$inbox) {
    echo "ERROR IMAP: " . imap_last_error() . PHP_EOL;
    exit(1);
}

// Buscar emails no leídos
$emails = imap_search($inbox, 'UNSEEN');

if (!$emails) {
    // Nada nuevo
    imap_close($inbox);
    exit(0);
}

// Procesar uno a uno
foreach ($emails as $emailNumber) {
    $overview = imap_fetch_overview($inbox, $emailNumber, 0);
    $structure = imap_fetchstructure($inbox, $emailNumber);

    $subjectRaw = $overview[0]->subject ?? '';
    $subject    = imap_utf8($subjectRaw);
    $fromRaw    = $overview[0]->from ?? '';
    $dateRaw    = $overview[0]->date ?? date('r');

    // Intentar sacar el destinatario principal
    $toHeader = imap_headerinfo($inbox, $emailNumber);
    $emailRebut = '';
    if (!empty($toHeader->to) && is_array($toHeader->to)) {
        // cogemos el primero
        $firstTo = $toHeader->to[0];
        $emailRebut = (isset($firstTo->mailbox, $firstTo->host))
            ? $firstTo->mailbox . '@' . $firstTo->host
            : '';
    }

    // Obtener body en texto plano
    $body = getBodyText($inbox, $emailNumber, $structure);

    // Extraer el token del subject: [MT-000123]
    $token = null;
    if (preg_match('/\[(MT-[0-9]{6})\]/', $subject, $matches)) {
        $token = $matches[1];
    }

    if (!$token) {
        // No está el token, no lo podemos asignar a un missatge concreto.
        // Opcional: podrías guardarlo en otra tabla o una "bandeja de entrada genérica".
        echo "Email $emailNumber sense token al subject, s'ignora." . PHP_EOL;
        // Lo marcamos como leído igualmente para que no se repita
        imap_setflag_full($inbox, $emailNumber, "\\Seen");
        continue;
    }

    try {
        // Buscar el missatge original por token_assumpte
        $stmt = $pdo->prepare("
            SELECT id 
            FROM db_form_contacte
            WHERE token_assumpte = :token
            LIMIT 1
        ");
        $stmt->execute([':token' => $token]);
        $missatge = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$missatge) {
            echo "No s'ha trobat missatge amb token $token." . PHP_EOL;
            // Igual que antes, podrías guardarlo en "pendents" si quieres
            imap_setflag_full($inbox, $emailNumber, "\\Seen");
            continue;
        }

        $missatgeId = (int)$missatge['id'];

        // Parsear el remitent (puede venir como "Nom <email@domini>")
        $emailRemitent = extractEmailFromHeader($fromRaw);

        // Fecha/hora del email
        $rebutA = date('Y-m-d H:i:s', strtotime($dateRaw));

        // Insertar en db_form_contacte_respostes_email
        $stmtInsert = $pdo->prepare("
            INSERT INTO db_form_contacte_respostes_email (
                missatge_id,
                email_remitent,
                email_rebut,
                subject,
                body,
                rebut_a
            ) VALUES (
                :missatge_id,
                :email_remitent,
                :email_rebut,
                :subject,
                :body,
                :rebut_a
            )
        ");

        $stmtInsert->execute([
            ':missatge_id'   => $missatgeId,
            ':email_remitent' => $emailRemitent,
            ':email_rebut'   => $emailRebut,
            ':subject'       => $subject,
            ':body'          => $body,
            ':rebut_a'       => $rebutA,
        ]);

        echo "Email $emailNumber guardat com a resposta del missatge ID $missatgeId (token $token)." . PHP_EOL;

        // Opcional: podrías actualizar l'estat del missatge, p. ex. 3 = resposta usuari
        /*
        $stmtUpdate = $pdo->prepare("UPDATE db_form_contacte SET estat = 3 WHERE id = :id");
        $stmtUpdate->execute([':id' => $missatgeId]);
        */

        // Marcar el email como leído
        imap_setflag_full($inbox, $emailNumber, "\\Seen");
    } catch (Throwable $e) {
        echo "ERROR processant email $emailNumber: " . $e->getMessage() . PHP_EOL;
        // Puedes decidir si NO marcarlo como leído para reintentar en el futuro,
        // o moverlo a una carpeta de errores con imap_mail_move().
        // Aquí de momento lo marcamos como leído para no entrar en bucle.
        imap_setflag_full($inbox, $emailNumber, "\\Seen");
    }
}

imap_close($inbox);
exit(0);


/**
 * Obtiene el cuerpo del mensaje en texto plano, si es posible.
 */
function getBodyText($inbox, int $emailNumber, $structure): string
{
    if (!isset($structure->parts) || !is_array($structure->parts)) {
        // Mensaje simple
        $body = imap_body($inbox, $emailNumber);
        return decodeBody($body, $structure->encoding ?? 0);
    }

    $body = '';

    // Buscar una parte text/plain
    foreach ($structure->parts as $partNumber => $part) {
        $isTextPlain = ($part->type == 0 && strtolower($part->subtype ?? '') === 'plain');
        if ($isTextPlain) {
            $bodyPart = imap_fetchbody($inbox, $emailNumber, $partNumber + 1);
            $body = decodeBody($bodyPart, $part->encoding ?? 0);
            break;
        }
    }

    // Si no encontramos text/plain, como fallback usamos el body completo
    if ($body === '') {
        $fallbackBody = imap_body($inbox, $emailNumber);
        $body = decodeBody($fallbackBody, $structure->encoding ?? 0);
    }

    return trim($body);
}

/**
 * Decodifica el cuerpo según su encoding.
 */
function decodeBody(string $text, int $encoding): string
{
    switch ($encoding) {
        case 3: // BASE64
            return base64_decode($text);
        case 4: // QUOTED-PRINTABLE
            return quoted_printable_decode($text);
        default:
            return $text;
    }
}

/**
 * Extrae una dirección de email de un header tipo "Nom Cognom <email@domini>".
 */
function extractEmailFromHeader(string $headerValue): string
{
    // Ej: "Nom Cognom <email@domini>"
    if (preg_match('/<([^>]+)>/', $headerValue, $matches)) {
        return trim($matches[1]);
    }

    // Si no hay <>, asumimos que el header es directamente el email o algo similar
    return trim($headerValue);
}
