<?php

declare(strict_types=1);

/**
 * /src/backend/api/export/persones_xlsx.php
 * Genera un Excel (XLSX) con los resultados filtrados.
 * - Recibe filtros por querystring (arrays permitidos) + q (texto) + type (filtreGeneral|filtreRepresaliats|filtreExili|filtreCostHuma)
 * - Evita setCellValueByColumnAndRow: usa Coordinate::stringFromColumnIndex
 */

use App\Config\DatabaseConnection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

// -------- Headers de descarga --------
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="memoriaterrassa_export_' . date('Ymd_His') . '.xlsx"');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

set_time_limit(0);

// -------- Conexión BD --------
$pdo = DatabaseConnection::getConnection();
if (!$pdo) {
  http_response_code(500);
  echo "DB error";
  exit;
}

// -------- Helpers --------
/** Devuelve un array de strings (limpios) para una clave GET que puede venir como escalar o array. */
// Reemplaza getArray y lecturas sueltas de $_GET por esto:

/** Devuelve el valor crudo de una clave (prioriza POST, luego GET) */
function rq(string $key)
{
  return $_POST[$key] ?? $_GET[$key] ?? null;
}

/** Devuelve array limpio (acepta 'key=value1&key=value2' y 'key[]=v1&key[]=v2') */
function getArray(string $key): array
{
  $raw = rq($key);
  if ($raw === null) return [];
  $v = is_array($raw) ? $raw : [$raw];
  return array_values(array_filter(array_map(fn($x) => trim((string)$x), $v), fn($x) => $x !== ''));
}

/** Texto simple (first-or-empty) */
function getScalar(string $key): string
{
  $raw = rq($key);
  if ($raw === null) return '';
  if (is_array($raw)) return trim((string)($raw[0] ?? ''));
  return trim((string)$raw);
}

/**
 * Construye WHERE y $params a partir de un whitelist:
 *   $wl = ['claveState' => ['columna_sql', 'in|in_text|eq|like|csvset']]
 * - in:       IN (?, ?, ?)
 * - in_text:  IN sobre LOWER(columna) con valores ya lowercased
 * - eq:       =
 * - like:     LIKE '%valor%'
 * - csvset:   campo string con '{1,6,7}' → OR de FIND_IN_SET sobre el string sin llaves
 */
function buildWhere(array $wl, array &$params): string
{
  $where = [];
  foreach ($wl as $key => $def) {
    if (!isset($_GET[$key])) continue;
    $vals = getArray($key);
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
      $ors = [];
      foreach ($vals as $i => $v) {
        $name = ":{$key}_$i";
        $params[$name] = (string)$v;
        // REPLACE llaves para dejar "1,6,7" y usar FIND_IN_SET
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

// -------- Entrada --------
$type = getScalar('type') ?: 'filtreGeneral';
$q    = getScalar('q');

if (!empty($_GET['q'])) {
  $q = is_array($_GET['q']) ? (string)($_GET['q'][0] ?? '') : (string)$_GET['q'];
  $q = trim($q);
}

// -------- Whitelists y JOINs por tipo --------
$params = [];
$joins  = [];
$wl     = [];

/**
 * Ajusta las claves a tus stateKey reales.
 * Si tus columnas de año no son DATE y están en string, YEAR(...) puede seguir funcionando si el formato es yyyy-mm-dd;
 * si no, crea columnas normalizadas (any_naixement/any_defuncio) o ajusta el filtro a tu realidad.
 */
if ($type === 'filtreGeneral') {
  $wl = [
    'municipis_naixement' => ['p.municipi_naixement', 'in'],
    'provincies'          => ['m.provincia',          'in_text'], // en el front es nombre, no id
    'anys_naixement'      => ['YEAR(p.data_naixement)', 'in'],
    'anys_defuncio'       => ['YEAR(p.data_defuncio)', 'in'],
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
  $joins[] = "LEFT JOIN aux_tipus_via tv ON tv.id = p.tipus_via";
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
  $joins[] = "LEFT JOIN aux_tipus_via tv ON tv.id = p.tipus_via";
} elseif ($type === 'filtreExili') {
  $wl = [
    'categories'   => ['p.categoria', 'csvset'],
    'paisos_exili' => ['e.pais_id',   'in'],
    'camps'        => ['e.camp_id',   'in'],
    'unitats_cte'  => ['e.cte_id',    'in'],
    'sexes'        => ['p.sexe',      'in'],
  ];
  $joins[] = "LEFT JOIN exili e ON e.persona_id = p.id";
  $joins[] = "LEFT JOIN aux_tipus_via tv ON tv.id = p.tipus_via";
  // municipis si necesitas ciutat:
  $joins[] = "LEFT JOIN aux_dades_municipis m ON m.id = p.municipi_naixement";
} elseif ($type === 'filtreCostHuma') {
  $wl = [
    'categories' => ['p.categoria', 'csvset'],
    'fronts'     => ['c.front_id',  'in'],
    'situacions' => ['c.situacio',  'in'],
  ];
  $joins[] = "LEFT JOIN cost_huma c ON c.persona_id = p.id";
  $joins[] = "LEFT JOIN aux_tipus_via tv ON tv.id = p.tipus_via";
  $joins[] = "LEFT JOIN aux_dades_municipis m ON m.id = p.municipi_naixement";
} else {
  // fallback mínimo
  $wl = ['categories' => ['p.categoria', 'csvset']];
  $joins[] = "LEFT JOIN aux_dades_municipis m ON m.id = p.municipi_naixement";
  $joins[] = "LEFT JOIN aux_tipus_via tv ON tv.id = p.tipus_via";
}

// WHERE por whitelist
$where = buildWhere($wl, $params);

// Texto libre q sobre nom/cognoms
if ($q !== '') {
  $where .= ($where ? ' AND ' : 'WHERE ')
    . "(LOWER(p.nom) LIKE :q OR LOWER(p.cognom1) LIKE :q OR LOWER(p.cognom2) LIKE :q)";
  $params[':q'] = '%' . mb_strtolower($q, 'UTF-8') . '%';
}

// -------- Consulta --------
// (Añade/quita columnas a gusto; aquí incluyo 'tipus_via' como en el CSV)
$sql = "
SELECT
  p.id,
  p.slug,
  p.nom,
  p.cognom1,
  p.cognom2,
  COALESCE(m.ciutat_ca, m.ciutat)     AS ciutat,
 COALESCE(tv.tipus_ca, '') AS tipus_via,
  p.adreca,
  p.adreca_num,
  p.lat,
  p.lng,
  REPLACE(REPLACE(p.categoria,'{',''),'}','') AS categoria_ids
FROM db_dades_personals p
" . implode("\n", $joins) . "
$where
ORDER BY p.id ASC
";

$stmt = $pdo->prepare($sql);
foreach ($params as $k => $v) {
  $stmt->bindValue($k, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
}
$stmt->execute();

// -------- Generar XLSX --------
$headers = [
  'id',
  'slug',
  'nom',
  'cognom1',
  'cognom2',
  'ciutat',
  'tipus_via',
  'adreca',
  'adreca_num',
  'lat',
  'lng',
  'categoria_ids'
];

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Cabeceras (fila 1)
for ($i = 0; $i < count($headers); $i++) {
  $col = Coordinate::stringFromColumnIndex($i + 1); // 1→A, 2→B...
  $sheet->setCellValue($col . '1', $headers[$i]);
}

// Datos (desde fila 2)
$r = 2;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  for ($i = 0; $i < count($headers); $i++) {
    $h = $headers[$i];
    $col = Coordinate::stringFromColumnIndex($i + 1);
    $sheet->setCellValue($col . $r, (string)($row[$h] ?? ''));
  }
  $r++;
}

// Auto-size columnas
for ($i = 1; $i <= count($headers); $i++) {
  $col = Coordinate::stringFromColumnIndex($i);
  $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Output
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
