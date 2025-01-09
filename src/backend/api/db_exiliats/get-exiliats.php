<?php

// Configuración de cabeceras para aceptar JSON y responder JSON
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: https://memoriaterrassa.cat");
header("Access-Control-Allow-Methods: GET");

// Dominio permitido (modifica con tu dominio)
$allowed_origin = "https://memoriaterrassa.cat";

// Verificar el encabezado 'Origin'
if (isset($_SERVER['HTTP_ORIGIN'])) {
    if ($_SERVER['HTTP_ORIGIN'] !== $allowed_origin) {
        http_response_code(403); // Respuesta 403 Forbidden
        echo json_encode(["error" => "Acceso denegado. Origen no permitido."]);
        exit;
    }
}

// Verificar que el método HTTP sea PUT
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405); // Método no permitido
    echo json_encode(["error" => "Método no permitido. Se requiere PUT."]);
    exit;
}


// 1) Llistat afusellats
// ruta GET => "/api/afusellats/get/?type=llistat"
if (isset($_GET['type']) && $_GET['type'] == 'llistat') {
    global $conn;
    $data = array();
    $stmt = $conn->prepare(
        "SELECT a.id, dp.cognom1, dp.cognom2, dp.nom, a.copia_exp, dp.data_naixement, dp.edat, dp.data_defuncio,
            e1.ciutat, e1.comarca, e1.provincia, e1.comunitat, e1.pais, e2.ciutat AS ciutat2, e2.comarca AS comarca2, e2.provincia AS provincia2, e2.comunitat AS comunitat2, e2.pais AS pais2
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
    // ruta GET => "/api/exiliats/get/?type=fitxa&id=35"
} elseif (isset($_GET['type']) && $_GET['type'] == 'fitxa' && isset($_GET['id'])) {
    $id = $_GET['id'];
    global $conn;
    $data = array();
    $stmt = $conn->prepare(
        "SELECT 
            e.residencia,
            e.referencies_arxiu
            FROM db_exiliats AS e
            WHERE e.idPersona = $id"
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
