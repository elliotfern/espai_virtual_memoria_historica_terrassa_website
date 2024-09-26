<?php

// Verificar si se proporciona un token en el encabezado de autorización
$headers = apache_request_headers();

if (isset($headers['Authorization'])) {
    $token = str_replace('Bearer ', '', $headers['Authorization']);

    // Verificar el token aquí según tus requerimientos
    if (verificarToken($token)) {
        // Token válido, puedes continuar con el código para obtener los datos del usuario

        // 1) Llistat afusellats
        // ruta GET => "/api/represaliats/get/?type=tots"
        if (isset($_GET['type']) && $_GET['type'] == 'tots' ) {
            global $conn;
            $data = array();
            $stmt = $conn->prepare(
            "SELECT a.id, a.cognom1, a.cognom2, a.nom, a.data_naixement, a.data_defuncio, e1.ciutat, e1.comarca, e1.provincia, e1.comunitat, e1.pais, a.categoria, e2.ciutat AS ciutat2, e2.comarca AS comarca2, e2.provincia AS provincia2, e2.comunitat AS comunitat2, e2.pais AS pais2
            FROM db_dades_personals AS a
            LEFT JOIN aux_dades_municipis AS e1 ON a.municipi_naixement = e1.id
            LEFT JOIN aux_dades_municipis AS e2 ON a.municipi_defuncio = e2.id
            ORDER BY a.cognom1 ASC");
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