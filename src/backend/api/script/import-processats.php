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

/* ------------------------ Config básica ------------------------ */

$DEFAULT_PATH = '/home/epgylzqu/memoriaterrassa.cat/datos.ods';

/** Mapeos texto -> código */
$TIPUS_PROCEDIMENT = [
    'CONSELL DE GUERRA'          => 1,
    'CONSELLS DE GUERRA'         => 1,
    'CONSELLS DE GUERRA []'      => 1,
    'DILIGÈNCIES PRÈVIES'        => 2,
    'JURISDICCIÓ ORDINÀRIA'      => 4,
    'DESCONEGUT'                 => 5,
    ''                           => 5,
    "'--"                        => 5,
    "//"                          => 5,

];

$TIPUS_JUDICI = [
    'SUMARÍSSIM'                 => 1,
    'CAUSA ORDINÀRIA'            => 2,
    'DILIGÈNCIES PRÈVIES'        => 3,
    'PROCEDIMENT PREVI'          => 4,
    "CAUSA D'OFICIALS GENERALS"  => 6,
    "//"                         => 7,
    "--"                         => 7,
    ""                           => 7,
    "'--"                        => 7,
    "DESCONEGUT"                 => 7,
    "Causa acumulada"            => 8,
    "//"                          => 7,
];

/** PENA: acepta códigos "1..18" o descripciones */
$PENA = [
    '20 ANYS DE RECLUSIÓ TEMPORAL'        => 1,
    'VINT ANYS DE RECLUSIÓ TEMPORAL'      => 1,
    'SIS MESOS I UN DIA DE PRESÓ MENOR'   => 2,
    'MORT'                                 => 3,
    '6 ANYS I UN DIA'                      => 4,
    'SIS ANYS I UN DIA'                      => 4,
    'DOTZE ANYS I UN DIA DE RECLUSIÓ TEMPORAL' => 5,
    'ABSOLUCIÓ'                            => 7,
    'VUIT ANYS DE PRESÓ MAJOR'            => 8,
    'DOS ANYS DE PRESÓ'                    => 9,
    'SOBRESEÏMENT'                         => 10,
    'SENSE DECLARACIÓ DE RESPONSABILITATS' => 11,
    'DOTZE ANYS DE PRESÓ MAJOR'            => 12,
    'LLIBERTAT'                            => 13,
    'TRENTA ANYS DE PRESÓ'                => 14,
    'SIS ANYS I UN DIA DE PRESÓ MAJOR'     => 15,
    'QUINZE ANYS DE RECLUSIÓ TEMPORAL'     => 16,
    'RECLUSIÓ PERPÈTUA'                    => 17,
    'NOU ANYS DE PRESÓ'                    => 18,
];

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



/* ------------------------ Helpers ------------------------ */

/**
 * Normaliza el campo categoria para que siempre incluya el número 6.
 * - Acepta formatos como "{1, 6}", "1,6", "6", "{ }", NULL...
 * - Devuelve SIEMPRE con llaves y sin espacios: "{...}"
 */
function ensureCategoriaHas6(?string $s): string
{
    if ($s === null) {
        return '{6}';
    }
    $s = trim($s);
    if ($s === '') {
        return '{6}';
    }
    // quitar llaves si existen
    if ($s[0] === '{' && substr($s, -1) === '}') {
        $s = substr($s, 1, -1);
    }
    // separar por comas
    $parts = array_map('trim', explode(',', $s));
    // quedarnos solo con números válidos
    $nums = [];
    foreach ($parts as $p) {
        if ($p === '') continue;
        if (preg_match('/^\d+$/', $p)) {
            $nums[] = (int)$p;
        }
    }
    // añadir 6 si no está
    if (!in_array(6, $nums, true)) {
        $nums[] = 6;
    }
    // quitar duplicados manteniendo orden
    $uniq = [];
    foreach ($nums as $n) {
        if (!in_array($n, $uniq, true)) {
            $uniq[] = $n;
        }
    }
    // reconstruir sin espacios
    return '{' . implode(',', $uniq) . '}';
}


// Imprime una línea: en CLI con \n; en web con <br>
function out(string $line): void
{
    if (PHP_SAPI === 'cli') {
        echo $line . PHP_EOL;
    } else {
        echo htmlspecialchars($line, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "<br>\n";
    }
    @ob_flush();
    @flush();
}

function normKey(?string $s): string
{
    $s = (string)$s;
    // sustituye NBSP por espacio normal
    $s = str_replace("\xC2\xA0", ' ', $s);
    // normaliza guiones largos a '-'
    $s = str_replace(["\xE2\x80\x93", "\xE2\x80\x94", "–", "—"], "-", $s);
    // colapsa y recorta
    $s = preg_replace('/\s+/', ' ', trim($s));
    // mayúsculas
    return mb_strtoupper($s, 'UTF-8');
}

function stripAccents(string $s): string
{
    $t = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
    return $t !== false ? $t : $s;
}

// Construye un mapa bidireccional: cada miembro del grupo mapea a todas las variantes
// Mapa bidireccional: cualquier variante => TODAS las variantes (incluye sin acentos)
$NOM_VARIANTS = [];
foreach ($NOM_EQUIV as $group) {
    $all = [];
    foreach ($group as $name) {
        $all[] = normKey($name);
        $all[] = normKey(stripAccents($name));
    }
    $all = array_values(array_unique($all));
    foreach ($group as $name) {
        $k  = normKey($name);
        $kA = normKey(stripAccents($name));
        $NOM_VARIANTS[$k]  = $all;
        $NOM_VARIANTS[$kA] = $all;
    }
}


function iGet(array $row, array $colsByName, string $header, ?string $altHeader = null): ?string
{
    $h1 = normKey($header);
    $col = $colsByName[$h1] ?? null;
    if (!$col && $altHeader) {
        $h2 = normKey($altHeader);
        $col = $colsByName[$h2] ?? null;
    }
    if (!$col) return null;
    $val = $row[$col] ?? null;
    if (!is_scalar($val)) return null;
    $val = trim((string)$val);
    return $val === '' ? null : $val;
}
function splitCognoms(?string $cognoms): array
{
    if (!$cognoms) return [null, null];
    $parts = preg_split('/\s+/', trim($cognoms)) ?: [];
    if (!$parts) return [null, null];
    if (count($parts) === 1) return [$parts[0], null];
    $c1 = array_shift($parts);
    $c2 = implode(' ', $parts);
    return [$c1, $c2];
}
function toIntOrNull(?string $v): ?int
{
    if ($v === null || $v === '') return null;
    if (preg_match('/^-?\d+$/', $v)) return (int)$v;
    if (preg_match('/-?\d+/', $v, $m)) return (int)$m[0];
    return null;
}
function mapTextOrCode(?string $txt, array $map): ?int
{
    if ($txt === null) return null;
    $trim = trim($txt);
    if (preg_match('/^\d+$/', $trim)) return (int)$trim; // código directo
    $key = normKey($trim);
    return $map[$key] ?? null;
}
function mapSexeToInt(?string $s): ?int
{
    if ($s === null) return null;
    $k = normKey(stripAccents($s));
    // valores típicos: H, M, Home, Dona, Masculí, Femení, Male, Female, 1, 2...
    if (preg_match('/^(1|H|HOME|M|MASCULI|MALE)$/', $k)) return 1;
    if (preg_match('/^(2|D|DONA|F|FEMENI|FEMALE)$/', $k)) return 2;
    return null;
}

/**
 * Genera variantes del nombre (Catalán↔Castellano) + formas sin acento
 */
function nomVariantsSmart(string $nom, array $NOM_VARIANTS): array
{
    $k  = normKey($nom);
    $kA = normKey(stripAccents($nom));
    if (isset($NOM_VARIANTS[$k]))  return $NOM_VARIANTS[$k];
    if (isset($NOM_VARIANTS[$kA])) return $NOM_VARIANTS[$kA];
    return array_values(array_unique([$k, $kA]));
}

/* ------------------------ DB & fichero ------------------------ */

$pdo = DatabaseConnection::getConnection();
if (!$pdo) {
    exit("❌ No se pudo conectar a la base de datos.\n");
}
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// ruta
$filePath = $argv[1] ?? $DEFAULT_PATH;
if (!is_file($filePath)) {
    exit("❌ No encuentro el fichero: $filePath\n");
}

// cargar hoja
$type = IOFactory::identify($filePath);
$spreadsheet = IOFactory::load($filePath);
$sheet = $spreadsheet->getActiveSheet();

echo "✔️ Abierto: $filePath (tipo: $type)" . PHP_EOL;

// índice cabeceras
$highestDataCol = $sheet->getHighestDataColumn();
$headersRow = $sheet->rangeToArray("A1:{$highestDataCol}1", null, true, true, true)[1] ?? [];
$colsByName = [];
foreach ($headersRow as $col => $name) {
    if ($name === null || $name === '') continue;
    $colsByName[normKey((string)$name)] = $col;
}

/* ------------------------ Prepared statements ------------------------ */

// Comparación acento-insensible (ajusta collation si tu servidor usa otro)
$COLLATE_CI = 'utf32_general_ci';

$getCategoria = $pdo->prepare("SELECT categoria FROM db_dades_personals WHERE id = :id LIMIT 1");

// NUEVO: comprobar si ya existe un registro de processats para ese idPersona
$existsProcessat = $pdo->prepare(
    "SELECT 1 FROM db_processats WHERE idPersona = :id LIMIT 1"
);


// update solo si hay dato (IFNULL conserva el existente)
$updatePersona = $pdo->prepare(
    "UPDATE db_dades_personals
     SET municipi_naixement = IFNULL(:naix, municipi_naixement),
         municipi_residencia = IFNULL(:resid, municipi_residencia),
         sexe = IFNULL(:sexe, sexe),
         categoria = :categoria,
         autor = 3,
         autor2 = 4,
         colab1 = 1,
         completat = 2,
         visibilitat = 2,
         data_actualitzacio = CURDATE()
     WHERE id = :id"
);

$touchCategoria = $pdo->prepare(
    "UPDATE db_dades_personals
   SET categoria = :categoria,
       data_actualitzacio = CURDATE()
   WHERE id = :id"
);

$insertProcessat = $pdo->prepare(
    "INSERT INTO db_processats
     (idPersona, anyDetingut, tipus_procediment, tipus_judici, num_causa, any_inicial, any_final, pena, commutacio)
     VALUES
     (:idPersona, :anyDetingut, :tipus_procediment, :tipus_judici, :num_causa, :any_inicial, :any_final, :pena, :commutacio)"
);

// municipi lookup
function buscarMunicipi(PDO $pdo, ?string $nom): ?int
{
    if (!$nom) return null;
    $nomTrim = trim($nom);

    // exacto por ciutat/ciutat_ca
    $q1 = $pdo->prepare("SELECT id FROM aux_dades_municipis
                         WHERE ciutat = :n OR ciutat_ca = :n
                         LIMIT 1");
    $q1->execute([':n' => $nomTrim]);
    $id = $q1->fetchColumn();
    if ($id !== false) return (int)$id;

    // aproximado (prefijo)
    $q2 = $pdo->prepare("SELECT id FROM aux_dades_municipis
                         WHERE ciutat LIKE :pfx OR ciutat_ca LIKE :pfx
                         ORDER BY LENGTH(ciutat) ASC
                         LIMIT 1");
    $q2->execute([':pfx' => $nomTrim . '%']);
    $id2 = $q2->fetchColumn();
    return $id2 !== false ? (int)$id2 : null;
}

/** busca persona probando variantes de nombre */
function findPersona(PDO $pdo, string $nom, ?string $c1, ?string $c2, array $NOM_VARIANTS, string $COLLATE_CI): array
{
    $variants = nomVariantsSmart($nom, $NOM_VARIANTS);
    $c1n = normKey($c1 ?? '');
    $c2n = normKey($c2 ?? '');

    static $stmtWithC2 = null, $stmtNoC2 = null;
    if ($stmtWithC2 === null) {
        // Columnas sin CONVERT (conserva índices). Parámetros convertidos a utf32 y collation utf32_general_ci en ambos lados.
        $stmtWithC2 = $pdo->prepare("
          SELECT id
          FROM db_dades_personals
          WHERE
            nom     COLLATE $COLLATE_CI = CONVERT(:nom USING utf32)     COLLATE $COLLATE_CI
            AND cognom1 COLLATE $COLLATE_CI = CONVERT(:c1  USING utf32) COLLATE $COLLATE_CI
            AND cognom2 COLLATE $COLLATE_CI = CONVERT(:c2 USING utf32)  COLLATE $COLLATE_CI
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
    return [0, ''];
}



/* ------------------------ Procesar filas ------------------------ */

@ini_set('output_buffering', '0');
@ini_set('implicit_flush', '1');
ob_implicit_flush(true);

$highestRow = $sheet->getHighestRow();
out("→ Filas detectadas: $highestRow");

for ($r = 2; $r <= $highestRow; $r++) {
    $row = $sheet->rangeToArray("A{$r}:{$highestDataCol}{$r}", null, true, true, true)[$r] ?? [];

    $cognoms         = iGet($row, $colsByName, 'COGNOMS');
    $nom             = iGet($row, $colsByName, 'NOM');
    $sexeTxt         = iGet($row, $colsByName, 'SEXE');
    $edatDetencio    = iGet($row, $colsByName, 'EDAT – detencio', 'EDAT - detencio');
    $munNaix         = iGet($row, $colsByName, 'MUNICIPI NAIXEMENT');
    $munResid        = iGet($row, $colsByName, 'LOCALIDAD RESIDENCIA');
    $tipusProcTxt    = iGet($row, $colsByName, 'TIPUS PROCEDIMENT');
    $tipusJudiciTxt  = iGet($row, $colsByName, 'TIPUS JUDICI');
    $numCausa        = iGet($row, $colsByName, 'NUM CAUSA');
    $anyInici        = iGet($row, $colsByName, 'ANY INICI CAUSA');
    $anySentencia    = iGet($row, $colsByName, 'ANY SENTENCIA');
    $penaTxt         = iGet($row, $colsByName, 'PENA');
    $commutacio      = iGet($row, $colsByName, 'COMMUNITACIO O INDULT', 'COMMUTACIO O INDULT');

    if (!$cognoms && !$nom) continue;

    [$c1, $c2] = splitCognoms($cognoms);
    $sexe = mapSexeToInt($sexeTxt);

    // encontrar persona con variantes
    [$idPersona, $nomUsat] = findPersona($pdo, $nom ?? '', $c1, $c2, $NOM_VARIANTS, $COLLATE_CI);
    if ($idPersona === 0) {
        out("❌ Fila {$r} | NO ENCONTRADA | NOM='{$nom}' | COGNOMS='{$cognoms}'");
        continue;
    }

    // NUEVO: si ya existe en db_processats, saltar todo (ni UPDATE ni INSERT)
    $existsProcessat->execute([':id' => $idPersona]);
    if ($existsProcessat->fetchColumn()) {
        // Asegurar {6} en categoria también en SKIP
        $getCategoria->execute([':id' => $idPersona]);
        $currentCategoria = $getCategoria->fetchColumn();
        $newCategoria = ensureCategoriaHas6($currentCategoria === false ? null : (string)$currentCategoria);

        $touchCategoria->execute([
            ':categoria' => $newCategoria,
            ':id' => $idPersona,
        ]);

        out("⏭️ Fila {$r} | SKIP+CAT | idPersona={$idPersona} -> categoria={$newCategoria}");
        continue;
    }

    // NUEVO: leer categoría actual y calcular nueva con {6}
    $getCategoria->execute([':id' => $idPersona]);
    $currentCategoria = $getCategoria->fetchColumn();
    $newCategoria = ensureCategoriaHas6($currentCategoria === false ? null : (string)$currentCategoria);


    // lookups y mapeos
    $idNaix  = buscarMunicipi($pdo, $munNaix);
    $idResid = buscarMunicipi($pdo, $munResid);

    $tipusProc   = mapTextOrCode($tipusProcTxt, $TIPUS_PROCEDIMENT);
    $tipusJudici = mapTextOrCode($tipusJudiciTxt, $TIPUS_JUDICI);
    $pena        = mapTextOrCode($penaTxt, $PENA);

    $anyDetingut = toIntOrNull($edatDetencio);
    $anyInicial  = toIntOrNull($anyInici);
    $anyFinal    = toIntOrNull($anySentencia);

    // transacción por fila
    $pdo->beginTransaction();
    try {
        // UPDATE persona
        $updatePersona->execute([
            ':naix'  => $idNaix,
            ':resid' => $idResid,
            ':sexe'  => $sexe,
            ':categoria' => $newCategoria,   // <<< NUEVO
            ':id'    => $idPersona,
        ]);

        // INSERT processat
        $insertProcessat->execute([
            ':idPersona'         => $idPersona,
            ':anyDetingut'       => $anyDetingut,
            ':tipus_procediment' => $tipusProc,
            ':tipus_judici'      => $tipusJudici,
            ':num_causa'         => $numCausa ?: null,
            ':any_inicial'       => $anyInicial,
            ':any_final'         => $anyFinal,
            ':pena'              => $pena,
            ':commutacio'        => $commutacio ?: null,
        ]);

        $pdo->commit();

        // construir una línea (una fila = una línea)
        $parts = [
            "Fila {$r}",
            "OK",
            "idPersona={$idPersona}",
            "nomUsat={$nomUsat}",
            "sexe=" . ($sexe ?? '—'),
            "naix=" . ($idNaix ?? '—'),
            "resid=" . ($idResid ?? '—'),
            "proc=" . ($tipusProc ?? '—'),
            "judici=" . ($tipusJudici ?? '—'),
            "pena=" . ($pena ?? '—'),
        ];
        out(implode(' | ', $parts));
    } catch (Throwable $e) {
        $pdo->rollBack();
        out("❌ Fila {$r} | idPersona={$idPersona} | ERROR=" . $e->getMessage());
    }
}

out("✔️ Proceso finalizado.");
