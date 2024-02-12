<?php

// Verificar si se proporciona un token en el encabezado de autorización
$headers = apache_request_headers();

if (isset($headers['Authorization'])) {
    $token = str_replace('Bearer ', '', $headers['Authorization']);

    // Verificar el token aquí según tus requerimientos
    if (verificarToken($token)) {
        // Token válido, puedes continuar con el código para obtener los datos del usuario

        // 1) Llistat afusellats
        // ruta GET => "/api/afusellats/get/?type=llistat"
        if (isset($_GET['type']) && $_GET['type'] == 'llistat' ) {
            global $conn;
            $data = array();
            $stmt = $conn->prepare(
            "SELECT 	
            a.id, a.cognoms, nom, a.copia_exp, a.data_naixement, a.edat, e.espai, a.data_execucio
            FROM db_afusellats AS a
            INNER JOIN aux_espai AS e ON a.lloc_execucio_enterrament = e.id
            ORDER BY a.cognoms ASC");
            $stmt->execute();
            if($stmt->rowCount() === 0) echo ('No rows');
                while($users = $stmt->fetch(PDO::FETCH_ASSOC) ){
                    $data[] = $users;
                }
            echo json_encode($data);
        
        // 2) Pagina informacio fitxa afusellat
        // ruta GET => "/api/afusellats/get/?type=fitxa&id=35"
        } elseif (isset($_GET['type']) && $_GET['type'] == 'fitxa' && isset($_GET['id'])) {
            $id = $_GET['id'];
            global $conn;
            $data = array();
            $stmt = $conn->prepare(
            "SELECT a.id, a.cognoms, a.nom, a.copia_exp, a.data_naixement, a.edat, m1.id AS ciutat_naixement_id, m1.ciutat AS ciutat_naixement, m2.ciutat AS ciutat_residencia, m2.id AS ciutat_residencia_id, a.adreca, ec.estat_cat AS estat_civil, ec.id AS estat_civil_id, a.esposa, a.fills_num, a.fills_noms, es.estudi_cat, es.id AS estudis_id, o.ofici_cat, o.id AS ofici_id, a.empresa, fp.partit_politic, fp.id AS partit_politic_id, fs.sindicat, fs.id AS sindicat_id, pj.procediment_cat, a.num_causa, a.data_inici_proces, a.jutge_instructor, a.secretari_instructor, a.jutjat, a.any_inicial, a.consell_guerra_data, m3.ciutat AS ciutat_consellGuerra, a.president_tribunal, a.defensor, a.fiscal, a.ponent, a.tribunal_vocals, a.acusacio, a.acusacio_2, a.testimoni_acusacio, a.sentencia_data, a.sentencia, a.data_sentencia, a.data_execucio, m4.ciutat AS ciutat_enterrament, e.espai, a.ref_num_arxiu, a.font_1, a.font_2, a.familiars, a.observacions, a.biografia
            FROM db_afusellats AS a
            LEFT JOIN aux_espai AS e ON a.lloc_execucio_enterrament = e.id
            LEFT JOIN aux_dades_municipis AS m1 ON a.municipi_naixement = m1.id
            LEFT JOIN aux_dades_municipis AS m2 ON a.municipi_residencia = m2.id
            LEFT JOIN aux_dades_municipis AS m3 ON a.lloc_consell_guerra = m3.id
            LEFT JOIN aux_dades_municipis AS m4 ON a.enterrament_lloc = m4.id
            LEFT JOIN aux_filiacio_politica AS fp ON a.filiciacio_politica = fp.id
            LEFT JOIN aux_estudis AS es ON a.estudis = es.id
            LEFT JOIN aux_oficis AS o ON a.ofici = o.id 
            LEFT JOIN aux_filiacio_sindical AS fs ON a.filiacio_sindical = fs.id
            LEFT JOIN aux_procediment_judicial AS pj ON a.procediment = pj.id
            LEFT JOIN aux_estat_civil as ec ON a.estat_civil = ec.id
            WHERE a.id = $id");
            $stmt->execute();
            if($stmt->rowCount() === 0) echo ('No rows');
                while($users = $stmt->fetch(PDO::FETCH_ASSOC) ){
                    $data[] = $users;
                }
            echo json_encode($data);

        } else {
            // Si 'type', 'id' o 'token' están ausentes o 'type' no es 'user' en la URL
            header('HTTP/1.1 403 Forbidden');
            echo json_encode(['error' => 'Something get wrong']);
            exit();
        }

    } else {
    // Token no válido
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Invalid token']);
    exit();
    }

} else {
// No se proporcionó un token
header('HTTP/1.1 403 Forbidden');
echo json_encode(['error' => 'Access not allowed']);
exit();
}