<?php

declare(strict_types=1);

namespace MT\Export;

use PDO;

/** ---------------- Helpers POST/GET seguros ---------------- */
function rq(string $key)
{
    return $_POST[$key] ?? $_GET[$key] ?? null;
}

function getArray(string $key): array
{
    $raw = rq($key);
    if ($raw === null) return [];
    $v = is_array($raw) ? $raw : [$raw];
    return array_values(array_filter(array_map(fn($x) => trim((string)$x), $v), fn($x) => $x !== ''));
}

function getScalar(string $key): string
{
    $raw = rq($key);
    if ($raw === null) return '';
    if (is_array($raw)) return trim((string)($raw[0] ?? ''));
    return trim((string)$raw);
}

/** WL → WHERE + params. modes: in | in_text | eq | like | csvset  */
function buildWhere(array $wl, array &$params): string
{
    $where = [];
    foreach ($wl as $key => $def) {
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

/** Añade el guard de visibilidad pública (completat=2 y visibilitat=2) */
function applyPublicVisibilityGuard(string $where): string
{
    $guard = '(p.completat = 2 AND p.visibilitat = 2)';
    return $where === '' ? "WHERE $guard" : "$where AND $guard";
}

/** WHERE que exige que p.categoria contenga AL MENOS uno de los $ids (ej: [2,10]) */
function whereAnyCategoryInSet(array $ids, array &$params, string $col = 'p.categoria', string $prefix = 'defcat'): string
{
    $ors = [];
    foreach (array_values($ids) as $i => $id) {
        $name = ":{$prefix}{$i}";
        $params[$name] = (string)$id;
        // p.categoria es "{1,6,7}" → quitamos llaves y usamos FIND_IN_SET
        $ors[] = "FIND_IN_SET($name, REPLACE(REPLACE($col,'{',''),'}','')) > 0";
    }
    return '(' . implode(' OR ', $ors) . ')';
}

/** ---------------- Traducciones / formateo ---------------- */
const SEXE_MAP = [
    '1' => 'Home',
    '2' => 'Dona',
];

const CATEGORY_MAP = [
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

function parseSetIds(?string $raw): array
{
    if ($raw === null) return [];
    $raw = trim(str_replace(['{', '}', ' '], '', $raw));
    if ($raw === '') return [];
    return array_values(array_filter(explode(',', $raw), fn($x) => $x !== ''));
}

function categoriesToNames(?string $raw): string
{
    $ids = parseSetIds($raw);
    if (!$ids) return '';
    $names = [];
    foreach ($ids as $id) $names[] = CATEGORY_MAP[$id] ?? $id;
    return implode(' | ', $names);
}

/** Traduce campos en la fila (sexe, categoria/categoria_ids) */
function translateRow(array &$row): void
{
    if (array_key_exists('sexe', $row)) {
        $row['sexe'] = SEXE_MAP[(string)($row['sexe'] ?? '')] ?? (string)($row['sexe'] ?? '');
    }
    if (array_key_exists('categoria', $row)) {
        $row['categoria'] = categoriesToNames($row['categoria']);
    } elseif (array_key_exists('categoria_ids', $row)) {
        $row['categoria_ids'] = categoriesToNames($row['categoria_ids']);
    }
}

/** ---------------- SELECT + headers comunes ---------------- */
function commonHeaders(): array
{
    return [
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
        'filiacio_politica_noms',
        'filiacio_sindical_noms',
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
}

/** SELECT base (sin joins de represalia/exili/cost_huma; esos van en $joins) */
function baseSelect(): string
{
    return "
SELECT
  p.id,
  p.nom,
  p.cognom1,
  p.cognom2,
  p.categoria,
  p.sexe,
  p.data_naixement,
  p.data_defuncio,

  m1.ciutat                    AS ciutat_naixement,
  m1a.comarca                  AS comarca_naixement,
  m1b.provincia                AS provincia_naixement,
  m1c.comunitat_ca             AS comunitat_naixement,
  m1d.estat_ca                 AS pais_naixement,

  m2.ciutat                    AS ciutat_residencia,
  m2a.comarca                  AS comarca_residencia,
  m2b.provincia                AS provincia_residencia,
  m2c.comunitat_ca             AS comunitat_residencia,
  m2d.estat_ca                 AS pais_residencia,

  m3.ciutat                    AS ciutat_defuncio,
  m3a.comarca                  AS comarca_defuncio,
  m3b.provincia                AS provincia_defuncio,
  m3c.comunitat_ca             AS comunitat_defuncio,
  m3d.estat_ca                 AS pais_defuncio,

  p.adreca,
  tespai.tipologia_espai_ca    AS tipologia_espai_ca,
  tespai.observacions          AS observacions_espai,
  causaD.causa_defuncio_ca     AS causa_defuncio_ca,
  ec.estat_cat                 AS estat_civil,
  es.estudi_cat,
  o.ofici_cat,
  em.empresa_ca                AS empresa,

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
  LEFT JOIN aux_dades_municipis           m1  ON p.municipi_naixement = m1.id
  LEFT JOIN aux_dades_municipis_comarca   m1a ON m1.comarca          = m1a.id
  LEFT JOIN aux_dades_municipis_provincia m1b ON m1.provincia        = m1b.id
  LEFT JOIN aux_dades_municipis_comunitat m1c ON m1.comunitat        = m1c.id
  LEFT JOIN aux_dades_municipis_estat     m1d ON m1.estat            = m1d.id

  LEFT JOIN aux_dades_municipis           m2  ON p.municipi_residencia = m2.id
  LEFT JOIN aux_dades_municipis_comarca   m2a ON m2.comarca          = m2a.id
  LEFT JOIN aux_dades_municipis_provincia m2b ON m2.provincia        = m2b.id
  LEFT JOIN aux_dades_municipis_comunitat m2c ON m2.comunitat        = m2c.id
  LEFT JOIN aux_dades_municipis_estat     m2d ON m2.estat            = m2d.id

  LEFT JOIN aux_dades_municipis           m3  ON p.municipi_defuncio = m3.id
  LEFT JOIN aux_dades_municipis_comarca   m3a ON m3.comarca          = m3a.id
  LEFT JOIN aux_dades_municipis_provincia m3b ON m3.provincia        = m3b.id
  LEFT JOIN aux_dades_municipis_comunitat m3c ON m3.comunitat        = m3c.id
  LEFT JOIN aux_dades_municipis_estat     m3d ON m3.estat            = m3d.id

  LEFT JOIN aux_tipologia_espais          tespai ON p.tipologia_lloc_defuncio = tespai.id
  LEFT JOIN aux_causa_defuncio            causaD ON p.causa_defuncio          = causaD.id
  LEFT JOIN aux_estudis                   es     ON p.estudis                 = es.id
  LEFT JOIN aux_oficis                    o      ON p.ofici                   = o.id
  LEFT JOIN aux_estat_civil               ec     ON p.estat_civil             = ec.id
  LEFT JOIN aux_sector_economic           se     ON p.sector                  = se.id
  LEFT JOIN aux_sub_sector_economic       sse    ON p.sub_sector              = sse.id
  LEFT JOIN aux_ofici_carrec              oc     ON p.carrec_empresa          = oc.id
  LEFT JOIN aux_empreses                  em     ON p.empresa                 = em.id
  LEFT JOIN db_biografies                 bio    ON p.id                      = bio.idRepresaliat
";
}

/**
 * Devuelve [headers, sql, params] listo para ejecutar.
 * Aplica filtros de $type + texto $q (si viene).
 */
function buildQuery(string $type, string $q): array
{
    $params = [];
    $joins  = [];
    $wl     = [];

    /* ------- filtreIndividual (1 persona o unas pocas por id/slug) ------- */
    if ($type === 'filtreIndividual') {
        $params = [];
        $joins  = []; // baseSelect ya trae los joins estándar

        // Sólo escuchamos identificadores; no tiene sentido mezclar más filtros aquí.
        $wl = [
            'ids'   => ['p.id',   'in'],  // ids[]=123&ids[]=456
            'id'    => ['p.id',   'eq'],  // id=123
            'slugs' => ['p.slug', 'in_text'], // slugs[]=jaime-artiola...
            'slug'  => ['p.slug', 'in_text'], // slug=jaime-artiola...
        ];

        $where = buildWhere($wl, $params);

        // Guard-rail: si no vino ningún identificador, no devolvemos nada.
        if ($where === '') {
            $where = 'WHERE 1=0';
        }

        // (Opcional) permitir también q sobre nom/cognoms SI hubo algún id/slug:
        if ($q !== '' && $where !== 'WHERE 1=0') {
            $where .= " AND (LOWER(p.nom) LIKE :q OR LOWER(p.cognom1) LIKE :q OR LOWER(p.cognom2) LIKE :q)";
            $params[':q'] = '%' . mb_strtolower($q, 'UTF-8') . '%';
        }

        $headers = commonHeaders();
        $sql = baseSelect() . "\n" . $where . "\nORDER BY p.id ASC";
        return [$headers, $sql, $params];
    }

    if ($type === 'filtreGeneral') {
        $wl = [
            'municipis_naixement' => ['p.municipi_naixement', 'in'],
            'provincies'          => ['m1b.provincia',        'in_text'], // nombre provincia (naixement)
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

        $where = buildWhere($wl, $params);
        if ($q !== '') {
            $where .= ($where ? ' AND ' : 'WHERE ')
                . "(LOWER(p.nom) LIKE :q OR LOWER(p.cognom1) LIKE :q OR LOWER(p.cognom2) LIKE :q)";
            $params[':q'] = '%' . mb_strtolower($q, 'UTF-8') . '%';
        }

        // <-- añade esta línea
        $where = applyPublicVisibilityGuard($where);

        $headers = commonHeaders();
        $sql = baseSelect() . "\n" . implode("\n", $joins) . "\n" . $where . "\nORDER BY p.id ASC";
        return [$headers, $sql, $params];
    }

    if ($type === 'filtreRepresaliats') {
        $params = [];
        $joins  = ["LEFT JOIN represalia r ON r.persona_id = p.id"];
        $wl = [
            'categories'           => ['p.categoria', 'csvset'],
            'processos'            => ['r.proces_id', 'in'],
            'presons'              => ['r.pres_o_id', 'in'],
            'condenes'             => ['r.condena_id', 'in'],
            'municipis_naixement'  => ['p.municipi_naixement', 'in'],
            'sexes'                => ['p.sexe', 'in'],
            'provincies'           => ['m1b.provincia', 'in_text'],
            'anys_naixement'       => ['YEAR(p.data_naixement)', 'in'],
            'anys_defuncio'        => ['YEAR(p.data_defuncio)',  'in'],
        ];

        $where = buildWhere($wl, $params);

        // ↓↓↓ filtro por defecto si NO llega categories[] desde el front
        if (!getArray('categories')) {
            $whereCat = whereAnyCategoryInSet([1, 6, 7, 8, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20], $params, 'p.categoria', 'rep_cat_');
            $where .= ($where ? ' AND ' : 'WHERE ') . $whereCat;
        }

        if ($q !== '') {
            $where .= ($where ? ' AND ' : 'WHERE ')
                . "(LOWER(p.nom) LIKE :q OR LOWER(p.cognom1) LIKE :q OR LOWER(p.cognom2) LIKE :q)";
            $params[':q'] = '%' . mb_strtolower($q, 'UTF-8') . '%';
        }

        // <-- añade esta línea
        $where = applyPublicVisibilityGuard($where);

        $headers = commonHeaders();
        $sql = "
    SELECT DISTINCT base.*
    FROM (
      " . baseSelect() . "
      " . implode("\n", $joins) . "
      $where
    ) AS base
    ORDER BY base.id ASC
  ";
        return [$headers, $sql, $params];
    }

    if ($type === 'filtreExili') {
        $params = [];
        $joins  = [];
        $wl = [
            'municipis_naixement' => ['p.municipi_naixement', 'in'],
            'provincies'          => ['m1b.provincia',        'in_text'], // nombre provincia (naixement)
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
            // específicos de exili.ts
            'anys_exili'           => ['YEAR(p.data_exili)',          'in'],
            'primer_desti_exili'   => ['p.primer_desti_exili',        'in'],
            'deportat'             => ['p.deportat',                  'in'],
            'resistencia_fr'       => ['p.participacio_resistencia',  'in'],
        ];
        // legacy opcional:
        if (getArray('paisos_exili') || getArray('camps') || getArray('unitats_cte')) {
            $wl += [
                'paisos_exili' => ['e.pais_id', 'in'],
                'camps'        => ['e.camp_id', 'in'],
                'unitats_cte'  => ['e.cte_id',  'in'],
            ];
            $joins[] = "LEFT JOIN exili e ON e.persona_id = p.id";
        }

        $where = buildWhere($wl, $params);

        // ↓↓↓ si NO llega categories[], limita a 2 y 10
        if (!getArray('categories')) {
            $whereCat = whereAnyCategoryInSet([2, 10], $params, 'p.categoria', 'ex_cat_');
            $where .= ($where ? ' AND ' : 'WHERE ') . $whereCat;
        }

        if ($q !== '') {
            $where .= ($where ? ' AND ' : 'WHERE ')
                . "(LOWER(p.nom) LIKE :q OR LOWER(p.cognom1) LIKE :q OR LOWER(p.cognom2) LIKE :q)";
            $params[':q'] = '%' . mb_strtolower($q, 'UTF-8') . '%';
        }

        // <-- añade esta línea
        $where = applyPublicVisibilityGuard($where);

        $headers = commonHeaders();
        $sql = "
    SELECT DISTINCT base.*
    FROM (
      " . baseSelect() . "
      " . implode("\n", $joins) . "
      $where
    ) AS base
    ORDER BY base.id ASC
  ";
        return [$headers, $sql, $params];
    }

    if ($type === 'filtreCostHuma') {
        $params = [];
        $joins  = [];
        $wl = [
            // comunes
            'municipis_naixement' => ['p.municipi_naixement', 'in'],
            'provincies'          => ['m1b.provincia',        'in_text'], // nombre provincia (naixement)
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
            // específicos cost-huma.ts (ajusta si tu esquema usa otros)
            'anys_ferits'          => ['YEAR(p.data_ferits)',  'in'], // si existe
            'va_morir'             => ['p.va_morir',           'in'], // 1/2
            // legacy con tabla cost_huma:
            'fronts'               => ['c.front_id',  'in'],
            'situacions'           => ['c.situacio',  'in'],
        ];
        if (getArray('fronts') || getArray('situacions')) {
            $joins[] = "LEFT JOIN cost_huma c ON c.persona_id = p.id";
        }

        $where = buildWhere($wl, $params);

        // ↓↓↓ si NO llega categories[], limita a 3,4,5
        if (!getArray('categories')) {
            $whereCat = whereAnyCategoryInSet([3, 4, 5], $params, 'p.categoria', 'ch_cat_');
            $where .= ($where ? ' AND ' : 'WHERE ') . $whereCat;
        }

        if ($q !== '') {
            $where .= ($where ? ' AND ' : 'WHERE ')
                . "(LOWER(p.nom) LIKE :q OR LOWER(p.cognom1) LIKE :q OR LOWER(p.cognom2) LIKE :q)";
            $params[':q'] = '%' . mb_strtolower($q, 'UTF-8') . '%';
        }

        // <-- añade esta línea
        $where = applyPublicVisibilityGuard($where);

        $headers = commonHeaders();
        $sql = "
    SELECT DISTINCT base.*
    FROM (
      " . baseSelect() . "
      " . implode("\n", $joins) . "
      $where
    ) AS base
    ORDER BY base.id ASC
  ";
        return [$headers, $sql, $params];
    }


    // fallback
    $headers = commonHeaders();
    $where = buildWhere(['categories' => ['p.categoria', 'csvset']], $params);

    // <-- añade esta línea
    $where = applyPublicVisibilityGuard($where);
    $sql = baseSelect() . "\n$where\nORDER BY p.id ASC";
    return [$headers, $sql, $params];
}

/** Ajustes de sesión recomendables (p.ej. GROUP_CONCAT largo) */
function initSession(PDO $pdo): void
{
    @$pdo->exec("SET SESSION group_concat_max_len = 8192");
}
