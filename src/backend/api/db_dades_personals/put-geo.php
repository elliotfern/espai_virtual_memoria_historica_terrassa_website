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

// Configuración de cabeceras para aceptar JSON y responder JSON
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: https://memoriaterrassa.cat");
header("Access-Control-Allow-Methods: PUT");

// Dominio permitido (modifica con tu dominio)
$allowed_origin = "https://memoriaterrassa.cat";

// Verificar el encabezado 'Origin'
if (isset($_SERVER['HTTP_ORIGIN'])) {
    if ($_SERVER['HTTP_ORIGIN'] !== $allowed_origin) {
        http_response_code(403); // Respuesta 403 Forbidden
        echo json_encode(["error" => "Acceso denegado. Origen no permitido."]);
        exit;
    }
}

// Verificar que el método HTTP sea PUT
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405); // Método no permitido
    echo json_encode(["error" => "Método no permitido. Se requiere PUT."]);
    exit;
}

$userId = getAuthenticatedUserId();
if (!$userId) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit;
}


if (isset($_GET['type']) && $_GET['type'] === 'geocodePersona' && isset($_GET['id'])) {

    // === Config geocoder ===
    $USER_AGENT    = 'MemoriaTerrassa/1.0 (contacto: memoria@memoriaterrassa.cat)';
    $CONTACT_EMAIL = 'memoria@memoriaterrassa.cat';

    $id = (int)($_GET['id'] ?? 0);
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
            '/^\s*(c\/|c\.|cl\/|cl\.|calle)\b/iu'                    => $ca ? 'Carrer '   : 'Calle ',
            '/^\s*(Avda\.?|av\.?|avenida)\b/iu'                      => $ca ? 'Avinguda ' : 'Avenida ',
            '/^\s*(Psg\.?|pº|paseo|ps\.?|pso\.?)\b/iu'                => $ca ? 'Passeig '  : 'Paseo ',
            '/^\s*(Pl\.?|plaza)\b/iu'                                => $ca ? 'Plaça '    : 'Plaza ',
            '/^\s*(rda\.?|ronda)\b/iu'                               => 'Ronda ',
            '/^\s*(Ctra\.?|carretera)\b/iu'                          => 'Carretera ',
            '/^\s*(Ptge\.?|psje\.?|pasaje|passatge)\b/iu'            => $ca ? 'Passatge ' : 'Pasaje ',
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
    function buildFullAddress_local(array $r, string $defaultCountry): string
    {
        $provinciaVal = $r['provincia'] ?? null;
        $comunitatVal = $r['comunitat'] ?? null;
        $caPref   = preferCatalan_local($provinciaVal, $comunitatVal);
        $parts    = [];
        $adreca   = isset($r['adreca']) ? normalizeStreet_local((string)$r['adreca'], $caPref) : '';
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
        if ($err || $code !== 200 || !$body) return ['code' => $code ?: 0, 'result' => null];
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
        if ($street)  $params['street'] = $street;
        if ($city)    $params['city'] = $city;
        if ($state)   $params['state'] = $state;
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


    // Cargamos datos de la persona
    $sql = "
      SELECT
        a.id AS rid,
        a.adreca,
        a.lat, a.lng,
        m.ciutat,
        m.comunitat,                -- puede ser INT (FK) o string
        p.provincia,                -- texto
        s.estat                     -- texto
      FROM db_dades_personals a
      LEFT JOIN aux_dades_municipis           m ON m.id  = a.municipi_residencia
      LEFT JOIN aux_dades_municipis_provincia p ON p.id  = m.provincia
      LEFT JOIN aux_dades_municipis_estat     s ON s.id  = m.estat
      WHERE a.id = :id
      LIMIT 1
    ";
    $st = $conn->prepare($sql);
    $st->execute([':id' => $id]);
    $row = $st->fetch(PDO::FETCH_ASSOC);

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
            echo json_encode(['status' => 'success', 'message' => 'Coordenades ja existents', 'data' => ['lat' => $latNum, 'lng' => $lngNum]]);
            exit;
        }
    }

    // Construcción de dirección
    $addr = buildFullAddress_local($row, 'España');
    if ($addr === '') {
        http_response_code(422);
        echo json_encode(['status' => 'fail', 'message' => 'No hi ha prou dades per geocodificar (adreça/ciutat/província/pais)']);
        exit;
    }

    // Componentes para estructurado
    $provVal = $row['provincia'] ?? null;
    $comuVal = $row['comunitat'] ?? null;
    $caPref  = preferCatalan_local($provVal, $comuVal);
    $street  = isset($row['adreca']) ? normalizeStreet_local((string)$row['adreca'], $caPref) : null;
    $city    = $row['ciutat']    ?? null;
    $state   = is_string($provVal) ? $provVal : null;
    $country = is_string($row['estat'] ?? null) ? $row['estat'] : 'España';


    // ============== Proceso por lotes ==============
    // 1) estructurado
    $resp = nominatimStructured_local($street, $city, $state, $country, $USER_AGENT, $CONTACT_EMAIL);
    if ($resp['code'] === 429) {
        usleep(5000 * 1000);
        $resp = nominatimStructured_local($street, $city, $state, $country, $USER_AGENT, $CONTACT_EMAIL);
    }
    $res = $resp['result'];

    // 2) fallback
    if (!$res) {
        $resp = nominatimFree_local($addr, $USER_AGENT, $CONTACT_EMAIL);
        if ($resp['code'] === 429) {
            usleep(5000 * 1000);
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

    echo json_encode(['status' => 'success', 'message' => 'Coordenades actualitzades', 'data' => ['lat' => $lat, 'lng' => $lng]]);
    exit;
}
