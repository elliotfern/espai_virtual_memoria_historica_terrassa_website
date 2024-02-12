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