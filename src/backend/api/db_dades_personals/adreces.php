<?php

/**
 * Normalizador de direcciones:
 * - Lee filas (ej. personas) con adreca no vacía.
 * - Intenta deducir tipus_via y adreca_num.
 * - Actualiza solo cuando tiene datos fiables.
 */

use App\Config\DatabaseConnection;


// ============== Conexión ==============
$conn = DatabaseConnection::getConnection();
if (!$conn) {
    http_response_code(500);
    echo json_encode(['status' => 'fail', 'message' => 'No se pudo establecer conexión a la base de datos']);
    exit;
}

const DRY_RUN = false; // ← Pon a false para escribir en BD


// === Mapeo de tipos de vía ===
// Usa claves en minúsculas y sin puntos. Añadimos alias comunes al mismo código.
$TIPUS_VIA_MAP = [
    // 1: Calle
    'c/'        => 1,
    'c' => 1,
    'C/' => 1,
    'C/ ' => 1,
    'carrer' => 1,
    'c/ ' => 1,
    // 2: Carretera
    'ctra'      => 2,
    'carretera' => 2,
    // 18: Rambleta
    'rambleta'  => 18,
    // 4: Rambla
    'rambla'    => 4,
    // 6: Plaza / Plaça
    'pl'        => 6,
    'plaça' => 6,
    'placa' => 6,
    'pça' => 6,
    'plaza' => 6,
    // 8: Puente (abreviado)
    'pgte'      => 8,
    'pte' => 8,
    'puente' => 8,
];

// Normaliza acentos y puntos, minúsculas
function norm_token(string $s): string
{
    $s = trim($s);
    $s = mb_strtolower($s, 'UTF-8');
    $s = preg_replace('/[\.]+$/', '', $s); // quita puntos finales
    // normaliza acentos (mejor con intl, pero iconv ayuda)
    $t = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
    if ($t !== false) {
        $s = $t;
    }
    $s = preg_replace('/\s+/', ' ', $s);
    return $s;
}

/**
 * Intenta detectar tipo de vía al inicio y devuelve [codigo|null, nombreSinTipo]
 * Acepta formas con o sin slash y punto: "C/", "C.", "C ", "Carrer", "Ctra.", "Pl.", "Pça", etc.
 */
function parse_tipus_via_y_rest(string $raw, array $map): array
{
    $s = trim($raw);

    // 2.1) Detección por prefijo anclado (sin requerir espacio):
    // construimos lista de prefijos equivalentes -> clave del mapa
    $prefixes = [
        // calle
        '/^\s*c[\/\.]\s*/iu'        => 'c/',      // C/  o C.
        '/^\s*carrer\s+/iu'         => 'carrer',
        // carretera
        '/^\s*ctra\.?\s*/iu'        => 'ctra',    // Ctra o Ctra.
        '/^\s*carretera\s+/iu'      => 'carretera',
        // rambleta / rambla
        '/^\s*rambleta\s+/iu'       => 'rambleta',
        '/^\s*rambla\s+/iu'         => 'rambla',
        // plaza / plaça
        '/^\s*pl\.?\s*/iu'          => 'pl',      // Pl o Pl.
        '/^\s*p[çc]a\.?\s*/iu'      => 'pça',     // Pça / Pca
        '/^\s*pla[çc]a\s+/iu'       => 'plaça',   // Plaça / Placa
        '/^\s*plaza\s+/iu'          => 'plaza',
        // puente
        '/^\s*pgte\.?\s*/iu'        => 'pgte',
        '/^\s*pte\.?\s*/iu'         => 'pte',
        '/^\s*puente\s+/iu'         => 'puente',
    ];

    foreach ($prefixes as $rx => $key) {
        if (preg_match($rx, $s, $m)) {
            $code = $map[norm_token($key)] ?? null;
            if ($code !== null) {
                $rest = preg_replace($rx, '', $s, 1);
                return [$code, trim($rest)];
            }
        }
    }

    // 2.2) Fallback por primer token (tu lógica original)
    if (!preg_match('/^\s*([^\s,]+)\s*(.*)$/u', $s, $m)) {
        return [null, $s];
    }
    $token = $m[1];
    $rest  = trim($m[2]);

    $tokenKey = norm_token($token);
    $tokenKeyNoSlash = rtrim($tokenKey, '/');

    $code = $map[$tokenKey] ?? ($map[$tokenKeyNoSlash] ?? null);

    if ($code !== null) {
        return [$code, $rest];
    }

    return [null, $s];
}
/**
 * Extrae el número al final (con o sin coma intermedia).
 * Soporta: "..., 177", " ... 177", " ... 12-14" (toma 12), " ... s/n"
 * Y elimina cualquier texto que venga después del número.
 */
function parse_numero_final(string $raw): array
{
    $s = trim($raw);

    // s/n (sin número)
    if (preg_match('/(?:^|[\s,])s[\s\-\/]?n\.?$/iu', $s)) {
        $s = preg_replace('/(?:^|[\s,])s[\s\-\/]?n\.?$/iu', '', $s);
        return [trim($s, " \t,-"), null];
    }

    // número al final (posible "basura" después: paréntesis, texto, etc.)
    // permite separador coma o espacio antes del número, y corta todo lo posterior
    if (preg_match('/[, ]+\s*([0-9]+)(?:[\-\/][0-9]+)?(?:.*)$/u', $s, $m)) {
        $num = (int)$m[1];
        // recorta desde el separador previo al número incluido todo lo que siga
        $s = preg_replace('/[, ]+\s*([0-9]+)(?:[\-\/][0-9]+)?(?:.*)$/u', '', $s);
        return [trim($s, " \t,-"), $num === 0 ? null : $num];
    }

    return [$s, null];
}

/**
 * Normaliza una línea de adreca → [tipus_via|null, adreca (nombre), adreca_num|null]
 */
function normalizar_adreca_line(string $adreca, array $map): array
{
    $adreca = trim($adreca);

    // 1) Detectar tipo de vía inicial
    [$tipusVia, $resto] = parse_tipus_via_y_rest($adreca, $map);

    // 2) Extraer número al final (si hay)
    [$nombre, $num] = parse_numero_final($resto);

    // Limpieza nombre: quitar dobles espacios, comas residuales
    $nombre = trim(preg_replace('/\s+/', ' ', $nombre), " \t,-");

    // Si quedó vacío el nombre, devolvemos original
    if ($nombre === '') {
        $nombre = $adreca;
    }

    return [$tipusVia, $nombre, $num];
}

// === Selecciona filas a normalizar ===
// Elige el criterio: por ejemplo, filas con adreca no vacía y (tipus_via IS NULL o adreca_num IS NULL)
$sql = "
    SELECT id, adreca, tipus_via, adreca_num
    FROM db_dades_personals
   WHERE adreca IS NOT NULL AND TRIM(adreca) <> ''
      AND (tipus_via IS NULL OR adreca_num IS NULL)
        AND id BETWEEN 1000 AND 10000
    ORDER BY id
    
";

//LIMIT 200
$rows = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);

$update = $conn->prepare("
    UPDATE db_dades_personals
    SET tipus_via = :tipus_via,
        adreca = :adreca,
        adreca_num = :adreca_num
    WHERE id = :id
");

$updated = 0;

$did = 0;
foreach ($rows as $r) {
    $id = (int)$r['id'];
    $adreca = (string)$r['adreca'];
    $tipus_via_actual = $r['tipus_via'] !== null ? (int)$r['tipus_via'] : null;
    $adreca_num_actual = $r['adreca_num'] !== null ? (int)$r['adreca_num'] : null;

    [$tipusViaNuevo, $adrecaNombre, $numNuevo] = normalizar_adreca_line($adreca, $TIPUS_VIA_MAP);

    // Decisión de escritura: solo si aporta algo nuevo
    $tipusViaFinal = $tipus_via_actual ?? $tipusViaNuevo;
    $numFinal      = $adreca_num_actual ?? $numNuevo;

    // Si no cambia nada, continúa
    if ($tipusViaFinal === $tipus_via_actual && $numFinal === $adreca_num_actual && $adrecaNombre === $adreca) {
        // Nada que cambiar, pero mostramos por qué
        // echo "[SKIP] id=$id :: '{$adreca}'\n";
        continue;
    }

    if (!DRY_RUN) {
        // mostrar el cambio propuesto
        echo "[UPD] id={$id} :: '{$adreca}' => tipus_via="
            . var_export($tipusViaFinal, true)
            . " | adreca='{$adrecaNombre}' | adreca_num="
            . var_export($numFinal, true) . PHP_EOL;
    }

    $update->execute([
        ':tipus_via'  => $tipusViaFinal,
        ':adreca'     => $adrecaNombre,
        ':adreca_num' => $numFinal,
        ':id'         => $id,
    ]);
    $updated++;
    $did++;
}
if (!$did) {
    echo "No se encontró nada que actualizar en el rango.\n";
}


echo "Hecho. Filas procesadas: {$updated}" . (DRY_RUN ? " (simulado)\n" : "\n");
