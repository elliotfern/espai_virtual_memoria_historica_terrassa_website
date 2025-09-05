<?php

declare(strict_types=1);

use App\Config\DatabaseConnection;

$BASE = APP_WEB;

// 👇 Docroot = carpeta raíz servida por el server (ej. /home/USER/memoriaterrassa.cat)
$DOCROOT   = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/');
if ($DOCROOT === '') {
    // Fallback si por lo que sea no viene (CLI, etc.)
    $DOCROOT = realpath(__DIR__ . '/..');
}

$OUT_DIR   = $DOCROOT . '/sitemaps';            // /sitemaps (pública)
$INDEX_XML = $DOCROOT . '/sitemap.xml';         // /sitemap.xml (público)

$CHUNK_SIZE = 5000; // puedes subirlo a 50k si quieres un único fichero

if (!is_dir($OUT_DIR)) mkdir($OUT_DIR, 0775, true);

// ——— helpers ———
function isoDate(?string $ts): string
{
    // sitemap acepta YYYY-MM-DD o ISO8601; usamos fecha simple
    return $ts ? date('Y-m-d', strtotime($ts)) : date('Y-m-d');
}


function writeUrlset(string $filepath, array $items): void
{
    $x = new XMLWriter();
    $x->openURI($filepath);
    $x->startDocument('1.0', 'UTF-8');
    $x->setIndent(true);
    $x->startElement('urlset');
    $x->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

    foreach ($items as $it) {
        $x->startElement('url');
        $x->writeElement('loc', $it['loc']);
        if (!empty($it['lastmod']))     $x->writeElement('lastmod', isoDate($it['lastmod']));
        if (!empty($it['changefreq']))  $x->writeElement('changefreq', $it['changefreq']);
        if (!empty($it['priority']))    $x->writeElement('priority', $it['priority']);
        $x->endElement(); // url
    }
    $x->endElement(); // urlset
    $x->endDocument();
    $x->flush();
}

function writeSitemapIndex(string $filepath, array $sitemaps): void
{
    $x = new XMLWriter();
    $x->openURI($filepath);
    $x->startDocument('1.0', 'UTF-8');
    $x->setIndent(true);
    $x->startElement('sitemapindex');
    $x->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

    foreach ($sitemaps as $sm) {
        $x->startElement('sitemap');
        $x->writeElement('loc', $sm['loc']);
        $x->writeElement('lastmod', isoDate($sm['lastmod'] ?? date('Y-m-d')));
        $x->endElement();
    }
    $x->endElement();
    $x->endDocument();
    $x->flush();
}

// ——— 1) URLs estáticas ———
$static = [
    ['loc' => "$BASE/",                                 'changefreq' => 'weekly',  'priority' => '0.9'],
    ['loc' => "$BASE/base-dades",                       'changefreq' => 'weekly',  'priority' => '0.8'],
    ['loc' => "$BASE/base-dades/general",               'changefreq' => 'weekly',  'priority' => '0.7'],
    ['loc' => "$BASE/base-dades/cost-huma",             'changefreq' => 'weekly',  'priority' => '0.7'],
    ['loc' => "$BASE/base-dades/exiliats-deportats",    'changefreq' => 'weekly',  'priority' => '0.7'],
    ['loc' => "$BASE/base-dades/represaliats",          'changefreq' => 'weekly',  'priority' => '0.7'],
    ['loc' => "$BASE/base-dades/geolocalitzacio",       'changefreq' => 'weekly', 'priority' => '0.6'],
    ['loc' => "$BASE/documents-estudis",                'changefreq' => 'weekly', 'priority' => '0.6'],
    ['loc' => "$BASE/cronologia",                       'changefreq' => 'monthly', 'priority' => '0.6'],
    ['loc' => "$BASE/fonts-documentals",                'changefreq' => 'yearly',  'priority' => '0.4'],
    ['loc' => "$BASE/que-es-espai-virtual",             'changefreq' => 'yearly',  'priority' => '0.4'],
    ['loc' => "$BASE/links",                            'changefreq' => 'yearly',  'priority' => '0.3'],
    ['loc' => "$BASE/contacte",                         'changefreq' => 'yearly',  'priority' => '0.3'],
];

// 1) estático
writeUrlset("$OUT_DIR/sitemap-static.xml", $static);

// ——— 2) URLs dinámicas (fichas) ———
// Opción A: desde BD
$pdo = DatabaseConnection::getConnection();
/** @var PDO $pdo */

// Ajusta los campos de fecha según tu esquema. Intentamos “última modificación”.
// Si no tienes timestamps, quita GREATEST(...) y no rellenes lastmod.
$sql = "SELECT p.slug,
         IFNULL(p.data_actualitzacio, p.data_creacio) AS lastmod
FROM db_dades_personals p
WHERE p.visibilitat = 2 AND p.completat = 2
ORDER BY p.slug ASC;";

$rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// Si prefieres tirarlo de una API propia, sustituye lo anterior por una llamada y parseo del JSON de slugs.

// Trocear en sitemaps de N URLs
$sitemaps = [];
$chunkIndex = 1;
foreach (array_chunk($rows, $CHUNK_SIZE) as $chunk) {
    $items = [];
    foreach ($chunk as $r) {

        $lm = $r['lastmod'] ? strtotime($r['lastmod']) : 0;
        $days = $lm ? floor((time() - $lm) / 86400) : 9999;
        $freq = $days <= 7 ? 'daily' : ($days <= 60 ? 'weekly' : 'monthly');

        $items[] = [
            'loc'       => "$BASE/fitxa/" . $r['slug'],
            'lastmod'   => $r['lastmod'] ?? null,
            'changefreq' => $freq,
            'priority'  => '0.5',
        ];
    }
    $filename = "sitemap-fitxes-$chunkIndex.xml";
    writeUrlset("$OUT_DIR/$filename", $items);

    $sitemaps[] = [
        'loc' => "$BASE/sitemaps/$filename",
        'lastmod' => date('Y-m-d'),
    ];
    $chunkIndex++;
}

// Añadir el estático al índice
array_unshift($sitemaps, [
    'loc' => "$BASE/sitemaps/sitemap-static.xml",
    'lastmod' => date('Y-m-d'),
]);

// ——— 3) Índice ———
writeSitemapIndex($INDEX_XML, $sitemaps);

// (Opcional) GZIP los ficheros (sirve pero no es obligatorio)
// foreach (glob("$OUT_DIR/*.xml") as $file) {
//     $gz = gzopen($file . '.gz', 'w9');
//     gzwrite($gz, file_get_contents($file));
//     gzclose($gz);
// }