<?php

/**
 * geocode_persones_paginat_icgc.php (1 id)
 * - Construye la dirección a partir de BD: tipus_via_ca (tipo vía), adreca (nombre vía), adreca_num (portal)
 * - Geocodifica con ICGC (cerca)
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
if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'PUT'], true)) {
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

// === Config ICGC ===
const ICGC_BASE = 'https://eines.icgc.cat/geocodificador/cerca';
const UA_HEADER = 'User-Agent: MemoriaTerrassa/1.0 (memoria@memoriaterrassa.cat)';

// === id desde query o cuerpo JSON ===
$raw  = file_get_contents('php://input');
$body = json_decode($raw ?: 'null', true);
$id   = isset($_GET['id']) ? (int)$_GET['id'] : (int)($body['id'] ?? 0);
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'fail', 'message' => 'id inválido']);
    exit;
}

// ============== Conexión ==============
$conn = DatabaseConnection::getConnection();
if (!$conn) {
    http_response_code(500);
    echo json_encode(['status' => 'fail', 'message' => 'No se pudo establecer conexión a la base de datos']);
    exit;
}

// Cargar datos de la persona
$sql = "SELECT
  a.id AS rid,
  a.lat, a.lng,
  t.tipus_ca       AS tipus_via_ca,   -- tipo de vía (Carrer, Passeig, ...)
  a.adreca         AS via_nom,        -- nombre vía
  a.adreca_num     AS via_num,        -- número portal
  m.ciutat         AS ciutat,
  p.provincia      AS provincia,
  s.estat          AS estat,
  mc.comunitat_ca  AS comunitat_ca
FROM db_dades_personals a
LEFT JOIN aux_dades_municipis            m  ON m.id  = a.municipi_residencia
LEFT JOIN aux_dades_municipis_provincia  p  ON p.id  = m.provincia
LEFT JOIN aux_dades_municipis_estat      s  ON s.id  = m.estat
LEFT JOIN aux_dades_municipis_comunitat  mc ON mc.id = m.comunitat
LEFT JOIN aux_tipus_via                  t  ON a.tipus_via = t.id
WHERE a.id = :id
LIMIT 1";
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

// ===== Helpers ICGC =====
/**
 * Llama al geocodificador del ICGC con el formato recomendado:
 *  - text: "<tipus_via> <adreca> <adreca_num>, <ciutat>"  (si hay número)
 *          "<tipus_via> <adreca>, <ciutat>"               (si no hay número)
 * Filtra por layers=address y size=1.
 *
 * @return array|null  [
 *   'lat' => float, 'lng' => float,
 *   'precision' => 'house'|'street',
 *   'icgc' => [...props útiles...],
 *   'text' => string  // consulta enviada
 * ]
 */
function icgcGeocodeAddress(string $tipus_via, string $adreca, ?string $adreca_num, string $ciutat): ?array
{
    $hasNum = $adreca_num !== null && $adreca_num !== '' && strtolower($adreca_num) !== 's/n';

    // 1) Intento con número (si existe) y exigimos coincidencia exacta de portal
    if ($hasNum) {
        $textHouse = trim($tipus_via . ' ' . $adreca . ' ' . $adreca_num) . ', ' . $ciutat;
        $resHouse  = icgcRequest($textHouse);
        if ($resHouse && isset($resHouse['features'][0])) {
            $feat  = $resHouse['features'][0];
            $props = $feat['properties'] ?? [];
            $portalResp = isset($props['portal']) ? trim((string)$props['portal']) : '';

            if ($portalResp !== '' && strcasecmp($portalResp, (string)$adreca_num) === 0) {
                [$lng, $lat] = $feat['geometry']['coordinates'];
                return [
                    'lat' => (float)$lat,
                    'lng' => (float)$lng,
                    'precision' => 'house',
                    'icgc' => [
                        'etiqueta'   => $props['etiqueta'] ?? null,
                        'tipus_via'  => $props['tipus_via'] ?? null,
                        'nom'        => $props['nom'] ?? null,
                        'portal'     => $props['portal'] ?? null,
                        'municipi'   => $props['municipi'] ?? null,
                        'comarca'    => $props['comarca'] ?? null,
                    ],
                    'text' => $textHouse
                ];
            }
            // si no coincide portal, continuamos a calle
        }
    }

    // 2) Intento sin número (nivel calle)
    $textStreet = trim($tipus_via . ' ' . $adreca) . ', ' . $ciutat;
    $resStreet  = icgcRequest($textStreet);
    if ($resStreet && isset($resStreet['features'][0])) {
        $feat = $resStreet['features'][0];
        if (isset($feat['geometry']['coordinates'][0], $feat['geometry']['coordinates'][1])) {
            [$lng, $lat] = $feat['geometry']['coordinates'];
            $props = $feat['properties'] ?? [];
            return [
                'lat' => (float)$lat,
                'lng' => (float)$lng,
                'precision' => 'street',
                'icgc' => [
                    'etiqueta'   => $props['etiqueta'] ?? null,
                    'tipus_via'  => $props['tipus_via'] ?? null,
                    'nom'        => $props['nom'] ?? null,
                    'portal'     => $props['portal'] ?? null, // normalmente vacío en calle
                    'municipi'   => $props['municipi'] ?? null,
                    'comarca'    => $props['comarca'] ?? null,
                ],
                'text' => $textStreet
            ];
        }
    }

    return null;
}

/**
 * Petición HTTP al endpoint ICGC `cerca` devolviendo el JSON como array.
 * Usa layers=address y size=1 para obtener la mejor coincidencia de dirección postal.
 */
function icgcRequest(string $text): ?array
{
    $query = http_build_query([
        'text'   => $text,
        'layers' => 'address',
        'size'   => 1
        // Consejo: si trabajas sólo en Terrassa puedes añadir 'mun' => 'Terrassa'
    ]);

    $url = ICGC_BASE . '?' . $query;

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_HTTPHEADER => [UA_HEADER],
    ]);
    $body = curl_exec($ch);
    $err  = curl_error($ch);
    $code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);

    if ($err || $code !== 200 || !$body) return null;

    $json = json_decode($body, true);
    if (!is_array($json)) return null;
    return $json;
}

// ===== Construcción directa desde BD (sin normalizar ni variantes) =====
$viaNom  = trim((string)($row['via_nom']      ?? ''));   // nombre calle
$viaType = trim((string)($row['tipus_via_ca'] ?? ''));   // tipo vía (Carrer, Passeig, ...)
$viaNum  = trim((string)($row['via_num']      ?? ''));   // número
$city    = is_string($row['ciutat']        ?? null) ? trim($row['ciutat'])        : '';
$county  = is_string($row['provincia']     ?? null) ? trim($row['provincia'])     : '';
$state   = is_string($row['comunitat_ca']  ?? null) ? trim($row['comunitat_ca'])  : '';
$country = is_string($row['estat']         ?? null) ? trim($row['estat'])         : 'España';

if ($viaNom === '') {
    http_response_code(422);
    echo json_encode(['status' => 'fail', 'message' => 'Falta el nombre de la vía (adreca/via_nom)']);
    exit;
}
if ($city === '') {
    http_response_code(422);
    echo json_encode(['status' => 'fail', 'message' => 'Falta la ciutat en la ficha (municipi_residencia)']);
    exit;
}

// ===== Llamada ICGC =====
$result = icgcGeocodeAddress($viaType, $viaNom, $viaNum, $city);

if (!$result) {
    http_response_code(404);
    echo json_encode([
        'status'  => 'fail',
        'message' => 'No s’han trobat coordenades al ICGC',
        'debug'   => [
            'tipus_via_ca' => $viaType,
            'via_nom'      => $viaNom,
            'via_num'      => $viaNum,
            'ciutat'       => $city,
            'provincia'    => $county,
            'comunitat_ca' => $state,
            'estat'        => $country
        ]
    ]);
    exit;
}

$lat = (float)$result['lat'];
$lng = (float)$result['lng'];

// Guardar en BD
$upd = $conn->prepare("UPDATE db_dades_personals SET lat = :lat, lng = :lng WHERE id = :id");
$upd->execute([':lat' => $lat, ':lng' => $lng, ':id' => $id]);

echo json_encode([
    'status'  => 'success',
    'message' => 'Coordenades actualitzades (ICGC)',
    'data'    => [
        'lat'        => $lat,
        'lng'        => $lng,
        'precision'  => $result['precision'] ?? null,
        'query_text' => $result['text'] ?? null,
        'icgc'       => $result['icgc'] ?? null
    ],
]);
exit;
