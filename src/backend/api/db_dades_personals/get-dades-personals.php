<?php

use App\Config\DatabaseConnection;

$conn = DatabaseConnection::getConnection();

if (!$conn) {
    die("No se pudo establecer conexión a la base de datos.");
}

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
    $query = "SELECT a.id, a.cognom1, a.cognom2, a.nom, a.data_naixement, a.data_defuncio, e1.ciutat, a.categoria, e2.ciutat AS ciutat2, a.completat, a.font_intern, a.visibilitat
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

    // 2) Llistat complet represaliats (intranet) completades :dinamic
    // ruta GET => "https://memoriaterrassa.cat/api/dades_personals/get/?type=llistatComplertIntranet&completat=3"
} else if (isset($_GET['type']) && $_GET['type'] == 'llistatComplertIntranet' && isset($_GET['completat'])) {

    // Verificamos si el parámetro 'completat' está presente
    $completat = isset($_GET['completat']) ? $_GET['completat'] : null;

    global $conn;
    /** @var PDO $conn */

    if ($completat === '3') {
        // Caso especial: completat=3 significa completat IN (1, 2)
        $query = "SELECT a.id, a.cognom1, a.cognom2, a.nom, a.data_naixement, a.data_defuncio, e1.ciutat, a.categoria, e2.ciutat AS ciutat2, a.completat, a.font_intern, a.visibilitat
              FROM db_dades_personals AS a
              LEFT JOIN aux_dades_municipis AS e1 ON a.municipi_naixement = e1.id
              LEFT JOIN aux_dades_municipis AS e2 ON a.municipi_defuncio = e2.id
              WHERE a.completat IN (1, 2)
              ORDER BY a.cognom1 ASC";
        $stmt = $conn->prepare($query);
        $stmt->execute();
    } else {
        // Caso normal: filtro por un solo valor
        $query = "SELECT a.id, a.cognom1, a.cognom2, a.nom, a.data_naixement, a.data_defuncio, e1.ciutat, a.categoria, e2.ciutat AS ciutat2, a.completat, a.font_intern, a.visibilitat
              FROM db_dades_personals AS a
              LEFT JOIN aux_dades_municipis AS e1 ON a.municipi_naixement = e1.id
              LEFT JOIN aux_dades_municipis AS e2 ON a.municipi_defuncio = e2.id
              WHERE a.completat = :completat
              ORDER BY a.cognom1 ASC";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':completat', $completat, PDO::PARAM_INT);
        $stmt->execute();
    }

    if ($stmt->rowCount() === 0) {
        header("Content-Type: application/json");
        echo json_encode(null);  // Devuelve un objeto JSON nulo si no hay resultados
    } else {
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        header("Content-Type: application/json");
        echo json_encode($row);  // Codifica la fila como un objeto JSON
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

        $sql = "SELECT a.id, a.cognom1, a.cognom2, a.nom, a.data_naixement, a.data_defuncio, e1.ciutat, a.categoria, e2.ciutat AS ciutat2, a.completat, a.font_intern, a.visibilitat
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
        $sql = "SELECT a.id, a.cognom1, a.cognom2, a.nom, a.data_naixement, a.data_defuncio, e1.ciutat, a.categoria, e2.ciutat AS ciutat2, a.completat, a.font_intern, a.visibilitat
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
        $sql = "SELECT a.id, a.cognom1, a.cognom2, a.nom, a.data_naixement, a.data_defuncio, e1.ciutat, a.categoria, e2.ciutat AS ciutat2, a.completat, a.font_intern, a.visibilitat
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

    // 3) Llistat per categories de repressio (intranet, completades :dinamic)
    // ruta GET => "https://memoriaterrassa.cat/api/dades_personals/get/?type=totesCategoriesIntranet&categoria=afusellats"
} elseif (isset($_GET['type']) && $_GET['type'] == 'totesCategoriesIntranet' && isset($_GET['categoria']) && isset($_GET['completat'])) {

    // Verificamos si el parámetro 'completat' está presente
    $completat = isset($_GET['completat']) ? $_GET['completat'] : null;

    // Obtener y sanitizar la entrada
    $cat = filter_input(INPUT_GET, 'categoria', FILTER_DEFAULT);

    if ($cat === "cost-huma") {
        if ($completat == 3) {
            $catNum1 = 3;
            $catNum2 = 4;
            $catNum3 = 5;

            $sql = "SELECT a.id, a.cognom1, a.cognom2, a.nom, a.data_naixement, a.data_defuncio, e1.ciutat, a.categoria, e2.ciutat AS ciutat2, a.completat, a.font_intern, a.visibilitat
                FROM db_dades_personals AS a
                LEFT JOIN aux_dades_municipis AS e1 ON a.municipi_naixement = e1.id
                LEFT JOIN aux_dades_municipis AS e2 ON a.municipi_defuncio = e2.id
                WHERE 
                (FIND_IN_SET(?, REPLACE(REPLACE(categoria, '{', ''), '}', '')) > 0
                    OR FIND_IN_SET(?, REPLACE(REPLACE(categoria, '{', ''), '}', '')) > 0
                    OR FIND_IN_SET(?, REPLACE(REPLACE(categoria, '{', ''), '}', '')) > 0)
                    AND a.completat IN (1, 2)
                    AND a.visibilitat IN (1, 2)
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
        } else {
            $catNum1 = 3;
            $catNum2 = 4;
            $catNum3 = 5;

            $sql = "SELECT a.id, a.cognom1, a.cognom2, a.nom, a.data_naixement, a.data_defuncio, e1.ciutat, a.categoria, e2.ciutat AS ciutat2, a.completat, a.font_intern, a.visibilitat
            FROM db_dades_personals AS a
            LEFT JOIN aux_dades_municipis AS e1 ON a.municipi_naixement = e1.id
            LEFT JOIN aux_dades_municipis AS e2 ON a.municipi_defuncio = e2.id
            WHERE 
            (FIND_IN_SET(?, REPLACE(REPLACE(categoria, '{', ''), '}', '')) > 0
                OR FIND_IN_SET(?, REPLACE(REPLACE(categoria, '{', ''), '}', '')) > 0
                OR FIND_IN_SET(?, REPLACE(REPLACE(categoria, '{', ''), '}', '')) > 0)
                AND a.completat = ?
                AND a.visibilitat IN (1,2)
            ORDER BY a.cognom1 ASC;";
            global $conn;
            $data = array();
            /** @var PDO $conn */
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(1, $catNum1, PDO::PARAM_STR);
            $stmt->bindParam(2, $catNum2, PDO::PARAM_STR);
            $stmt->bindParam(3, $catNum3, PDO::PARAM_STR);
            $stmt->bindParam(4, $completat, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() === 0) echo ('No rows');
            while ($users = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $users;
            }
            echo json_encode($data);
        }
    } else if ($cat === "exiliats-deportats") {
        if ($completat == 3) {
            $catNum1 = 10;
            $catNum2 = 2;
            $sql = "SELECT a.id, a.cognom1, a.cognom2, a.nom, a.data_naixement, a.data_defuncio, e1.ciutat, a.categoria, e2.ciutat AS ciutat2, a.completat, a.font_intern, a.visibilitat
                FROM db_dades_personals AS a
                LEFT JOIN aux_dades_municipis AS e1 ON a.municipi_naixement = e1.id
                LEFT JOIN aux_dades_municipis AS e2 ON a.municipi_defuncio = e2.id
                WHERE 
                    (FIND_IN_SET(?, REPLACE(REPLACE(categoria, '{', ''), '}', '')) > 0
                    OR FIND_IN_SET(?, REPLACE(REPLACE(categoria, '{', ''), '}', '')) > 0)
                    AND a.completat IN (1, 2)
                    AND a.visibilitat IN (1, 2)
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
        } else {
            $catNum1 = 10;
            $catNum2 = 2;
            $sql = "SELECT a.id, a.cognom1, a.cognom2, a.nom, a.data_naixement, a.data_defuncio, e1.ciutat, a.categoria, e2.ciutat AS ciutat2, a.completat, a.font_intern, a.visibilitat
                FROM db_dades_personals AS a
                LEFT JOIN aux_dades_municipis AS e1 ON a.municipi_naixement = e1.id
                LEFT JOIN aux_dades_municipis AS e2 ON a.municipi_defuncio = e2.id
                WHERE 
                    (FIND_IN_SET(?, REPLACE(REPLACE(categoria, '{', ''), '}', '')) > 0
                    OR FIND_IN_SET(?, REPLACE(REPLACE(categoria, '{', ''), '}', '')) > 0)
                    AND a.completat = ?
                    AND a.visibilitat IN (1,2)
                ORDER BY a.cognom1 ASC;";
            global $conn;
            $data = array();
            /** @var PDO $conn */
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(1, $catNum1, PDO::PARAM_STR);
            $stmt->bindParam(2, $catNum2, PDO::PARAM_STR);
            $stmt->bindParam(3, $completat, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() === 0) echo ('No rows');
            while ($users = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $users;
            }
            echo json_encode($data);
        }
    } else if ($cat === "represaliats") {
        if ($completat == 3) {
            $catNum1 = 1;
            $catNum2 = 6;
            $catNum3 = 7;
            $catNum4 = 11;
            $sql = "SELECT a.id, a.cognom1, a.cognom2, a.nom, a.data_naixement, a.data_defuncio, e1.ciutat, a.categoria, e2.ciutat AS ciutat2, a.completat, a.font_intern, a.visibilitat
                FROM db_dades_personals AS a
                LEFT JOIN aux_dades_municipis AS e1 ON a.municipi_naixement = e1.id
                LEFT JOIN aux_dades_municipis AS e2 ON a.municipi_defuncio = e2.id
                WHERE 
                    (FIND_IN_SET(?, REPLACE(REPLACE(categoria, '{', ''), '}', '')) > 0
                    OR FIND_IN_SET(?, REPLACE(REPLACE(categoria, '{', ''), '}', '')) > 0
                    OR FIND_IN_SET(?, REPLACE(REPLACE(categoria, '{', ''), '}', '')) > 0
                    OR FIND_IN_SET(?, REPLACE(REPLACE(categoria, '{', ''), '}', '')) > 0)
                    AND a.completat IN (1, 2)
                    AND a.visibilitat IN (1, 2)
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
        } else {
            $catNum1 = 1;
            $catNum2 = 6;
            $catNum3 = 7;
            $catNum4 = 11;
            $sql = "SELECT a.id, a.cognom1, a.cognom2, a.nom, a.data_naixement, a.data_defuncio, e1.ciutat, a.categoria, e2.ciutat AS ciutat2, a.completat, a.font_intern, a.visibilitat
                FROM db_dades_personals AS a
                LEFT JOIN aux_dades_municipis AS e1 ON a.municipi_naixement = e1.id
                LEFT JOIN aux_dades_municipis AS e2 ON a.municipi_defuncio = e2.id
                WHERE 
                    (FIND_IN_SET(?, REPLACE(REPLACE(categoria, '{', ''), '}', '')) > 0
                    OR FIND_IN_SET(?, REPLACE(REPLACE(categoria, '{', ''), '}', '')) > 0
                    OR FIND_IN_SET(?, REPLACE(REPLACE(categoria, '{', ''), '}', '')) > 0
                    OR FIND_IN_SET(?, REPLACE(REPLACE(categoria, '{', ''), '}', '')) > 0)
                    AND a.completat = ?
                    AND a.visibilitat IN (1,2)
                ORDER BY a.cognom1 ASC;";
            global $conn;
            $data = array();
            /** @var PDO $conn */
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(1, $catNum1, PDO::PARAM_STR);
            $stmt->bindParam(2, $catNum2, PDO::PARAM_STR);
            $stmt->bindParam(3, $catNum3, PDO::PARAM_STR);
            $stmt->bindParam(4, $catNum4, PDO::PARAM_STR);
            $stmt->bindParam(5, $completat, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() === 0) echo ('No rows');
            while ($users = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $users;
            }
            echo json_encode($data);
        }
    }


    // 4) Pagina informacio fitxa Represaliat
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
            m1c.comunitat AS comunitat_naixement,
            m1d.estat AS pais_naixement,

            m2.ciutat AS ciutat_residencia,
            m2a.comarca AS comarca_residencia,
            m2b.provincia AS provincia_residencia,
            m2c.comunitat AS comunitat_residencia,
            m2d.estat AS pais_residencia,

            m2.id AS ciutat_residencia_id,
            m3.ciutat AS ciutat_defuncio,
            m3.id AS ciutat_defuncio_id,
            m3a.comarca AS comarca_defuncio,
            m3b.provincia AS provincia_defuncio,
            m3c.comunitat AS comunitat_defuncio,
            m3d.estat AS pais_defuncio,
            
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
            dp.empresa, 
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
            dp.autor AS autor_id,
            u.biografia_cat,
            dp.data_creacio,
            dp.data_actualitzacio,
            dp.observacions,
            dp.completat,
            img.nomArxiu AS img,
            bio.biografiaCa,
            bio.biografiaEs,
            dp.visibilitat
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
            LEFT JOIN aux_imatges AS img ON dp.img = img.id
            LEFT JOIN db_biografies AS bio ON dp.id = bio.idRepresaliat
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
            dp.cognom2
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

    // 3) Pagina informacio fitxa Represaliat > Dades familiars
    // ruta GET => "https://memoriaterrassa.cat/api/represaliats/get/?type=fitxaDadesFamiliars&id=35"
} elseif (isset($_GET['type']) && $_GET['type'] == 'fitxaDadesFamiliars' && isset($_GET['id'])) {
    $id = $_GET['id'];
    global $conn;
    /** @var PDO $conn */
    $query = "SELECT f.id as idFamiliar, f.nom AS nomFamiliar, f.cognom1 AS cognomFamiliar1, f.cognom2 AS cognomFamiliar2, idParent, r.relacio_parentiu, f.anyNaixement AS anyNaixementFamiliar
        FROM aux_familiars AS f
        LEFT JOIN aux_familiars_relacio as r ON f.relacio_parentiu = r.id
        WHERE f.idParent = $id";

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

    // 4) Per obtenir nom, cognom1 i cognom2 al cercador homepage Texts complets
    // ruta GET => "https://memoriaterrassa.cat/api/dades_personals/get/?llistatPersonesCercador"
} elseif (isset($_GET['llistatPersonesCercador'])) {

    global $conn;
    /** @var PDO $conn */
    $query = "SELECT d.id, d.nom, d.cognom1, d.cognom2
        FROM db_dades_personals AS d";

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
} else {
    // Si 'type', 'id' o 'token' están ausentes o 'type' no es 'user' en la URL
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Something get wrong']);
    exit();
}
