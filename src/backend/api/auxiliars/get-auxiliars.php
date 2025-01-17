<?php

// DB_DADES PERSONALS
// 1) Llistat municipis
// ruta GET => "/api/auxiliars/get/?type=municipis"
if (isset($_GET['type']) && $_GET['type'] == 'municipis') {
    global $conn;
    $data = array();
    $stmt = $conn->prepare(
        "SELECT m.id, m.ciutat
        FROM aux_dades_municipis AS m
        ORDER BY m.ciutat ASC"
    );
    $stmt->execute();
    if ($stmt->rowCount() === 0) echo ('No rows');
    while ($users = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $users;
    }
    echo json_encode($data);

    // 1) Llistat comarques
    // ruta GET => "/api/auxiliars/get/?type=comarques"
} elseif (isset($_GET['type']) && $_GET['type'] == 'comarques') {
    global $conn;
    $data = array();
    $stmt = $conn->prepare(
        "SELECT c.id, c.comarca
        FROM aux_dades_municipis_comarca AS c
        ORDER BY c.comarca ASC"
    );
    $stmt->execute();
    if ($stmt->rowCount() === 0) echo ('No rows');
    while ($users = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $users;
    }
    echo json_encode($data);

    // 1) Llistat provincies
    // ruta GET => "/api/auxiliars/get/?type=provincies"
} elseif (isset($_GET['type']) && $_GET['type'] == 'provincies') {
    global $conn;
    $data = array();
    $stmt = $conn->prepare(
        "SELECT p.id, p.provincia
        FROM aux_dades_municipis_provincia AS p
        ORDER BY p.provincia ASC"
    );
    $stmt->execute();
    if ($stmt->rowCount() === 0) echo ('No rows');
    while ($users = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $users;
    }
    echo json_encode($data);

    // 1) Llistat comunitats autonomes
    // ruta GET => "/api/auxiliars/get/?type=comunitats"
} elseif (isset($_GET['type']) && $_GET['type'] == 'comunitats') {
    global $conn;
    $data = array();
    $stmt = $conn->prepare(
        "SELECT c.id, c.comunitat
        FROM aux_dades_municipis_comunitat AS c
        ORDER BY c.comunitat ASC"
    );
    $stmt->execute();
    if ($stmt->rowCount() === 0) echo ('No rows');
    while ($users = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $users;
    }
    echo json_encode($data);

    // 1) Llistat estats
    // ruta GET => "/api/auxiliars/get/?type=estats"
} elseif (isset($_GET['type']) && $_GET['type'] == 'estats') {
    global $conn;
    $data = array();
    $stmt = $conn->prepare(
        "SELECT e.id, e.estat
        FROM aux_dades_municipis_estat AS e
        ORDER BY e.estat ASC"
    );
    $stmt->execute();
    if ($stmt->rowCount() === 0) echo ('No rows');
    while ($users = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $users;
    }
    echo json_encode($data);

    // 2) Llistat estudis
    // ruta GET => "/api/auxiliars/get/?type=estudis"
} elseif (isset($_GET['type']) && $_GET['type'] == 'estudis') {
    global $conn;
    $data = array();
    $stmt = $conn->prepare(
        "SELECT e.id, e.estudi_cat
        FROM aux_estudis AS e
        ORDER BY e.estudi_cat ASC"
    );
    $stmt->execute();
    if ($stmt->rowCount() === 0) echo ('No rows');
    while ($users = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $users;
    }
    echo json_encode($data);

    // 3) Llistat oficis
    // ruta GET => "/api/auxiliars/get/?type=oficis"
} elseif (isset($_GET['type']) && $_GET['type'] == 'oficis') {
    global $conn;
    $data = array();
    $stmt = $conn->prepare(
        "SELECT o.id, o.ofici_cat
            FROM aux_oficis AS o
            ORDER BY o.ofici_cat ASC"
    );
    $stmt->execute();
    if ($stmt->rowCount() === 0) echo ('No rows');
    while ($users = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $users;
    }
    echo json_encode($data);


    // 3-b) Llistat sectors
    // ruta GET => "/api/auxiliars/get/?type=sectors_economics"
} elseif (isset($_GET['type']) && $_GET['type'] == 'sectors_economics') {
    global $conn;
    $data = array();
    $stmt = $conn->prepare(
        "SELECT se.id, se.sector_cat
            FROM aux_sector_economic AS se
            ORDER BY se.sector_cat ASC"
    );
    $stmt->execute();
    if ($stmt->rowCount() === 0) echo ('No rows');
    while ($users = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $users;
    }
    echo json_encode($data);

    // 3-c) Llistat sub-sectors economics
    // ruta GET => "/api/auxiliars/get/?type=sub_sectors_economics"
} elseif (isset($_GET['type']) && $_GET['type'] == 'sub_sectors_economics') {
    global $conn;
    $data = array();
    $stmt = $conn->prepare(
        "SELECT sse.id, sse.sub_sector_cat
            FROM aux_sub_sector_economic AS sse
            ORDER BY sse.sub_sector_cat ASC"
    );
    $stmt->execute();
    if ($stmt->rowCount() === 0) echo ('No rows');
    while ($users = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $users;
    }
    echo json_encode($data);

    // 3-d) Llistat càrrecs empresa
    // ruta GET => "/api/auxiliars/get/?type=carrecs_empresa"
} elseif (isset($_GET['type']) && $_GET['type'] == 'carrecs_empresa') {
    global $conn;
    $data = array();
    $stmt = $conn->prepare(
        "SELECT ce.id, ce.carrec_cat
            FROM aux_ofici_carrec AS ce
            ORDER BY ce.carrec_cat ASC"
    );
    $stmt->execute();
    if ($stmt->rowCount() === 0) echo ('No rows');
    while ($users = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $users;
    }
    echo json_encode($data);

    // 4) Llistat estat civil
    // ruta GET => "/api/auxiliars/get/?type=estats_civils"
} elseif (isset($_GET['type']) && $_GET['type'] == 'estats_civils') {
    global $conn;
    $data = array();
    $stmt = $conn->prepare(
        "SELECT ec.id, ec.estat_cat
            FROM aux_estat_civil AS ec
            ORDER BY ec.estat_cat ASC"
    );
    $stmt->execute();
    if ($stmt->rowCount() === 0) echo ('No rows');
    while ($users = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $users;
    }
    echo json_encode($data);

    // 5) Llistat partits politics
    // ruta GET => "/api/auxiliars/get/?type=partits"
} elseif (isset($_GET['type']) && $_GET['type'] == 'partits') {
    global $conn;
    $data = array();
    $stmt = $conn->prepare(
        "SELECT p.id, p.partit_politic
            FROM aux_filiacio_politica AS p
            ORDER BY p.partit_politic ASC"
    );
    $stmt->execute();
    if ($stmt->rowCount() === 0) echo ('No rows');
    while ($users = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $users;
    }
    echo json_encode($data);

    // 6) Llistat sindicats
    // ruta GET => "/api/auxiliars/get/?type=sindicats"
} elseif (isset($_GET['type']) && $_GET['type'] == 'sindicats') {
    global $conn;
    $data = array();
    $stmt = $conn->prepare(
        "SELECT s.id, s.sindicat
            FROM aux_filiacio_sindical AS s
            ORDER BY s.sindicat ASC"
    );
    $stmt->execute();
    if ($stmt->rowCount() === 0) echo ('No rows');
    while ($users = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $users;
    }
    echo json_encode($data);

    // 7) Llistat espais
    // ruta GET => "/api/auxiliars/get/?type=espais"
} elseif (isset($_GET['type']) && $_GET['type'] == 'espais') {
    global $conn;
    $data = array();
    $stmt = $conn->prepare(
        "SELECT esp.id, esp.espai_cat
                FROM aux_espai AS esp
                ORDER BY esp.espai_cat ASC"
    );
    $stmt->execute();
    if ($stmt->rowCount() === 0) echo ('No rows');
    while ($users = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $users;
    }
    echo json_encode($data);

    // 10) Tipologia espais
    // ruta GET => "/api/auxiliars/get/?type=tipologia_espais"
} elseif (isset($_GET['type']) && $_GET['type'] == 'tipologia_espais') {
    global $conn;
    $data = array();
    $stmt = $conn->prepare(
        "SELECT tipologia.id, tipologia.tipologia_espai_ca
                FROM aux_tipologia_espais AS tipologia
                ORDER BY tipologia.tipologia_espai_ca ASC"
    );
    $stmt->execute();
    if ($stmt->rowCount() === 0) echo ('No rows');
    while ($users = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $users;
    }
    echo json_encode($data);

    // 11) Causa defuncio
    // ruta GET => "/api/auxiliars/get/?type=causa_defuncio"
} elseif (isset($_GET['type']) && $_GET['type'] == 'causa_defuncio') {
    global $conn;
    $data = array();
    $stmt = $conn->prepare(
        "SELECT c.id, c.causa_defuncio_ca
                FROM aux_causa_defuncio AS c
                ORDER BY c.causa_defuncio_ca ASC"
    );
    $stmt->execute();
    if ($stmt->rowCount() === 0) echo ('No rows');
    while ($users = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $users;
    }
    echo json_encode($data);

    // 12) Autors fitxes
    // ruta GET => "/api/auxiliars/get/?type=autors_fitxa"
} elseif (isset($_GET['type']) && $_GET['type'] == 'autors_fitxa') {
    global $conn;
    $data = array();
    $stmt = $conn->prepare(
        "SELECT u.id, u.nom
                FROM auth_users AS u
                ORDER BY u.nom ASC"
    );
    $stmt->execute();
    if ($stmt->rowCount() === 0) echo ('No rows');
    while ($users = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $users;
    }
    echo json_encode($data);

    // DB_AFUSELLATS
    // 7) Llistat procediments judicials
    // ruta GET => "/api/auxiliars/get/?type=procediments"
} elseif (isset($_GET['type']) && $_GET['type'] == 'procediments') {
    global $conn;
    $data = array();
    $stmt = $conn->prepare(
        "SELECT pj.id, pj.procediment_cat
                    FROM aux_procediment_judicial AS pj
                    ORDER BY pj.procediment_cat ASC"
    );
    $stmt->execute();
    if ($stmt->rowCount() === 0) echo ('No rows');
    while ($users = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $users;
    }
    echo json_encode($data);

    // 7) Llistat jutjats
    // ruta GET => "/api/auxiliars/get/?type=jutjats"
} elseif (isset($_GET['type']) && $_GET['type'] == 'jutjats') {
    global $conn;
    $data = array();
    $stmt = $conn->prepare(
        "SELECT j.id, j.jutjat_cat
                    FROM aux_jutjats AS j
                    ORDER BY j.jutjat_cat ASC"
    );
    $stmt->execute();
    if ($stmt->rowCount() === 0) echo ('No rows');
    while ($users = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $users;
    }
    echo json_encode($data);

    // 8) Llistat tipus acusacions
    // ruta GET => "/api/auxiliars/get/?type=acusacions"
} elseif (isset($_GET['type']) && $_GET['type'] == 'acusacions') {
    global $conn;
    $data = array();
    $stmt = $conn->prepare(
        "SELECT sa.id, sa.acusacio_cat
                    FROM aux_acusacions AS sa
                    ORDER BY sa.acusacio_cat ASC"
    );
    $stmt->execute();
    if ($stmt->rowCount() === 0) echo ('No rows');
    while ($users = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $users;
    }
    echo json_encode($data);

    // 9) Llistat sentencies
    // ruta GET => "/api/auxiliars/get/?type=sentencies"
} elseif (isset($_GET['type']) && $_GET['type'] == 'sentencies') {
    global $conn;
    $data = array();
    $stmt = $conn->prepare(
        "SELECT sen.id, sen.sentencia_cat
                    FROM aux_sentencies AS sen
                    ORDER BY sen.sentencia_cat ASC"
    );
    $stmt->execute();
    if ($stmt->rowCount() === 0) echo ('No rows');
    while ($users = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $users;
    }
    echo json_encode($data);

    // DB COST HUMA
    // 1) LListat condició civil/militar
    // ruta GET => "/api/auxiliars/get/?type=condicio_civil_militar"
} elseif (isset($_GET['type']) && $_GET['type'] == 'condicio_civil_militar') {
    global $conn;
    $data = array();
    $stmt = $conn->prepare(
        "SELECT c.id, c.condicio_ca
        FROM aux_condicio AS c
        ORDER BY c.condicio_ca ASC"
    );
    $stmt->execute();
    if ($stmt->rowCount() === 0) echo ('No rows');
    while ($users = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $users;
    }
    echo json_encode($data);

    // 2) Llistat bàndols guerra
    // ruta GET => "/api/auxiliars/get/?type=bandols_guerra"
} elseif (isset($_GET['type']) && $_GET['type'] == 'bandols_guerra') {
    global $conn;
    $data = array();
    $stmt = $conn->prepare(
        "SELECT b.id, b.bandol_ca
        FROM aux_bandol AS b
        ORDER BY b.bandol_ca ASC"
    );
    $stmt->execute();
    if ($stmt->rowCount() === 0) echo ('No rows');
    while ($users = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $users;
    }
    echo json_encode($data);

    // 2) Llistat cossos militats
    // ruta GET => "/api/auxiliars/get/?type=cossos_militars"
} elseif (isset($_GET['type']) && $_GET['type'] == 'cossos_militars') {
    global $conn;
    $data = array();
    $stmt = $conn->prepare(
        "SELECT c.id, c.cos_militar_ca 
        FROM aux_cossos_militars AS c
        ORDER BY c.cos_militar_ca ASC"
    );
    $stmt->execute();
    if ($stmt->rowCount() === 0) echo ('No rows');
    while ($users = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $users;
    }
    echo json_encode($data);

    // DB_DEPORTATS AUXILIARS
    // 1) Llistat situacions deportats
    // ruta GET => "/api/auxiliars/get/?type=situacions_deportats"
} elseif (isset($_GET['type']) && $_GET['type'] == 'situacions_deportats') {
    global $conn;
    $data = array();
    $stmt = $conn->prepare(
        "SELECT s.id, s.situacio_ca 
        FROM aux_situacions_deportats AS s
        ORDER BY s.situacio_ca ASC"
    );
    $stmt->execute();
    if ($stmt->rowCount() === 0) echo ('No rows');
    while ($users = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $users;
    }
    echo json_encode($data);

    // 2) Llistat tipus de presons deportats
    // ruta GET => "/api/auxiliars/get/?type=tipus_presons"
} elseif (isset($_GET['type']) && $_GET['type'] == 'tipus_presons') {
    global $conn;
    $data = array();
    $stmt = $conn->prepare(
        "SELECT t.id, t.tipus_preso_ca
        FROM aux_tipus_presons AS t
        ORDER BY t.tipus_preso_ca ASC"
    );
    $stmt->execute();
    if ($stmt->rowCount() === 0) echo ('No rows');
    while ($users = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $users;
    }
    echo json_encode($data);

    // DB_FAMILIARS
    // 1) Llistat relacions de parentiu
    // ruta GET => "/api/auxiliars/get/?type=relacions_parentiu"
} elseif (isset($_GET['type']) && $_GET['type'] == 'relacions_parentiu') {
    global $conn;
    $data = array();
    $stmt = $conn->prepare(
        "SELECT r.id, r.relacio_parentiu
        FROM aux_familiars_relacio AS r
        ORDER BY r.relacio_parentiu ASC"
    );
    $stmt->execute();
    if ($stmt->rowCount() === 0) echo ('No rows');
    while ($users = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $users;
    }
    echo json_encode($data);

    // 2) Llistat complert represaliats 
    // ruta GET => "/api/auxiliars/get/?type=llistat_complert_represaliats"
} elseif (isset($_GET['type']) && $_GET['type'] == 'llistat_complert_represaliats') {
    global $conn;
    $data = array();
    $stmt = $conn->prepare(
        "SELECT dp.id, 
       dp.nom, 
       dp.cognom1, 
       dp.cognom2, 
       CONCAT(dp.cognom1, ' ', dp.cognom2, ', ', dp.nom) AS nom_complert
        FROM db_dades_personals AS dp
        ORDER BY dp.cognom1 ASC"
    );
    $stmt->execute();
    if ($stmt->rowCount() === 0) echo ('No rows');
    while ($users = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $users;
    }
    echo json_encode($data);

    // DB_MORTS CIVILS
    // 1) Llistat relacions de parentiu
    // ruta GET => "/api/auxiliars/get/?type=llocs_bombardeig"
} elseif (isset($_GET['type']) && $_GET['type'] == 'llocs_bombardeig') {
    global $conn;
    $data = array();
    $stmt = $conn->prepare(
        "SELECT l.id, l.lloc_bombardeig_ca
        FROM aux_llocs_bombardeig AS l
        ORDER BY l.lloc_bombardeig_ca ASC"
    );
    $stmt->execute();
    if ($stmt->rowCount() === 0) echo ('No rows');
    while ($users = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $users;
    }
    echo json_encode($data);

    // DB_FONTS DOCUMENTALS
    // BIBLIOGRAFIA
    // 1) Llistat relacions de parentiu
    // ruta GET => "/api/auxiliars/get/?type=llistat_llibres_bibliografia"
} elseif (isset($_GET['type']) && $_GET['type'] == 'llistat_llibres_bibliografia') {
    global $conn;
    $data = array();
    $stmt = $conn->prepare(
        "SELECT l.id, l.llibre, l.autor, l.any,
       CONCAT(l.autor, ', ', SUBSTRING(l.llibre, 1, 40), '...', ', ', l.any) AS llibre
        FROM aux_bibliografia_llibre_detalls AS l
        ORDER BY l.llibre ASC"
    );
    $stmt->execute();
    if ($stmt->rowCount() === 0) echo ('No rows');
    while ($users = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $users;
    }
    echo json_encode($data);

    // 1) Llistat relacions de parentiu
    // ruta GET => "/api/auxiliars/get/?type=llistat_arxivistica"
} elseif (isset($_GET['type']) && $_GET['type'] == 'llistat_arxivistica') {
    global $conn;
    $data = array();
    $stmt = $conn->prepare(
        "SELECT l.id, 
           CONCAT(l.codi, ', ', SUBSTRING(l.arxiu, 1, 40), '...') AS arxiu
        FROM aux_bibliografia_arxius_codis AS l
        ORDER BY l.arxiu ASC"
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
