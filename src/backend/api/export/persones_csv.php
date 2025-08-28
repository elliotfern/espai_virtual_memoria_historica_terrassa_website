<?php

declare(strict_types=1);

use App\Config\DatabaseConnection;

require __DIR__ . '/../../../vendor/autoload.php';

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="memoriaterrassa_export_' . date('Ymd_His') . '.csv"');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

$pdo = DatabaseConnection::getConnection();
if (!$pdo) {
    http_response_code(500);
    exit('DB error');
}

$g = $_GET;
$type = isset($g['type']) ? (string)$g['type'] : 'filtreGeneral';  // viene del controller
$q = '';
if (!empty($g['q'])) {
    $q = is_array($g['q']) ? (string)($g['q'][0] ?? '') : (string)$g['q'];
    $q = trim($q);
}

// -------- helpers genéricos ----------
function getArray(string $key): array
{
    $v = $_GET[$key] ?? [];
    if (!is_array($v)) $v = [$v];
    return array_values(array_filter(array_map(fn($x) => trim((string)$x), $v), fn($x) => $x !== ''));
}

/** Construye WHERE y params a partir de un whitelist */
function buildWhere(array $wl, array &$params): string
{
    $where = [];
    foreach ($wl as $key => $def) {
        if (!isset($_GET[$key])) continue;
        $vals = getArray($key);
        if (!$vals) continue;

        [$col, $mode] = $def; // mode: in, in_text, eq, like, csvset
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

// -------- casos por tipo (whitelists + joins específicos) ----------
$params = [];
$joins = [];
$wl = [];

/** GENERAL: usa tus stateKey de COMMON_FILTERS (ajusta columnas a tu esquema) */
if ($type === 'filtreGeneral') {
    $wl = [
        'municipis_naixement' => ['p.municipi_naixement', 'in'],
        'provincies'          => ['m.provincia', 'in_text'],  // en el front es nombre, no id
        'anys_naixement'      => ['YEAR(p.data_naixement)', 'in'], // o 'p.any_naixement' si existe
        'anys_defuncio'       => ['YEAR(p.data_defuncio)',  'in'], // o 'p.any_defuncio'
        'estats'              => ['p.estat_civil',         'in'],
        'estudis'             => ['p.estudis',             'in'],
        'oficis'              => ['p.ofici',               'in'],
        'municipis_defuncio'  => ['p.municipi_defuncio',   'in'],
        'sexes'               => ['p.sexe',                'in'],
        'partits'             => ['p.filiacio_politica',   'csvset'], // si es '{...}'
        'sindicats'           => ['p.filiacio_sindical',   'csvset'],
        'causes'              => ['p.causa_defuncio',      'in'],
        'categories'          => ['p.categoria',           'csvset'], // '{1,6,7}'
    ];
    // JOINs mínimos
    $joins[] = "LEFT JOIN aux_dades_municipis m ON m.id = p.municipi_naixement";
}

/** REPRESALIATS: ejemplo de llaves específicas (ajusta a tus keys reales) */
elseif ($type === 'filtreRepresaliats') {
    $wl = [
        'categories'        => ['p.categoria', 'csvset'],
        'processos'         => ['r.proces_id', 'in'],
        'presons'           => ['r.pres_o_id', 'in'],
        'condenes'          => ['r.condena_id', 'in'],
        // + los comunes que también uses en esa página:
        'municipis_naixement' => ['p.municipi_naixement', 'in'],
        'sexes'               => ['p.sexe', 'in'],
    ];
    $joins[] = "LEFT JOIN represalia r ON r.persona_id = p.id";
    $joins[] = "LEFT JOIN aux_dades_municipis m ON m.id = p.municipi_naixement";
}

/** EXILI: ejemplo (ajusta keys/relaciones) */
elseif ($type === 'filtreExili') {
    $wl = [
        'categories'        => ['p.categoria', 'csvset'],
        'paisos_exili'      => ['e.pais_id',   'in'],
        'camps'             => ['e.camp_id',   'in'],
        'unitats_cte'       => ['e.cte_id',    'in'],
        // comunes que tengas activos:
        'sexes'             => ['p.sexe', 'in'],
    ];
    $joins[] = "LEFT JOIN exili e ON e.persona_id = p.id";
}

/** COST-HUMA: ejemplo */
elseif ($type === 'filtreCostHuma') {
    $wl = [
        'categories'        => ['p.categoria', 'csvset'],
        'fronts'            => ['c.front_id',  'in'],
        'situacions'        => ['c.situacio',  'in'],
    ];
    $joins[] = "LEFT JOIN cost_huma c ON c.persona_id = p.id";
}

/** fallback */
else {
    $wl = ['categories' => ['p.categoria', 'csvset']];
}

$where = buildWhere($wl, $params);

// texto libre (nom/cognoms)
if ($q !== '') {
    $where .= ($where ? ' AND ' : 'WHERE ')
        . "(LOWER(p.nom) LIKE :q OR LOWER(p.cognom1) LIKE :q OR LOWER(p.cognom2) LIKE :q)";
    $params[':q'] = '%' . mb_strtolower($q, 'UTF-8') . '%';
}

// SELECT base (común a todos; añade columnas si quieres)
$sql = "
SELECT
  p.id, p.slug, p.nom, p.cognom1, p.cognom2,
  COALESCE(m.ciutat_ca, m.ciutat) AS ciutat,
  p.adreca, p.adreca_num, p.lat, p.lng,
  REPLACE(REPLACE(p.categoria,'{',''),'}','') AS categoria_ids
FROM db_dades_personals p
" . implode("\n", $joins) . "
$where
ORDER BY p.id ASC
";

$stmt = $pdo->prepare($sql);
foreach ($params as $k => $v) $stmt->bindValue($k, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
$stmt->execute();

// stream CSV
echo "\xEF\xBB\xBF";
echo "sep=,\n";
$headers = ['id', 'slug', 'nom', 'cognom1', 'cognom2', 'ciutat', 'adreca', 'adreca_num', 'lat', 'lng', 'categoria_ids'];
$out = fopen('php://output', 'w');
fputcsv($out, $headers);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $line = [];
    foreach ($headers as $h) $line[] = (string)($row[$h] ?? '');
    fputcsv($out, $line);
}
fclose($out);
exit;
