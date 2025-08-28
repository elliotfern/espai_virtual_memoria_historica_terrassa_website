<?php

declare(strict_types=1);

use App\Config\DatabaseConnection;

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="memoriaterrassa_export_' . date('Ymd_His') . '.csv"');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
set_time_limit(0);

$pdo = DatabaseConnection::getConnection();
if (!$pdo) {
    http_response_code(500);
    exit('DB error');
}

/** ================= Helpers: POST/GET seguros ================= */

function rq(string $key)
{
    return $_POST[$key] ?? $_GET[$key] ?? null;
}

/** Devuelve array limpio; acepta key=v1&key=v2 y key[]=v1&key[]=v2 */
function getArray(string $key): array
{
    $raw = rq($key);
    if ($raw === null) return [];
    $v = is_array($raw) ? $raw : [$raw];
    return array_values(array_filter(array_map(fn($x) => trim((string)$x), $v), fn($x) => $x !== ''));
}

/** Devuelve escalar (primer valor si viene como array) */
function getScalar(string $key): string
{
    $raw = rq($key);
    if ($raw === null) return '';
    if (is_array($raw)) return trim((string)($raw[0] ?? ''));
    return trim((string)$raw);
}

/** Construye WHERE y params a partir de un whitelist */
function buildWhere(array $wl, array &$params): string
{
    $where = [];
    foreach ($wl as $key => $def) {
        // ❌ elimina esta línea:
        // if (!isset($_GET[$key])) continue;

        $vals = getArray($key);            // ✅ recoge POST o GET indistintamente
        if (!$vals) continue;

        [$col, $mode] = $def;

        if ($mode === 'in') {
            $ph = [];
            foreach ($vals as $i => $v) {
                $name = ":{$key}_$i";
                $ph[] = $name;
                $params[$name] = ctype_digit($v) ? (int)$v : $v;
            }
            $where[] = "$col IN (" . implode(',', $ph) . ")";
        } elseif ($mode === 'in_text') {
            $ph = [];
            foreach ($vals as $i => $v) {
                $name = ":{$key}_$i";
                $ph[] = $name;
                $params[$name] = mb_strtolower($v, 'UTF-8');
            }
            $where[] = "LOWER($col) IN (" . implode(',', $ph) . ")";
        } elseif ($mode === 'csvset') {
            // Campo string con '{1,6,7}' → busca cada valor con FIND_IN_SET
            $ors = [];
            foreach ($vals as $i => $v) {
                $name = ":{$key}_$i";
                $params[$name] = (string)$v;
                $ors[] = "FIND_IN_SET($name, REPLACE(REPLACE($col,'{',''),'}','')) > 0";
            }
            $where[] = '(' . implode(' OR ', $ors) . ')';
        } elseif ($mode === 'like') {
            $ors = [];
            foreach ($vals as $i => $v) {
                $name = ":{$key}_$i";
                $params[$name] = "%$v%";
                $ors[] = "$col LIKE $name";
            }
            $where[] = '(' . implode(' OR ', $ors) . ')';
        } else { // eq
            $name = ":$key";
            $params[$name] = ctype_digit($vals[0]) ? (int)$vals[0] : $vals[0];
            $where[] = "$col = $name";
        }
    }
    return $where ? ('WHERE ' . implode(' AND ', $where)) : '';
}

// === Traducciones ===
$SEXE_MAP = [
    '1' => 'Home',
    '2' => 'Dona',
];

$CATEGORY_MAP = [
    '1'  => 'Afusellat',
    '2'  => 'Deportat',
    '3'  => 'Mort/desaparegut en combat',
    '4'  => 'Mort civil',
    '5'  => 'Represàlia republicana',
    '6'  => 'Detingut/Processat',
    '7'  => 'Depurat',
    '8'  => 'Dona',
    '9'  => 'Sense assignar',
    '10' => 'Exiliat',
    '11' => 'Represaliats pendents classificació',
    '12' => 'Empresonat Presó Model',
    '13' => 'Detingut Guàrdia Urbana',
    '14' => 'Detingut Comitè Solidaritat (1971-1977)',
    '15' => 'Llei Responsabilitats Polítiques',
    '16' => 'Empresonat dipòsit municipal Sant Llàtzer (1951-19...)',
    '17' => 'Processat Tribunal Orden Público',
    '18' => 'Detingut Comitè Relacions de Solidaritat (1939-194...)',
    '19' => 'Camps de treball',
    '20' => 'Batalló de presos',
];

/** Normaliza "{1, 6,7}" → ["1","6","7"] */
function parseSetIds(string $raw): array
{
    $raw = trim(str_replace(['{', '}', ' '], '', $raw));
    if ($raw === '') return [];
    return array_values(array_filter(explode(',', $raw), fn($x) => $x !== ''));
}

/** Devuelve nombres de categorías a partir del set/ids + mapa */
function categoriesToNames(?string $raw, array $map): string
{
    if ($raw === null) return '';
    $ids = parseSetIds($raw);
    if (!$ids) return '';
    $names = [];
    foreach ($ids as $id) $names[] = $map[$id] ?? $id; // fallback: id si no hay nombre
    return implode(' | ', $names);
}

/** ================= Entrada ================= */

$type = getScalar('type') ?: 'filtreGeneral';
$q    = getScalar('q');               // texto libre (nom/cognoms)
$full = getScalar('full') === '1';    // opcional: export “completa”

/** ================= Whitelists/JOINS por tipo ================= */

$params = [];
$joins  = [];
$wl     = [];

if ($type === 'filtreGeneral') {
    $wl = [
        'municipis_naixement' => ['p.municipi_naixement', 'in'],
        'provincies'          => ['COALESCE(pr.provincia_ca, pr.provincia)', 'in_text'], // ← CAMBIO
        'anys_naixement'      => ['YEAR(p.data_naixement)', 'in'],     // ajusta si usas p.any_naixement
        'anys_defuncio'       => ['YEAR(p.data_defuncio)', 'in'],     // o p.any_defuncio
        'estats'              => ['p.estat_civil',        'in'],
        'estudis'             => ['p.estudis',            'in'],
        'oficis'              => ['p.ofici',              'in'],
        'municipis_defuncio'  => ['p.municipi_defuncio',  'in'],
        'sexes'               => ['p.sexe',               'in'],
        'partits'             => ['p.filiacio_politica',  'csvset'],
        'sindicats'           => ['p.filiacio_sindical',  'csvset'],
        'causes'              => ['p.causa_defuncio',     'in'],
        'categories'          => ['p.categoria',          'csvset'],
    ];
    $joins[] = "LEFT JOIN aux_dades_municipis m ON m.id = p.municipi_naixement";
    $joins[] = "LEFT JOIN aux_dades_municipis_provincia pr ON pr.id = m.provincia"; // ← NUEVO
} elseif ($type === 'filtreRepresaliats') {
    $wl = [
        'categories'           => ['p.categoria', 'csvset'],
        'processos'            => ['r.proces_id', 'in'],
        'presons'              => ['r.pres_o_id', 'in'],
        'condenes'             => ['r.condena_id', 'in'],
        'municipis_naixement'  => ['p.municipi_naixement', 'in'],
        'sexes'                => ['p.sexe', 'in'],
    ];
    $joins[] = "LEFT JOIN represalia r ON r.persona_id = p.id";
    $joins[] = "LEFT JOIN aux_dades_municipis m ON m.id = p.municipi_naixement";
    $joins[] = "LEFT JOIN aux_dades_municipis_provincia pr ON pr.id = m.provincia"; // ← NUEVO
} elseif ($type === 'filtreExili') {
    $wl = [
        'categories'   => ['p.categoria', 'csvset'],
        'paisos_exili' => ['e.pais_id',   'in'],
        'camps'        => ['e.camp_id',   'in'],
        'unitats_cte'  => ['e.cte_id',    'in'],
        'sexes'        => ['p.sexe',      'in'],
    ];
    $joins[] = "LEFT JOIN exili e ON e.persona_id = p.id";
} elseif ($type === 'filtreCostHuma') {
    $wl = [
        'categories' => ['p.categoria', 'csvset'],
        'fronts'     => ['c.front_id',  'in'],
        'situacions' => ['c.situacio',  'in'],
    ];
    $joins[] = "LEFT JOIN cost_huma c ON c.persona_id = p.id";
} else {
    $wl = ['categories' => ['p.categoria', 'csvset']];
}

$where = buildWhere($wl, $params);

// Texto libre sobre nom/cognoms
if ($q !== '') {
    $where .= ($where ? ' AND ' : 'WHERE ')
        . "(LOWER(p.nom) LIKE :q OR LOWER(p.cognom1) LIKE :q OR LOWER(p.cognom2) LIKE :q)";
    $params[':q'] = '%' . mb_strtolower($q, 'UTF-8') . '%';
}

/** ================= SELECT + headers ================= */
$full = true;

if ($full) {
    // Export “completo” (añade/ajusta columnas a tu esquema)
    $headers = [
        'id',
        'nom',
        'cognom1',
        'cognom2',
        'categoria',
        'sexe',
        'data_naixement',
        'data_defuncio',
        'ciutat_naixement',
        'comarca_naixement',
        'provincia_naixement',
        'comunitat_naixement',
        'pais_naixement',
        'ciutat_residencia',
        'comarca_residencia',
        'provincia_residencia',
        'comunitat_residencia',
        'pais_residencia',
        'ciutat_defuncio',
        'comarca_defuncio',
        'provincia_defuncio',
        'comunitat_defuncio',
        'pais_defuncio',
        'adreca',
        'tipologia_espai_ca',
        'observacions_espai',
        'causa_defuncio_ca',
        'estat_civil',
        'estudi_cat',
        'ofici_cat',
        'empresa',
        'filiacio_politica_noms',   // ← NUEVO (nombres)
        'filiacio_sindical_noms',   // ← NUEVO (nombres)
        'activitat_durant_guerra',
        'sector_cat',
        'sub_sector_cat',
        'carrec_cat',
        'data_creacio',
        'data_actualitzacio',
        'observacions',
        'biografiaCa',
        'biografiaEs',
        'lat',
        'lng',
        'adreca_antic',
        'adreca_num',
        'causa_defuncio_detalls'
    ];

    // === SELECT completo (alias p) ===
    $sql = "
  SELECT
    p.id,
    p.nom,
    p.cognom1,
    p.cognom2,
    p.categoria,
    p.sexe,
    p.data_naixement,
    p.data_defuncio,

    m1.ciutat                         AS ciutat_naixement,
    m1a.comarca                       AS comarca_naixement,
    m1b.provincia                     AS provincia_naixement,
    m1c.comunitat_ca                  AS comunitat_naixement,
    m1d.estat_ca                      AS pais_naixement,

    m2.ciutat                         AS ciutat_residencia,
    m2a.comarca                       AS comarca_residencia,
    m2b.provincia                     AS provincia_residencia,
    m2c.comunitat_ca                  AS comunitat_residencia,
    m2d.estat_ca                      AS pais_residencia,

    m3.ciutat                         AS ciutat_defuncio,
    m3a.comarca                       AS comarca_defuncio,
    m3b.provincia                     AS provincia_defuncio,
    m3c.comunitat_ca                  AS comunitat_defuncio,
    m3d.estat_ca                      AS pais_defuncio,

    p.adreca,
    tespai.tipologia_espai_ca         AS tipologia_espai_ca,
    tespai.observacions               AS observacions_espai,
    causaD.causa_defuncio_ca          AS causa_defuncio_ca,
    ec.estat_cat                      AS estat_civil,
    es.estudi_cat,
    o.ofici_cat,
    em.empresa_ca                     AS empresa,
     (SELECT GROUP_CONCAT(fp.partit_politic ORDER BY fp.partit_politic SEPARATOR ' | ')
     FROM aux_filiacio_politica fp
    WHERE FIND_IN_SET(fp.id, REPLACE(REPLACE(p.filiacio_politica,'{',''),'}','')) > 0
    ) AS filiacio_politica_noms,

    (SELECT GROUP_CONCAT(fs.sindicat ORDER BY fs.sindicat SEPARATOR ' | ')
        FROM aux_filiacio_sindical fs
        WHERE FIND_IN_SET(fs.id, REPLACE(REPLACE(p.filiacio_sindical,'{',''),'}','')) > 0
    ) AS filiacio_sindical_noms,

    p.activitat_durant_guerra,
    se.sector_cat,
    sse.sub_sector_cat,
    oc.carrec_cat,
    p.data_creacio,
    p.data_actualitzacio,
    p.observacions,
    bio.biografiaCa,
    bio.biografiaEs,
    p.lat,
    p.lng,
    p.adreca_antic,
    p.adreca_num,
    p.causa_defuncio_detalls
  FROM db_dades_personals p
    LEFT JOIN aux_dades_municipis              m1  ON p.municipi_naixement   = m1.id
    LEFT JOIN aux_dades_municipis_comarca      m1a ON m1.comarca             = m1a.id
    LEFT JOIN aux_dades_municipis_provincia    m1b ON m1.provincia           = m1b.id
    LEFT JOIN aux_dades_municipis_comunitat    m1c ON m1.comunitat           = m1c.id
    LEFT JOIN aux_dades_municipis_estat        m1d ON m1.estat               = m1d.id

    LEFT JOIN aux_dades_municipis              m2  ON p.municipi_residencia  = m2.id
    LEFT JOIN aux_dades_municipis_comarca      m2a ON m2.comarca             = m2a.id
    LEFT JOIN aux_dades_municipis_provincia    m2b ON m2.provincia           = m2b.id
    LEFT JOIN aux_dades_municipis_comunitat    m2c ON m2.comunitat           = m2c.id
    LEFT JOIN aux_dades_municipis_estat        m2d ON m2.estat               = m2d.id

    LEFT JOIN aux_dades_municipis              m3  ON p.municipi_defuncio    = m3.id
    LEFT JOIN aux_dades_municipis_comarca      m3a ON m3.comarca             = m3a.id
    LEFT JOIN aux_dades_municipis_provincia    m3b ON m3.provincia           = m3b.id
    LEFT JOIN aux_dades_municipis_comunitat    m3c ON m3.comunitat           = m3c.id
    LEFT JOIN aux_dades_municipis_estat        m3d ON m3.estat               = m3d.id

    LEFT JOIN aux_tipologia_espais             tespai ON p.tipologia_lloc_defuncio = tespai.id
    LEFT JOIN aux_causa_defuncio               causaD ON p.causa_defuncio          = causaD.id
    LEFT JOIN aux_estudis                      es     ON p.estudis                 = es.id
    LEFT JOIN aux_oficis                       o      ON p.ofici                   = o.id 
    LEFT JOIN aux_estat_civil                  ec     ON p.estat_civil             = ec.id
    LEFT JOIN aux_sector_economic              se     ON p.sector                  = se.id
    LEFT JOIN aux_sub_sector_economic          sse    ON p.sub_sector              = sse.id
    LEFT JOIN aux_ofici_carrec                 oc     ON p.carrec_empresa          = oc.id
    LEFT JOIN auth_users                       u      ON p.autor                   = u.id
    LEFT JOIN auth_users                       u2     ON p.autor2                  = u2.id
    LEFT JOIN auth_users                       u3     ON p.autor3                  = u3.id
    LEFT JOIN auth_users                       u4     ON p.colab1                  = u4.id
    LEFT JOIN aux_imatges                      img    ON p.img                     = img.id
    LEFT JOIN db_biografies                    bio    ON p.id                      = bio.idRepresaliat
    LEFT JOIN aux_empreses                     em     ON p.empresa                 = em.id
    LEFT JOIN aux_tipus_via                    v      ON p.tipus_via               = v.id

    " . implode("\n", $joins) . "
    $where
  ORDER BY p.id ASC
  ";
}

/** ================= Ejecutar y emitir CSV ================= */

$stmt = $pdo->prepare($sql);

foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v, is_int($v) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
}

$stmt->execute();

// BOM + hint separador para Excel
echo "\xEF\xBB\xBF";

$out = fopen('php://output', 'w');
fputcsv($out, $headers);
while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
    // ↳ Traducción de sexe (1→Home, 2→Dona)
    if (array_key_exists('sexe', $row)) {
        $row['sexe'] = $SEXE_MAP[(string)($row['sexe'] ?? '')] ?? (string)($row['sexe'] ?? '');
    }

    // ↳ Traducción de categorías (acepta 'categoria' o 'categoria_ids')
    if (array_key_exists('categoria', $row)) {
        $row['categoria'] = categoriesToNames((string)$row['categoria'], $CATEGORY_MAP);
    } elseif (array_key_exists('categoria_ids', $row)) {
        $row['categoria_ids'] = categoriesToNames((string)$row['categoria_ids'], $CATEGORY_MAP);
    }

    // Escribir con los headers definidos
    $line = [];
    foreach ($headers as $h) $line[] = (string)($row[$h] ?? '');
    fputcsv($out, $line);
}

fclose($out);
exit;
