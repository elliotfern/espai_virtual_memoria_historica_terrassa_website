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

    $query = "SELECT m.id, COALESCE(m.ciutat_ca, m.ciutat) AS ciutat, c.comarca, p.provincia, co.comunitat, e.estat
            FROM  aux_dades_municipis AS m
            LEFT JOIN aux_dades_municipis_comarca AS c ON m.comarca = c.id
            LEFT JOIN aux_dades_municipis_provincia AS p ON m.provincia = p.id
            LEFT JOIN aux_dades_municipis_comunitat AS co ON m.comunitat = co.id
            LEFT JOIN aux_dades_municipis_estat AS e ON m.estat = e.id
            ORDER BY m.ciutat ASC";

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

    $query = "SELECT 
	        p.id, p.partit_politic, p.sigles
            FROM aux_filiacio_politica AS p
            ORDER BY p.partit_politic ASC";

    $result = getData2($query);
    echo json_encode($result);

    // GET : llistat de sindicats
    // URL: https://memoriaterrassa.cat/api/auxiliars/get/sindicats
} else if ($slug === "sindicats") {

    $query = "SELECT 
	        s.id, s.sindicat, s.sigles
            FROM aux_filiacio_sindical AS s
            ORDER BY s.sindicat ASC";

    $result = getData2($query);
    echo json_encode($result);

    // GET : llistat de provincies
    // URL: https://memoriaterrassa.cat/api/auxiliars/get/provincies
} else if ($slug === "provincies") {

    $query = "SELECT p.id, COALESCE(p.provincia_ca, p.provincia) AS provincia
        FROM aux_dades_municipis_provincia AS p
        ORDER BY p.provincia ASC";

    $result = getData2($query);
    echo json_encode($result);


    // GET : llistat de comarques
    // URL: https://memoriaterrassa.cat/api/auxiliars/get/comarques
} else if ($slug === "comarques") {

    $query = "SELECT c.id, COALESCE(c.comarca_ca, c.comarca) AS comarca
        FROM aux_dades_municipis_comarca AS c
        ORDER BY c.comarca ASC";

    $result = getData2($query);
    echo json_encode($result);

    // GET : llistat de comunitats autonomes
    // URL: https://memoriaterrassa.cat/api/auxiliars/get/comunitats
} else if ($slug === "comunitats") {

    $query = "SELECT c.id, COALESCE(c.comunitat_ca, c.comunitat) AS comunitat
        FROM aux_dades_municipis_comunitat AS c
        ORDER BY c.comunitat ASC";

    $result = getData2($query);
    echo json_encode($result);

    // GET : llistat de països
    // URL: https://memoriaterrassa.cat/api/auxiliars/get/estats
} else if ($slug === "estats") {

    $query = "SELECT e.id, COALESCE(e.estat_ca, e.estat) AS estat
        FROM aux_dades_municipis_estat AS e
        ORDER BY e.estat ASC";

    $result = getData2($query);
    echo json_encode($result);

    // GET : llistat d'estudis (analfabet, alfabetitzat...)
    // URL: https://memoriaterrassa.cat/api/auxiliars/get/estudis
} else if ($slug === "estudis") {

    $query = "SELECT e.id, e.estudi_cat
        FROM aux_estudis AS e
        ORDER BY e.estudi_cat ASC";

    $result = getData2($query);
    echo json_encode($result);

    // GET : Llistat oficis
    // URL: /api/auxiliars/get/oficis
} else if ($slug === "oficis") {
    $query = "SELECT o.id, o.ofici_cat
              FROM aux_oficis AS o
              ORDER BY o.ofici_cat ASC";

    $result = getData2($query);
    echo json_encode($result);

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
    $query = "SELECT se.id, se.sector_cat
              FROM aux_sector_economic AS se
              ORDER BY se.sector_cat ASC";

    $result = getData2($query);
    echo json_encode($result);

    // GET : Llistat sub-sectors economics
    // URL: /api/auxiliars/get/sub_sectors_economics
} elseif ($slug === "sub_sectors_economics") {
    $query = "SELECT sse.id, sse.sub_sector_cat, s.sector_cat
              FROM aux_sub_sector_economic AS sse
              INNER JOIN aux_sector_economic AS s ON sse.idSector = s.id
              ORDER BY sse.sub_sector_cat ASC";

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
    $query = "SELECT ce.id, ce.carrec_cat
              FROM aux_ofici_carrec AS ce
              ORDER BY ce.carrec_cat ASC";

    $result = getData2($query);
    echo json_encode($result);

    // GET : Llistat estat civil
    // URL: /api/auxiliars/get/?type=estats_civils
} elseif ($slug === "estats_civils") {
    $query = "SELECT ec.id, ec.estat_cat
              FROM aux_estat_civil AS ec
              ORDER BY ec.estat_cat ASC";

    $result = getData2($query);
    echo json_encode($result);

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
    $query = "SELECT c.id, c.causa_defuncio_ca
              FROM aux_causa_defuncio AS c
              ORDER BY c.causa_defuncio_ca ASC";

    $result = getData2($query);
    echo json_encode($result);

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
    $query = "SELECT u.id, u.nom
              FROM auth_users AS u
              ORDER BY u.nom ASC";

    $result = getData2($query);
    echo json_encode($result);

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
    $query = "SELECT r.id, r.relacio_parentiu
              FROM aux_familiars_relacio AS r
              ORDER BY r.relacio_parentiu ASC";

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
    $query = "SELECT l.id, l.llibre, l.autor, l.any,
                     CONCAT(l.autor, ', ', SUBSTRING(l.llibre, 1, 40), '...', ', ', l.any) AS llibre
              FROM aux_bibliografia_llibre_detalls AS l
              ORDER BY l.llibre ASC";

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
} else {
    // Si el parámetro 'type' no coincide con ninguno de los casos anteriores, mostramos un error
    echo json_encode(["error" => "Tipo no válido"]);
}
