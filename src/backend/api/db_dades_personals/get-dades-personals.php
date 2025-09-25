<?php

use App\Config\DatabaseConnection;
use App\Config\Database;
use App\Utils\Response;
use App\Utils\MissatgesAPI;

// No lanza notice aunque $routeParams no exista o no tenga [0]
$slug = $routeParams[0] ?? null;

$conn = DatabaseConnection::getConnection();

if (!$conn) {
    die("No se pudo establecer conexión a la base de datos.");
}

$db = new Database();

// Configuración de cabeceras para aceptar JSON y responder JSON
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: https://memoriaterrassa.cat");
header("Access-Control-Allow-Methods: GET");
header('Access-Control-Allow-Credentials: true');

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


// 1) Llistat complet represaliats (web pública) completades 2 / visibilitat 2
// ruta GET => "https://memoriaterrassa.cat/api/dades_personals/get/?type=llistatComplertWeb"
if (isset($_GET['type']) && $_GET['type'] == 'llistatComplertWeb') {

    global $conn;
    /** @var PDO $conn */
    $query = "SELECT a.id, a.cognom1, a.cognom2, a.nom, a.data_naixement, a.data_defuncio, e1.ciutat, a.categoria, e2.ciutat AS ciutat2, a.completat, a.font_intern, a.visibilitat, a.slug
                FROM db_dades_personals AS a
                LEFT JOIN aux_dades_municipis AS e1 ON a.municipi_naixement = e1.id
                LEFT JOIN aux_dades_municipis AS e2 ON a.municipi_defuncio = e2.id
                WHERE a.completat = 2
                    AND a.visibilitat = 2
                ORDER BY a.cognom1 ASC";
    $stmt = $conn->prepare($query);
    $stmt->execute();

    $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header("Content-Type: application/json");
    echo json_encode($row);  // Codifica la fila como un objeto JSON

    // 2) Llistat complet represaliats (intranet) - Pàgina general
    // ruta GET => "https://memoriaterrassa.cat/api/dades_personals/get/llistatCompletIntranet"
} else if ($slug === 'llistatCompletIntranet') {

    $query = "SELECT a.id, CONCAT(a.cognom1, ' ', a.cognom2, ', ', a.nom) AS nom_complet, a.data_naixement, a.data_defuncio, e1.ciutat, a.categoria, e2.ciutat AS ciutat2, a.completat, a.font_intern, a.visibilitat, a.slug
              FROM db_dades_personals AS a
              LEFT JOIN aux_dades_municipis AS e1 ON a.municipi_naixement = e1.id
              LEFT JOIN aux_dades_municipis AS e2 ON a.municipi_defuncio = e2.id
              ORDER BY a.cognom1 ASC";

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

    // 2) Llistat per categories de repressio (web publica, només mostrarem les fitxes completades 2 i amb visibilitat completada 2)
    // ruta GET => "https://memoriaterrassa.cat/api/dades_personals/get/?type=totesCategoriesWeb&categoria=afusellats"
} elseif (isset($_GET['type']) && $_GET['type'] == 'totesCategoriesWeb' && isset($_GET['categoria'])) {

    // Obtener y sanitizar la entrada
    $cat = filter_input(INPUT_GET, 'categoria', FILTER_DEFAULT);

    if ($cat === "cost-huma") {
        $catNum1 = 3;
        $catNum2 = 4;
        $catNum3 = 5;

        $sql = "SELECT a.id, a.cognom1, a.cognom2, a.nom, a.data_naixement, a.data_defuncio, e1.ciutat, a.categoria, e2.ciutat AS ciutat2, a.completat, a.font_intern, a.visibilitat, a.slug
                FROM db_dades_personals AS a
                LEFT JOIN aux_dades_municipis AS e1 ON a.municipi_naixement = e1.id
                LEFT JOIN aux_dades_municipis AS e2 ON a.municipi_defuncio = e2.id
                WHERE 
                (FIND_IN_SET(?, REPLACE(REPLACE(categoria, '{', ''), '}', '')) > 0
                    OR FIND_IN_SET(?, REPLACE(REPLACE(categoria, '{', ''), '}', '')) > 0
                    OR FIND_IN_SET(?, REPLACE(REPLACE(categoria, '{', ''), '}', '')) > 0)
                    AND a.completat = 2
                AND a.visibilitat = 2
                ORDER BY a.cognom1 ASC;";
        global $conn;
        $data = array();
        /** @var PDO $conn */
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $catNum1, PDO::PARAM_STR);
        $stmt->bindParam(2, $catNum2, PDO::PARAM_STR);
        $stmt->bindParam(3, $catNum3, PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->rowCount() === 0) echo ('No rows');
        while ($users = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $users;
        }
        echo json_encode($data);
    } else if ($cat === "exiliats-deportats") {
        $catNum1 = 10;
        $catNum2 = 2;
        $sql = "SELECT a.id, a.cognom1, a.cognom2, a.nom, a.data_naixement, a.data_defuncio, e1.ciutat, a.categoria, e2.ciutat AS ciutat2, a.completat, a.font_intern, a.visibilitat, a.slug
                FROM db_dades_personals AS a
                LEFT JOIN aux_dades_municipis AS e1 ON a.municipi_naixement = e1.id
                LEFT JOIN aux_dades_municipis AS e2 ON a.municipi_defuncio = e2.id
                WHERE 
                    (FIND_IN_SET(?, REPLACE(REPLACE(categoria, '{', ''), '}', '')) > 0
                    OR FIND_IN_SET(?, REPLACE(REPLACE(categoria, '{', ''), '}', '')) > 0)
                    AND a.completat =  2
                    AND a.visibilitat = 2
                ORDER BY a.cognom1 ASC;";
        global $conn;
        $data = array();
        /** @var PDO $conn */
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $catNum1, PDO::PARAM_STR);
        $stmt->bindParam(2, $catNum2, PDO::PARAM_STR);

        $stmt->execute();
        if ($stmt->rowCount() === 0) echo ('No rows');
        while ($users = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $users;
        }
        echo json_encode($data);
    } else if ($cat === "represaliats") {
        $catNum1 = 1;
        $catNum2 = 6;
        $catNum3 = 7;
        $catNum4 = 11;
        $sql = "SELECT a.id, a.cognom1, a.cognom2, a.nom, a.data_naixement, a.data_defuncio, e1.ciutat, a.categoria, e2.ciutat AS ciutat2, a.completat, a.font_intern, a.visibilitat, a.slug
                FROM db_dades_personals AS a
                LEFT JOIN aux_dades_municipis AS e1 ON a.municipi_naixement = e1.id
                LEFT JOIN aux_dades_municipis AS e2 ON a.municipi_defuncio = e2.id
                WHERE 
                    (FIND_IN_SET(?, REPLACE(REPLACE(categoria, '{', ''), '}', '')) > 0
                    OR FIND_IN_SET(?, REPLACE(REPLACE(categoria, '{', ''), '}', '')) > 0
                    OR FIND_IN_SET(?, REPLACE(REPLACE(categoria, '{', ''), '}', '')) > 0
                    OR FIND_IN_SET(?, REPLACE(REPLACE(categoria, '{', ''), '}', '')) > 0)
                    AND a.completat = 2
                    AND a.visibilitat = 2
                ORDER BY a.cognom1 ASC;";
        global $conn;
        $data = array();
        /** @var PDO $conn */
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $catNum1, PDO::PARAM_STR);
        $stmt->bindParam(2, $catNum2, PDO::PARAM_STR);
        $stmt->bindParam(3, $catNum3, PDO::PARAM_STR);
        $stmt->bindParam(4, $catNum4, PDO::PARAM_STR);

        $stmt->execute();
        if ($stmt->rowCount() === 0) echo ('No rows');
        while ($users = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $users;
        }
        echo json_encode($data);
    }

    // 2) Llistat categories repressió - Represaliats (intranet)
    // ruta GET => "https://memoriaterrassa.cat/api/dades_personals/get/llistatRepresaliatsIntranet"
} else if ($slug === 'llistatRepresaliatsIntranet') {

    $query = "SELECT a.id, a.cognom1, a.cognom2, a.nom, a.data_naixement, a.data_defuncio, e1.ciutat, a.categoria, e2.ciutat AS ciutat2, a.completat, a.font_intern, a.visibilitat, a.slug
                FROM db_dades_personals AS a
                LEFT JOIN aux_dades_municipis AS e1 ON a.municipi_naixement = e1.id
                LEFT JOIN aux_dades_municipis AS e2 ON a.municipi_defuncio = e2.id
                WHERE 
                   REPLACE(REPLACE(REPLACE(COALESCE(a.categoria,''), '{',''), '}',''), ' ', '') 
                        REGEXP '(^|,)(6|7|11|12|13|14|15|16|17|18|19|20)(,|$)'
                ORDER BY a.cognom1 ASC";

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

    // 2) Llistat categories exiliats (intranet)
    // ruta GET => "https://memoriaterrassa.cat/api/dades_personals/get/llistatExiliatsIntranet"
} else if ($slug === 'llistatExiliatsIntranet') {

    $query = "SELECT a.id, a.cognom1, a.cognom2, a.nom, a.data_naixement, a.data_defuncio, e1.ciutat, a.categoria, e2.ciutat AS ciutat2, a.completat, a.font_intern, a.visibilitat, a.slug
                FROM db_dades_personals AS a
                LEFT JOIN aux_dades_municipis AS e1 ON a.municipi_naixement = e1.id
                LEFT JOIN aux_dades_municipis AS e2 ON a.municipi_defuncio = e2.id
                WHERE 
                   REPLACE(REPLACE(REPLACE(COALESCE(a.categoria,''), '{',''), '}',''), ' ', '') 
                       REGEXP '(^|,)(2|10)(,|$)'
                ORDER BY a.cognom1 ASC";

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

    // 2) Llistat categories exiliats (intranet)
    // ruta GET => "https://memoriaterrassa.cat/api/dades_personals/get/llistatCostHumaIntranet"
} else if ($slug === 'llistatCostHumaIntranet') {

    $query = "SELECT a.id, a.cognom1, a.cognom2, a.nom, a.data_naixement, a.data_defuncio, e1.ciutat, a.categoria, e2.ciutat AS ciutat2, a.completat, a.font_intern, a.visibilitat, a.slug
                FROM db_dades_personals AS a
                LEFT JOIN aux_dades_municipis AS e1 ON a.municipi_naixement = e1.id
                LEFT JOIN aux_dades_municipis AS e2 ON a.municipi_defuncio = e2.id
                WHERE 
                   REPLACE(REPLACE(REPLACE(COALESCE(a.categoria,''), '{',''), '}',''), ' ', '') 
                       REGEXP '(^|,)(3|4|5)(,|$)'
                ORDER BY a.cognom1 ASC";

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


    // 4) Pagina informacio fitxa Represaliat - INTRANET
    // ruta GET => "https://memoriaterrassa.cat/api/represaliats/get/?type=fitxa&id=35"
} elseif (isset($_GET['type']) && $_GET['type'] == 'fitxa' && isset($_GET['id'])) {
    $id = $_GET['id'];

    global $conn;
    /** @var PDO $conn */
    $query = "SELECT 
            dp.id,
            dp.nom,
            dp.cognom1,
            dp.cognom2, 
            dp.categoria,
            dp.sexe,
            dp.data_naixement,
            dp.data_defuncio,
            
            m1.id AS ciutat_naixement_id,
            m1.ciutat AS ciutat_naixement,
            m1a.comarca AS comarca_naixement,
            m1b.provincia AS provincia_naixement,
            m1c.comunitat_ca AS comunitat_naixement,
            m1d.estat_ca AS pais_naixement,

            m2.ciutat AS ciutat_residencia,
            m2a.comarca AS comarca_residencia,
            m2b.provincia AS provincia_residencia,
            m2c.comunitat_ca AS comunitat_residencia,
            m2d.estat_ca AS pais_residencia,

            m2.id AS ciutat_residencia_id,
            m3.ciutat AS ciutat_defuncio,
            m3.id AS ciutat_defuncio_id,
            m3a.comarca AS comarca_defuncio,
            m3b.provincia AS provincia_defuncio,
            m3c.comunitat_ca AS comunitat_defuncio,
            m3d.estat_ca AS pais_defuncio,
            
            dp.adreca, 
            tespai.tipologia_espai_ca,
            tespai.id AS tipologia_lloc_defuncio_id,
            tespai.observacions AS observacions_espai,
            causaD.causa_defuncio_ca,
            causaD.id AS causa_defuncio_id,
            ec.estat_cat AS estat_civil, 
            ec.id AS estat_civil_id,  
            es.estudi_cat, 
            es.id AS estudis_id, 
            o.ofici_cat, 
            o.id AS ofici_id, 
            em.empresa_ca AS empresa,
            dp.empresa AS empresa_id,
            fp.partit_politic, 
            fp.id AS partit_politic_id,
            dp.filiacio_politica,
            fs.sindicat, 
            fs.id AS sindicat_id,
            dp.filiacio_sindical,
            dp.activitat_durant_guerra,
            se.sector_cat,
            se.id AS sector_id,
            sse.sub_sector_cat,
            sse.id AS sub_sector_id,
            oc.carrec_cat,
            oc.id AS carrecs_empresa_id,
            u.nom AS autorNom,
            u2.nom AS autor2Nom,
            u3.nom AS autor3Nom,
            u4.nom AS colab1Nom,
            dp.autor AS autor_id,
            dp.autor2 AS autor_id2,
            dp.autor3 AS autor_id3,
            dp.colab1 AS colab1_id,
            u.biografia_cat,
            dp.data_creacio,
            dp.data_actualitzacio,
            dp.observacions,
            dp.completat,
            img.nomArxiu AS img,
            dp.img AS imatgePerfil,
            bio.biografiaCa,
            bio.biografiaEs,
            dp.visibilitat,
            dp.slug,
            dp.observacions_internes,
            dp.lat,
            dp.lng,
            dp.tipus_via AS tipus_via_id,
            dp.adreca_antic,
            dp.adreca_num,
            dp.causa_defuncio_detalls
            FROM db_dades_personals AS dp
            LEFT JOIN aux_dades_municipis AS m1 ON dp.municipi_naixement = m1.id
            LEFT JOIN aux_dades_municipis_comarca AS m1a ON m1.comarca = m1a.id
            LEFT JOIN aux_dades_municipis_provincia AS m1b ON m1.provincia = m1b.id
            LEFT JOIN aux_dades_municipis_comunitat AS m1c ON m1.comunitat = m1c.id
            LEFT JOIN aux_dades_municipis_estat AS m1d ON m1.estat = m1d.id

            LEFT JOIN aux_dades_municipis AS m2 ON dp.municipi_residencia = m2.id
            LEFT JOIN aux_dades_municipis_comarca AS m2a ON m2.comarca = m2a.id
            LEFT JOIN aux_dades_municipis_provincia AS m2b ON m2.provincia = m2b.id
            LEFT JOIN aux_dades_municipis_comunitat AS m2c ON m2.comunitat = m2c.id
            LEFT JOIN aux_dades_municipis_estat AS m2d ON m2.estat = m2d.id

            LEFT JOIN aux_dades_municipis AS m3 ON dp.municipi_defuncio = m3.id
            LEFT JOIN aux_dades_municipis_comarca AS m3a ON m3.comarca = m3a.id
            LEFT JOIN aux_dades_municipis_provincia AS m3b ON m3.provincia = m3b.id
            LEFT JOIN aux_dades_municipis_comunitat AS m3c ON m3.comunitat = m3c.id
            LEFT JOIN aux_dades_municipis_estat AS m3d ON m3.estat = m3d.id

            LEFT JOIN aux_tipologia_espais AS tespai ON dp.tipologia_lloc_defuncio = tespai.id
            LEFT JOIN aux_causa_defuncio AS causaD ON dp.causa_defuncio = causaD.id
            LEFT JOIN aux_filiacio_politica AS fp ON dp.filiacio_politica = fp.id
            LEFT JOIN aux_estudis AS es ON dp.estudis = es.id
            LEFT JOIN aux_oficis AS o ON dp.ofici = o.id 
            LEFT JOIN aux_filiacio_sindical AS fs ON dp.filiacio_sindical = fs.id
            LEFT JOIN aux_estat_civil as ec ON dp.estat_civil = ec.id
            LEFT JOIN aux_sector_economic AS se ON dp.sector = se.id
            LEFT JOIN aux_sub_sector_economic AS sse ON dp.sub_sector = sse.id
            LEFT JOIN aux_ofici_carrec AS oc ON dp.carrec_empresa = oc.id
            LEFT JOIN auth_users AS u ON dp.autor = u.id
            LEFT JOIN auth_users AS u2 ON dp.autor2 = u2.id
            LEFT JOIN auth_users AS u3 ON dp.autor3 = u3.id
            LEFT JOIN auth_users AS u4 ON dp.colab1 = u4.id
            LEFT JOIN aux_imatges AS img ON dp.img = img.id
            LEFT JOIN db_biografies AS bio ON dp.id = bio.idRepresaliat
            LEFT JOIN aux_empreses AS em ON dp.empresa = em.id
            LEFT JOIN aux_tipus_via AS v ON dp.tipus_via = v.id
            WHERE dp.id = $id";


    $stmt = $conn->prepare($query);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        header("Content-Type: application/json");
        echo json_encode(null);  // Devuelve un objeto JSON nulo si no hay resultados
    } else {
        // Solo obtenemos la primera fila ya que parece ser una búsqueda por ID
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        header("Content-Type: application/json");
        echo json_encode($row);  // Codifica la fila como un objeto JSON
    }


    // 4) Pagina informacio fitxa Represaliat - WEB PUBLICA
    // ruta GET => "https://memoriaterrassa.cat/api/dades_personals/get/?type=fitxa&id=35"
} elseif (isset($_GET['type']) && $_GET['type'] == 'fitxaRepresaliat' && isset($_GET['slug'])) {
    $slug = $_GET['slug'];
    $db = new Database();
    $query = "SELECT 
            dp.id,
            dp.nom,
            dp.cognom1,
            dp.cognom2, 
            dp.categoria,
            dp.sexe,
            dp.data_naixement,
            dp.data_defuncio,
            
            m1.id AS ciutat_naixement_id,
            COALESCE(m1.ciutat_ca, m1.ciutat) AS ciutat_naixement,
            COALESCE(m1a.comarca_ca, m1a.comarca) AS comarca_naixement,
            COALESCE(m1b.provincia_ca, m1b.provincia) AS provincia_naixement,
            COALESCE(m1c.comunitat_ca, m1c.comunitat) AS comunitat_naixement,
            COALESCE(m1d.estat_ca, m1d.estat) AS pais_naixement,

            m2.id AS ciutat_residencia_id,
            COALESCE(m2.ciutat_ca, m2.ciutat) AS ciutat_residencia,
            COALESCE(m2a.comarca_ca, m2a.comarca) AS comarca_residencia,
            COALESCE(m2b.provincia_ca, m2b.provincia) AS provincia_residencia,
            COALESCE(m2c.comunitat_ca, m2c.comunitat) AS comunitat_residencia,
            COALESCE(m2d.estat_ca, m2d.estat) AS pais_residencia,

            m3.id AS ciutat_defuncio_id,
            COALESCE(m3.ciutat_ca, m3.ciutat) AS ciutat_defuncio,
            COALESCE(m3a.comarca_ca, m3a.comarca) AS comarca_defuncio,
            COALESCE(m3b.provincia_ca, m3b.provincia) AS provincia_defuncio,
            COALESCE(m3c.comunitat_ca, m3c.comunitat) AS comunitat_defuncio,
            COALESCE(m3d.estat_ca, m3d.estat) AS pais_defuncio,
            
            dp.adreca, 
            tespai.tipologia_espai_ca,
            tespai.id AS tipologia_lloc_defuncio_id,
            tespai.observacions AS observacions_espai,
            causaD.causa_defuncio_ca,
            causaD.id AS causa_defuncio_id,
            ec.estat_cat AS estat_civil, 
            ec.id AS estat_civil_id,  
            es.estudi_cat, 
            es.id AS estudis_id, 
            o.ofici_cat, 
            o.id AS ofici_id, 
            em.empresa_ca AS empresa,
            dp.empresa AS empresa_id,
            fp.partit_politic, 
            fp.id AS partit_politic_id,
            dp.filiacio_politica,
            fs.sindicat, 
            fs.id AS sindicat_id,
            dp.filiacio_sindical,
            dp.activitat_durant_guerra,
            se.sector_cat,
            se.id AS sector_id,
            sse.sub_sector_cat,
            sse.id AS sub_sector_id,
            oc.carrec_cat,
            oc.id AS carrecs_empresa_id,
            u.nom AS autorNom,
            u2.nom AS autor2Nom,
            u3.nom AS autor3Nom,
            u4.nom AS colab1Nom,
            dp.autor AS autor_id,
            dp.autor2 AS autor_id2,
            dp.autor3 AS autor_id3,
            dp.colab1 AS colab1_id,
            u.biografia_cat,
            dp.data_creacio,
            dp.data_actualitzacio,
            dp.observacions,
            dp.completat, 
            dp.visibilitat,
            img.nomArxiu AS img,
            dp.img AS imatgePerfil,
            bio.biografiaCa,
            bio.biografiaEs,
            dp.slug,
            dp.lat,
            dp.lng,
            dp.tipus_via AS tipus_via_id,
            v.tipus_ca,
            dp.adreca_antic,
            dp.adreca_num,
            dp.causa_defuncio_detalls,
            causaDD.defuncio_detalls_ca
            FROM db_dades_personals AS dp
            LEFT JOIN aux_dades_municipis AS m1 ON dp.municipi_naixement = m1.id
            LEFT JOIN aux_dades_municipis_comarca AS m1a ON m1.comarca = m1a.id
            LEFT JOIN aux_dades_municipis_provincia AS m1b ON m1.provincia = m1b.id
            LEFT JOIN aux_dades_municipis_comunitat AS m1c ON m1.comunitat = m1c.id
            LEFT JOIN aux_dades_municipis_estat AS m1d ON m1.estat = m1d.id

            LEFT JOIN aux_dades_municipis AS m2 ON dp.municipi_residencia = m2.id
            LEFT JOIN aux_dades_municipis_comarca AS m2a ON m2.comarca = m2a.id
            LEFT JOIN aux_dades_municipis_provincia AS m2b ON m2.provincia = m2b.id
            LEFT JOIN aux_dades_municipis_comunitat AS m2c ON m2.comunitat = m2c.id
            LEFT JOIN aux_dades_municipis_estat AS m2d ON m2.estat = m2d.id

            LEFT JOIN aux_dades_municipis AS m3 ON dp.municipi_defuncio = m3.id
            LEFT JOIN aux_dades_municipis_comarca AS m3a ON m3.comarca = m3a.id
            LEFT JOIN aux_dades_municipis_provincia AS m3b ON m3.provincia = m3b.id
            LEFT JOIN aux_dades_municipis_comunitat AS m3c ON m3.comunitat = m3c.id
            LEFT JOIN aux_dades_municipis_estat AS m3d ON m3.estat = m3d.id

            LEFT JOIN aux_tipologia_espais AS tespai ON dp.tipologia_lloc_defuncio = tespai.id
            LEFT JOIN aux_causa_defuncio AS causaD ON dp.causa_defuncio = causaD.id
            LEFT JOIN aux_causa_defuncio_detalls AS causaDD ON dp.causa_defuncio_detalls = causaDD.id
            LEFT JOIN aux_filiacio_politica AS fp ON dp.filiacio_politica = fp.id
            LEFT JOIN aux_estudis AS es ON dp.estudis = es.id
            LEFT JOIN aux_oficis AS o ON dp.ofici = o.id 
            LEFT JOIN aux_filiacio_sindical AS fs ON dp.filiacio_sindical = fs.id
            LEFT JOIN aux_estat_civil as ec ON dp.estat_civil = ec.id
            LEFT JOIN aux_sector_economic AS se ON dp.sector = se.id
            LEFT JOIN aux_sub_sector_economic AS sse ON dp.sub_sector = sse.id
            LEFT JOIN aux_ofici_carrec AS oc ON dp.carrec_empresa = oc.id
            LEFT JOIN auth_users AS u ON dp.autor = u.id
            LEFT JOIN auth_users AS u2 ON dp.autor2 = u2.id
            LEFT JOIN auth_users AS u3 ON dp.autor3 = u3.id
            LEFT JOIN auth_users AS u4 ON dp.colab1 = u4.id
            LEFT JOIN aux_imatges AS img ON dp.img = img.id
            LEFT JOIN db_biografies AS bio ON dp.id = bio.idRepresaliat
            LEFT JOIN aux_empreses AS em ON dp.empresa = em.id
            LEFT JOIN aux_tipus_via AS v ON dp.tipus_via = v.id
            WHERE dp.slug = :slug";

    try {
        $params = [':slug' => $slug];
        $result = $db->getData($query, $params, false);

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

    // 4) Nom i cognoms del represaliat
    // ruta GET => "https://memoriaterrassa.cat/api/represaliats/get/?type=nomCognoms&id=35"
} elseif (isset($_GET['type']) && $_GET['type'] == 'nomCognoms' && isset($_GET['id'])) {
    $id = $_GET['id'];

    global $conn;
    /** @var PDO $conn */
    $query = "SELECT 
            dp.id,
            dp.nom,
            dp.cognom1,
            dp.cognom2,
            dp.slug
            FROM db_dades_personals AS dp
            WHERE dp.id = $id";

    $stmt = $conn->prepare($query);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        header("Content-Type: application/json");
        echo json_encode(null);  // Devuelve un objeto JSON nulo si no hay resultados
    } else {
        // Solo obtenemos la primera fila ya que parece ser una búsqueda por ID
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        header("Content-Type: application/json");
        echo json_encode($row);  // Codifica la fila como un objeto JSON
    }

    // 4) Nom i cognoms del represaliat - ENDPOINT NOU
    // ruta GET => "https://memoriaterrassa.cat/api/dades_personals/get/?type=nomRepresaliat&id=35"
} elseif (isset($_GET['type']) && $_GET['type'] == 'nomRepresaliat' && isset($_GET['id'])) {

    $id = $_GET['id'];
    $db = new Database();

    $query = "SELECT 
            dp.id AS idRepresaliat,
            dp.nom,
            dp.cognom1,
            dp.cognom2,
            dp.slug
            FROM db_dades_personals AS dp
            WHERE dp.id = :id";

    try {
        $params = [':id' => $id];
        $result = $db->getData($query, $params, false);

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

    // 3) Pagina informacio fitxa Represaliat > Dades familiars
    // ruta GET => "https://memoriaterrassa.cat/api/represaliats/get/?type=fitxaDadesFamiliars&id=35"
} elseif (isset($_GET['type']) && $_GET['type'] == 'fitxaDadesFamiliars' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $db = new Database();

    $query = "SELECT f.id as idFamiliar, f.nom AS nomFamiliar, f.cognom1 AS cognomFamiliar1, f.cognom2 AS cognomFamiliar2, idParent, r.relacio_parentiu, f.anyNaixement AS anyNaixementFamiliar
        FROM aux_familiars AS f
        LEFT JOIN aux_familiars_relacio as r ON f.relacio_parentiu = r.id
        WHERE f.idParent = :id";


    try {
        $params = [':id' => $id];
        $result = $db->getData($query, $params, false);

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

    // 4) Per obtenir nom, cognom1 i cognom2 al cercador homepage Texts complets
    // ruta GET => "https://memoriaterrassa.cat/api/dades_personals/get/?llistatPersonesCercador"
} elseif (isset($_GET['llistatPersonesCercador'])) {

    $db = new Database();
    $query = "SELECT d.id, d.nom, d.cognom1, d.cognom2, d.slug
        FROM db_dades_personals AS d";

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


    // 4) Registre edicions fitxa represaliat
    // ruta GET => "https://memoriaterrassa.cat/api/dades_personals/get/?type=registreEdicions&id=35"
} elseif (isset($_GET['type']) && $_GET['type'] == 'registreEdicions' && isset($_GET['id'])) {

    $id = $_GET['id'];
    $db = new Database();

    $query = "SELECT crc.operacio, crc.detalls, crc.taula_afectada, crc.dataHora, crc.ip_usuari, crc.user_agent, u.nom
    FROM control_registre_canvis AS crc
    LEFT JOIN auth_users AS u ON u.id = crc.idUser
    WHERE crc.registre_id = :id
    AND crc.taula_afectada = 'db_dades_personals'
    ORDER BY crc.dataHora DESC";

    try {
        $params = [':id' => $id];
        $result = $db->getData($query, $params, false);

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

    // 4) Llistat exiliats i deportats
    // ruta GET => "https://memoriaterrassa.cat/api/dades_personals/get/?type=filtreExili"
} elseif (isset($_GET['type']) && $_GET['type'] == 'filtreExili') {
    $db = new Database();
    $catNum1 = 10;
    $catNum2 = 2;
    $lang = isset($_GET['lang']) ? $_GET['lang'] : 'ca'; // Valor por defecto 'ca'

    $query = "SELECT 
                a.id,
                a.cognom1,
                a.cognom2,
                a.nom, 
                a.data_naixement,
                a.data_defuncio,
                e1.ciutat,
                a.municipi_naixement,
                a.municipi_defuncio,
                REPLACE(REPLACE(a.categoria, '{', '['), '}', ']') AS categoria,
                e2.ciutat AS ciutat2, 
                a.slug,
                a.sexe,
                REPLACE(REPLACE(a.filiacio_politica, '{', '['), '}', ']') AS filiacio_politica,
                REPLACE(REPLACE(a.filiacio_sindical, '{', '['), '}', ']') AS filiacio_sindical,
                a.estat_civil,
                a.estudis,
                a.ofici,
                a.causa_defuncio,
                ex.data_exili,
                ex.primer_desti_exili,
                ex.deportat,
                ex.participacio_resistencia
          FROM db_dades_personals AS a
          LEFT JOIN aux_dades_municipis AS e1 ON a.municipi_naixement = e1.id
          LEFT JOIN aux_dades_municipis AS e2 ON a.municipi_defuncio = e2.id
          LEFT JOIN db_exiliats AS ex ON a.id = ex.idPersona
          WHERE a.visibilitat = 2
            AND a.completat = 2
            AND (
                 FIND_IN_SET(:catNum1, REPLACE(REPLACE(categoria, '{', ''), '}', '')) > 0
                 OR FIND_IN_SET(:catNum2, REPLACE(REPLACE(categoria, '{', ''), '}', '')) > 0
          )
          ORDER BY a.cognom1 ASC;";

    try {
        // Pasamos los valores como array en el mismo orden de los placeholders
        $params = [
            ':catNum1' => $catNum1,
            ':catNum2' => $catNum2,
        ];

        $result = $db->getData($query, $params, false);

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

    // 4) Llistat filtre Cost huma guerra civil
    // ruta GET => "https://memoriaterrassa.cat/api/dades_personals/get/?type=filtreCostHuma"
} elseif (isset($_GET['type']) && $_GET['type'] == 'filtreCostHuma') {
    $db = new Database();
    $catNum1 = 3;
    $catNum2 = 4;
    $catNum3 = 5;

    $query = "SELECT 
                a.id,
                a.cognom1,
                a.cognom2,
                a.nom, 
                a.data_naixement,
                a.data_defuncio,
                e1.ciutat,
                a.municipi_naixement,
                a.municipi_defuncio,
                REPLACE(REPLACE(a.categoria, '{', '['), '}', ']') AS categoria,
                e2.ciutat AS ciutat2, 
                a.slug,
                a.sexe,
                REPLACE(REPLACE(a.filiacio_politica, '{', '['), '}', ']') AS filiacio_politica,
                REPLACE(REPLACE(a.filiacio_sindical, '{', '['), '}', ']') AS filiacio_sindical,
                a.estat_civil,
                a.estudis,
                a.ofici,
                a.causa_defuncio,
                ex.data_exili,
                ex.primer_desti_exili,
                ex.deportat,
                ex.participacio_resistencia
          FROM db_dades_personals AS a
          LEFT JOIN aux_dades_municipis AS e1 ON a.municipi_naixement = e1.id
          LEFT JOIN aux_dades_municipis AS e2 ON a.municipi_defuncio = e2.id
          LEFT JOIN db_exiliats AS ex ON a.id = ex.idPersona
          WHERE a.visibilitat = 2
            AND a.completat = 2
            AND (
                 FIND_IN_SET(:catNum1, REPLACE(REPLACE(categoria, '{', ''), '}', '')) > 0
                 OR FIND_IN_SET(:catNum2, REPLACE(REPLACE(categoria, '{', ''), '}', '')) > 0
                 OR FIND_IN_SET(:catNum3, REPLACE(REPLACE(categoria, '{', ''), '}', '')) > 0
          )
          ORDER BY a.cognom1 ASC;";

    try {
        // Pasamos los valores como array en el mismo orden de los placeholders
        $params = [
            ':catNum1' => $catNum1,
            ':catNum2' => $catNum2,
            ':catNum3' => $catNum3,
        ];

        $result = $db->getData($query, $params, false);

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


    // 4) Llistat filtre General
    // ruta GET => "https://memoriaterrassa.cat/api/dades_personals/get/?type=filtreGeneral"
} elseif (isset($_GET['type']) && $_GET['type'] == 'filtreGeneral') {
    $db = new Database();
    $catNum1 = 3;
    $catNum2 = 4;
    $catNum3 = 5;

    $query = "SELECT 
                a.id,
                a.cognom1,
                a.cognom2,
                a.nom, 
                a.data_naixement,
                a.data_defuncio,
                e1.ciutat,
                a.municipi_naixement,
                a.municipi_defuncio,
                REPLACE(REPLACE(a.categoria, '{', '['), '}', ']') AS categoria,
                e2.ciutat AS ciutat2, 
                a.slug,
                a.sexe,
                REPLACE(REPLACE(a.filiacio_politica, '{', '['), '}', ']') AS filiacio_politica,
                REPLACE(REPLACE(a.filiacio_sindical, '{', '['), '}', ']') AS filiacio_sindical,
                a.estat_civil,
                a.estudis,
                a.ofici,
                a.causa_defuncio,
                ex.data_exili,
                ex.primer_desti_exili,
                ex.deportat,
                ex.participacio_resistencia
          FROM db_dades_personals AS a
          LEFT JOIN aux_dades_municipis AS e1 ON a.municipi_naixement = e1.id
          LEFT JOIN aux_dades_municipis AS e2 ON a.municipi_defuncio = e2.id
          LEFT JOIN db_exiliats AS ex ON a.id = ex.idPersona
          WHERE a.visibilitat = 2
            AND a.completat = 2
          ORDER BY a.cognom1 ASC;";

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

    // 4) Llistat filtre Represaliats
    // ruta GET => "https://memoriaterrassa.cat/api/dades_personals/get/?type=filtreRepresaliats"
} elseif (isset($_GET['type']) && $_GET['type'] == 'filtreRepresaliats') {
    $db = new Database();
    $catNum1 = 1;
    $catNum2 = 6;
    $catNum3 = 7;
    $catNum4 = 8;
    $catNum5 = 10;
    $catNum6 = 11;
    $catNum7 = 12;
    $catNum8 = 15;
    $catNum9 = 14;
    $catNum10 = 15;
    $catNum11 = 16;
    $catNum12 = 17;
    $catNum13 = 18;
    $catNum14 = 19;
    $catNum15 = 20;

    $query = "SELECT 
                a.id,
                a.cognom1,
                a.cognom2,
                a.nom, 
                a.data_naixement,
                a.data_defuncio,
                e1.ciutat,
                a.municipi_naixement,
                a.municipi_defuncio,
                REPLACE(REPLACE(a.categoria, '{', '['), '}', ']') AS categoria,
                e2.ciutat AS ciutat2, 
                a.slug,
                a.sexe,
                REPLACE(REPLACE(a.filiacio_politica, '{', '['), '}', ']') AS filiacio_politica,
                REPLACE(REPLACE(a.filiacio_sindical, '{', '['), '}', ']') AS filiacio_sindical,
                a.estat_civil,
                a.estudis,
                a.ofici,
                a.causa_defuncio,
                ex.data_exili,
                ex.primer_desti_exili,
                ex.deportat,
                ex.participacio_resistencia
          FROM db_dades_personals AS a
          LEFT JOIN aux_dades_municipis AS e1 ON a.municipi_naixement = e1.id
          LEFT JOIN aux_dades_municipis AS e2 ON a.municipi_defuncio = e2.id
          LEFT JOIN db_exiliats AS ex ON a.id = ex.idPersona
          WHERE a.visibilitat = 2
            AND a.completat = 2
            AND 
                (
                    FIND_IN_SET(:catNum1,  REPLACE(REPLACE(a.categoria, '{', ''), '}', '')) > 0 OR
                    FIND_IN_SET(:catNum2,  REPLACE(REPLACE(a.categoria, '{', ''), '}', '')) > 0 OR
                    FIND_IN_SET(:catNum3,  REPLACE(REPLACE(a.categoria, '{', ''), '}', '')) > 0 OR
                    FIND_IN_SET(:catNum4,  REPLACE(REPLACE(a.categoria, '{', ''), '}', '')) > 0 OR
                    FIND_IN_SET(:catNum5,  REPLACE(REPLACE(a.categoria, '{', ''), '}', '')) > 0 OR
                    FIND_IN_SET(:catNum6,  REPLACE(REPLACE(a.categoria, '{', ''), '}', '')) > 0 OR
                    FIND_IN_SET(:catNum7,  REPLACE(REPLACE(a.categoria, '{', ''), '}', '')) > 0 OR
                    FIND_IN_SET(:catNum8,  REPLACE(REPLACE(a.categoria, '{', ''), '}', '')) > 0 OR
                    FIND_IN_SET(:catNum9,  REPLACE(REPLACE(a.categoria, '{', ''), '}', '')) > 0 OR
                    FIND_IN_SET(:catNum10, REPLACE(REPLACE(a.categoria, '{', ''), '}', '')) > 0 OR
                    FIND_IN_SET(:catNum11, REPLACE(REPLACE(a.categoria, '{', ''), '}', '')) > 0 OR
                    FIND_IN_SET(:catNum12, REPLACE(REPLACE(a.categoria, '{', ''), '}', '')) > 0 OR
                    FIND_IN_SET(:catNum13, REPLACE(REPLACE(a.categoria, '{', ''), '}', '')) > 0 OR
                    FIND_IN_SET(:catNum14, REPLACE(REPLACE(a.categoria, '{', ''), '}', '')) > 0 OR
                    FIND_IN_SET(:catNum15, REPLACE(REPLACE(a.categoria, '{', ''), '}', '')) > 0
                )
          ORDER BY a.cognom1 ASC;";


    try {
        // Pasamos los valores como array en el mismo orden de los placeholders
        $params = [
            ':catNum1'  => $catNum1,
            ':catNum2'  => $catNum2,
            ':catNum3'  => $catNum3,
            ':catNum4'  => $catNum4,
            ':catNum5'  => $catNum5,
            ':catNum6'  => $catNum6,
            ':catNum7'  => $catNum7,
            ':catNum8'  => $catNum8,
            ':catNum9'  => $catNum9,
            ':catNum10' => $catNum10,
            ':catNum11' => $catNum11,
            ':catNum12' => $catNum12,
            ':catNum13' => $catNum13,
            ':catNum14' => $catNum14,
            ':catNum15' =>  $catNum15
        ];

        $result = $db->getData($query, $params, false);

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

    // 4) Llistat geolocalitzacio
    // ruta GET => "https://memoriaterrassa.cat/api/dades_personals/get/?type=geolocalitzacio"
} elseif (isset($_GET['type']) && $_GET['type'] == 'geolocalitzacio') {
    $db = new Database();

    $query = "SELECT 
            a.id,
            a.nom,
            a.cognom1,
            a.cognom2,
            a.slug,
            a.adreca,
            a.tipus_via,
            a.lat,
            a.lng,
            COALESCE(m.ciutat_ca, m.ciutat) AS ciutat,
            v.tipus_ca,
            a.adreca_num
          FROM db_dades_personals AS a
          LEFT JOIN aux_dades_municipis AS m ON a.municipi_residencia = m.id
          LEFT JOIN aux_tipus_via AS v ON a.tipus_via = v.id
         WHERE a.lat IS NOT NULL
            AND a.lng IS NOT NULL
            AND a.lat BETWEEN -90 AND 90
            AND a.lng BETWEEN -180 AND 180";


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

    // 4) Llistat persones sense geolocalitzacioo
    // ruta GET => "https://memoriaterrassa.cat/api/dades_personals/get/?type=llistatSenseGeolocalitzacio"
} elseif (isset($_GET['type']) && $_GET['type'] == 'llistatSenseGeolocalitzacio') {
    $db = new Database();

    $query = "SELECT 
            a.id,
            a.nom,
            a.cognom1,
            a.cognom2,
            a.slug,
            a.adreca,
            a.lat,
            a.lng,
            COALESCE(m.ciutat_ca, m.ciutat) AS ciutat,
            v.tipus_ca,
            a.adreca_num
          FROM db_dades_personals AS a
          LEFT JOIN aux_dades_municipis AS m ON a.municipi_residencia = m.id
          LEFT JOIN aux_tipus_via AS v ON a.tipus_via = v.id
          WHERE a.lat IS NULL AND a.lng IS NULL
          ORDER BY a.cognom1 ASC";

    $query2 = "SELECT
            a.id                                   AS id,
            a.adreca                               AS adreca,
            COALESCE(m.ciutat_ca, m.ciutat)        AS ciutat,
            p.provincia                            AS provincia,
            s.estat                                AS estat,
            a.nom,
            a.cognom1,
            a.cognom2,
            a.slug,
            v.tipus_ca,
            a.adreca_num
        FROM db_dades_personals a
        LEFT JOIN aux_dades_municipis           m ON m.id  = a.municipi_residencia
        LEFT JOIN aux_dades_municipis_provincia p ON p.id  = m.provincia
        LEFT JOIN aux_dades_municipis_estat     s ON s.id  = m.estat
        LEFT JOIN aux_tipus_via AS v ON a.tipus_via = v.id
        WHERE (a.lat IS NULL OR a.lng IS NULL)
            AND (COALESCE(a.adreca,'') <> '' OR a.municipi_residencia IS NOT NULL);";

    try {

        $result = $db->getData($query2);

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
    // Si 'type', 'id' o 'token' están ausentes o 'type' no es 'user' en la URL
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Something get wrong']);
    exit();
}
