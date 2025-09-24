<?php

declare(strict_types=1);

/**
 * Importa datos desde un ODS/XLSX/CSV a la BD.
 * - Mapea por NOMBRE DE CABECERA (no por A/B/C...).
 * - UPDATE en db_dades_personals (solo si hay dato; no pisa con NULL).
 * - INSERT en db_processats.
 * - Transacción por fila y feedback en consola.
 *
 * Uso:
 *   php importar_ods.php                       # usa ruta por defecto (hosting)
 *   php importar_ods.php /ruta/archivo.ods     # ruta alternativa
 */

use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Config\DatabaseConnection;
use PhpOffice\PhpSpreadsheet\Shared\Date as XlsDate;

/* ------------------------ Config básica ------------------------ */

// Equivalencias de nombre propio: CATALÁN (clave) → variantes aceptadas (incluye castellano)
// Cada grupo contiene TODAS las variantes que deben considerarse equivalentes
$NOM_EQUIV = [
    // Masculinos muy frecuentes
    ['Joan', 'Juan'],
    ['Jaume', 'Jaime'],
    ['Francesc', 'Francisco'],
    ['Miquel', 'Miguel'],
    ['Pere', 'Pedro'],
    ['Carles', 'Carlos'],
    ['Lluís', 'Luis', 'Lluis', 'Luís'],
    ['Ramon', 'Ramón'],
    ['Sebastià', 'Sebastián', 'Sebastia', 'Sebastian'],
    ['Agustí', 'Agustín', 'Agusti', 'Agustin'],
    ['Martí', 'Martín', 'Marti', 'Martin'],
    ['Vicent', 'Vicente'],
    ['Vicenç', 'Vicenc', 'Vicente'],
    ['Pau', 'Pablo'],
    ['Enric', 'Enrique'],
    ['Guillem', 'Guillermo'],
    ['Esteve', 'Esteban'],
    ['Ferran', 'Fernando'],
    ['Rafel', 'Rafael'],
    ['Nicolau', 'Nicolás', 'Nicolas'],
    ['Jordi', 'Jorge'],
    ['Xavier', 'Javier'],
    ['Àngel', 'Ángel', 'Angel'],
    ['Àlvar', 'Álvaro', 'Alvaro', 'Alvar'],
    ['Andreu', 'Andrés', 'Andres'],
    ['Joaquim', 'Joaquín', 'Joaquin'],
    ['Tomàs', 'Tomás', 'Tomas'],
    ['Fèlix', 'Félix', 'Felix'],
    ['Isidre', 'Isidro'],
    ['Ignasi', 'Ignacio'],
    ['Valentí', 'Valentín', 'Valentin'],
    ['Llorenç', 'Lorenzo'],
    ['Higini', 'Higinio'],
    ['Hipòlit', 'Hipólito', 'Hipolito'],
    ['Eleuteri', 'Eleuterio'],
    ['Celestí', 'Celestino'],
    ['Felip', 'Felipe'],
    ['Ernest', 'Ernesto'],
    ['Gregori', 'Gregorio'],
    ['Ricard', 'Ricardo'],
    ['Eusebi', 'Eusebio'],
    ['Claudi', 'Claudio'],
    ['Rogeli', 'Rogelio'],
    ['Leopold', 'Leopoldo'],
    ['Ponç', 'Poncio'],
    ['Amat', 'Amado'],
    ['Amadeu', 'Amadeo'],
    ['Romà', 'Román'], // (ojo: distinto de Ramon/Ramón)
    ['Octavi', 'Octavio'],
    ['Paulí', 'Paulino'],
    ['Juli', 'Julio'],
    ['Julià', 'Julián', 'Julian'],
    ['Maurici', 'Mauricio'],
    ['Estanislau', 'Estanislao'],
    ['Fermí', 'Fermín', 'Fermin'],
    ['Manuel', 'Manel'],
    ['Felicità?', 'Feliciano'], // opcional, si te aparece "Felicità" en fuentes; si no, puedes omitirlo
    ['Benet', 'Benito'],
    ['Benvingut', 'Bienvenido'],
    ['Benvinguda', 'Bienvenida'],
    ['Teodor', 'Teodoro'],
    ['Eladi', 'Eladio'],
    ['Hermini', 'Herminio'],
    ['Desideri', 'Desiderio'],
    ['Fulgenci', 'Fulgencio'],
    ['Florentí', 'Florentino'],
    ['Hilari', 'Hilario'],
    ['Policarp', 'Policarpo'],
    ['Venanci', 'Venancio'],
    ['Senen', 'Senén'],  // admite ambas grafías
    ['Medí', 'Medín', 'Medin'], // muy raro; útil si te aparece “Medin/Medín”
    ['Oriol', 'Oriol'],
    ['Balbino', 'Balbí'],
    ['Alfredo', 'Alfred'],
    ['Liberto', 'Llibert'],
    ['Caietà', 'Cayetano'],
    ['Deogràcies', 'Deogracias'],
    ['Antoni', 'Antonio'],
    ['Alejandro', 'Alexandre'],
    ['Magín', 'Magí'],
    ['Roman', 'Romà'],
    ['Delfín', 'Delfí'],
    ['Arcadio', 'Arcadi'],
    ['Bonaventura', 'Buenaventura'],
    ['Avel·lí', 'Avelino'],
    ['Artemio', 'Artemi'],
    ['Remigi', 'Remigio'],
    ['Facund', 'Facundo'],
    ['Patrocinio', 'Patrocini'],
    ['Patricio', 'Patrici'],
    ['Ana', 'Anita'],
    ['Adelino', 'Adeli'],
    ['Inocencio', 'Inocenci'],
    ['Marcos', 'Marc'],
    ['Conrad', 'Conrado'],
    ['Roser', 'Rosario'],
    ['Rosario', 'Roser'],
    ['Bartomeu', 'Bartolome'],
    ['Jacinto', 'Jacint'],
    ['Agustín', 'Agustí'],
    ['Ponç', 'Poncio'],
    ['Anselm', 'Anselmo'],
    ['Eliseo', 'Eliseu'],
    ['Bonifacio', 'Bonifaci'],
    ['Eduardo', 'Eduard'],
    ['José', 'Jose', 'Josep'],
    ['Emili', 'Emilio'],
    ['Tesifonte', 'Tesifont'],
    ['Domènec', 'Domingo'],
    ['Alfons', 'Alfonso'],
    ['Genar', 'Genaro'],
    ['Jerónimo', 'Jeroni'],
    ['Marcelino', 'Marcel·lí'],
    ['Lluc', 'Lucas'],

    // Femeninos frecuentes en tu lista
    ['Dolors', 'Dolores'],
    ['Carme', 'Carmen'],
    ['Mercè', 'Mercedes'],
    ['Conxita', 'Conchita'],
    ['Josepa', 'Josefa'],
    ['Margarida', 'Margarita'],
    ['Esperança', 'Esperanza'],
    ['Amàlia', 'Amalia'],
    ['Júlia', 'Julia'],
    ['Encarnació', 'Encarnación'],
    ['Llúcia', 'Lucía', 'Lucia', 'Lúcia'],
    ['Rosari', 'Rosario'],
    ['Leocàdia', 'Leocadia'],
    ['Caterina', 'Catalina'],
    ['Palmira', 'Palmira'], // igual en ambos; lo dejamos por si quieres hipocorísticos
    ['Montserrat', 'Montserrat'], // igual
    ['Teresa', 'Teresa'], // igual
    ['Rosa', 'Rosa'], // igual
    ['Pilar', 'Pilar'], // igual
    ['Esmeralda', 'Esmeralda'], // igual
    ['Consuelo', 'Consol'], // castellano ↔ catalán
    ['Margarida', 'Margarita'],
    ['Magdalena', 'Magdalena', 'Madalena'], // a veces “Madalena” en fuentes
    ['Margarida', 'Margarita'],
    ['Quitèria', 'Quiteria'],
    ['Joaquima', 'Joaquina'],
    ['Francisca', 'Francesca'],

    // Compestos que aparecen en tu lista
    ['Josep Maria', 'José María', 'Jose Maria'],
    ['Josep Manuel', 'José Manuel', 'Jose Manuel'],
    ['Francesc Josep', 'Francisco José', 'Francisco Jose'],
    ['Joan Antoni', 'Juan Antonio'],
    ['Joan Baptista', 'Juan Bautista'],
    ['Pere Josep', 'Pedro José'],
    ['Pere Joaquim', 'Pedro Joaquín', 'Pedro Joaquin'],
    ['Josep Oriol', 'José Oriol', 'Jose Oriol'],
    ['Josep Vicent', 'José Vicente', 'Jose Vicente'],
    ['Josep Maria', 'José Maria'],
    ['Pedro Joaquín', 'Pere Joaquim'],
    ['Juan Bautista', 'Bautista'],
];


/* ---------------- Config ---------------- */
$DEFAULT_PATH = '/home/epgylzqu/memoriaterrassa.cat/datos3.ods';

// BD en utf32_general_ci
$COLLATE_CI = 'utf32_general_ci';

// Mapeos
$MAP_ESTAT_CIVIL = [ // S > 1 / C > 2 / V > 3 / D > 5 / SD > 6
    'S' => 1,
    'C' => 2,
    'V' => 3,
    'D' => 5,
    'SD' => 6,
];

$MAP_SECTOR = [ // 1 > PRIMARI / 2 > SECUNDARI / 3 > TERCIARI
    '1' => 1,
    'PRIMARI' => 1,
    '2' => 2,
    'SECUNDARI' => 2,
    '3' => 3,
    'TERCIARI' => 3,
    'TERCIARIO' => 3,
];


/* ---------------- Helpers ---------------- */
function ensureCategoriaHasN(?string $s, int $n): string
{
    if ($s === null || trim($s) === '') return '{' . $n . '}';
    $s = trim($s);
    if ($s[0] === '{' && substr($s, -1) === '}') $s = substr($s, 1, -1);
    $parts = array_map('trim', explode(',', $s));
    $nums = [];
    foreach ($parts as $p) {
        if ($p !== '' && preg_match('/^\d+$/', $p)) $nums[] = (int)$p;
    }
    if (!in_array($n, $nums, true)) $nums[] = $n;
    $uniq = [];
    foreach ($nums as $x) {
        if (!in_array($x, $uniq, true)) $uniq[] = $x;
    }
    return '{' . implode(',', $uniq) . '}';
}

function out(string $s): void
{
    if (PHP_SAPI === 'cli') echo $s . PHP_EOL;
    else echo htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "<br>\n";
    @ob_flush();
    @flush();
}
function normKey(?string $s): string
{
    $s = (string)$s;
    $s = str_replace("\xC2\xA0", ' ', $s);
    $s = str_replace(["\xE2\x80\x93", "\xE2\x80\x94", "–", "—"], "-", $s);
    $s = preg_replace('/\s+/', ' ', trim($s));
    return mb_strtoupper($s, 'UTF-8');
}
function stripAccents(string $s): string
{
    $t = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
    return $t !== false ? $t : $s;
}
function toIntOrNull($v): ?int
{
    if ($v === null) return null;
    if ($v instanceof \DateTimeInterface) return null;
    $s = trim((string)$v);
    if ($s === '') return null;
    if (preg_match('/^-?\d+$/', $s)) return (int)$s;
    if (preg_match('/-?\d+/', $s, $m)) return (int)$m[0];
    return null;
}
function mapIn(array $map, ?string $val): ?int
{
    if ($val === null) return null;
    $k = normKey(stripAccents($val));
    return $map[$k] ?? $map[(string)(int)$k] ?? null;
}

/* ---- Nombre compuesto y búsqueda persona ---- */

function parseCombinedName(?string $s): array
{
    // "COGNOM1 COGNOM2, NOM(S)" → [nomCompleto, cognom1, cognom2]
    $nom = $c1 = $c2 = null;
    if ($s === null || trim($s) === '') return [$nom, $c1, $c2];
    [$left, $right] = array_map('trim', array_pad(explode(',', $s, 2), 2, ''));
    if ($right !== '') $nom = preg_replace('/\s+/', ' ', $right);
    if ($left !== '') {
        $surn = preg_split('/\s+/', $left);
        if (count($surn) == 1) {
            $c1 = $surn[0];
            $c2 = null;
        } else {
            $c1 = array_shift($surn);
            $c2 = implode(' ', $surn);
        }
    }
    return [$nom, $c1, $c2];
}
function nomVariantsSmart(string $nom, array $NOM_EQUIV): array
{
    static $NOM_VARIANTS = null;
    if ($NOM_VARIANTS === null) {
        $NOM_VARIANTS = [];
        foreach ($NOM_EQUIV as $grp) {
            $all = [];
            foreach ($grp as $n) {
                $all[] = normKey($n);
                $all[] = normKey(stripAccents($n));
            }
            $all = array_values(array_unique($all));
            foreach ($grp as $n) {
                $NOM_VARIANTS[normKey($n)] = $all;
                $NOM_VARIANTS[normKey(stripAccents($n))] = $all;
            }
        }
    }
    $k = normKey($nom);
    $kA = normKey(stripAccents($nom));
    return $NOM_VARIANTS[$k] ?? $NOM_VARIANTS[$kA] ?? array_values(array_unique([$k, $kA]));
}
function findPersona(PDO $pdo, string $nom, ?string $c1, ?string $c2, array $NOM_EQUIV, string $COLLATE_CI): array
{
    $candidatos = [];
    $nomTrim = trim($nom);
    if ($nomTrim !== '') {
        $candidatos[] = $nomTrim;
        $toks = preg_split('/\s+/', $nomTrim);
        if ($toks && count($toks) > 1) $candidatos[] = $toks[0];
    }
    $c1n = normKey($c1 ?? '');
    $c2n = normKey($c2 ?? '');

    static $stmtWithC2 = null, $stmtNoC2 = null;
    if ($stmtWithC2 === null) {
        $stmtWithC2 = $pdo->prepare("
      SELECT id FROM db_dades_personals
      WHERE nom COLLATE $COLLATE_CI = CONVERT(:nom USING utf32) COLLATE $COLLATE_CI
        AND cognom1 COLLATE $COLLATE_CI = CONVERT(:c1 USING utf32) COLLATE $COLLATE_CI
        AND cognom2 COLLATE $COLLATE_CI = CONVERT(:c2 USING utf32) COLLATE $COLLATE_CI
      LIMIT 1
    ");
        $stmtNoC2 = $pdo->prepare("
      SELECT id FROM db_dades_personals
      WHERE nom COLLATE $COLLATE_CI = CONVERT(:nom USING utf32) COLLATE $COLLATE_CI
        AND cognom1 COLLATE $COLLATE_CI = CONVERT(:c1 USING utf32) COLLATE $COLLATE_CI
        AND (cognom2 IS NULL OR cognom2 = '')
      LIMIT 1
    ");
    }
    foreach ($candidatos as $cand) {
        $variants = nomVariantsSmart($cand, $NOM_EQUIV);
        foreach ($variants as $vn) {
            if ($c2n !== '') {
                $stmtWithC2->execute([':nom' => $vn, ':c1' => $c1n, ':c2' => $c2n]);
                $id = $stmtWithC2->fetchColumn();
                if ($id !== false) return [(int)$id, $vn];
            }
            $stmtNoC2->execute([':nom' => $vn, ':c1' => $c1n]);
            $id2 = $stmtNoC2->fetchColumn();
            if ($id2 !== false) return [(int)$id2, $vn];
        }
    }
    return [0, ''];
}

/* ---- Municipis ---- */

function buscarMunicipi(PDO $pdo, ?string $nom): ?int
{
    if (!$nom) return null;
    $nomTrim = trim($nom);

    $q1 = $pdo->prepare("SELECT id FROM aux_dades_municipis WHERE ciutat = :n OR ciutat_ca = :n LIMIT 1");
    $q1->execute([':n' => $nomTrim]);
    $id = $q1->fetchColumn();
    if ($id !== false) return (int)$id;

    $q2 = $pdo->prepare("SELECT id FROM aux_dades_municipis
                       WHERE ciutat LIKE :p OR ciutat_ca LIKE :p
                       ORDER BY LENGTH(ciutat) ASC LIMIT 1");
    $q2->execute([':p' => $nomTrim . '%']);
    $id2 = $q2->fetchColumn();
    return $id2 !== false ? (int)$id2 : null;
}

/* ---- Fechas (celda directa, soporta DateTime/serial/texto) ---- */

function parseExcelSerial($v): ?string
{
    if (!is_numeric($v)) return null;
    try {
        $dt = XlsDate::excelToDateTimeObject((float)$v);
        return $dt->format('Y-m-d');
    } catch (Throwable $e) {
        return null;
    }
}
function parseDMYAny(?string $s): ?string
{
    if ($s === null) return null;
    $s = trim($s);
    if ($s === '') return null;
    if (preg_match('/^\d{4}-\d{2}-\d{2}(?:[ T]\d{2}:\d{2}(?::\d{2})?)?$/', $s)) return substr($s, 0, 10);
    if (preg_match('/^(\d{1,2})[\/\-.](\d{1,2})[\/\-.](\d{2,4})(?:\s+\d{1,2}:\d{2}(?::\d{2})?)?$/', $s, $m)) {
        $d = (int)$m[1];
        $M = (int)$m[2];
        $y = (int)$m[3];
        if ($y < 100) $y = 1900 + $y;
        if ($d >= 1 && $d <= 31 && $M >= 1 && $M <= 12) return sprintf('%04d-%02d-%02d', $y, $M, $d);
    }
    return null;
}
function parseAnyDateFlexible($raw): ?string
{
    if ($raw instanceof DateTimeInterface) return $raw->format('Y-m-d');
    $serial = parseExcelSerial($raw);
    if ($serial) return $serial;
    $s = is_scalar($raw) ? (string)$raw : null;
    if ($s === null) return null;
    $dmy = parseDMYAny($s);
    if ($dmy) return $dmy;
    return null;
}
function getDateYmdFromCellMulti(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, array $colsByName, array $aliases, int $rowNum): ?string
{
    $col = null;
    foreach ($aliases as $a) {
        $k = normKey($a);
        if (isset($colsByName[$k])) {
            $col = $colsByName[$k];
            break;
        }
    }
    if (!$col) {
        foreach ($colsByName as $key => $c) {
            foreach ($aliases as $a) {
                $k = normKey($a);
                if (mb_strpos($key, $k) !== false) {
                    $col = $c;
                    break 2;
                }
            }
        }
    }
    if (!$col) return null;
    $cell = $sheet->getCell($col . (string)$rowNum);
    $d = parseAnyDateFlexible($cell->getValue());
    if ($d) return $d;
    $fmt = $cell->getFormattedValue();
    if (is_string($fmt)) $fmt = str_replace("\xC2\xA0", ' ', $fmt);
    return parseAnyDateFlexible($fmt);
}

/* ---- Excel helpers ---- */

function iGet(array $row, array $colsByName, string $header, ?string $alt = null): ?string
{
    $h1 = normKey($header);
    $col = $colsByName[$h1] ?? null;
    if (!$col && $alt) {
        $h2 = normKey($alt);
        $col = $colsByName[$h2] ?? null;
    }
    if (!$col) return null;
    $val = $row[$col] ?? null;
    if (!is_scalar($val)) return null;
    $val = trim((string)$val);
    return $val === '' ? null : $val;
}

/* ---------------- Start ---------------- */

$pdo = DatabaseConnection::getConnection();
if (!$pdo) exit("❌ No se pudo conectar a la base de datos\n");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$filePath = $argv[1] ?? $DEFAULT_PATH;
if (!is_file($filePath)) exit("❌ No encuentro el fichero: $filePath\n");

$spreadsheet = IOFactory::load($filePath);
$sheet = $spreadsheet->getActiveSheet();
$highestDataCol = $sheet->getHighestDataColumn();
$headersRow = $sheet->rangeToArray("A1:{$highestDataCol}1", null, true, true, true)[1] ?? [];
$colsByName = [];
foreach ($headersRow as $col => $name) {
    if ($name !== null && $name !== '') $colsByName[normKey((string)$name)] = $col;
}

out("✔️ Abierto: $filePath");
out("→ Filas: " . $sheet->getHighestRow());

/* ---- Prepared ---- */

// UPDATE persona
$getCategoria = $pdo->prepare("SELECT categoria FROM db_dades_personals WHERE id = :id");
$updatePersona = $pdo->prepare("
  UPDATE db_dades_personals
  SET estat_civil = IFNULL(:estat_civil, estat_civil),
      sector      = IFNULL(:sector, sector),
      ofici       = IFNULL(:ofici, ofici),
      municipi_residencia = IFNULL(:resid, municipi_residencia),
      categoria = :categoria,
        autor = 3,
         autor2 = 4,
         colab1 = 1,
         completat = 2,
         visibilitat = 2,
         data_actualitzacio = CURDATE()   
  WHERE id = :id
");

// INSERT guardia_urbana
$insertGU = $pdo->prepare("
  INSERT INTO db_detinguts_guardia_urbana
    (idPersona, data_empresonament, data_sortida, edat, motiu_empresonament, motiu_empresonament2, qui_ordena_detencio, top, observacions)
  VALUES
    (:idPersona, :data_empresonament, :data_sortida, :edat, :motiu, :motiu2, :ordena, :top, :observacions)
");

// Persona find statements (dentro de findPersona)

// Ofici
$findOfici = $pdo->prepare("SELECT id FROM aux_oficis WHERE ofici_cat COLLATE $COLLATE_CI = CONVERT(:n USING utf32) COLLATE $COLLATE_CI LIMIT 1");
$findOficiLike = $pdo->prepare("SELECT id FROM aux_oficis WHERE ofici_cat COLLATE $COLLATE_CI LIKE CONVERT(:p USING utf32) COLLATE $COLLATE_CI ORDER BY LENGTH(ofici_cat) ASC LIMIT 1");

/* ---- Bucle ---- */

$highestRow = $sheet->getHighestRow();
for ($r = 2; $r <= $highestRow; $r++) {
    $row = $sheet->rangeToArray("A{$r}:{$highestDataCol}{$r}", null, true, true, true)[$r] ?? [];

    $strNom = iGet($row, $colsByName, 'NOM');
    if ($strNom === null) {
        out("❌ Fila $r | NOM vacío");
        continue;
    }

    [$nom, $c1, $c2] = parseCombinedName($strNom);
    [$idPersona, $nomUsat] = findPersona($pdo, $nom ?? '', $c1, $c2, $NOM_EQUIV, $COLLATE_CI);
    if ($idPersona === 0) {
        out("❌ Fila $r | NO ENCONTRADA | NOM='$strNom'");
        continue;
    }

    // Lectura campos Excel
    $edatTxt     = iGet($row, $colsByName, 'EDAT');
    $estatTxt    = iGet($row, $colsByName, 'ESTAT CIVIL', 'ESTAT-CIVIL');
    $ciutatTxt   = iGet($row, $colsByName, 'CIUTAT', 'LOCALITAT');
    $dataEntrada = getDateYmdFromCellMulti($sheet, $colsByName, ['DATA ENTRADA', 'DATA D\'ENTRADA', 'DATA D’ENTRADA'], $r);
    $dataSortida = getDateYmdFromCellMulti($sheet, $colsByName, ['DATA SORTIDA', 'DATA DE SORTIDA'], $r);

    $motiuTxt    = iGet($row, $colsByName, 'MOTIU');
    $motiu2Txt   = iGet($row, $colsByName, 'MOTIU2');
    $ordenaTxt   = iGet($row, $colsByName, 'ORDENA DETENCIO', 'ORDENA DETENCIÓ');
    $topTxt      = iGet($row, $colsByName, 'TOP');
    $profTxt     = iGet($row, $colsByName, 'PROFESSIO', 'PROFESIÓN');
    $sectorTxt   = iGet($row, $colsByName, 'SECTOR ECONOMICS', 'SECTORS ECONOMICS');
    $obsExcel    = iGet($row, $colsByName, 'OBSERVACIONS');

    // Mapeos
    $edat        = toIntOrNull($edatTxt);
    $estatCivil  = mapIn($MAP_ESTAT_CIVIL, $estatTxt);
    $sector      = mapIn($MAP_SECTOR, $sectorTxt);

    // municipi_residencia
    $idResid = $ciutatTxt ? buscarMunicipi($pdo, $ciutatTxt) : null;

    // asegurar categoria {13}
    $getCategoria->execute([':id' => $idPersona]);
    $cat = $getCategoria->fetchColumn();
    $newCategoria = ensureCategoriaHasN($cat === false ? null : (string)$cat, 13);

    // ofici
    $oficiId = null;
    if ($profTxt) {
        $findOfici->execute([':n' => $profTxt]);
        $oficiId = $findOfici->fetchColumn() ?: null;
        if ($oficiId === null) {
            $findOficiLike->execute([':p' => $profTxt . '%']);
            $oficiId = $findOficiLike->fetchColumn() ?: null;
        }
    }

    // Motivos / ordena / top (INT directos si vienen numéricos)
    $motiu  = toIntOrNull($motiuTxt);
    $motiu2 = toIntOrNull($motiu2Txt);
    $ordena = toIntOrNull($ordenaTxt);
    $top    = toIntOrNull($topTxt);

    // Transacción
    $pdo->beginTransaction();
    try {
        // UPDATE persona
        $updatePersona->execute([
            ':estat_civil' => $estatCivil,
            ':sector'      => $sector,
            ':ofici'       => $oficiId,
            ':resid'       => $idResid,
            ':id'          => $idPersona,
            ':categoria'    => $newCategoria,
        ]);

        // INSERT GU
        $insertGU->execute([
            ':idPersona'          => $idPersona,
            ':data_empresonament' => $dataEntrada,
            ':data_sortida'       => $dataSortida,
            ':edat'               => $edat,
            ':motiu'              => $motiu,
            ':motiu2'             => $motiu2,
            ':ordena'             => $ordena, // si en tu Excel es texto y hay que buscar en aux_sistema_repressiu, avísame el nombre exacto de la columna
            ':top'                => $top,
            ':observacions'       => $obsExcel,
        ]);

        $pdo->commit();
        out("✅ Fila $r | ID={$idPersona} | edat=" . ($edat ?? '—') . " | estat=" . ($estatCivil ?? '—') . " | resid=" . ($idResid ?? '—') . " | ofici=" . ($oficiId ?? '—') . " | sector=" . ($sector ?? '—') . " | entrada={$dataEntrada} | sortida={$dataSortida} | motiu=" . ($motiu ?? '—') . " | motiu2=" . ($motiu2 ?? '—') . " | ordena=" . ($ordena ?? '—') . " | top=" . ($top ?? '—'));
    } catch (Throwable $e) {
        $pdo->rollBack();
        out("❌ Fila $r | ID={$idPersona} | ERROR=" . $e->getMessage());
    }
}

out("✔️ Proceso finalizado");
