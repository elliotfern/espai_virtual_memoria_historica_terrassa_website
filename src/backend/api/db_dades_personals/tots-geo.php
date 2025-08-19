<?php

/**
 * geocode_persones_rang_icgc.php
 * - Procesa un rango de IDs [from, to]
 * - Geocodifica con ICGC y guarda lat/lng
 * - Solo procesa registros con datos mínimos válidos (tipo+vía o al menos vía) y ciudad
 * - Opción: solo los que no tienen lat/lng
 * 
 * Cómo usarlo

 *Solo IDs sin coordenadas:

 *GET https://memoriaterrassa.cat/api/dades_personals/geo/tots?from=56&to=10000&only_missing=1


 *Forzar recalcular aunque ya tengan coordenadas:

 *GET https://memoriaterrassa.cat/api/dades_personals/geo/tots?from=56&to=10000&only_missing=0
 */

declare(strict_types=1);
ini_set('memory_limit', '512M');
set_time_limit(0);

use App\Config\DatabaseConnection;

// ===== CORS (igual que tu script) =====
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

// Autenticación
$userId = getAuthenticatedUserId();
if (!$userId) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

// === Parámetros ===
$from = isset($_GET['from']) ? (int)$_GET['from'] : 0;
$to   = isset($_GET['to'])   ? (int)$_GET['to']   : 0;
$onlyMissing = isset($_GET['only_missing']) ? ($_GET['only_missing'] === '0' ? false : true) : true;
$dry = isset($_GET['dry']) ? ($_GET['dry'] === '1') : false;

if ($from <= 0 || $to <= 0 || $to < $from) {
    http_response_code(400);
    echo json_encode(['status' => 'fail', 'message' => 'Parámetros inválidos: from/to']);
    exit;
}

// ============== Conexión ==============
$conn = DatabaseConnection::getConnection();
if (!$conn) {
    http_response_code(500);
    echo json_encode(['status' => 'fail', 'message' => 'No se pudo establecer conexión a la base de datos']);
    exit;
}

// === Config ICGC ===
const ICGC_BASE = 'https://eines.icgc.cat/geocodificador/cerca';
const UA_HEADER = 'User-Agent: MemoriaTerrassa/1.0 (memoria@memoriaterrassa.cat)';

// ===== Helpers ICGC (idénticos a tu script de 1 id) =====
function icgcGeocodeAddress(string $tipus_via, string $adreca, ?string $adreca_num, string $ciutat): ?array
{
    $hasNum = $adreca_num !== null && $adreca_num !== '' && strtolower($adreca_num) !== 's/n';

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
        }
    }

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
                    'portal'     => $props['portal'] ?? null,
                    'municipi'   => $props['municipi'] ?? null,
                    'comarca'    => $props['comarca'] ?? null,
                ],
                'text' => $textStreet
            ];
        }
    }
    return null;
}

function icgcRequest(string $text): ?array
{
    $query = http_build_query([
        'text'   => $text,
        'layers' => 'address',
        'size'   => 1
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

// ===== SELECT de candidatos en rango =====
// Requisitos mínimos:
//  - via_nom (adreca) no vacía
//  - ciutat no vacía
//  - si $onlyMissing: sin coordenadas o con 0/0
$condMissing = $onlyMissing
    ? "AND (a.lat IS NULL OR a.lng IS NULL OR a.lat = 0 OR a.lng = 0)"
    : "";

$sql = "
SELECT
  a.id AS rid,
  a.lat, a.lng,
  t.tipus_ca       AS tipus_via_ca,
  a.adreca         AS via_nom,
  a.adreca_num     AS via_num,
  m.ciutat         AS ciutat
FROM db_dades_personals a
LEFT JOIN aux_tipus_via                  t  ON a.tipus_via = t.id
LEFT JOIN aux_dades_municipis            m  ON m.id  = a.municipi_residencia
WHERE a.id BETWEEN :from AND :to
  AND a.adreca IS NOT NULL AND TRIM(a.adreca) <> ''
  AND m.ciutat IS NOT NULL AND TRIM(m.ciutat) <> ''
  $condMissing
ORDER BY a.id
";
$st = $conn->prepare($sql);
$st->execute([':from' => $from, ':to' => $to]);
$rows = $st->fetchAll(\PDO::FETCH_ASSOC);

// ===== Update preparado =====
$upd = $conn->prepare("UPDATE db_dades_personals SET lat = :lat, lng = :lng WHERE id = :id");

// ===== Proceso =====
$summary = [
    'status'        => 'success',
    'from'          => $from,
    'to'            => $to,
    'only_missing'  => $onlyMissing,
    'dry'           => $dry,
    'total_candidates' => count($rows),
    'updated'       => 0,
    'skipped'       => 0,
    'errors'        => 0,
    'details'       => []
];

foreach ($rows as $row) {
    $id      = (int)$row['rid'];
    $viaType = trim((string)($row['tipus_via_ca'] ?? '')); // puede estar vacío -> buscará a nivel de calle igualmente
    $viaNom  = trim((string)($row['via_nom']      ?? ''));
    $viaNum  = trim((string)($row['via_num']      ?? ''));
    $city    = trim((string)($row['ciutat']       ?? ''));

    // Validación mínima
    if ($viaNom === '' || $city === '') {
        $summary['skipped']++;
        $summary['details'][] = ['id' => $id, 'result' => 'skip', 'reason' => 'Faltan via_nom o ciutat'];
        continue;
    }

    // Si ya hay coords válidas y only_missing, saltamos
    if ($onlyMissing && $row['lat'] !== null && $row['lng'] !== null) {
        $latNum = (float)$row['lat'];
        $lngNum = (float)$row['lng'];
        if ($latNum >= -90 && $latNum <= 90 && $lngNum >= -180 && $lngNum <= 180 && ($latNum != 0 || $lngNum != 0)) {
            $summary['skipped']++;
            $summary['details'][] = ['id' => $id, 'result' => 'skip', 'reason' => 'coords ya válidas'];
            continue;
        }
    }

    try {
        $res = icgcGeocodeAddress($viaType, $viaNom, $viaNum !== '' ? $viaNum : null, $city);

        if (!$res) {
            $summary['errors']++;
            $summary['details'][] = ['id' => $id, 'result' => 'error', 'reason' => 'ICGC sin resultados', 'query' => trim($viaType . ' ' . $viaNom . ' ' . $viaNum) . ', ' . $city];
            continue;
        }

        $lat = (float)$res['lat'];
        $lng = (float)$res['lng'];

        if (!$dry) {
            $upd->execute([':lat' => $lat, ':lng' => $lng, ':id' => $id]);
        }

        $summary['updated']++;
        $summary['details'][] = [
            'id'        => $id,
            'result'    => $dry ? 'ok (dry)' : 'ok',
            'lat'       => $lat,
            'lng'       => $lng,
            'precision' => $res['precision'] ?? null,
            'query'     => $res['text'] ?? null
        ];

        // Pequeño respiro para no estresar el servicio (ajusta si hace falta)
        usleep(120000); // 120 ms

    } catch (\Throwable $e) {
        $summary['errors']++;
        $summary['details'][] = ['id' => $id, 'result' => 'error', 'reason' => $e->getMessage()];
    }
}

echo json_encode($summary);
exit;
