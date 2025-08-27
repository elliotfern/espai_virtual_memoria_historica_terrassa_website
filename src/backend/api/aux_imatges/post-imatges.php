<?php

declare(strict_types=1);

use App\Config\DatabaseConnection;
// use App\Config\Audit; // si registras auditoría
// use App\Config\Tables; // si tienes constantes de tablas

header('Content-Type: application/json');

// (Opcional) Referer/CSRF, si en tu proyecto ya lo usas.
// checkReferer(DOMAIN);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

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

$userId = getAuthenticatedUserId();
if (!$userId) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit;
}


/**
 * Sanea el nombre base del archivo (sin extensión):
 * - quita la extensión
 * - translitera acentos
 * - permite [a-z0-9-_]
 * - colapsa guiones
 * - recorta a 120 chars
 */
function sanitizeBaseName(string $original): string
{
    $base = pathinfo($original, PATHINFO_FILENAME); // sin extensión
    $base = trim($base);
    // transliterar (si está disponible)
    if (function_exists('iconv')) {
        $trans = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $base);
        if ($trans !== false) $base = $trans;
    }
    $base = strtolower($base);
    $base = preg_replace('/[^a-z0-9\-_]+/', '-', $base) ?? '';
    $base = preg_replace('/-+/', '-', $base) ?? '';
    $base = trim($base, '-_ ');
    if ($base === '') $base = 'imatge';
    if (strlen($base) > 120) $base = substr($base, 0, 120);
    return $base;
}

try {
    // Campos del form
    $nomImatge = trim($_POST['nomImatge'] ?? '');
    $file      = $_FILES['nomArxiu'] ?? null;
    $idPersona = isset($_POST['idPersona']) ? (int)$_POST['idPersona'] : null;

    if ($nomImatge === '' || !$file || $file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Falten camps o fitxer invàlid.');
    }

    // Validar tamaño (opcional)
    // Solo JPG y max 3MB
    $maxBytes = 3 * 1024 * 1024;
    if (!isset($file['size']) || (int)$file['size'] <= 0 || (int)$file['size'] > $maxBytes) {
        throw new RuntimeException('La mida supera el límit de 3MB.');
    }

    // Validar MIME real (no fiarse de la extensió)
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']);
    $allowed = [
        'image/jpeg' => 'jpg',
    ];
    if (!isset($allowed[$mime])) {
        throw new RuntimeException('Format no permès. Usa JPG');
    }
    $ext = $allowed[$mime];

    // nomArxiu = nombre base del archivo original sin extensión (saneado)
    $originalClientName = (string)($file['name'] ?? 'imatge');
    $nomArxiu = sanitizeBaseName($originalClientName);

    // Conexión BD
    $conn = DatabaseConnection::getConnection();
    if (!$conn) throw new RuntimeException('No s\'ha pogut connectar a la base de dades.');

    // Directorio destino
    // Configuración de rutas
    $targetDir = '/home/epgylzqu/media.memoriaterrassa.cat/assets_represaliats/img/';

    if (!is_dir($targetDir)) {
        if (!mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
            throw new RuntimeException('No s\'ha pogut crear el directori de destinació.');
        }
    }

    // Transacción para evitar orfes
    $conn->beginTransaction();

    // INSERT (tipus forçat a 1, dateCreated = NOW())
    $stmt = $conn->prepare("
    INSERT INTO aux_imatges (nomArxiu, nomImatge, tipus, dateCreated, dateModified, idPersona)
    VALUES (:nomArxiu, :nomImatge, 1, NOW(), NULL, :idPersona)
  ");
    $stmt->execute([
        ':nomArxiu'  => $nomArxiu,
        ':nomImatge' => $nomImatge,
        ':idPersona' => $idPersona ?: null,
    ]);
    $id = (int)$conn->lastInsertId();
    if ($id <= 0) {
        throw new RuntimeException('No s\'ha pogut crear el registre a la base de dades.');
    }

    // Guardar archivo físico como ID.ext (evita colisiones)
    $filename  = $nomArxiu . '.' . $ext;
    $targetAbs = $targetDir . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $targetAbs)) {
        throw new RuntimeException('No s\'ha pogut desar el fitxer al servidor.');
    }
    @chmod($targetAbs, 0644);

    // Commit
    $conn->commit();

    // Construir URL pública
    $baseUrl = "https://media.memoriaterrassa.cat";
    $url     = $baseUrl . '/assets_represaliats/img/' . $filename;

    echo json_encode([
        'status' => 'ok',
        'data'   => [
            'id'       => $id,
            'url'      => $url,
            'filename' => $filename,
            // opcionalmente puedes devolver nomArxiu/nomImatge si quieres verlo en el front:
            // 'nomArxiu'  => $nomArxiu,
            // 'nomImatge' => $nomImatge,
        ],
    ]);
} catch (Throwable $e) {
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
