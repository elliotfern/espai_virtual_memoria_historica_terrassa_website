<?php

declare(strict_types=1);

use App\Config\DatabaseConnection;
use Dompdf\Dompdf;
use Dompdf\Options;

require_once __DIR__ . '/_export_common.php'; // namespace MT\Export

// ——— Headers descarga
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="memoriaterrassa_ficha_' . date('Ymd_His') . '.pdf"');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

$pdo = DatabaseConnection::getConnection();
if (!$pdo) {
    http_response_code(500);
    echo 'DB error';
    exit;
}

// Helpers locales
function e(?string $s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function fmtDate(?string $d): string
{
    if (!$d) return '';
    $d = trim($d);
    if ($d === '' || $d === '0000-00-00') return '';
    // intenta DD/MM/YYYY si viene YYYY-MM-DD
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $d)) {
        $dt = DateTime::createFromFormat('Y-m-d', $d);
        return $dt ? $dt->format('d/m/Y') : $d;
    }
    return $d;
}

/** Resuelve URL absoluta de imagen (requiere isRemoteEnabled en Dompdf) */
function buildImgUrl(PDO $pdo, ?int $id, ?string $slug): string
{
    // Ajusta a tu host/base:
    $scheme = (!empty($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'https');
    $host   = $_SERVER['HTTP_HOST'] ?? 'memoriaterrassa.cat';
    $base   = $scheme . '://' . $host . '/assets_represaliats/img/';

    // Intentar obtener el campo p.img si existe (id numérico del recurso)
    if ($id) {
        $q = $pdo->prepare("SELECT img FROM db_dades_personals WHERE id = :id LIMIT 1");
        $q->bindValue(':id', $id, PDO::PARAM_INT);
        if ($q->execute()) {
            $img = $q->fetchColumn();
            if ($img) return $base . e((string)$img) . '.jpg';
        }
    }

    // Fallback a defecto
    return $base . 'foto_defecte.jpg';
}

// ——— Lee identificadores (ids[]/id | slugs[]/slug)
$ids   = \MT\Export\getArray('ids');
$idOne = \MT\Export\getScalar('id');
$slugs = \MT\Export\getArray('slugs');
$slug  = \MT\Export\getScalar('slug');

if (!$ids && $idOne !== '') $ids = [$idOne];
if (!$slugs && $slug !== '') $slugs = [$slug];

// Construye query con filtreIndividual
[$headers, $sql, $params] = \MT\Export\buildQuery('filtreIndividual', '');
\MT\Export\initSession($pdo);

// Asegura que haya identificador; si no, 0 filas (buildQuery ya hace guard-rail)
$stmt = $pdo->prepare($sql . " LIMIT 1");
foreach ($params as $k => $v) {
    $stmt->bindValue($k, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
}
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    http_response_code(404);
    echo "Persona no trobada.";
    exit;
}

// Traducciones (sexe, categoria…)
\MT\Export\translateRow($row);

// Datos compuestos
$fullName = trim(($row['nom'] ?? '') . ' ' . ($row['cognom1'] ?? '') . ' ' . ($row['cognom2'] ?? ''));
$imgUrl   = buildImgUrl($pdo, isset($row['id']) ? (int)$row['id'] : null, $row['slug'] ?? null);

// ——— PRECALCULA VALORES (sin expresiones dentro del heredoc) ———
$fullNameEsc = e($fullName);

$categoria                  = e((string)($row['categoria'] ?? ''));
$sexe                       = e((string)($row['sexe'] ?? ''));

$ciutat_naixement           = e((string)($row['ciutat_naixement'] ?? ''));
$comarca_naixement          = e((string)($row['comarca_naixement'] ?? ''));
$provincia_naixement        = e((string)($row['provincia_naixement'] ?? ''));
$comunitat_naixement        = e((string)($row['comunitat_naixement'] ?? ''));
$pais_naixement             = e((string)($row['pais_naixement'] ?? ''));

$ciutat_defuncio            = e((string)($row['ciutat_defuncio'] ?? ''));
$comarca_defuncio           = e((string)($row['comarca_defuncio'] ?? ''));
$provincia_defuncio         = e((string)($row['provincia_defuncio'] ?? ''));
$comunitat_defuncio         = e((string)($row['comunitat_defuncio'] ?? ''));
$pais_defuncio              = e((string)($row['pais_defuncio'] ?? ''));

$naixement_line = implode(' · ', array_filter([
    $ciutat_naixement,
    $comarca_naixement,
    $provincia_naixement,
    $comunitat_naixement,
    $pais_naixement
], fn($v) => $v !== ''));

$defuncio_line  = implode(' · ', array_filter([
    $ciutat_defuncio,
    $comarca_defuncio,
    $provincia_defuncio,
    $comunitat_defuncio,
    $pais_defuncio
], fn($v) => $v !== ''));

$data_naixement_fmt = e(fmtDate($row['data_naixement'] ?? null));
$data_defuncio_fmt  = e(fmtDate($row['data_defuncio']  ?? null));

$adreca_str       = e(trim((string)($row['adreca'] ?? '') . ' ' . (string)($row['adreca_num'] ?? '')));
$coords_str       = e(trim((string)($row['lat'] ?? '') . ' , ' . (string)($row['lng'] ?? '')));
$observacions     = e((string)($row['observacions'] ?? ''));
$data_actualitzacio = e((string)($row['data_actualitzacio'] ?? ''));
$id_str           = e((string)($row['id'] ?? ''));

// bloques “Dades”
$estat_civil              = e((string)($row['estat_civil'] ?? ''));
$estudi_cat               = e((string)($row['estudi_cat'] ?? ''));
$ofici_cat                = e((string)($row['ofici_cat'] ?? ''));
$empresa                  = e((string)($row['empresa'] ?? ''));
$filiacio_politica_noms   = e((string)($row['filiacio_politica_noms'] ?? ''));
$filiacio_sindical_noms   = e((string)($row['filiacio_sindical_noms'] ?? ''));
$sector_cat               = e((string)($row['sector_cat'] ?? ''));
$sub_sector_cat           = e((string)($row['sub_sector_cat'] ?? ''));
$carrec_cat               = e((string)($row['carrec_cat'] ?? ''));
$activitat_durant_guerra  = e((string)($row['activitat_durant_guerra'] ?? ''));
$causa_defuncio_ca        = e((string)($row['causa_defuncio_ca'] ?? ''));

// biografia (permite saltos con <br>)
$bio_ca_html = !empty($row['biografiaCa'])
    ? nl2br(e((string)$row['biografiaCa']))
    : '<span class="muted">—</span>';


// HTML + CSS (simple, limpio para A4)
$html = <<<HTML
<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="utf-8">
<style>
  @page { margin: 28mm 18mm; }
  body { font-family: DejaVu Sans, sans-serif; color: #222; font-size: 11pt; }
  h1 { font-size: 18pt; margin: 0 0 4mm; }
  .muted { color:#666; }
  .header { display: flex; gap: 12mm; align-items: flex-start; }
  .photo { width: 42mm; height: 56mm; background:#eee; border:1px solid #ccc; object-fit: cover; }
  .meta { flex:1; }
  .grid { display: grid; grid-template-columns: 38mm 1fr; gap: 2mm 6mm; }
  .label { font-weight: 600; }
  .section { margin-top: 8mm; }
  .section h2 { font-size: 13.5pt; margin: 0 0 3mm; border-bottom: 1px solid #ddd; padding-bottom: 2mm; }
  .kv { margin: 1mm 0; }
  .small { font-size: 9.5pt; }
  .mono { font-family: "DejaVu Sans Mono", monospace; }
</style>
</head>
<body>

  <div class="header">
    <img class="photo" src="{$imgUrl}" alt="fotografia" />
    <div class="meta">
      <h1>{$fullNameEsc}</h1>
      <div class="grid">
        <div class="label">Categoria</div><div>{$categoria}</div>
        <div class="label">Sexe</div><div>{$sexe}</div>
        <div class="label">Naixement</div>
        <div>
          {$naixement_line}
          <div class="small muted">{$data_naixement_fmt}</div>
        </div>
        <div class="label">Defunció</div>
        <div>
          {$defuncio_line}
          <div class="small muted">{$data_defuncio_fmt}</div>
        </div>
        <div class="label">Adreça</div><div>{$adreca_str}</div>
        <div class="label">Coordenades</div><div>{$coords_str}</div>
      </div>
    </div>
  </div>

  <div class="section">
    <h2>Biografia</h2>
    <div class="kv">{$bio_ca_html}</div>
  </div>

  <div class="section">
    <h2>Dades</h2>
    <div class="grid">
      <div class="label">Estat civil</div><div>{$estat_civil}</div>
      <div class="label">Estudis</div><div>{$estudi_cat}</div>
      <div class="label">Ofici</div><div>{$ofici_cat}</div>
      <div class="label">Empresa</div><div>{$empresa}</div>
      <div class="label">Filiació política</div><div>{$filiacio_politica_noms}</div>
      <div class="label">Filiació sindical</div><div>{$filiacio_sindical_noms}</div>
      <div class="label">Sector</div><div>{$sector_cat}</div>
      <div class="label">Sub-sector</div><div>{$sub_sector_cat}</div>
      <div class="label">Càrrec</div><div>{$carrec_cat}</div>
      <div class="label">Activitat (guerra)</div><div>{$activitat_durant_guerra}</div>
      <div class="label">Causa defunció</div><div>{$causa_defuncio_ca}</div>
      <div class="label">Observacions</div><div>{$observacions}</div>
      <div class="label">Actualització</div><div>{$data_actualitzacio}</div>
    </div>
  </div>

  <div class="section small muted">
    <div>Identificador: <span class="mono">{$id_str}</span></div>
  </div>

</body>
</html>
HTML;

// ——— Dompdf y render
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'DejaVu Sans');
$options->set('isHtml5ParserEnabled', true);
$options->set('isFontSubsettingEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html, 'UTF-8');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
echo $dompdf->output();
exit;
