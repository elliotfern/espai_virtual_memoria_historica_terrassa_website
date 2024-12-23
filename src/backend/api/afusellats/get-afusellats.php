<?php

// Configuración de cabeceras para aceptar JSON y responder JSON
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: https://memoriaterrassa.cat");
header("Access-Control-Allow-Methods: GET");

// 1) Llistat afusellats
// ruta GET => "/api/afusellats/get/?type=tots"
if (isset($_GET['type']) && $_GET['type'] == 'tots') {
    global $conn;
    $data = array();
    /** @var PDO $conn */
    $stmt = $conn->prepare(
        "SELECT a.id, dp.cognom1, dp.cognom2, dp.nom, a.copia_exp, dp.data_naixement, dp.edat, dp.data_defuncio,
            e1.ciutat, e1.comarca, e1.provincia, e1.comunitat, e1.pais, e2.ciutat AS ciutat2, e2.comarca AS comarca2, e2.provincia AS provincia2, e2.comunitat AS comunitat2, e2.pais AS pais2, dp.categoria
            FROM db_afusellats AS a
            LEFT JOIN db_dades_personals AS dp ON a.idPersona = dp.id
            LEFT JOIN aux_dades_municipis AS e1 ON dp.municipi_naixement = e1.id
            LEFT JOIN aux_dades_municipis AS e2 ON dp.municipi_defuncio = e2.id
            ORDER BY dp.cognom1 ASC"
    );
    $stmt->execute();
    if ($stmt->rowCount() === 0) echo ('No rows');
    while ($users = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $users;
    }
    echo json_encode($data);

    // 2) Pagina informacio fitxa afusellat
    // ruta GET => "/api/afusellats/get/?type=fitxa&id=35"
} elseif (isset($_GET['type']) && $_GET['type'] == 'fitxa' && isset($_GET['id'])) {
    $id = $_GET['id'];
    global $conn;
    $data = array();
    /** @var PDO $conn */
    $stmt = $conn->prepare(
        "SELECT 
            pj.procediment_cat, 
            pj.id AS procediment_id, 
            a.num_causa, 
            a.data_inici_proces, 
            a.jutge_instructor, 
            a.secretari_instructor, 
            j.jutjat_cat AS jutjat, 
            j.id AS jutjat_id, 
            a.any_inicial, 
            a.consell_guerra_data, 
            a.president_tribunal, 
            a.defensor, 
            a.fiscal, 
            a.ponent, 
            a.tribunal_vocals, 
            acu.acusacio_cat AS acusacio, 
            acu.id AS acusacio_id, 
            acu2.acusacio_cat AS acusacio2, 
            acu2.id AS acusacio_id2, 
            a.testimoni_acusacio, 
            a.sentencia_data, 
            sen.sentencia_cat AS sentencia, 
            sen.id AS sentencia_id, 
            a.data_sentencia, 
            a.data_execucio,
            a.ref_num_arxiu, 
            a.font_1, 
            a.font_2, 
            a.familiars, 
            a.observacions
            FROM db_afusellats AS a
            LEFT JOIN aux_procediment_judicial AS pj ON a.procediment = pj.id
            LEFT JOIN aux_jutjats as j ON a.jutjat = j.id
            LEFT JOIN aux_acusacions AS acu ON a.acusacio = acu.id
            LEFT JOIN aux_acusacions AS acu2 ON a.acusacio_2 = acu2.id
            LEFT JOIN aux_sentencies AS sen ON a.sentencia = sen.id
            WHERE a.idPersona = $id"
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
