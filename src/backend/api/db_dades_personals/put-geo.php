<?php

/**
 * geocode_persones_paginat.php (1 id)
 * - Construye la dirección a partir de campos separados: tipus_via_ca, adreca(adreça = nombre vía), adreca_num
 * - Geocodifica con Nominatim (estructurado + fallback)
 * - Guarda lat/lng en db_dades_personals
 */

declare(strict_types=1);
ini_set('memory_limit', '512M');
set_time_limit(0);

use App\Config\DatabaseConnection;

// ===== CORS =====
$allowedOrigins = ['https://memoriaterrassa.cat', 'https://www.memoriaterrassa.cat'];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin && in_array($origin, $allowedOrigins, true)) {
    header("Access-Control-Allow-Origin: $origin");
    header("Vary: Origin");
    header("Access-Control-Allow-Credentials: true");
} else {
    header("Access-Control-Allow-Origin: https://memoriaterrassa.cat");
    header("Access-Control-Allow-Credentials: true");
}
header("Access-Control-Allow-Methods: GET, PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept");
header("Content-Type: application/json; charset=utf-8");

// Preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Acepta GET o PUT (tu frontend usa PUT con ?id=...)
if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(['status' => 'fail', 'message' => 'Método no permitido']);
    exit;
}

$userId = getAuthenticatedUserId();
if (!$userId) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

// === Config geocoder ===
$USER_AGENT    = 'MemoriaTerrassa/1.0 (contacto: memoria@memoriaterrassa.cat)';
$CONTACT_EMAIL = 'memoria@memoriaterrassa.cat';

// === id desde query o cuerpo JSON ===
$raw  = file_get_contents('php://input');
$body = json_decode($raw ?: 'null', true);
$id   = isset($_GET['id']) ? (int)$_GET['id'] : (int)($body['id'] ?? 0);
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'fail', 'message' => 'id inválido']);
    exit;
}

// ===== Helpers HTTP/OSM =====
function http_get_first_json_local(string $url, string $ua): array
{
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_HTTPHEADER => ['User-Agent: ' . $ua],
    ]);
    $body = curl_exec($ch);
    $err  = curl_error($ch);
    $code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);
    if ($err || $code !== 200 || !$body) {
        return ['code' => $code ?: 0, 'result' => null];
    }
    $json = json_decode($body, true);
    return (is_array($json) && !empty($json)) ? ['code' => $code, 'result' => $json[0]] : ['code' => $code, 'result' => null];
}

function nominatimStructured_local(
    ?string $street,
    ?string $city,
    ?string $state,
    ?string $country,
    string $ua,
    string $email,
    ?string $postal = null
): array {
    $params = [
        'format'          => 'jsonv2',
        'limit'           => 1,
        'addressdetails'  => 1,
        'accept-language' => 'ca,es,en',
        'countrycodes'    => 'es',
        'email'           => $email
    ];
    if ($street)  $params['street']     = $street;  // "47 Carrer Sant Leopold"
    if ($city)    $params['city']       = $city;    // "Terrassa"
    if ($state)   $params['state']      = $state;   // "Barcelona"
    if ($country) $params['country']    = $country; // "España"
    if ($postal)  $params['postalcode'] = $postal;  // si lo tienes

    $url = 'https://nominatim.openstreetmap.org/search?' . http_build_query($params);
    return http_get_first_json_local($url, $ua);
}

function nominatimFree_local(string $q, string $ua, string $email): array
{
    $url = 'https://nominatim.openstreetmap.org/search?' . http_build_query([
        'format'          => 'jsonv2',
        'q'               => $q,       // "47, Sant Leopold, Terrassa, Barcelona, España"
        'limit'           => 1,
        'addressdetails'  => 1,
        'accept-language' => 'ca,es,en',
        'countrycodes'    => 'es',
        'email'           => $email
    ]);
    return http_get_first_json_local($url, $ua);
}

// ============== Conexión ==============
$conn = DatabaseConnection::getConnection();
if (!$conn) {
    die("No se pudo establecer conexión a la base de datos.\n");
}

// Cargar datos de la persona
$sql = "
    SELECT
        a.id AS rid,
        a.lat, a.lng,
        -- Campos de vía separados:
        t.tipus_ca      AS tipus_via_ca,   -- p.ej. 'Carrer'
        a.adreca        AS via_nom,        -- nombre de calle
        a.adreca_num    AS via_num,        -- número (puede ser 's/n' o vacío)
        -- Localización administrativa:
        m.ciutat        AS ciutat,         -- 'Terrassa'
        p.provincia     AS provincia,      -- 'Barcelona'
        s.estat         AS estat           -- 'España'
    FROM db_dades_personals a
    LEFT JOIN aux_dades_municipis           m ON m.id  = a.municipi_residencia
    LEFT JOIN aux_dades_municipis_provincia p ON p.id  = m.provincia
    LEFT JOIN aux_dades_municipis_estat     s ON s.id  = m.estat
    LEFT JOIN aux_tipus_via                 t ON a.tipus_via = t.id
    WHERE a.id = :id
    LIMIT 1
";
$st = $conn->prepare($sql);
$st->execute([':id' => $id]);
$row = $st->fetch(\PDO::FETCH_ASSOC);

if (!$row) {
    http_response_code(404);
    echo json_encode(['status' => 'fail', 'message' => 'No trobat']);
    exit;
}

// Si ya hay coords válidas, devuélvelas sin geocodificar
$latExist = $row['lat'];
$lngExist = $row['lng'];
if ($latExist !== null && $lngExist !== null) {
    $latNum = (float)$latExist;
    $lngNum = (float)$lngExist;
    if ($latNum >= -90 && $latNum <= 90 && $lngNum >= -180 && $lngNum <= 180) {
        echo json_encode([
            'status'  => 'success',
            'message' => 'Coordenades ja existents',
            'data'    => ['lat' => $latNum, 'lng' => $lngNum]
        ]);
        exit;
    }
}

// ===== Construcción de parámetros SIN normalización =====
$viaNum  = trim((string)($row['via_num']       ?? ''));   // "47" / "s/n" / ""
$viaType = trim((string)($row['tipus_via_ca']  ?? ''));   // "Carrer" / "Avinguda" / ""
$viaNom  = trim((string)($row['via_nom']       ?? ''));   // "Sant Leopold" / "Cervantes" ...

$city    = is_string($row['ciutat']    ?? null) ? trim($row['ciutat'])    : null; // "Terrassa"
$state   = is_string($row['provincia'] ?? null) ? trim($row['provincia']) : null; // "Barcelona"
$country = is_string($row['estat']     ?? null) ? trim($row['estat'])     : 'España';

// street (estructurado): "<num> <tipus_via> <via_nom>" (si hay num)
$streetParam = trim(
    ($viaNum !== '' && strtolower($viaNum) !== 's/n' ? ($viaNum . ' ') : '') .
        ($viaType !== '' ? ($viaType . ' ') : '') .
        $viaNom
);

// fallback (texto libre, como pediste): "47, Sant Leopold, Terrassa[, Barcelona, España]"
$partsFree = [];
if ($viaNum !== '')               $partsFree[] = $viaNum;       // incluye "s/n" si lo tienes así
if ($viaNom !== '')               $partsFree[] = $viaNom;
if ($city)                        $partsFree[] = $city;
if ($country)                     $partsFree[] = $country;       // opcional pero ayuda
$freeTextAddr = implode(', ', $partsFree);

// Si por lo que sea no hay nombre de vía, fallamos con 422 (evita pedir coords ambiguas)
if ($viaNom === '') {
    http_response_code(422);
    echo json_encode([
        'status'  => 'fail',
        'message' => 'Falta el nombre de la vía (via_nom/adreca) para geocodificar'
    ]);
    exit;
}

// ===== 1) Nominatim estructurado
$resp = nominatimStructured_local($streetParam, $city, $state, $country, $USER_AGENT, $CONTACT_EMAIL);
if ($resp['code'] === 429) {
    usleep(5_000_000); // 5s
    $resp = nominatimStructured_local($streetParam, $city, $state, $country, $USER_AGENT, $CONTACT_EMAIL);
}
$res = $resp['result'];

// ===== 2) Fallback a texto libre
if (!$res) {
    $resp = nominatimFree_local($freeTextAddr, $USER_AGENT, $CONTACT_EMAIL);
    if ($resp['code'] === 429) {
        usleep(5_000_000); // 5s
        $resp = nominatimFree_local($freeTextAddr, $USER_AGENT, $CONTACT_EMAIL);
    }
    $res = $resp['result'];
}

if (!is_array($res) || !isset($res['lat'], $res['lon'])) {
    http_response_code(404);
    echo json_encode([
        'status'  => 'fail',
        'message' => 'No s’han trobat coordenades',
        'debug'   => [
            'streetParam'   => $streetParam,
            'freeTextAddr'  => $freeTextAddr,
            'city'          => $city,
            'state'         => $state,
            'country'       => $country
        ]
    ]);
    exit;
}

$lat = (float)$res['lat'];
$lng = (float)$res['lon'];

// Guardar en BD
$upd = $conn->prepare("UPDATE db_dades_personals SET lat = :lat, lng = :lng WHERE id = :id");
$upd->execute([':lat' => $lat, ':lng' => $lng, ':id' => $id]);

echo json_encode([
    'status'  => 'success',
    'message' => 'Coordenades actualitzades',
    'data'    => [
        'lat' => $lat,
        'lng' => $lng,
        // útil para verificar qué se consultó
        'streetParam'  => $streetParam,
        'freeTextAddr' => $freeTextAddr,
    ],
]);
exit;
