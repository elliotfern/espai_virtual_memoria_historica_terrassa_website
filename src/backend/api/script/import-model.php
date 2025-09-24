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


$DEFAULT_PATH = '/home/epgylzqu/memoriaterrassa.cat/datos2.ods';

// BD en utf32_general_ci
$COLLATE_CI = 'utf32_general_ci';

// Mapeos
$MAP_SEXE = [
    'H' => 1,
    'HOME' => 1,
    'M' => 1,
    'MASCULI' => 1,
    'MASCULÍ' => 1,
    'MALE' => 1,
    '1' => 1,
    'D' => 2,
    'DONA' => 2,
    'F' => 2,
    'FEMENI' => 2,
    'FEMENÍ' => 2,
    'FEMALE' => 2,
    '2' => 2,
];

$MAP_ESTAT_CIVIL = [
    'S' => 1, // Solter/a
    'C' => 2, // Casat/da
    'V' => 3, // Vidu/a
    'D' => 5, // Divorciat/da
];

// Sector: guardamos INT 1/2/3 (PRIMARI/SECUNDARI/TERCIARI)
$MAP_SECTOR = [
    '1' => 1,
    'PRIMARI' => 1,
    '2' => 2,
    'SECUNDARI' => 2,
    '3' => 3,
    'TERCIARI' => 3,
    'TERCIARI' => 3,
    'TERCIARIO' => 3,
];

// Modalitat (texto → INT)
$MAP_MODALITAT = [
    'CONDICIONAL' => 1,
    'PREVENTIVA' => 2,
    'CONDICIONAL AMB DESTERRAMENT' => 3,
    'PROVISIONAL' => 4,
    'DEFINITIVA' => 5,
    'ATENUADA' => 6,
    'INDULTAT' => 7,
    'VIGILADA' => 8,
    'SENSE DADES' => 10,
    'DESTERRAMENT' => 11,
];

/**
 * Busca la columna por una lista de alias y lee la fecha de la celda (raw y formatted).
 */
function getDateYmdFromCellMulti(
    \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet,
    array $colsByName,
    array $aliases,
    int $rowNum
): ?string {
    // 1) exacto por alias
    $col = null;
    foreach ($aliases as $a) {
        $k = normKey($a);
        if (isset($colsByName[$k])) {
            $col = $colsByName[$k];
            break;
        }
    }
    // 2) si no, “contiene” (por si el encabezado es más largo)
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

    // RAW (DateTime / serial / texto)
    $d = parseAnyDateFlexible($cell->getValue());
    if ($d) return $d;

    // FORMATTED (lo visible en la hoja)
    $fmt = $cell->getFormattedValue();
    if (is_string($fmt)) $fmt = str_replace("\xC2\xA0", ' ', $fmt);
    $d2 = parseAnyDateFlexible($fmt);
    return $d2 ?: null;
}


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

    // ISO con o sin hora
    if (preg_match('/^\d{4}-\d{2}-\d{2}(?:[ T]\d{2}:\d{2}(?::\d{2})?)?$/', $s)) {
        return substr($s, 0, 10);
    }

    // dd/mm/yy(yy) o dd-mm-yy(yy) o dd.mm.yy(yy) con hora opcional al final
    if (preg_match('/^(\d{1,2})[\/\-.](\d{1,2})[\/\-.](\d{2,4})(?:\s+\d{1,2}:\d{2}(?::\d{2})?)?$/', $s, $m)) {
        $d = (int)$m[1];
        $M = (int)$m[2];
        $y = (int)$m[3];
        if ($y < 100) $y = 1900 + $y; // si quieres otro criterio me dices
        if ($d >= 1 && $d <= 31 && $M >= 1 && $M <= 12) {
            return sprintf('%04d-%02d-%02d', $y, $M, $d);
        }
    }
    return null;
}

function parseCatalanPretty(?string $s): ?string
{
    if ($s === null) return null;
    $t = str_replace("\xC2\xA0", ' ', $s);   // NBSP → espacio
    $t = str_replace('.', '', $t);           // quitar puntos en "nov."
    $t = mb_strtoupper(trim($t), 'UTF-8');

    // meses (abreviados y completos)
    $mesos = [
        'GEN' => 1,
        'GENER' => 1,
        'FEB' => 2,
        'FEBR' => 2,
        'FEBRER' => 2,
        'MARÇ' => 3,
        'MARC' => 3,
        'ABR' => 4,
        'ABRIL' => 4,
        'MAIG' => 5,
        'JUNY' => 6,
        'JUL' => 7,
        'JULIOL' => 7,
        'AG' => 8,
        'AGO' => 8,
        'AGOST' => 8,
        'SET' => 9,
        'SETEMBRE' => 9,
        'SEP' => 9,
        'SEPT' => 9,
        'OCT' => 10,
        'OCTUBRE' => 10,
        'NOV' => 11,
        'NOVEMBRE' => 11,
        'DES' => 12,
        'DESEMBRE' => 12,
        'DIC' => 12
    ];

    // acepta: "19 DE NOV DE 42", "19 DEL NOVEMBRE DE 1942", "19 D’OCT DE 42", ...
    if (!preg_match('/^(\d{1,2})\s+D[’\'E]?(?:EL\s+)?([A-ZÇ]+)\s+DE\s+(\d{2,4})$/u', $t, $m)) {
        return null;
    }
    $d = (int)$m[1];
    $monKey = $m[2];
    $y = (int)$m[3];
    if ($y < 100) $y = 1900 + $y;
    $M = $mesos[$monKey] ?? null;
    if (!$M || $d < 1 || $d > 31) return null;
    return sprintf('%04d-%02d-%02d', $y, $M, $d);
}

function parseAnyDateFlexible($raw): ?string
{
    // 1) DateTime (típico en ODS)
    if ($raw instanceof DateTimeInterface) {
        return $raw->format('Y-m-d');
    }
    // 2) Serial Excel
    $serial = parseExcelSerial($raw);
    if ($serial) return $serial;

    // 3) Texto
    $s = is_scalar($raw) ? (string)$raw : null;
    if ($s === null) return null;

    $dmy = parseDMYAny($s);
    if ($dmy) return $dmy;

    $cat = parseCatalanPretty($s);
    if ($cat) return $cat;

    return null;
}

/**
 * Lee una fecha desde la hoja por nombre de columna y nº de fila.
 * - Intenta: DateTime crudo → serial Excel → texto D/M/Y → texto catalán ("19 de nov. de 42")
 * - Si el raw falla, prueba también el getFormattedValue() de la celda.
 */
function getDateYmdFromCell(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, array $colsByName, string $header, ?string $altHeader, int $rowNum): ?string
{
    $h1 = normKey($header);
    $col = $colsByName[$h1] ?? null;
    if (!$col && $altHeader) {
        $h2 = normKey($altHeader);
        $col = $colsByName[$h2] ?? null;
    }
    if (!$col) return null;

    $coord = $col . (string)$rowNum;
    $cell  = $sheet->getCell($coord);

    // 1) RAW
    $raw = $cell->getValue();
    $d = parseAnyDateFlexible($raw);
    if ($d) return $d;

    // 2) FORMATTED (lo que ves en la hoja, p.ej. "04/02/1939")
    $fmt = $cell->getFormattedValue();
    // normalizar NBSP → espacio, por si acaso
    if (is_string($fmt)) {
        $fmt = str_replace("\xC2\xA0", ' ', $fmt);
    }
    $d2 = parseAnyDateFlexible($fmt);
    if ($d2) return $d2;

    return null;
}

// Devuelve el valor crudo de la celda (string, número, DateTime, etc.)
function iGetRaw(array $row, array $colsByName, string $header, ?string $altHeader = null)
{
    $h1 = normKey($header);
    $col = $colsByName[$h1] ?? null;
    if (!$col && $altHeader) {
        $h2 = normKey($altHeader);
        $col = $colsByName[$h2] ?? null;
    }
    if (!$col) return null;
    // devolvemos tal cual, sin filtrar por is_scalar()
    return $row[$col] ?? null;
}

function toIntOrNull(?string $v): ?int
{
    if ($v === null) return null;
    $v = trim($v);
    if ($v === '') return null;
    if (preg_match('/^-?\d+$/', $v)) return (int)$v;        // número puro
    if (preg_match('/-?\d+/', $v, $m)) return (int)$m[0];   // primer número dentro del texto
    return null;
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
    $s = str_replace("\xC2\xA0", ' ', $s); // NBSP → espacio
    $s = str_replace(["\xE2\x80\x93", "\xE2\x80\x94", "–", "—"], "-", $s);
    $s = preg_replace('/\s+/', ' ', trim($s));
    return mb_strtoupper($s, 'UTF-8');
}
function stripAccents(string $s): string
{
    $t = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
    return $t !== false ? $t : $s;
}
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
function mapIn(array $map, ?string $val): ?int
{
    if ($val === null) return null;
    $k = normKey(stripAccents($val));
    return $map[$k] ?? $map[(string)(int)$k] ?? null;
}
function parseDMY(?string $s): ?string
{
    if ($s === null || trim($s) === '') return null;
    $s = trim($s);
    if (!preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{2,4})$/', $s, $m)) return null;
    $d = (int)$m[1];
    $M = (int)$m[2];
    $y = (int)$m[3];
    if ($y < 100) $y = ($y >= 30 ? 1900 + $y : 2000 + $y);
    return sprintf('%04d-%02d-%02d', $y, $M, $d);
}
function parseCatalanDate(?string $s): ?string
{
    // ej: "19 de nov. de 42" → 1942-11-19
    if ($s === null || trim($s) === '') return null;
    $s = normKey($s);
    $s = str_replace('.', '', $s);
    $map = ['GEN' => 1, 'FEBR' => 2, 'FEB' => 2, 'MARÇ' => 3, 'MARC' => 3, 'ABR' => 4, 'MAIG' => 5, 'JUNY' => 6, 'JUL' => 7, 'AG' => 8, 'SET' => 9, 'OCT' => 10, 'NOV' => 11, 'DES' => 12, 'DEC' => 12];
    if (!preg_match('/(\d{1,2})\s+DE\s+([A-ZÇ]+)\s+DE\s+(\d{2,4})/u', $s, $m)) return null;
    $d = (int)$m[1];
    $monKey = $m[2];
    $y = (int)$m[3];
    if ($y < 100) $y = 1900 + $y;
    $mon = $map[$monKey] ?? null;
    if (!$mon) return null;
    return sprintf('%04d-%02d-%02d', $y, $mon, $d);
}
function parseCombinedName(?string $s): array
{
    // "COGNOM1 COGNOM2, NOM(S)" → [nomCompleto, cognom1, cognom2]
    $nom = $c1 = $c2 = null;
    if ($s === null || trim($s) === '') return [$nom, $c1, $c2];

    $parts = explode(',', $s, 2);
    $left  = trim($parts[0] ?? ''); // apellidos
    $right = trim($parts[1] ?? ''); // nombre(s)

    if ($right !== '') {
        // Conserva TODO el nombre (p. ej. "Maria gloria")
        // normaliza espacios internos
        $nom = preg_replace('/\s+/', ' ', $right);
    }

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
    // Genera candidatos: nombre completo y primer nombre
    $candidatos = [];
    $nomTrim = trim($nom);
    if ($nomTrim !== '') {
        // candidato 1: nombre completo
        $candidatos[] = $nomTrim;

        // candidato 2: primer nombre
        $tokens = preg_split('/\s+/', $nomTrim);
        if ($tokens && count($tokens) > 1) {
            $candidatos[] = $tokens[0];
        }
    }

    $c1n = normKey($c1 ?? '');
    $c2n = normKey($c2 ?? '');

    static $stmtWithC2 = null, $stmtNoC2 = null;
    if ($stmtWithC2 === null) {
        $stmtWithC2 = $pdo->prepare("
          SELECT id
          FROM db_dades_personals
          WHERE
            nom     COLLATE $COLLATE_CI = CONVERT(:nom USING utf32)     COLLATE $COLLATE_CI
            AND cognom1 COLLATE $COLLATE_CI = CONVERT(:c1  USING utf32) COLLATE $COLLATE_CI
            AND cognom2 COLLATE $COLLATE_CI = CONVERT(:c2  USING utf32) COLLATE $COLLATE_CI
          LIMIT 1
        ");
        $stmtNoC2 = $pdo->prepare("
          SELECT id
          FROM db_dades_personals
          WHERE
            nom     COLLATE $COLLATE_CI = CONVERT(:nom USING utf32)     COLLATE $COLLATE_CI
            AND cognom1 COLLATE $COLLATE_CI = CONVERT(:c1  USING utf32) COLLATE $COLLATE_CI
            AND (cognom2 IS NULL OR cognom2 = '')
          LIMIT 1
        ");
    }

    foreach ($candidatos as $candNom) {
        // variantes (catalán↔castellano; si el nombre es compuesto y no está en el mapa,
        // nomVariantsSmart devolverá al menos [NOM, NOM_sin_acentos])
        $variants = nomVariantsSmart($candNom, $NOM_EQUIV);

        foreach ($variants as $vn) {
            // 1) con cognom2 si lo tenemos
            if ($c2n !== '') {
                $stmtWithC2->execute([':nom' => $vn, ':c1' => $c1n, ':c2' => $c2n]);
                $id = $stmtWithC2->fetchColumn();
                if ($id !== false) return [(int)$id, $vn];
            }
            // 2) sin cognom2
            $stmtNoC2->execute([':nom' => $vn, ':c1' => $c1n]);
            $id2 = $stmtNoC2->fetchColumn();
            if ($id2 !== false) return [(int)$id2, $vn];
        }
    }

    return [0, ''];
}

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

/* --------------- Start --------------- */

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

/* Prepared statements */

$getCategoria = $pdo->prepare("SELECT categoria FROM db_dades_personals WHERE id = :id");
$updatePersona = $pdo->prepare("
  UPDATE db_dades_personals
  SET sexe = IFNULL(:sexe,sexe),
      estat_civil = IFNULL(:estat_civil, estat_civil),
      sector = IFNULL(:sector, sector),
      ofici = IFNULL(:ofici, ofici),
      observacions = :observacions,
      categoria = :categoria,
          autor = 3,
         autor2 = 4,
         colab1 = 1,
         completat = 2,
         visibilitat = 2,
         data_actualitzacio = CURDATE()     
  WHERE id = :id
");

$insertDetingut = $pdo->prepare("
  INSERT INTO db_detinguts_model
    (idPersona, data_empresonament, trasllats, lloc_trasllat, data_trasllat, llibertat, data_llibertat, modalitat, vicissituds, observacions)
  VALUES
    (:idPersona, :data_empresonament, :trasllats, :lloc_trasllat, :data_trasllat, :llibertat, :data_llibertat, :modalitat, :vicissituds, :observacions)
");

$findOfici = $pdo->prepare("SELECT id FROM aux_oficis WHERE ofici_cat = :n LIMIT 1");
$findOficiLike = $pdo->prepare("SELECT id FROM aux_oficis WHERE ofici_cat LIKE :n ORDER BY LENGTH(ofici_cat) ASC LIMIT 1");

/* Bucle filas */

$highestRow = $sheet->getHighestRow();
for ($r = 2; $r <= $highestRow; $r++) {
    $row = $sheet->rangeToArray("A{$r}:{$highestDataCol}{$r}", null, true, true, true)[$r] ?? [];
    $strNom = iGet($row, $colsByName, 'NOM'); // formato "COGNOM1 COGNOM2, NOM"
    if ($strNom === null) continue;

    [$nom, $c1, $c2] = parseCombinedName($strNom);
    if (!$nom && !$c1) {
        out("❌ Fila $r | NOM vacío");
        continue;
    }

    // buscar persona
    [$idPersona, $nomUsat] = findPersona($pdo, $nom ?? '', $c1, $c2, $NOM_EQUIV, $COLLATE_CI);
    if ($idPersona === 0) {
        out("❌ Fila $r | NO ENCONTRADA | NOM='$strNom'");
        continue;
    }

    // leer Excel
    $sexeTxt        = iGet($row, $colsByName, 'SEXE');
    $estatTxt       = iGet($row, $colsByName, 'ESTAT CIVIL', 'ESTAT-CIVIL');
    $anyNaix        = iGet($row, $colsByName, 'ANY NAIXEMENT', 'ANY NAIXEMENT ');
    $professioTxt   = iGet($row, $colsByName, 'PROFESSIO', 'PROFESIÓN');
    $sectorTxt      = iGet($row, $colsByName, 'SECTOR ECONOMICS', 'SECTOR ECONÒMICS');


    // ✅ ahora (con iGetRaw, deja pasar DateTime/num)
    $dataEmp  = getDateYmdFromCellMulti(
        $sheet,
        $colsByName,
        ['DATA EMPRESONAMENT', "DATA D'EMPRESONAMENT", 'DATA DEL EMPRESONAMENT', 'EMPRESONAMENT'],
        $r
    );
    $dataTras = getDateYmdFromCellMulti(
        $sheet,
        $colsByName,
        ['DATA TRASLLAT', 'DATA DE TRASLLAT', 'TRASLLAT'],
        $r
    );
    $dataLlib = getDateYmdFromCellMulti(
        $sheet,
        $colsByName,
        ['DATA LLIBERTAT', 'DATA DE LLIBERTAT', 'LLIBERTAT'],
        $r
    );


    $trasllatsTxt   = iGet($row, $colsByName, 'TRASLLATS');
    $llocTras       = iGet($row, $colsByName, 'LLOC TRASLLAT');
    $llibertatTxt   = iGet($row, $colsByName, 'LLIBERTAT');
    $modalitatTxt   = iGet($row, $colsByName, 'MODALITAT');
    $vicissituds    = iGet($row, $colsByName, 'VICISSITUDS');
    $obsExcel       = iGet($row, $colsByName, 'OBSERVACIONS');

    // mapeos
    $sexe = mapIn($MAP_SEXE, $sexeTxt);
    $estatCivil = mapIn($MAP_ESTAT_CIVIL, $estatTxt);
    $sector = mapIn($MAP_SECTOR, $sectorTxt);

    // ofici lookup
    $oficiId = null;
    if ($professioTxt) {
        $findOfici->execute([':n' => $professioTxt]);
        $oficiId = $findOfici->fetchColumn() ?: null;
        if ($oficiId === null) {
            $findOficiLike->execute([':n' => $professioTxt . '%']);
            $oficiId = $findOficiLike->fetchColumn() ?: null;
        }
    }

    // observacions en db_dades_personals (añadir ANY NAIXEMENT si viene)
    $obsPersona = $obsExcel ?? '';
    if ($anyNaix) {
        $obsPersona = trim($obsPersona) !== '' ? ($obsPersona . " | ANY NAIXEMENT: " . $anyNaix) : ("ANY NAIXEMENT: " . $anyNaix);
    }

    // asegurar categoria {12}
    $getCategoria->execute([':id' => $idPersona]);
    $cat = $getCategoria->fetchColumn();
    $newCategoria = ensureCategoriaHasN($cat === false ? null : (string)$cat, 12);

    // trasllats / llibertat
    $norm = function (?string $v): ?int {
        if ($v === null) return null;
        $k = normKey(stripAccents($v));
        if ($k === 'SI') return 1;
        if ($k === 'NO') return 2;
        if ($k === 'SENSE DADES' || $k === 'SENSEDADES' || $k === 'S/D' || $k === 'SD' || $k === '') return 3;
        return null;
    };
    $trasllats = $norm($trasllatsTxt);
    $llibertat = $norm($llibertatTxt);

    // modalitat
    $modalitat = null;
    if ($modalitatTxt !== null) {
        $mk = normKey(stripAccents($modalitatTxt));
        $modalitat = $MAP_MODALITAT[$mk] ?? toIntOrNull($modalitatTxt);
    }

    // transacción por fila
    $pdo->beginTransaction();
    try {
        // UPDATE persona
        $updatePersona->execute([
            ':sexe'         => $sexe,
            ':estat_civil'  => $estatCivil,
            ':sector'       => $sector,
            ':ofici'        => $oficiId,
            ':observacions' => $obsPersona !== '' ? $obsPersona : null,
            ':categoria'    => $newCategoria,
            ':id'           => $idPersona,
        ]);

        // INSERT detingut (puede repetirse idPersona)
        $insertDetingut->execute([
            ':idPersona'          => $idPersona,
            ':data_empresonament' => $dataEmp,
            ':trasllats'          => $trasllats,
            ':lloc_trasllat'      => $llocTras,
            ':data_trasllat'      => $dataTras,
            ':llibertat'          => $llibertat,
            ':data_llibertat'     => $dataLlib,
            ':modalitat'          => $modalitat,
            ':vicissituds'        => $vicissituds,
            ':observacions'       => $obsExcel,
        ]);

        $pdo->commit();
        out("✅ Fila $r | ID={$idPersona} | sexe=" . ($sexe ?? '—') . " | estat=" . $estatCivil . " | sector=" . ($sector ?? '—') . " | ofici=" . ($oficiId ?? '—') . " | cat={$newCategoria} | emp={$dataEmp} | trasl=" . ($trasllats ?? '—') . " | llib=" . ($llibertat ?? '—') . " | modalitat=" . ($modalitat ?? '—'));
    } catch (Throwable $e) {
        $pdo->rollBack();
        out("❌ Fila $r | ID={$idPersona} | ERROR=" . $e->getMessage());
    }
}

out("✔️ Proceso finalizado");
