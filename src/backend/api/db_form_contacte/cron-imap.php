<?php

// Mostrar errores en pantalla (puedes desactivar esto en producción si quieres)
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "<pre>Inici cron IMAP\n";

use App\Config\DatabaseConnection;

// ------ Configuración IMAP ------
$emailPass = $_ENV['EMAIL_PASS'] ?? '';

if (empty($emailPass)) {
    echo "ERROR: EMAIL_PASS no està definit a \$_ENV.\n";
    exit(1);
}

// Ajusta estos valores a tu servidor de correo real
$hostname = '{hl121.lucushost.org:993/imap/ssl}INBOX';
$username = 'email@memoriaterrassa.cat';
$password = $emailPass;

// --------------------------------

try {
    /** @var PDO $pdo */
    $conn = DatabaseConnection::getConnection();
    if (!$conn) {
        throw new RuntimeException("No es pot establir connexió amb la base de dades.");
    }
    echo "Connexió BD OK\n";
} catch (Throwable $e) {
    echo "ERROR BD: " . $e->getMessage() . "\n";
    exit(1);
}

// Comprobar que la extensión IMAP está cargada
if (!function_exists('imap_open')) {
    echo "ERROR: L'extensió IMAP de PHP no està habilitada.\n";
    exit(1);
}

// Conectar al buzón
$inbox = @imap_open($hostname, $username, $password);
if (!$inbox) {
    echo "ERROR IMAP: " . imap_last_error() . "\n";
    exit(1);
}

echo "Connexió IMAP OK\n";

// EN PRODUCCIÓ: només correus no llegits
$emails = imap_search($inbox, 'UNSEEN');

if ($emails === false) {
    echo "imap_search ha retornat false o cap mail UNSEEN.\n";
    echo "imap_last_error(): " . imap_last_error() . "\n";
    imap_close($inbox);
    echo "Fi cron IMAP (res a processar)\n</pre>";
    exit(0);
}

echo "Total de correus UNSEEN trobats: " . count($emails) . "\n";
echo "Llista d'IDs IMAP: " . implode(', ', $emails) . "\n\n";

foreach ($emails as $emailNumber) {
    echo "---- Processant email IMAP #$emailNumber ----\n";

    $overview  = imap_fetch_overview($inbox, $emailNumber, 0);
    $structure = imap_fetchstructure($inbox, $emailNumber);

    $subjectRaw = $overview[0]->subject ?? '';
    $subject    = imap_utf8($subjectRaw);
    $fromRaw    = $overview[0]->from ?? '';
    $dateRaw    = $overview[0]->date ?? date('r');

    echo "Subject brut: " . $subjectRaw . "\n";
    echo "Subject decodificat: " . $subject . "\n";
    echo "From: " . $fromRaw . "\n";
    echo "Date: " . $dateRaw . "\n";

    // Intentar sacar el destinatario principal
    $toHeader = imap_headerinfo($inbox, $emailNumber);
    $emailRebut = '';
    if (!empty($toHeader->to) && is_array($toHeader->to)) {
        $firstTo = $toHeader->to[0];
        $emailRebut = (isset($firstTo->mailbox, $firstTo->host))
            ? $firstTo->mailbox . '@' . $firstTo->host
            : '';
    }
    echo "Email rebut (To): " . $emailRebut . "\n";

    // Obtener body en texto plano
    $body = getBodyText($inbox, $emailNumber, $structure);

    echo "Body (primeres 200 lletres):\n" . substr($body, 0, 200) . "\n";

    // Extraer el token del subject: [MT-000123]
    $token = null;
    if (preg_match('/\[(MT-[0-9]{6})\]/', $subject, $matches)) {
        $token = $matches[1];
    }

    echo "Token trobat: " . ($token ?: 'CAP') . "\n";

    if (!$token) {
        echo "Email #$emailNumber sense token al subject, s'ignora per enllaçar amb missatge.\n\n";
        // Igualment, com que és UNSEEN, el marquem com a vist
        imap_setflag_full($inbox, (string)$emailNumber, "\\Seen");
        continue;
    }

    try {
        // Buscar el missatge original por token_assumpte
        $stmt = $conn->prepare("
            SELECT id 
            FROM db_form_contacte
            WHERE token_assumpte = :token
            LIMIT 1
        ");
        $stmt->execute([':token' => $token]);
        $missatge = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$missatge) {
            echo "No s'ha trobat missatge amb token $token.\n\n";
            // El marquem com a vist per no tornar-lo a processar
            imap_setflag_full($inbox, (string)$emailNumber, "\\Seen");
            continue;
        }

        $missatgeId = (int)$missatge['id'];
        echo "Missatge original trobat: ID " . $missatgeId . "\n";

        // Parsear el remitent
        $emailRemitent = extractEmailFromHeader($fromRaw);
        // Fecha/hora del email
        $rebutA = date('Y-m-d H:i:s', strtotime($dateRaw));

        // --- PREVENIR DUPLICATS ---
        // Comprovar si ja existeix una resposta amb mateix missatge_id, subject i rebut_a
        $stmtCheck = $conn->prepare("
            SELECT id 
            FROM db_form_contacte_respostes_email
            WHERE missatge_id = :missatge_id
              AND subject = :subject
              AND rebut_a = :rebut_a
            LIMIT 1
        ");
        $stmtCheck->execute([
            ':missatge_id' => $missatgeId,
            ':subject'     => $subject,
            ':rebut_a'     => $rebutA,
        ]);
        $existing = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            echo "Ja existeix una resposta amb el mateix missatge_id, subject i rebut_a (ID BD " . $existing['id'] . "). S'ignora per evitar duplicats.\n\n";
            // Igualment marquem el correu com a vist
            imap_setflag_full($inbox, (string)$emailNumber, "\\Seen");
            continue;
        }

        // Insertar en db_form_contacte_respostes_email
        $stmtInsert = $conn->prepare("
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
            ':missatge_id'    => $missatgeId,
            ':email_remitent' => $emailRemitent,
            ':email_rebut'    => $emailRebut,
            ':subject'        => $subject,
            ':body'           => $body,
            ':rebut_a'        => $rebutA,
        ]);

        echo "Email #$emailNumber guardat com a resposta del missatge ID $missatgeId (token $token).\n\n";

        // Ara sí: el marquem com a llegit perquè no torni a processar-se
        imap_setflag_full($inbox, (string)$emailNumber, "\\Seen");
    } catch (Throwable $e) {
        echo "ERROR processant email #$emailNumber: " . $e->getMessage() . "\n\n";
        // Opcional: podries NO marcar-lo com a llegit per reintentar en la següent execució
    }
}

imap_close($inbox);

echo "Fi cron IMAP\n</pre>";
exit(0);

/**
 * Obtiene el cuerpo del mensaje en texto plano, si es posible.
 */
function getBodyText($inbox, int $emailNumber, $structure): string
{
    if (!isset($structure->parts) || !is_array($structure->parts)) {
        $body = imap_body($inbox, $emailNumber);
        return decodeBody($body, $structure->encoding ?? 0);
    }

    $body = '';

    foreach ($structure->parts as $partNumber => $part) {
        $isTextPlain = ($part->type == 0 && strtolower($part->subtype ?? '') === 'plain');
        if ($isTextPlain) {
            $bodyPart = imap_fetchbody($inbox, $emailNumber, $partNumber + 1);
            $body = decodeBody($bodyPart, $part->encoding ?? 0);
            break;
        }
    }

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
        case 3:
            return base64_decode($text);
        case 4:
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
    if (preg_match('/<([^>]+)>/', $headerValue, $matches)) {
        return trim($matches[1]);
    }
    return trim($headerValue);
}
