<?php

/**
 * geocode_persones.php
 * Toma adreca + municipi_residencia -> (ciutat, provincia, estat),
 * geocodifica con Nominatim y guarda lat/lng en db_dades_personals.
 */

declare(strict_types=1);
ini_set('memory_limit', '512M');
set_time_limit(0);

use App\Config\Tables;
use App\Config\Audit;
use App\Config\DatabaseConnection;
use App\Utils\MissatgesAPI;
use App\Utils\Response;
use App\Utils\ValidacioErrors;

// ===== Límite y ritmo =====
$BATCH_LIMIT = 200;   // cuántos por pasada
$SLEEP_MS    = 1100;  // ~1.1s entre llamadas (Nominatim)

// ===== Identificación Nominatim =====
$USER_AGENT    = 'MemoriaTerrassa/1.0 (contacto: memoria@memoriaterrassa.cat)';
$CONTACT_EMAIL = 'memoria@memoriaterrassa.cat';


$conn = DatabaseConnection::getConnection();

if (!$conn) {
    die("No se pudo establecer conexión a la base de datos.");
}


global $conn;
/** @var PDO $conn */


// ===== Selección (sin coords) =====
$sel = $conn->prepare("
  SELECT
    a.id                                   AS rid,
    a.adreca                               AS adreca,
    m.ciutat                               AS ciutat,
    p.provincia                            AS provincia,
    s.estat                                AS estat
  FROM db_dades_personals a
  LEFT JOIN aux_dades_municipis           m ON m.id  = a.municipi_residencia
  LEFT JOIN aux_dades_municipis_provincia p ON p.id  = m.provincia
  LEFT JOIN aux_dades_municipis_estat     s ON s.id  = m.estat
  WHERE (a.lat IS NULL OR a.lng IS NULL)
    AND (COALESCE(a.adreca,'') <> '' OR a.municipi_residencia IS NOT NULL)
  LIMIT :lim
");
$sel->bindValue(':lim', $BATCH_LIMIT, PDO::PARAM_INT);
$sel->execute();
$rows = $sel->fetchAll();

if (!$rows) {
    echo "Nada que geocodificar.\n";
    exit(0);
}

// ===== Update preparado =====
$upd = $conn->prepare("
  UPDATE db_dades_personals
  SET lat = :lat, lng = :lng
  WHERE id = :id
");

// ===== Helpers =====
function sleep_ms(int $ms): void
{
    usleep($ms * 1000);
}

function buildAddress(array $r): string
{
    // Construye "adreca, ciutat, provincia, estat" sin vacíos ni duplicados
    $parts = [];
    foreach (['adreca', 'ciutat', 'provincia', 'estat'] as $k) {
        $v = isset($r[$k]) ? trim((string)$r[$k]) : '';
        if ($v !== '') $parts[] = $v;
    }
    $parts = array_values(array_unique($parts));
    return implode(', ', $parts);
}

function nominatimGeocode(string $q, string $ua, string $email): ?array
{
    $url = 'https://nominatim.openstreetmap.org/search?' . http_build_query([
        'format'          => 'jsonv2',
        'q'               => $q,
        'limit'           => 1,
        'addressdetails'  => 1,
        'accept-language' => 'ca,es,en',
        'countrycodes'    => 'es',        // sesgo España
        'email'           => $email,
    ]);

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

    if ($err || $code !== 200 || !$body) return null;
    $json = json_decode($body, true);
    if (!is_array($json) || empty($json)) return null;
    return $json[0];
}

// ===== Proceso =====
$processed = 0;
foreach ($rows as $r) {
    $id = (int)$r['rid'];
    $addr = buildAddress($r);
    if ($addr === '') {
        echo "#$id -> sin dirección\n";
        continue;
    }

    sleep_ms($SLEEP_MS); // respeta Nominatim

    $res = nominatimGeocode($addr, $USER_AGENT, $CONTACT_EMAIL);
    if ($res && isset($res['lat'], $res['lon'])) {
        $lat = (float)$res['lat'];
        $lng = (float)$res['lon'];
        $upd->execute([':lat' => $lat, ':lng' => $lng, ':id' => $id]);
        echo "#$id -> OK ($lat,$lng) | $addr\n";
    } else {
        echo "#$id -> NOT_FOUND | $addr\n";
    }

    $processed++;
}

echo "Hecho. Procesados: $processed\n";
