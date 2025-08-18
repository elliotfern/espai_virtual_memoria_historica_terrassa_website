<?php

/**
 * geocode_persones_paginat.php
 * - Procesa personas SIN coordenadas por lotes (keyset pagination)
 * - Construye dirección (adreca, ciutat, provincia, [comunitat si string], estat)
 * - Geocodifica con Nominatim (estructurado + fallback)
 * - Guarda lat/lng en db_dades_personals
 */

declare(strict_types=1);
ini_set('memory_limit', '512M');
set_time_limit(0);

use App\Config\DatabaseConnection;

// ============== Config ==============
$BATCH_SIZE    = 300; // tamaño de lote
$SLEEP_MS      = 1100; // ~1.1s entre llamadas (respeta Nominatim)
$USER_AGENT    = 'MemoriaTerrassa/1.0 (contacto: memoria@memoriaterrassa.cat)';
$CONTACT_EMAIL = 'memoria@memoriaterrassa.cat';

// ============== Conexión ==============
$conn = DatabaseConnection::getConnection();
if (!$conn) {
    die("No se pudo establecer conexión a la base de datos.\n");
}
/** @var PDO $conn */

// ============== SQL ==============
$selectSql = "
  SELECT
    a.id                                   AS rid,
    a.adreca                               AS adreca,
    m.ciutat                               AS ciutat,
    m.comunitat                            AS comunitat,    -- puede ser INT (FK) o string
    p.provincia                            AS provincia,
    s.estat                                AS estat
  FROM db_dades_personals a
  LEFT JOIN aux_dades_municipis           m ON m.id  = a.municipi_residencia
  LEFT JOIN aux_dades_municipis_provincia p ON p.id  = m.provincia
  LEFT JOIN aux_dades_municipis_estat     s ON s.id  = m.estat
  WHERE (a.lat IS NULL OR a.lng IS NULL)
    AND (COALESCE(a.adreca,'') <> '' OR a.municipi_residencia IS NOT NULL)
    AND a.id > :lastId
  ORDER BY a.id ASC
  LIMIT :lim
";

$updateSql = "
  UPDATE db_dades_personals
  SET lat = :lat, lng = :lng
  WHERE id = :id
";

$sel = $conn->prepare($selectSql);
$upd = $conn->prepare($updateSql);

// ============== Helpers ==============
function sleep_ms(int $ms): void
{
    usleep($ms * 1000);
}

/** acepta string o int y castea internamente */
function preferCatalan($provincia, $comunitat): bool
{
    $p = is_string($provincia) ? mb_strtolower(trim($provincia)) : '';
    $c = is_string($comunitat) ? mb_strtolower(trim($comunitat)) : '';
    $isCatProv = in_array($p, ['barcelona', 'girona', 'lleida', 'tarragona'], true);
    $isCatCom  = in_array($c, ['catalunya', 'cataluña', 'catalonia'], true);
    return $isCatProv || $isCatCom;
}

function normalizeStreet(string $street, bool $ca): string
{
    $s = trim($street);
    $reps = [
        '/^\s*(c\/|c\.|cl\/|cl\.|calle)\b/iu'                    => $ca ? 'Carrer '   : 'Calle ',
        '/^\s*(avda\.?|av\.?|avenida)\b/iu'                      => $ca ? 'Avinguda ' : 'Avenida ',
        '/^\s*(pg\.?|pº|paseo|ps\.?|pso\.?)\b/iu'                => $ca ? 'Passeig '  : 'Paseo ',
        '/^\s*(pl\.?|plaza)\b/iu'                                => $ca ? 'Plaça '    : 'Plaza ',
        '/^\s*(rda\.?|ronda)\b/iu'                               => 'Ronda ',
        '/^\s*(ctra\.?|carretera)\b/iu'                          => 'Carretera ',
        '/^\s*(ptge\.?|psje\.?|pasaje|passatge)\b/iu'            => $ca ? 'Passatge ' : 'Pasaje ',
        '/\bdr\.\b/iu'                                           => 'Doctor ',
        '/\bsd\.\b/iu'                                           => 'Santo ',
        '/\bs\/n\b/iu'                                           => ' s/n',
    ];
    foreach ($reps as $pattern => $replacement) {
        $s = preg_replace($pattern, $replacement, $s, 1);
    }
    $s = preg_replace('/\s*,\s*/u', ', ', $s);
    $s = preg_replace('/\s{2,}/u', ' ', $s);
    return trim($s);
}

/** comunitat solo si es string; si es id numérico, se omite */
function buildFullAddress(array $r, string $defaultCountry): string
{
    $provinciaVal = $r['provincia'] ?? null;
    $comunitatVal = $r['comunitat'] ?? null;

    $caPref   = preferCatalan($provinciaVal, $comunitatVal);
    $parts    = [];

    $adreca   = isset($r['adreca']) ? normalizeStreet((string)$r['adreca'], $caPref) : '';
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

// HTTP helper → primer resultado + código HTTP
function http_get_first_json(string $url, string $ua): array
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
    if (!is_array($json) || empty($json)) {
        return ['code' => $code, 'result' => null];
    }
    return ['code' => $code, 'result' => $json[0]];
}

// Nominatim estructurado
function nominatimGeocodeStructured(
    ?string $street,
    ?string $city,
    ?string $state,
    ?string $country,
    string $ua,
    string $email
): array {
    $params = [
        'format'          => 'jsonv2',
        'limit'           => 1,
        'addressdetails'  => 1,
        'accept-language' => 'ca,es,en',
        'countrycodes'    => 'es',
        'email'           => $email,
    ];
    if ($street)  $params['street']  = $street;
    if ($city)    $params['city']    = $city;
    if ($state)   $params['state']   = $state;
    if ($country) $params['country'] = $country;

    $url = 'https://nominatim.openstreetmap.org/search?' . http_build_query($params);
    return http_get_first_json($url, $ua);
}

// Nominatim libre (fallback)
function nominatimGeocodeFree(string $q, string $ua, string $email): array
{
    $url = 'https://nominatim.openstreetmap.org/search?' . http_build_query([
        'format'          => 'jsonv2',
        'q'               => $q,
        'limit'           => 1,
        'addressdetails'  => 1,
        'accept-language' => 'ca,es,en',
        'countrycodes'    => 'es',
        'email'           => $email,
    ]);
    return http_get_first_json($url, $ua);
}

// ============== Proceso por lotes ==============
$lastId    = 0;
$totalDone = 0;

do {
    // 1) Leer lote
    $sel->bindValue(':lastId', $lastId, PDO::PARAM_INT);
    $sel->bindValue(':lim', $BATCH_SIZE, PDO::PARAM_INT);
    $sel->execute();
    $rows = $sel->fetchAll(PDO::FETCH_ASSOC);
    $sel->closeCursor();

    if (!$rows) break;

    // 2) Procesar lote
    foreach ($rows as $r) {
        $id   = (int)$r['rid'];
        $addr = buildFullAddress($r, 'España');

        if ($addr === '') {
            echo "#$id -> sin dirección\n";
            $lastId = $id;
            continue;
        }

        // Componentes estructurados
        $provinciaVal = $r['provincia'] ?? null;
        $comunitatVal = $r['comunitat'] ?? null;
        $caPref  = preferCatalan($provinciaVal, $comunitatVal);
        $street  = isset($r['adreca']) ? normalizeStreet((string)$r['adreca'], $caPref) : null;
        $city    = $r['ciutat']     ?? null;
        $state   = is_string($provinciaVal) ? $provinciaVal : null; // solo si es string
        $country = $r['estat']      ?? 'España';
        $country = is_string($country) ? $country : 'España';

        // Ritmo
        sleep_ms($SLEEP_MS);

        // 1) Estructurado
        $resp = nominatimGeocodeStructured($street, $city, $state, $country, $USER_AGENT, $CONTACT_EMAIL);

        // 429 → espera y reintenta 1 vez
        if ($resp['code'] === 429) {
            fwrite(STDERR, "HTTP 429: esperando 5s...\n");
            sleep_ms(5000);
            $resp = nominatimGeocodeStructured($street, $city, $state, $country, $USER_AGENT, $CONTACT_EMAIL);
        }

        $res = $resp['result'];

        // 2) Fallback a texto libre
        if (!$res) {
            $resp = nominatimGeocodeFree($addr, $USER_AGENT, $CONTACT_EMAIL);
            if ($resp['code'] === 429) {
                fwrite(STDERR, "HTTP 429 (fallback): esperando 5s...\n");
                sleep_ms(5000);
                $resp = nominatimGeocodeFree($addr, $USER_AGENT, $CONTACT_EMAIL);
            }
            $res = $resp['result'];
        }

        if (is_array($res) && isset($res['lat'], $res['lon'])) {
            $lat = (float)$res['lat'];
            $lng = (float)$res['lon'];
            $upd->execute([':lat' => $lat, ':lng' => $lng, ':id' => $id]);
            echo "#$id -> OK ($lat,$lng) | $addr\n";
        } else {
            echo "#$id -> NOT_FOUND | $addr\n";
        }

        $lastId = $id; // avanzar cursor por id
        $totalDone++;
    }

    // 3) Siguiente lote mientras lleguen filas
} while (count($rows) === $BATCH_SIZE);

echo "Hecho. Procesados totales: $totalDone\n";
