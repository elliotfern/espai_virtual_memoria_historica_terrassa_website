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
$DEFAULT_PATH = '/home/epgylzqu/memoriaterrassa.cat/datos4.ods';

// BD en utf32_general_ci
$COLLATE_CI = 'utf32_general_ci';


/* ---------------- Helpers ---------------- */
function toIntOrNull(?string $v): ?int
{
    if ($v === null || $v === '') return null;
    if (preg_match('/^-?\d+$/', $v)) return (int)$v;
    if (preg_match('/-?\d+/', $v, $m)) return (int)$m[0];
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

/** Asegura que en categoria aparezca {N} preservando los existentes. */
function ensureCategoriaHasN(?string $s, int $n): string
{
    if ($s === null || trim($s) === '') return '{' . $n . '}';
    $s = trim($s);
    if ($s[0] === '{' && substr($s, -1) === '}') $s = substr($s, 1, -1);
    $nums = [];
    foreach (array_map('trim', explode(',', $s)) as $p) {
        if ($p !== '' && preg_match('/^\d+$/', $p)) $nums[] = (int)$p;
    }
    if (!in_array($n, $nums, true)) $nums[] = $n;
    $uniq = [];
    foreach ($nums as $x) {
        if (!in_array($x, $uniq, true)) $uniq[] = $x;
    }
    return '{' . implode(',', $uniq) . '}';
}

/** “COGNOM1 COGNOM2, NOM(S)” → [nomCompleto, cognom1, cognom2] */
function parseCombinedName(?string $s): array
{
    $nom = $c1 = $c2 = null;
    if ($s === null || trim($s) === '') return [$nom, $c1, $c2];
    [$left, $right] = array_map('trim', array_pad(explode(',', $s, 2), 2, ''));
    if ($right !== '') $nom = preg_replace('/\s+/', ' ', $right); // conserva nombres compuestos
    if ($left !== '') {
        $surn = preg_split('/\s+/', $left);
        if (count($surn) === 1) {
            $c1 = $surn[0];
            $c2 = null;
        } else {
            $c1 = array_shift($surn);
            $c2 = implode(' ', $surn);
        }
    }
    return [$nom, $c1, $c2];
}

/** Equivalencias catalán↔castellano + sin acentos */
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

/** Busca persona probando nombre completo y primer nombre; con/sin cognom2. */
function findPersona(PDO $pdo, string $nom, ?string $c1, ?string $c2, array $NOM_EQUIV, string $COLLATE_CI): array
{
    $candidatos = [];
    $nomTrim = trim($nom);
    if ($nomTrim !== '') {
        $candidatos[] = $nomTrim; // nombre completo
        $toks = preg_split('/\s+/', $nomTrim);
        if ($toks && count($toks) > 1) $candidatos[] = $toks[0]; // primer nombre
    }
    $c1n = normKey($c1 ?? '');
    $c2n = normKey($c2 ?? '');

    static $stmtWithC2 = null, $stmtNoC2 = null;
    if ($stmtWithC2 === null) {
        $stmtWithC2 = $pdo->prepare("
          SELECT id FROM db_dades_personals
          WHERE nom     COLLATE $COLLATE_CI = CONVERT(:nom USING utf32) COLLATE $COLLATE_CI
            AND cognom1 COLLATE $COLLATE_CI = CONVERT(:c1  USING utf32) COLLATE $COLLATE_CI
            AND cognom2 COLLATE $COLLATE_CI = CONVERT(:c2 USING utf32) COLLATE $COLLATE_CI
          LIMIT 1
        ");
        $stmtNoC2 = $pdo->prepare("
          SELECT id FROM db_dades_personals
          WHERE nom     COLLATE $COLLATE_CI = CONVERT(:nom USING utf32) COLLATE $COLLATE_CI
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

/** Lee valor de una cabecera (exacta o alias). */
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

/** Map SEXE → int (HOME→1, DONA→2; admite variantes) */
function mapSexeToInt(?string $s): ?int
{
    if ($s === null) return null;
    $k = normKey(stripAccents($s));
    if (preg_match('/^(1|H|HOME|M|MASCULI|MASCULI\.?|MASCULÍ|MALE)$/', $k)) return 1;
    if (preg_match('/^(2|D|DONA|F|FEMENI|FEMENÍ|FEMALE)$/', $k)) return 2;
    return null;
}

/* ---------------- Conexión y Excel ---------------- */

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

/* ---------------- Prepared statements ---------------- */

// ¿ya existe en RP?
$existsRP = $pdo->prepare("
    SELECT 1 FROM db_responsabilitats_politiques
    WHERE idPersona = :id
    LIMIT 1
");

// obtener categoria actual
$getCategoria = $pdo->prepare("
    SELECT categoria FROM db_dades_personals
    WHERE id = :id
    LIMIT 1
");

// UPDATE persona (incluye sexe, filiaciones y categoria {15})
$updatePersona = $pdo->prepare("
  UPDATE db_dades_personals
     SET sexe = IFNULL(:sexe, sexe),
         filiacio_politica = IFNULL(:fp, filiacio_politica),
         filiacio_sindical = IFNULL(:fs, filiacio_sindical),
         categoria = :categoria,
         autor = 3,
         autor2 = 4,
         colab1 = 1,
         completat = 2,
         visibilitat = 2,
         data_actualitzacio = CURDATE()
   WHERE id = :id
");

// INSERT responsabilitats_politiques
$insertRP = $pdo->prepare("
  INSERT INTO db_responsabilitats_politiques
    (idPersona, lloc_empresonament, lloc_exili, condemna, observacions)
  VALUES
    (:idPersona, :emp, :exili, :condemna, :observacions)
");

/* ---------------- Bucle ---------------- */

$highestRow = $sheet->getHighestRow();
for ($r = 2; $r <= $highestRow; $r++) {
    $row = $sheet->rangeToArray("A{$r}:{$highestDataCol}{$r}", null, true, true, true)[$r] ?? [];

    // NOM en formato "COGNOM1 COGNOM2, NOM(S)"
    $strNom = iGet($row, $colsByName, 'NOM');
    if ($strNom === null) {
        out("❌ Fila $r | NOM vacío");
        continue;
    }

    [$nom, $c1, $c2] = parseCombinedName($strNom);
    if (!$nom && !$c1) {
        out("❌ Fila $r | NOM no parseable: '$strNom'");
        continue;
    }

    // localizar persona
    [$idPersona, $nomUsat] = findPersona($pdo, $nom ?? '', $c1, $c2, $NOM_EQUIV, $COLLATE_CI);
    if ($idPersona === 0) {
        out("❌ Fila $r | NO ENCONTRADA | NOM='$strNom'");
        continue;
    }

    // si ya existe en RP → saltar
    $existsRP->execute([':id' => $idPersona]);
    if ($existsRP->fetchColumn()) {
        out("⏭️ Fila $r | SKIP | idPersona={$idPersona} ya en db_responsabilitats_politiques");
        continue;
    }

    // leer Excel
    $sexeTxt   = iGet($row, $colsByName, 'SEXE');
    $fpTxt     = iGet($row, $colsByName, 'FILIACIÓ POLÍTICA', 'FILIACIO POLITICA');
    $fsTxt     = iGet($row, $colsByName, 'FILIACIÓ SINDICAL', 'FILIACIO SINDICAL');
    $empTxt    = iGet($row, $colsByName, 'EMPRESONAT');
    $exiliTxt  = iGet($row, $colsByName, 'EXILI');
    $condemna  = iGet($row, $colsByName, 'CONDEMNA');
    $obsExcel  = iGet($row, $colsByName, 'OBSERVACIONS');

    // mapeos
    $sexe = mapSexeToInt($sexeTxt);
    $emp  = $empTxt !== null ? (int)toIntOrNull($empTxt) : null;   // lloc_empresonament
    $exil = $exiliTxt !== null ? (int)toIntOrNull($exiliTxt) : null; // lloc_exili

    // asegurar categoria {15}
    $getCategoria->execute([':id' => $idPersona]);
    $cat = $getCategoria->fetchColumn();
    $newCategoria = ensureCategoriaHasN($cat === false ? null : (string)$cat, 15);

    // transacción
    $pdo->beginTransaction();
    try {
        // UPDATE persona
        $updatePersona->execute([
            ':sexe'      => $sexe,
            ':fp'        => $fpTxt ?: null,
            ':fs'        => $fsTxt ?: null,
            ':categoria' => $newCategoria,
            ':id'        => $idPersona,
        ]);

        // INSERT RP
        $insertRP->execute([
            ':idPersona'    => $idPersona,
            ':emp'          => $emp,
            ':exili'        => $exil,
            ':condemna'     => $condemna,
            ':observacions' => $obsExcel,
        ]);

        $pdo->commit();
        out("✅ Fila $r | ID={$idPersona} | sexe=" . ($sexe ?? '—') . " | cat={$newCategoria} | emp=" . ($emp ?? '—') . " | exili=" . ($exil ?? '—'));
    } catch (Throwable $e) {
        $pdo->rollBack();
        out("❌ Fila $r | ID={$idPersona} | ERROR=" . $e->getMessage());
    }
}

out("✔️ Proceso finalizado");
