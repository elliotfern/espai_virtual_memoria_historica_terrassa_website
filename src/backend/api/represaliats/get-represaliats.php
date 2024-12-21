<?php

// 1) Llistat afusellats
// ruta GET => "/api/represaliats/get/?type=tots"
if (isset($_GET['type']) && $_GET['type'] == 'tots') {
    global $conn;
    $data = array();
    $stmt = $conn->prepare(
        "SELECT a.id, a.cognom1, a.cognom2, a.nom, a.data_naixement, a.data_defuncio, e1.ciutat, e1.comarca, e1.provincia, e1.comunitat, e1.pais, a.categoria, e2.ciutat AS ciutat2, e2.comarca AS comarca2, e2.provincia AS provincia2, e2.comunitat AS comunitat2, e2.pais AS pais2
            FROM db_dades_personals AS a
            LEFT JOIN aux_dades_municipis AS e1 ON a.municipi_naixement = e1.id
            LEFT JOIN aux_dades_municipis AS e2 ON a.municipi_defuncio = e2.id
            ORDER BY a.cognom1 ASC"
    );
    $stmt->execute();
    if ($stmt->rowCount() === 0) echo ('No rows');
    while ($users = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $users;
    }
    echo json_encode($data);

    // 2) Llistat tots per categories
    // ruta GET => "/api/represaliats/get/?type=totesCategories&cat="
    // api/represaliats/get/?type=totesCategories&categoria=afusellats
} elseif (isset($_GET['type']) && $_GET['type'] == 'totesCategories' && isset($_GET['categoria'])) {

    // Obtener y sanitizar la entrada
    $cat = filter_input(INPUT_GET, 'categoria', FILTER_DEFAULT);

    if ($cat === "afusellats") {
        $catNum = 1;
    } else if ($cat === "exili-deportacio") {
        $catNum = 10;
    }

    global $conn;
    $data = array();
    $stmt = $conn->prepare(
        "SELECT a.id, a.cognom1, a.cognom2, a.nom, a.data_naixement, a.data_defuncio, e1.ciutat, e1.comarca, e1.provincia, e1.comunitat, e1.pais, a.categoria, e2.ciutat AS ciutat2, e2.comarca AS comarca2, e2.provincia AS provincia2, e2.comunitat AS comunitat2, e2.pais AS pais2
            FROM db_dades_personals AS a
            LEFT JOIN aux_dades_municipis AS e1 ON a.municipi_naixement = e1.id
            LEFT JOIN aux_dades_municipis AS e2 ON a.municipi_defuncio = e2.id
            WHERE FIND_IN_SET(?, REPLACE(REPLACE(categoria, '{', ''), '}', '')) > 0
            ORDER BY a.cognom1 ASC;"
    );
    $stmt->bindParam(1, $catNum, PDO::PARAM_STR);
    $stmt->execute();
    if ($stmt->rowCount() === 0) echo ('No rows');
    while ($users = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $users;
    }
    echo json_encode($data);

    // 2) Pagina informacio fitxa afusellat
    // ruta GET => "/api/represaliats/get/?type=fitxa&id=35"
} elseif (isset($_GET['type']) && $_GET['type'] == 'fitxa' && isset($_GET['id'])) {
    $id = $_GET['id'];
    global $conn;
    $data = array();
    $stmt = $conn->prepare(
        "SELECT dp.id, 
            dp.cognom1,
            dp.cognom2, 
            dp.nom,
            dp.sexe,
            dp.data_naixement,
            dp.data_defuncio,
            dp.edat,
            dp.categoria,
            m1.id AS ciutat_naixement_id,
            m1.ciutat AS ciutat_naixement,
            m1.comarca AS comarca_naixement,
            m1.provincia AS provincia_naixement,
            m1.comunitat AS comunitat_naixement,
            m1.pais AS pais_naixement, 
            m2.ciutat AS ciutat_residencia,
            m2.comarca AS comarca_residencia,
            m2.provincia AS provincia_residencia,
            m2.comunitat AS comunitat_residencia,
            m2.pais AS pais_residencia,
            m2.id AS ciutat_residencia_id,
            m3.ciutat AS ciutat_defuncio,
            m3.comarca AS comarca_defuncio,
            m3.provincia AS provincia_defuncio,
            m3.comunitat AS comunitat_defuncio,
            m3.pais AS pais_defuncio,
            dp.adreca, 
            ec.estat_cat AS estat_civil, 
            ec.id AS estat_civil_id,  
            es.estudi_cat, 
            es.id AS estudis_id, 
            o.ofici_cat, 
            o.id AS ofici_id, 
            dp.empresa, 
            fp.partit_politic, 
            fp.id AS partit_politic_id, 
            fs.sindicat, 
            fs.id AS sindicat_id,
            se.sector_cat,
            sse.sub_sector_cat,
            oc.carrec_cat,
            u.nom AS autorNom,
            u.biografia_cat,
            dp.data_creacio,
            dp.data_actualitzacio
            FROM db_dades_personals AS dp
            LEFT JOIN aux_dades_municipis AS m1 ON dp.municipi_naixement = m1.id
            LEFT JOIN aux_dades_municipis AS m2 ON dp.municipi_residencia = m2.id
            LEFT JOIN aux_dades_municipis AS m3 ON dp.municipi_defuncio = m3.id
            LEFT JOIN aux_filiacio_politica AS fp ON dp.filiciacio_politica = fp.id
            LEFT JOIN aux_estudis AS es ON dp.estudis = es.id
            LEFT JOIN aux_oficis AS o ON dp.ofici = o.id 
            LEFT JOIN aux_filiacio_sindical AS fs ON dp.filiacio_sindical = fs.id
            LEFT JOIN aux_estat_civil as ec ON dp.estat_civil = ec.id
            LEFT JOIN aux_sector_economic AS se ON dp.sector = se.id
            LEFT JOIN aux_sub_sector_economic AS sse ON dp.sub_sector = sse.id
            LEFT JOIN aux_ofici_carrec AS oc ON dp.carrec_empresa = oc.id
            LEFT JOIN auth_users AS u ON dp.autor = u.id
            WHERE dp.id = $id"
    );
    $stmt->execute();
    if ($stmt->rowCount() === 0) echo ('No rows');
    while ($users = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $users;
    }
    echo json_encode($data);
} else {
    // Si 'type', 'id' o 'token' están ausentes o 'type' no es 'user' en la URL
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Something get wrong']);
    exit();
}
