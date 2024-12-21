<?php

// Verificar si se proporciona un token en el encabezado de autorización
$headers = apache_request_headers();

if (isset($headers['Authorization'])) {
    $token = str_replace('Bearer ', '', $headers['Authorization']);

    // Verificar el token aquí según tus requerimientos
    if (verificarToken($token)) {
        // Token válido, puedes continuar con el código para obtener los datos del usuario

        // 1) Llistat municipis
        // ruta GET => "/api/auxiliars/get/?type=municipis"
        if (isset($_GET['type']) && $_GET['type'] == 'municipis' ) {
            global $conn;
            $data = array();
            $stmt = $conn->prepare(
            "SELECT m.id, m.ciutat
            FROM aux_dades_municipis AS m
            ORDER BY m.ciutat ASC");
            $stmt->execute();
            if($stmt->rowCount() === 0) echo ('No rows');
                while($users = $stmt->fetch(PDO::FETCH_ASSOC) ){
                    $data[] = $users;
                }
            echo json_encode($data);
        
        // 2) Llistat estudis
        // ruta GET => "/api/auxiliars/get/?type=estudis"
        } elseif (isset($_GET['type']) && $_GET['type'] == 'estudis' ) {
            global $conn;
            $data = array();
            $stmt = $conn->prepare(
            "SELECT e.id, e.estudi_cat
            FROM aux_estudis AS e
            ORDER BY e.estudi_cat ASC");
            $stmt->execute();
            if($stmt->rowCount() === 0) echo ('No rows');
                while($users = $stmt->fetch(PDO::FETCH_ASSOC) ){
                    $data[] = $users;
                }
            echo json_encode($data);
        
        // 3) Llistat oficis
        // ruta GET => "/api/auxiliars/get/?type=oficis"
        } elseif (isset($_GET['type']) && $_GET['type'] == 'oficis' ) {
            global $conn;
            $data = array();
            $stmt = $conn->prepare(
                "SELECT o.id, o.ofici_cat
                FROM aux_oficis AS o
                ORDER BY o.ofici_cat ASC");
            $stmt->execute();
            if($stmt->rowCount() === 0) echo ('No rows');
                while($users = $stmt->fetch(PDO::FETCH_ASSOC) ){
                    $data[] = $users;
                }
            echo json_encode($data);

        // 4) Llistat estat civil
        // ruta GET => "/api/auxiliars/get/?type=estats"
        } elseif (isset($_GET['type']) && $_GET['type'] == 'estats' ) {
            global $conn;
            $data = array();
            $stmt = $conn->prepare(
                "SELECT ec.id, ec.estat_cat
                FROM aux_estat_civil AS ec
                ORDER BY ec.estat_cat ASC");
            $stmt->execute();
            if($stmt->rowCount() === 0) echo ('No rows');
                while($users = $stmt->fetch(PDO::FETCH_ASSOC) ){
                    $data[] = $users;
                }
            echo json_encode($data);
        
        // 5) Llistat partits politics
        // ruta GET => "/api/auxiliars/get/?type=partits"
        } elseif (isset($_GET['type']) && $_GET['type'] == 'partits' ) {
            global $conn;
            $data = array();
            $stmt = $conn->prepare(
                "SELECT p.id, p.partit_politic
                FROM aux_filiacio_politica AS p
                ORDER BY p.partit_politic ASC");
            $stmt->execute();
            if ($stmt->rowCount() === 0) echo ('No rows');
                while($users = $stmt->fetch(PDO::FETCH_ASSOC) ){
                    $data[] = $users;
            }
            echo json_encode($data);
        
        // 6) Llistat sindicats
        // ruta GET => "/api/auxiliars/get/?type=sindicats"
        } elseif (isset($_GET['type']) && $_GET['type'] == 'sindicats' ) {
            global $conn;
            $data = array();
            $stmt = $conn->prepare(
                "SELECT s.id, s.sindicat
                FROM aux_filiacio_sindical AS s
                ORDER BY s.sindicat ASC");
            $stmt->execute();
            if ($stmt->rowCount() === 0) echo ('No rows');
                while($users = $stmt->fetch(PDO::FETCH_ASSOC) ){
                    $data[] = $users;
            }
            echo json_encode($data);

        // 7) Llistat procediments judicials
        // ruta GET => "/api/auxiliars/get/?type=procediments"
        } elseif (isset($_GET['type']) && $_GET['type'] == 'procediments' ) {
            global $conn;
            $data = array();
            $stmt = $conn->prepare(
                "SELECT pj.id, pj.procediment_cat
                FROM aux_procediment_judicial AS pj
                ORDER BY pj.procediment_cat ASC");
            $stmt->execute();
            if ($stmt->rowCount() === 0) echo ('No rows');
                while($users = $stmt->fetch(PDO::FETCH_ASSOC) ){
                    $data[] = $users;
            }
            echo json_encode($data);
        
        // 7) Llistat jutjats
        // ruta GET => "/api/auxiliars/get/?type=jutjats"
        } elseif (isset($_GET['type']) && $_GET['type'] == 'jutjats' ) {
            global $conn;
            $data = array();
            $stmt = $conn->prepare(
                "SELECT j.id, j.jutjat_cat
                FROM aux_jutjats AS j
                ORDER BY j.jutjat_cat ASC");
            $stmt->execute();
            if ($stmt->rowCount() === 0) echo ('No rows');
                while($users = $stmt->fetch(PDO::FETCH_ASSOC) ){
                    $data[] = $users;
            }
            echo json_encode($data);

        // 8) Llistat tipus acusacions
        // ruta GET => "/api/auxiliars/get/?type=acusacions"
        } elseif (isset($_GET['type']) && $_GET['type'] == 'acusacions' ) {
            global $conn;
            $data = array();
            $stmt = $conn->prepare(
                "SELECT sa.id, sa.acusacio_cat
                FROM aux_acusacions AS sa
                ORDER BY sa.acusacio_cat ASC");
            $stmt->execute();
            if ($stmt->rowCount() === 0) echo ('No rows');
                while($users = $stmt->fetch(PDO::FETCH_ASSOC) ){
                    $data[] = $users;
            }
            echo json_encode($data);
        
        // 9) Llistat sentencies
        // ruta GET => "/api/auxiliars/get/?type=sentencies"
        } elseif (isset($_GET['type']) && $_GET['type'] == 'sentencies' ) {
            global $conn;
            $data = array();
            $stmt = $conn->prepare(
                "SELECT sen.id, sen.sentencia_cat
                FROM aux_sentencies AS sen
                ORDER BY sen.sentencia_cat ASC");
            $stmt->execute();
            if ($stmt->rowCount() === 0) echo ('No rows');
                while($users = $stmt->fetch(PDO::FETCH_ASSOC) ){
                    $data[] = $users;
            }
            echo json_encode($data);

        // 9) Llistat espais
        // ruta GET => "/api/auxiliars/get/?type=espais"
        } elseif (isset($_GET['type']) && $_GET['type'] == 'espais' ) {
            global $conn;
            $data = array();
            $stmt = $conn->prepare(
                "SELECT esp.id, esp.espai_cat
                FROM aux_espai AS esp
                ORDER BY esp.espai_cat ASC");
            $stmt->execute();
            if ($stmt->rowCount() === 0) echo ('No rows');
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