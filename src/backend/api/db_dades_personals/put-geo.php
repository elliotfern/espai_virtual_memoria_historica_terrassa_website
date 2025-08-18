<?php

/**
 * geocode_persones_paginat.php
 * - Procesa personas SIN coordenadas por lotes (keyset pagination)
 * - Construye dirección (tipus_via + adreca, ciutat, provincia, [comunitat si string], estat)
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

// === Helpers locales ===
function preferCatalan_local($provincia, $comunitat): bool
{
    $p = is_string($provincia) ? mb_strtolower(trim($provincia)) : '';
    $c = is_string($comunitat) ? mb_strtolower(trim($comunitat)) : '';
    $isCatProv = in_array($p, ['barcelona', 'girona', 'lleida', 'tarragona'], true);
    $isCatCom  = in_array($c, ['catalunya', 'cataluña', 'catalonia'], true);
    return $isCatProv || $isCatCom;
}

function normalizeStreet_local(string $street, bool $ca): string
{
    $s = trim($street);
    $reps = [
        '/^\s*(c\/|c\.|cl\/|cl\.|calle)\b/iu'                     => $ca ? 'Carrer '   : 'Calle ',
        '/^\s*(Avda\.?|av\.?|avenida)\b/iu'                       => $ca ? 'Avinguda ' : 'Avenida ',
        '/^\s*(Psg\.?|pº|paseo|ps\.?|pso\.?)\b/iu'                => $ca ? 'Passeig '  : 'Paseo ',
        '/^\s*(Pl\.?|plaza)\b/iu'                                 => $ca ? 'Plaça '    : 'Plaza ',
        '/^\s*(rda\.?|ronda)\b/iu'                                => 'Ronda ',
        '/^\s*(Ctra\.?|carretera)\b/iu'                           => 'Carretera ',
        '/^\s*(Ptge\.?|psje\.?|pasaje|passatge)\b/iu'             => $ca ? 'Passatge ' : 'Pasaje ',
        '/\bdr\.\b/iu'                                            => 'Doctor ',
        '/\bsd\.\b/iu'                                            => 'Santo ',
        '/\bs\/n\b/iu'                                            => ' s/n',
    ];
    foreach ($reps as $pattern => $replacement) {
        $s = preg_replace($pattern, $replacement, $s, 1);
    }
    $s = preg_replace('/\s*,\s*/u', ', ', $s);
    $s = preg_replace('/\s{2,}/u', ' ', $s);
    return trim($s);
}

/**
 * Elimina prefijos de tipo de vía ya escritos para evitar duplicados
 * (Carrer, Calle, Avinguda, etc.) en la adreça cruda.
 */
function stripStreetPrefix_local(string $street): string
{
    $s = trim($street);
    $patterns = [
        '/^\s*(c\/|c\.|cl\/|cl\.|calle|carrer|crr\.?|cr\.)\b/iu',
        '/^\s*(avda\.?|av\.?|avenida|avinguda|avgda\.?)\b/iu',
        '/^\s*(psg\.?|pº|paseo|ps\.?|pso\.?|passeig)\b/iu',
        '/^\s*(pl\.?|plaza|plaça)\b/iu',
        '/^\s*(rda\.?|ronda)\b/iu',
        '/^\s*(ctra\.?|carretera)\b/iu',
        '/^\s*(ptge\.?|psje\.?|pasaje|passatge)\b/iu',
        '/^\s*(via)\b/iu',
        '/^\s*(camí|cami|camino)\b/iu',
        '/^\s*(travessera|travessia|trv\.?)\b/iu',
        '/^\s*(boulevard|bulevard|blvd\.?)\b/iu',
        '/^\s*(baixada)\b/iu',
        '/^\s*(pujada)\b/iu',
    ];
    foreach ($patterns as $p) {
        $s = preg_replace($p, '', $s, 1);
    }
    // limpia coma inicial si la hubiera
    $s = preg_replace('/^\s*,\s*/u', '', $s);
    return trim($s);
}

/**
 * Construye la calle final:
 * - Si viene tipus_via (t.tipus_ca), lo antepone a la adreça (tras quitar prefijo duplicado).
 * - Si no, usa la normalización antigua sobre adreça.
 */
function assembleStreet_local(?string $rawAdreca, ?string $tipusVia, bool $ca): ?string
{
    $raw = is_string($rawAdreca) ? trim($rawAdreca) : '';
    $typ = is_string($tipusVia) ? trim($tipusVia) : '';
    if ($raw === '' && $typ === '') return null;

    if ($typ !== '') {
        $name = $raw !== '' ? stripStreetPrefix_local($raw) : '';
        $full = trim($typ . ' ' . $name);
        return normalizeStreet_local($full, $ca);
    }
    // sin tipus_via -> comportamiento antiguo
    return normalizeStreet_local($raw, $ca);
}

/**
 * Permite pasar una calle ya montada ($streetOverride).
 * Si no se pasa, hace el comportamiento antiguo con $r['adreca'].
 */
function buildFullAddress_local(array $r, string $defaultCountry, ?string $streetOverride = null): string
{
    $provinciaVal = $r['provincia'] ?? null;
    $comunitatVal = $r['comunitat'] ?? null;
    $caPref   = preferCatalan_local($provinciaVal, $comunitatVal);
    $parts    = [];

    $adreca = $streetOverride !== null
        ? trim((string)$streetOverride)
        : (isset($r['adreca']) ? normalizeStreet_local((string)$r['adreca'], $caPref) : '');

    if ($adreca !== '')           $parts[] = $adreca;
    $ciutat = trim((string)($r['ciutat'] ?? ''));
    if ($ciutat !== '')           $parts[] = $ciutat;
    $provincia = is_string($provinciaVal) ? trim($provinciaVal) : '';
    if ($provincia !== '')        $parts[] = $provincia;
    $comunitat = is_string($comunitatVal) ? trim($comunitatVal) : '';
    if ($comunitat !== '')        $parts[] = $comunitat;

    $estat = trim((string)($r['estat'] ?? ''));
    if ($estat === '')            $estat = $defaultCountry ?: 'España';
    $parts[] = $estat;

    $parts = array_values(array_unique(array_filter($parts)));
    return implode(', ', $parts);
}

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

function nominatimStructured_local(?string $street, ?string $city, ?string $state, ?string $country, string $ua, string $email): array
{
    $params = [
        'format' => 'jsonv2',
        'limit' => 1,
        'addressdetails' => 1,
        'accept-language' => 'ca,es,en',
        'countrycodes' => 'es',
        'email' => $email
    ];
    if ($street)  $params['street']  = $street;
    if ($city)    $params['city']    = $city;
    if ($state)   $params['state']   = $state;
    if ($country) $params['country'] = $country;

    $url = 'https://nominatim.openstreetmap.org/search?' . http_build_query($params);
    return http_get_first_json_local($url, $ua);
}

function nominatimFree_local(string $q, string $ua, string $email): array
{
    $url = 'https://nominatim.openstreetmap.org/search?' . http_build_query([
        'format' => 'jsonv2',
        'q' => $q,
        'limit' => 1,
        'addressdetails' => 1,
        'accept-language' => 'ca,es,en',
        'countrycodes' => 'es',
        'email' => $email
    ]);
    return http_get_first_json_local($url, $ua);
}

// ============== Conexión ==============
$conn = DatabaseConnection::getConnection();
if (!$conn) {
    die("No se pudo establecer conexión a la base de datos.\n");
}

// Cargamos datos de la persona (incluye tipus_via en catalán si existe)
$sql = "
    SELECT
        a.id AS rid,
        a.adreca,
        a.lat, a.lng,
        m.ciutat,
        m.comunitat,
        p.provincia, 
        s.estat,
        t.tipus_ca AS tipus_via_ca
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

// Preferencia idioma y composición de calle final (retro-compat)
$provVal = $row['provincia'] ?? null;
$comuVal = $row['comunitat'] ?? null;
$caPref  = preferCatalan_local($provVal, $comuVal);

$tipusVia = $row['tipus_via_ca'] ?? null;
$street   = assembleStreet_local($row['adreca'] ?? null, $tipusVia, $caPref);

// Construcción de dirección completa para fallback de texto libre
$addr = buildFullAddress_local($row, 'España', $street);
if ($addr === '') {
    http_response_code(422);
    echo json_encode([
        'status'  => 'fail',
        'message' => 'No hi ha prou dades per geocodificar (adreça/ciutat/província/pais)'
    ]);
    exit;
}

// Componentes para búsqueda estructurada
$city    = $row['ciutat'] ?? null;
$state   = is_string($provVal) ? $provVal : null;
$country = is_string($row['estat'] ?? null) ? $row['estat'] : 'España';

// 1) Nominatim estructurado
$resp = nominatimStructured_local($street, $city, $state, $country, $USER_AGENT, $CONTACT_EMAIL);
if ($resp['code'] === 429) {
    usleep(5_000_000); // 5s
    $resp = nominatimStructured_local($street, $city, $state, $country, $USER_AGENT, $CONTACT_EMAIL);
}
$res = $resp['result'];

// 2) Fallback a texto libre
if (!$res) {
    $resp = nominatimFree_local($addr, $USER_AGENT, $CONTACT_EMAIL);
    if ($resp['code'] === 429) {
        usleep(5_000_000); // 5s
        $resp = nominatimFree_local($addr, $USER_AGENT, $CONTACT_EMAIL);
    }
    $res = $resp['result'];
}

if (!is_array($res) || !isset($res['lat'], $res['lon'])) {
    http_response_code(404);
    echo json_encode(['status' => 'fail', 'message' => 'No s’han trobat coordenades']);
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
    'data'    => ['lat' => $lat, 'lng' => $lng]
]);
exit;
