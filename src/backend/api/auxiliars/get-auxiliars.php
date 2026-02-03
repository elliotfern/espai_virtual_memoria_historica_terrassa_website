<?php

use App\Config\Database;
use App\Utils\Response;
use App\Utils\MissatgesAPI;

$slug = $routeParams[0];

// Configuración de cabeceras para aceptar JSON y responder JSON
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET");

// Definir el dominio permitido
$allowedOrigin = DOMAIN;

// Llamar a la función para verificar el referer
checkReferer($allowedOrigin);

// Verificar que el método de la solicitud sea GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// GET : llistat de municipis
// URL: https://memoriaterrassa.cat/api/auxiliars/get/municipis
if ($slug === "municipis") {
    $db = new Database();

    $query = "SELECT
        m.id,
        CONCAT(
            COALESCE(NULLIF(m.ciutat_ca, ''), NULLIF(m.ciutat, '')),
            CASE
            WHEN COALESCE(
                    NULLIF(co.comunitat_ca, ''), NULLIF(co.comunitat_es, ''),
                    NULLIF(e.estat_ca, ''),      NULLIF(e.estat_es, '')
                ) IS NOT NULL
            THEN CONCAT(
                    ' (',
                    CONCAT_WS(', ',
                    COALESCE(NULLIF(co.comunitat_ca, ''), NULLIF(co.comunitat_es, '')),
                    COALESCE(NULLIF(e.estat_ca, ''), NULLIF(e.estat_es, ''))
                    ),
                    ')'
                )
            ELSE ''
            END
        ) AS ciutat,

        COALESCE(NULLIF(c.comarca_ca, ''),    NULLIF(c.comarca, ''))        AS comarca,
        COALESCE(NULLIF(p.provincia_ca, ''),  NULLIF(p.provincia_es, ''))   AS provincia,
        COALESCE(NULLIF(co.comunitat_ca, ''), NULLIF(co.comunitat_es, ''))  AS comunitat,
        COALESCE(NULLIF(e.estat_ca, ''),      NULLIF(e.estat_es, ''))       AS estat

        FROM aux_dades_municipis AS m
        LEFT JOIN aux_dades_municipis_comarca   AS c  ON m.comarca   = c.id
        LEFT JOIN aux_dades_municipis_provincia AS p  ON m.provincia = p.id
        LEFT JOIN aux_dades_municipis_comunitat AS co ON m.comunitat = co.id
        LEFT JOIN aux_dades_municipis_estat     AS e  ON m.estat     = e.id

        ORDER BY
        ciutat ASC";

    try {
        $result = $db->getData($query);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;  // o exit; según cómo funcione Response::error
        }

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }


    // GET: consulta llistat de tipus d'usuaris
    // URL: https://memoriaterrassa.cat/api/auxiliars/get/tipusUsuaris
} else if ($slug === "tipusUsuaris") {

    $db = new Database();
    $query = "SELECT u.id, u.tipus
                FROM auth_users_tipus AS u
                ORDER BY u.id";

    try {
        $result = $db->getData($query);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;  // o exit; según cómo funcione Response::error
        }

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }

    // GET : Municipi per ID
    // URL: https://memoriaterrassa.cat/api/auxiliars/get/municipi?id=${id}
} else if ($slug === "municipi") {
    $db = new Database();

    $id = $_GET['id'] ?? null;

    $query = "SELECT id, ciutat, ciutat_ca, comarca, provincia, comunitat, estat
            FROM  aux_dades_municipis
            WHERE id = :id";

    try {

        $params = [':id' => $id];
        $result = $db->getData($query, $params, true);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;  // o exit; según cómo funcione Response::error
        }

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }

    // GET : llistat de partits polítics
    // URL: https://memoriaterrassa.cat/api/auxiliars/get/partitsPolitics
} else if ($slug === "partitsPolitics") {

    $db = new Database();
    $query = "SELECT 
	        p.id, p.partit_politic, p.sigles
            FROM aux_filiacio_politica AS p
            ORDER BY p.partit_politic ASC";

    try {

        $result = $db->getData($query);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;
        }

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }


    // GET : llistat de sindicats
    // URL: https://memoriaterrassa.cat/api/auxiliars/get/sindicats
} else if ($slug === "sindicats") {

    $db = new Database();
    $query = "SELECT 
	        s.id, s.sindicat, s.sigles
            FROM aux_filiacio_sindical AS s
            ORDER BY s.sindicat ASC";


    try {

        $result = $db->getData($query);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;
        }

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }

    // GET : llistat de provincies
    // URL: https://memoriaterrassa.cat/api/auxiliars/get/provincies
} else if ($slug === "provincies") {

    $query = "SELECT
            p.id,
            CONCAT_WS(
                '',
                COALESCE(p.provincia_ca, p.provincia_es),
                CONCAT(' (', NULLIF(MAX(e.estat_ca), ''), ')')
            ) AS provincia
            FROM aux_dades_municipis_provincia AS p
            LEFT JOIN aux_dades_municipis AS co ON p.id = co.provincia
            LEFT JOIN aux_dades_municipis_estat AS e ON co.estat = e.id
            GROUP BY p.id
            ORDER BY COALESCE(p.provincia_ca, p.provincia_es) ASC;";

    $result = getData2($query);
    echo json_encode($result);


    // GET : llistat de comarques
    // URL: https://memoriaterrassa.cat/api/auxiliars/get/comarques
} else if ($slug === "comarques") {

    $query = "SELECT c.id,
                 CONCAT(
                    COALESCE(c.comarca_ca, c.comarca),
                    IF(
                    MAX(COALESCE(co.comunitat_ca, co.comunitat_es)) IS NOT NULL 
                    OR MAX(e.estat_ca) IS NOT NULL,
                    CONCAT(
                        ' (',
                        CONCAT_WS(', ',
                        NULLIF(MAX(COALESCE(co.comunitat_ca, co.comunitat_es)), ''),
                        NULLIF(MAX(e.estat_ca), '')
                        ),
                        ')'
                    ),
                    ''
                    )
                ) AS comarca
        FROM aux_dades_municipis_comarca AS c
        LEFT JOIN aux_dades_municipis AS m ON m.comarca = c.id
        LEFT JOIN aux_dades_municipis_comunitat AS co ON m.comunitat = co.id
        LEFT JOIN aux_dades_municipis_estat AS e ON m.estat = e.id
        GROUP BY c.id
        ORDER BY c.comarca ASC";

    $result = getData2($query);
    echo json_encode($result);

    // GET : llistat de comunitats autonomes
    // URL: https://memoriaterrassa.cat/api/auxiliars/get/comunitats
} else if ($slug === "comunitats") {

    $query = "SELECT
        c.id,
        CONCAT_WS(
            ' ',
            COALESCE(c.comunitat_ca, c.comunitat_es),
            CASE WHEN MAX(e.estat_ca) IS NULL THEN NULL
                ELSE CONCAT('(', MAX(e.estat_ca), ')')
            END
        ) AS comunitat
        FROM aux_dades_municipis_comunitat AS c
        LEFT JOIN aux_dades_municipis AS co ON co.comunitat = c.id
        LEFT JOIN aux_dades_municipis_estat AS e ON e.id = co.estat
        GROUP BY c.id, c.comunitat_ca, c.comunitat_es
        ORDER BY c.comunitat_ca ASC;";

    $result = getData2($query);
    echo json_encode($result);

    // GET : llistat de països
    // URL: https://memoriaterrassa.cat/api/auxiliars/get/estats
} else if ($slug === "estats") {

    $query = "SELECT e.id, COALESCE(e.estat_ca, e.estat_es) AS estat
        FROM aux_dades_municipis_estat AS e
        ORDER BY e.estat_ca ASC";

    $result = getData2($query);
    echo json_encode($result);

    // GET : llistat d'estudis (analfabet, alfabetitzat...)
    // URL: https://memoriaterrassa.cat/api/auxiliars/get/estudis
} else if ($slug === "estudis") {

    $db = new Database();

    $query = "SELECT e.id, e.estudi_ca AS estudi_cat
        FROM aux_estudis AS e
        ORDER BY e.estudi_ca ASC";

    try {

        $result = $db->getData($query);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;
        }

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }

    // GET : Llistat oficis
    // URL: /api/auxiliars/get/oficis
} else if ($slug === "oficis") {

    $db = new Database();

    $query = "SELECT o.id, o.ofici_ca AS ofici_cat
              FROM aux_oficis AS o
              ORDER BY o.ofici_ca ASC";

    try {

        $result = $db->getData($query);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;
        }

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }


    // GET : Llistat empreses
    // URL: /api/auxiliars/get/empreses
} else if ($slug === "empreses") {
    $query = "SELECT id, empresa_ca
              FROM aux_empreses
              ORDER BY empresa_ca ASC";

    $result = getData2($query);
    echo json_encode($result);

    // GET : Llistat sectors
    // URL: /api/auxiliars/get/sectors_economics
} elseif ($slug === "sectors_economics") {
    $query = "SELECT se.id, se.sector_ca AS sector_cat
              FROM aux_sector_economic AS se
              ORDER BY se.sector_ca ASC";

    $result = getData2($query);
    echo json_encode($result);

    // GET : Llistat sub-sectors economics
    // URL: /api/auxiliars/get/sub_sectors_economics
} elseif ($slug === "sub_sectors_economics") {
    $query = "SELECT sse.id, sse.sub_sector_ca AS sub_sector_cat, s.sector_ca AS sector_cat
              FROM aux_sub_sector_economic AS sse
              INNER JOIN aux_sector_economic AS s ON sse.idSector = s.id
              ORDER BY sse.sub_sector_ca ASC";

    $result = getData2($query);
    echo json_encode($result);
} elseif ($slug === "sub_sector_economic") {
    // GET : Sub-sectors economic per ID 
    // URL: /api/auxiliars/get/sub_sector_economic?id=44
    $db = new Database();

    $id = $_GET['id'] ?? null;

    $query = "SELECT id, sub_sector_cat, sub_sector_es, sub_sector_en, sub_sector_it, sub_sector_fr, sub_sector_pt, idSector
              FROM aux_sub_sector_economic 
              WHERE id = :id";

    try {

        $params = [':id' => $id];
        $result = $db->getData($query, $params, true);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;  // o exit; según cómo funcione Response::error
        }

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }

    // GET : Llistat càrrecs empresa
    // URL: /api/auxiliars/get/carrecs_empresa
} elseif ($slug === "carrecs_empresa") {
    $query = "SELECT ce.id, ce.carrec_ca AS carrec_cat
              FROM aux_ofici_carrec AS ce
              ORDER BY ce.carrec_ca ASC";

    $result = getData2($query);
    echo json_encode($result);

    // GET : Llistat estat civil
    // URL: /api/auxiliars/get/?type=estats_civils
} elseif ($slug === "estats_civils") {

    $db = new Database();

    $query = "SELECT ec.id, ec.estat_ca AS estat_cat
              FROM aux_estat_civil AS ec
              ORDER BY ec.estat_ca ASC";

    try {
        // Pasamos los valores como array en el mismo orden de los placeholders
        $result = $db->getData($query);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;
        }

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }

    // GET : Llistat espais
    // URL: /api/auxiliars/get/espais
} elseif ($slug === "espais") {
    $query = "SELECT esp.id, esp.espai_cat
              FROM aux_espai AS esp
              ORDER BY esp.espai_cat ASC";

    $result = getData2($query);
    echo json_encode($result);

    // GET : Espai ID
    // URL: /api/auxiliars/get/espai?id=${id}
} elseif ($slug === "espai") {
    $db = new Database();
    $id = $_GET['id'] ?? null;

    $query = "SELECT id, espai_cat, municipi, espai_es, espai_en, espai_fr, espai_it, espai_pt, descripcio_espai
              FROM aux_espai
              WHERE id = :id";

    try {

        $params = [':id' => $id];
        $result = $db->getData($query, $params, true);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;  // o exit; según cómo funcione Response::error
        }

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }

    // GET : Tipologia espais
    // URL: /api/auxiliars/get/tipologia_espais
} elseif ($slug === "tipologia_espais") {
    $query = "SELECT tipologia.id, tipologia.tipologia_espai_ca
              FROM aux_tipologia_espais AS tipologia
              ORDER BY tipologia.tipologia_espai_ca ASC";

    $result = getData2($query);
    echo json_encode($result);

    // GET : Tipologia espais
    // URL: /api/auxiliars/get/tipologia_espaisExili
} elseif ($slug === "tipologia_espaisExili") {
    $query = "SELECT tipologia.id, tipologia.tipologia_espai_ca
              FROM aux_tipologia_espais AS tipologia
              WHERE cat = 1
              ORDER BY tipologia.tipologia_espai_ca ASC";

    $result = getData2($query);
    echo json_encode($result);

    // GET : Causa defuncio - tots els casos (per formulari fitxa dades personals)
    // URL: /api/auxiliars/get/causa_defuncio
} elseif ($slug === "causa_defuncio") {

    $db = new Database();
    $query = "SELECT c.id, c.causa_defuncio_ca
              FROM aux_causa_defuncio AS c
              ORDER BY c.causa_defuncio_ca ASC";
    try {

        $result = $db->getData($query);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;
        }

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }

    // GET : Causa defuncio - tots els casos (per formulari fitxa dades personals)
    // URL: /api/auxiliars/get/causa_defuncio_detalls
} elseif ($slug === "causa_defuncio_detalls") {

    $db = new Database();
    $query = "SELECT id, defuncio_detalls_ca
              FROM aux_causa_defuncio_detalls
              ORDER BY defuncio_detalls_ca ASC";
    try {

        $result = $db->getData($query);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;
        }

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }


    // GET : Causa defuncio - per formulari morts civils
    // tipus 1 > morts militars / tipus 2 > morts civils / tipus 3 > tots
    // URL: /api/auxiliars/get/causa_defuncio_repressio?tipus=1
} elseif ($slug === "causa_defuncio_repressio") {

    $tipus = $_GET['tipus'] ?? 1;
    $query = "SELECT c.id, c.causa_defuncio_ca
              FROM aux_causa_defuncio AS c
             WHERE c.cat = :cat
             OR c.cat = 3
             ORDER BY c.causa_defuncio_ca ASC";

    $result = getData2($query, ['cat' => $tipus], false);
    echo json_encode($result);

    // GET : Autors fitxes
    // URL: /api/auxiliars/get/autors_fitxa
} elseif ($slug === "autors_fitxa") {

    $db = new Database();
    $query = "SELECT u.id, u.nom
              FROM auth_users AS u
              ORDER BY u.nom ASC";

    try {
        $result = $db->getData($query);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;  // o exit; según cómo funcione Response::error
        }

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }

    // GET : Tipus Procediments judicials
    // URL: /api/auxiliars/get/procediments
} elseif ($slug === "procediments") {
    $query = "SELECT pj.id, pj.procediment_ca
              FROM aux_procediment_judicial AS pj
              ORDER BY pj.procediment_ca ASC";

    $result = getData2($query);
    echo json_encode($result);

    // GET : tipus judicis
    // URL: /api/auxiliars/get/tipusJudici
} elseif ($slug === "tipusJudici") {
    $query = "SELECT id, tipusJudici_ca, tipusJudici_es, tipusJudici_en, tipusJudici_fr, tipusJudici_it, tipusJudici_pt
              FROM aux_tipus_judici
              ORDER BY tipusJudici_ca ASC";

    $result = getData2($query);
    echo json_encode($result);

    // GET : Llistat jutjats
    // URL: /api/auxiliars/get/jutjats
} elseif ($slug === "jutjats") {

    $db = new Database();
    $query = "SELECT j.id, j.jutjat_ca, jutjat_es,
                jutjat_en,
                jutjat_fr,
                jutjat_it,
                jutjat_pt
              FROM aux_jutjats AS j
              ORDER BY j.jutjat_ca ASC";

    try {
        $result = $db->getData($query);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;  // o exit; según cómo funcione Response::error
        }

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }

    // GET : Llistat tipus acusacions
    // URL: /api/auxiliars/get/acusacions
} elseif ($slug === "acusacions") {
    $query = "SELECT sa.id, sa.acusacio_ca
              FROM aux_acusacions AS sa
              ORDER BY sa.acusacio_ca ASC";

    $result = getData2($query);
    echo json_encode($result);

    // GET : Llistat sentencies
    // URL: /api/auxiliars/get/sentencies
} elseif ($slug === "sentencies") {
    $db = new Database();
    $query = "SELECT id, sentencia_ca, sentencia_es, sentencia_en, sentencia_fr, sentencia_it, sentencia_pt
              FROM aux_sentencies
              ORDER BY sentencia_ca ASC";

    try {
        $result = $db->getData($query);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;  // o exit; según cómo funcione Response::error
        }

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }

    // GET : Llistat penes
    // URL: /api/auxiliars/get/penes
} elseif ($slug === "penes") {

    $db = new Database();
    $query = "SELECT id, pena_ca, pena_es, pena_en, pena_it, pena_fr, pena_pt
              FROM aux_penes
              ORDER BY pena_ca ASC";

    try {
        $result = $db->getData($query);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;  // o exit; según cómo funcione Response::error
        }

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }


    // GET : Condició civil/militar
    // URL: /api/auxiliars/get/condicio_civil_militar
} elseif ($slug === "condicio_civil_militar") {
    $query = "SELECT c.id, c.condicio_ca
              FROM aux_condicio AS c
              ORDER BY c.condicio_ca ASC";

    $result = getData2($query);
    echo json_encode($result);

    // GET : Llistat bàndols guerra
    // URL: /api/auxiliars/get/bandols_guerra
} elseif ($slug === "bandols_guerra") {
    $query = "SELECT b.id, b.bandol_ca
              FROM aux_bandol AS b
              ORDER BY b.bandol_ca ASC";

    $result = getData2($query);
    echo json_encode($result);

    // GET : Llistat cossos militats
    // URL: /api/auxiliars/get/cossos_militars
} elseif ($slug === "cossos_militars") {
    $query = "SELECT c.id, c.cos_militar_ca
              FROM aux_cossos_militars AS c
              ORDER BY c.cos_militar_ca ASC";

    $result = getData2($query);
    echo json_encode($result);

    // GET : Situacions deportats
    // URL: /api/auxiliars/get/situacions_deportats
} elseif ($slug === "situacions_deportats") {
    $query = "SELECT s.id, s.situacio_ca
              FROM aux_situacions_deportats AS s
              ORDER BY s.situacio_ca ASC";

    $result = getData2($query);
    echo json_encode($result);

    // GET : Tipus presons deportats
    // URL: /api/auxiliars/get/tipus_presons
} elseif ($slug === "tipus_presons") {
    $query = "SELECT t.id, t.tipus_preso_ca
              FROM aux_tipus_presons AS t
              ORDER BY t.tipus_preso_ca ASC";

    $result = getData2($query);
    echo json_encode($result);

    // GET : Relacions parentiu
    // URL: /api/auxiliars/get/relacions_parentiu
} elseif ($slug === "relacions_parentiu") {
    $query = "SELECT r.id, r.relacio_parentiu_ca AS relacio_parentiu
              FROM aux_familiars_relacio AS r
              ORDER BY r.relacio_parentiu_ca ASC";

    $result = getData2($query);
    echo json_encode($result);

    // GET : Llistat completo represaliats
    // URL: /api/auxiliars/get/llistat_complert_represaliats
} else if ($slug === "llistat_complert_represaliats") {
    $query = "SELECT dp.id, 
       dp.nom, 
       dp.cognom1, 
       dp.cognom2, 
       CONCAT(dp.cognom1, ' ', dp.cognom2, ', ', dp.nom) AS nom_complert
        FROM db_dades_personals AS dp
        ORDER BY dp.cognom1 ASC";

    $result = getData2($query);
    echo json_encode($result);


    // GET : Llistat llocs bombardeig
    // URL: /api/auxiliars/get/llocs_bombardeig
} else if ($slug === 'llocs_bombardeig') {
    $query = "SELECT l.id, l.lloc_bombardeig_ca
              FROM aux_llocs_bombardeig AS l
              ORDER BY l.lloc_bombardeig_ca ASC";

    $result = getData2($query);
    echo json_encode($result);

    // GET : Llistat llibres bibliografia
    // URL: /api/auxiliars/get/llistat_llibres_bibliografia
} elseif ($slug === 'llistat_llibres_bibliografia') {
    $query = "SELECT 
        l.id,
        CONCAT(l.autor, ', ', SUBSTRING(l.llibre, 1, 40), '...', ', ', l.any) AS llibre
        FROM aux_bibliografia_llibre_detalls AS l
        ORDER BY l.autor ASC;";

    $result = getData2($query);
    echo json_encode($result);

    // GET : Llistat arxius bibliografia
    // URL: /api/auxiliars/get/llistat_arxivistica
} elseif ($slug === 'llistat_arxivistica') {
    $query = "SELECT l.id, 
                     CONCAT(l.codi, ', ', l.arxiu) AS arxiu
              FROM aux_bibliografia_arxius_codis AS l
              ORDER BY l.codi ASC";

    $result = getData2($query);
    echo json_encode($result);

    // GET : Llistat arxius bibliografia
    // URL: /api/auxiliars/get/modalitatPreso
} elseif ($slug === 'modalitatPreso') {

    $db = new Database();

    $query = "SELECT id, modalitat_ca, 
                modalitat_es,
                modalitat_en,
                modalitat_fr,
                modalitat_it,
                modalitat_pt
              FROM aux_modalitat_preso
              ORDER BY modalitat_ca ASC";

    try {
        $result = $db->getData($query);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;
        }

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }

    // GET : llistat d'avatars usuaris
    // URL: https://memoriaterrassa.cat/api/auxiliars/get/avatarsUsuaris
} else if ($slug === "avatarsUsuaris") {

    $query = "SELECT i.id, i.nomImatge
            FROM aux_imatges AS i
            WHERE i.tipus = 2
            ORDER BY i.nomImatge ASC";

    $result = getData2($query);
    echo json_encode($result);

    // GET : llistat de categories de repressió (col·lectius)
    // URL: https://memoriaterrassa.cat/api/auxiliars/get/categoriesRepressio?lang=ca
} else if ($slug === "categoriesRepressio") {
    // Lista de idiomas permitidos y sus columnas
    $db = new Database();
    $allowedLanguages = [
        'ca' => 'categoria_cat',
        'es' => 'categoria_es',
        'en' => 'categoria_en',
        'fr' => 'categoria_fr',
        'it' => 'categoria_it',
        'pt' => 'categoria_pt',
    ];

    // Obtener idioma de la query string (default: 'ca')
    $lang = $_GET['lang'] ?? 'ca';

    // Validar idioma
    if (!array_key_exists($lang, $allowedLanguages)) {
        http_response_code(400);
        echo json_encode(['error' => 'Idioma no soportado']);
        exit;
    }

    $column = $allowedLanguages[$lang];
    $query = "SELECT c.id, c.{$column} AS name, g.grup_ca
    FROM aux_categoria AS c
    INNER JOIN aux_categoria_grup as g ON c.grup = g.id
    ORDER BY c.id";

    try {
        $result = $db->getData($query);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;
        }

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }

    // GET : categoria de repressió per ID
    // URL: https://memoriaterrassa.cat/api/auxiliars/get/categoriaRepressioId?id=3
} else if ($slug === "categoriaRepressioId") {
    $id = $_GET['id'];
    $db = new Database();

    $query = "SELECT c.id, c.categoria_cat, c.categoria_es, c.categoria_en, c.categoria_fr, c.categoria_it, c.categoria_pt, c.grup
            FROM aux_categoria AS c
            WHERE c.id = :id";

    $params = [
        ':id' => $id,
    ];

    try {
        $result = $db->getData($query, $params, true);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;
        }

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }

    // GET : llistat grups de repressió
    // URL: https://memoriaterrassa.cat/api/auxiliars/get/categoriesGrupRepressio
} else if ($slug === "categoriesGrupRepressio") {
    $db = new Database();

    $query = "SELECT id, grup_ca, grup_es, grup_en, grup_fr, grup_it, grup_pt
            FROM aux_categoria_grup
            ORDER BY grup_ca ASC";

    try {
        $result = $db->getData($query);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;
        }

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }

    // GET : Empresa per ID 
    // URL: /api/auxiliars/get/empresa?id=44
} elseif ($slug === "empresa") {

    $db = new Database();

    $id = $_GET['id'] ?? null;

    $query = "SELECT id, empresa_ca, empresa_es, empresa_fr, empresa_en, empresa_it, empresa_pt
              FROM aux_empreses 
              WHERE id = :id";

    try {

        $params = [':id' => $id];
        $result = $db->getData($query, $params, true);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;  // o exit; según cómo funcione Response::error
        }

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }

    // GET : Tipus empleat - no cal fer petició a la base de dades
    // URL: /api/auxiliars/get/tipusEmpleat
} elseif ($slug === "tipusEmpleat") {

    $result = [
        ["id" => 1, "tipus_ca" => "Empleat sector públic (funcionari públic)"],
        ["id" => 2, "tipus_ca" => "Empleat sector públic (professor educació pública)"],
        ["id" => 3, "tipus_ca" => "Empleat sector privat"]
    ];

    Response::success(
        MissatgesAPI::success('get'),
        $result,
        200
    );

    // GET : Empresa per ID 
    // URL: /api/auxiliars/get/empresa?id=44
} elseif ($slug === "empresa") {

    $db = new Database();

    $id = $_GET['id'] ?? null;

    $query = "SELECT id, empresa_ca, empresa_es, empresa_fr, empresa_en, empresa_it, empresa_pt
              FROM aux_empreses 
              WHERE id = :id";

    try {

        $params = [':id' => $id];
        $result = $db->getData($query, $params, true);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;  // o exit; según cómo funcione Response::error
        }

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }

    // GET : Motius detenció
    // URL: /api/auxiliars/get/motiusEmpresonament
} elseif ($slug === "motiusEmpresonament") {

    $db = new Database();
    $query = "SELECT id, motiuEmpresonament_ca, motiuEmpresonament_es, motiuEmpresonament_en, motiuEmpresonament_fr, motiuEmpresonament_it, motiuEmpresonament_pt
              FROM aux_motius_empresonament
              ORDER BY motiuEmpresonament_ca";

    try {
        $result = $db->getData($query);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;  // o exit; según cómo funcione Response::error
        }

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }

    // GET :TOP - no cal fer petició a la base de dades
    // URL: /api/auxiliars/get/top
} elseif ($slug === "top") {

    $result = [
        ["id" => 1, "ordena_top" => "Sí"],
        ["id" => 2, "ordena_top" => "No"],
        ["id" => 3, "ordena_top" => "SD"]
    ];

    Response::success(
        MissatgesAPI::success('get'),
        $result,
        200
    );

    // GET : Grups de repressió
    // URL: /api/auxiliars/get/sistemaRepressiu
} elseif ($slug === "sistemaRepressiu") {

    $db = new Database();
    $query = "SELECT id, CONCAT_WS(' - ', carrec, nom_institucio) AS carrec
              FROM aux_sistema_repressiu
              ORDER BY carrec";

    try {
        $result = $db->getData($query);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;  // o exit; según cómo funcione Response::error
        }

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }

    // GET : Grups de repressió - categoria
    // URL: /api/auxiliars/get/sistemaRepressiuGrup
} elseif ($slug === "sistemaRepressiuGrup") {

    $db = new Database();
    $query = "SELECT id, grup
              FROM aux_sistema_repressiu_grup
              ORDER BY grup";

    try {
        $result = $db->getData($query);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;  // o exit; según cómo funcione Response::error
        }

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }

    // GET : Grups de repressió ID
    // URL: /api/auxiliars/get/sistemaRepressiuID
} elseif ($slug === "sistemaRepressiuID") {
    $db = new Database();

    $id = $_GET['id'] ?? null;

    $query = "SELECT id, carrec, nom_institucio, grup_institucio
              FROM aux_sistema_repressiu
              WHERE id = :id";

    try {
        $params = [':id' => $id];
        $result = $db->getData($query, $params, true);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;  // o exit; según cómo funcione Response::error
        }

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }

    // GET : Llistat de presons
    // URL: /api/auxiliars/get/llistatPresons
} elseif ($slug === "llistatPresons") {

    $db = new Database();
    $query = "SELECT CONCAT_WS(' - ', p.nom_preso, m.ciutat) AS preso, p.id
              FROM aux_presons AS p
              LEFT JOIN aux_dades_municipis AS m ON p.municipi_preso = m.id
              ORDER BY p.nom_preso ASC";

    try {
        $result = $db->getData($query);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;  // o exit; según cómo funcione Response::error
        }

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }

    // GET : Presons ID
    // URL: /api/auxiliars/get/presonsID
} elseif ($slug === "presonsID") {
    $db = new Database();

    $id = $_GET['id'] ?? null;

    $query = "SELECT nom_preso, id, municipi_preso
              FROM aux_presons
              WHERE id = :id";

    try {
        $params = [':id' => $id];
        $result = $db->getData($query, $params, true);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;  // o exit; según cómo funcione Response::error
        }

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }


    // GET : Llistat de tipus de procediments judicials
    // URL: /api/auxiliars/get/procedimentsJudicials
} elseif ($slug === "procedimentsJudicials") {

    $db = new Database();
    $query = "SELECT id, procediment_ca
              FROM aux_procediment_judicial
              ORDER BY procediment_ca ASC";

    try {
        $result = $db->getData($query);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;  // o exit; según cómo funcione Response::error
        }

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }

    // GET : Llistat de tipus de judicis
    // URL: /api/auxiliars/get/tipusJudicis
} elseif ($slug === "tipusJudicis") {

    $db = new Database();
    $query = "SELECT 
            id,
            tipusJudici_ca,
            tipusJudici_es,
            tipusJudici_en,
            tipusJudici_fr,
            tipusJudici_it,
            tipusJudici_pt
        FROM aux_tipus_judici
        ORDER BY tipusJudici_ca";

    try {
        $result = $db->getData($query);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;  // o exit; según cómo funcione Response::error
        }

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }


    // GET : Llistat reaparegut
    // URL: /api/auxiliars/get/reaparegut
} elseif ($slug === "reaparegut") {

    $options = [
        ['id' => 1, 'value' => 'Sí'],
        ['id' => 2, 'value' => 'No']
    ];

    // Responder con éxito y devolver las opciones
    Response::success(
        MissatgesAPI::success('get'),
        $options,
        200
    );

    // GET: consulta llistat de tipus de via
    // URL: https://memoriaterrassa.cat/api/auxiliars/get/tipusVia
} else if ($slug === "tipusVia") {

    $db = new Database();
    $query = "SELECT id, tipus_ca
                FROM aux_tipus_via
                ORDER BY id";

    try {
        $result = $db->getData($query);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;  // o exit; según cómo funcione Response::error
        }

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }

    // GET : Camps de preso frança
    // URL: /api/auxiliars/get/deportacioPreso
} elseif ($slug === "deportacioPreso") {

    $db = new Database();
    $query = "SELECT p.id, CONCAT(p.nom, ' - ', m.ciutat) AS nom_camp
              FROM aux_deportacio_preso AS p
              LEFT JOIN aux_dades_municipis as m ON p.municipi = m.id
              ORDER BY p.nom ASC";
    try {

        $result = $db->getData($query);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;
        }

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }

    // GET : Camps de concentracio/extermini
    // URL: /api/auxiliars/get/campsConcentracio
} elseif ($slug === "campsConcentracio") {

    $db = new Database();
    $query = "SELECT p.id, CONCAT(p.nom_ca, ' - ', m.ciutat_ca) AS nom_camp
              FROM aux_camps_concentracio AS p
              LEFT JOIN aux_dades_municipis as m ON p.municipi = m.id
              ORDER BY nom_ca ASC";
    try {

        $result = $db->getData($query);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;
        }

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }

    // GET : Grups usuaris
    // URL: /api/auxiliars/get/grupsUsuaris
} elseif ($slug === "grupsUsuaris") {

    try {
        // Datos fijos
        $result = [
            ['id' => 1, 'nom' => 'Equip web'],
            ['id' => 2, 'nom' => 'Equip recerca històrica'],
            ['id' => 3, 'nom' => 'Equip col·laboradors/es introducció dades'],
            ['id' => 4, 'nom' => 'Sense assignar'],
        ];

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (Throwable $e) {
        // Por coherencia con tu patrón
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }

    // GET : Digitalitzat sumari consell de guerra
    // URL: /api/auxiliars/get/digitalitzat
} elseif ($slug === "digitalitzat") {

    try {
        // Datos fijos
        $result = [
            ['id' => 1, 'nom' => 'Si'],
            ['id' => 2, 'nom' => 'No'],
        ];

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (Throwable $e) {
        // Por coherencia con tu patrón
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }
    // GET : Premsa - Mitjans (llistat en català)
    // URL: /api/auxiliars/get/premsaMitjans
} elseif ($slug === "premsaMitjans") {

    $db = new Database();

    $query = "SELECT 
                m.id,
                m.slug,
                m.tipus,
                m.web_url,
                i.nom,
                i.descripcio
            FROM aux_premsa_mitjans AS m
            LEFT JOIN aux_premsa_mitjans_i18n AS i
                ON i.mitja_id = m.id AND i.lang = 'ca'
            GROUP BY m.id
            ORDER BY i.nom ASC";

    try {

        $result = $db->getData($query);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;
        }

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }

    // GET : Premsa - Mitjà (detall per slug, amb i18n)
    // URL: /api/auxiliars/get/premsaMitja?slug=xxx
} elseif ($slug === "premsaMitja") {

    if (empty($_GET['slug'])) {
        Response::error(
            MissatgesAPI::error('missing_params'),
            ['slug'],
            400
        );
        return;
    }

    $slugMitja = $_GET['slug'];
    $db = new Database();

    $query = "SELECT
                m.id,
                m.slug,
                m.tipus,
                m.web_url,
                m.created_at,
                m.updated_at,
                i.lang,
                i.nom,
                i.descripcio
              FROM aux_premsa_mitjans AS m
              LEFT JOIN aux_premsa_mitjans_i18n AS i
                ON i.mitja_id = m.id
              WHERE m.slug = :slug";

    try {

        $result = $db->getData($query, [
            ':slug' => $slugMitja
        ]);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;
        }

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }

    // GET : Premsa - Aparicions (llistat, amb i18n)
    // URL: /api/auxiliars/get/premsaAparicions
} elseif ($slug === "premsaAparicions") {

    $db = new Database();

    $query = "SELECT
                a.id,
                a.data_aparicio,
                a.tipus_aparicio,
                a.mitja_id,
                a.url_noticia,
                a.image_id,
                a.destacat,
                a.estat,
                a.created_at,
                a.updated_at,
                i.lang,
                i.titol,
                i.resum,
                i.notes,
                i.pdf_url,
                m.nom
            FROM db_premsa_aparicions AS a
            LEFT JOIN db_premsa_aparicions_i18n AS i ON i.aparicio_id = a.id
            LEFT JOIN aux_premsa_mitjans_i18n AS m ON a.mitja_id = m.mitja_id
            WHERE m.lang = 'ca'
            GROUP BY a.id
            ORDER BY a.data_aparicio DESC, a.id DESC";

    try {

        $result = $db->getData($query);

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }

    // GET : Premsa - Aparició (detall per id, amb i18n)
    // URL: /api/auxiliars/get/premsaAparicio?id=123
} else if ($slug === "premsaAparicio") {

    if (empty($_GET['id'])) {
        Response::error(
            MissatgesAPI::error('missing_params'),
            ['id'],
            400
        );
        return;
    }

    $idAparicio = (int) $_GET['id'];

    if ($idAparicio <= 0) {
        Response::error(
            MissatgesAPI::error('missing_params'),
            ['id'],
            400
        );
        return;
    }

    $db = new Database();

    $query = "SELECT
                a.id,
                a.data_aparicio,
                a.tipus_aparicio,
                a.mitja_id,
                a.url_noticia,
                a.image_id,
                a.destacat,
                a.estat,
                a.created_at,
                a.updated_at,
                i.lang,
                i.titol,
                i_ca.titol AS titol_ca,
                i.resum,
                i.notes,
                i.pdf_url
              FROM db_premsa_aparicions AS a
              LEFT JOIN db_premsa_aparicions_i18n AS i ON i.aparicio_id = a.id
              LEFT JOIN db_premsa_aparicions_i18n i_ca ON i_ca.aparicio_id = a.id AND i_ca.lang = 'ca'
              WHERE a.id = :id";

    try {

        $result = $db->getData($query, [
            ':id' => $idAparicio
        ]);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;
        }

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }

    // GET : Premsa - TIPUS Aparició
    // URL: /api/auxiliars/get/premsaTipusAparicio
} else if ($slug === "premsaTipusAparicio") {

    $result = [
        [
            'id' => 'presentacio',
            'nom' => 'Presentació'
        ],
        [
            'id' => 'roda_premsa',
            'nom' => 'Roda de premsa'
        ],
        [
            'id' => 'activitat',
            'nom' => 'Activitat'
        ],
        [
            'id' => 'entrevista',
            'nom' => 'Entrevista'
        ],
        [
            'id' => 'reportatge',
            'nom' => 'Reportatge'
        ],
        [
            'id' => 'nota_premsa',
            'nom' => 'Nota de premsa'
        ],
        [
            'id' => 'publicacio_xarxes',
            'nom' => 'Publicació xarxes'
        ],
        [
            'id' => 'altres',
            'nom' => 'Altres'
        ]
    ];

    Response::success(
        MissatgesAPI::success('get'),
        $result,
        200
    );

    // GET : Premsa - Imatges (select, tipus = 4)
    // URL: /api/auxiliars/get/premsaImatges
} else if ($slug === "premsaImatges") {

    $db = new Database();

    $query = "SELECT
                id,
                nomImatge
              FROM aux_imatges
              WHERE tipus = 4
              ORDER BY nomImatge ASC";

    try {

        $result = $db->getData($query);

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }

    // GET : Aux - Imatges (llistat)
    // URL: /api/auxiliars/get/imatges
} else if ($slug === "imatges") {

    $db = new Database();

    $query = "SELECT
                i.id,
                i.idPersona,
                i.nomArxiu,
                i.nomImatge,
                i.tipus,
                i.mime,
                i.dateCreated,
                i.dateModified,
                p.nom,
                p.cognom1,
                p.cognom2,
                p.slug
              FROM aux_imatges AS i
              LEFT JOIN db_dades_personals AS p ON i.idPersona = p.id
              ORDER BY id DESC";

    try {

        $result = $db->getData($query);

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }

    // GET : Aux - Imatge (detall per id)
    // URL: /api/auxiliars/get/imatge?id=123
} elseif ($slug === "imatge") {

    if (empty($_GET['id'])) {
        Response::error(
            MissatgesAPI::error('missing_params'),
            ['id'],
            400
        );
        return;
    }

    $idImatge = (int) $_GET['id'];

    if ($idImatge <= 0) {
        Response::error(
            MissatgesAPI::error('missing_params'),
            ['id'],
            400
        );
        return;
    }

    $db = new Database();

    $query = "SELECT
                i.id,
                i.idPersona,
                i.nomArxiu,
                i.nomImatge,
                i.tipus,
                i.mime,
                i.dateCreated,
                i.dateModified
              FROM aux_imatges AS i
              WHERE i.id = :id";

    try {

        $result = $db->getData($query, [
            ':id' => $idImatge
        ]);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;
        }

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }

    // GET : Aux - llistat persones
    // URL: /api/auxiliars/get/persones
} elseif ($slug === "persones") {

    $db = new Database();

    $query = "SELECT
                p.id,
                TRIM(
                CONCAT(
                    COALESCE(p.nom, ''),
                    ' ',
                    COALESCE(p.cognom1, ''),
                    ' ',
                    COALESCE(p.cognom2, '')
                )
                ) AS nom_complet
              FROM db_dades_personals AS p
              ORDER BY p.cognom1";

    try {

        $result = $db->getData($query);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;
        }

        Response::success(
            MissatgesAPI::success('get'),
            $result,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }
} else {
    // Si el parámetro 'type' no coincide con ninguno de los casos anteriores, mostramos un error
    echo json_encode(["error" => "Tipo no válido"]);
}
