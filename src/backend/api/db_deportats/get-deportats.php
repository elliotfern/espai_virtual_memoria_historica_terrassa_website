<?php

use App\Config\DatabaseConnection;

$conn = DatabaseConnection::getConnection();

if (!$conn) {
    die("No se pudo establecer conexión a la base de datos.");
}

$slug = $routeParams[0];

// Configuración de cabeceras para aceptar JSON y responder JSON
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: https://memoriaterrassa.cat");
header("Access-Control-Allow-Methods: GET");

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

// GET : llistat complet deportats
// URL: https://memoriaterrassa.cat/api/api/deportats/get/llistatComplet
if ($slug === "llistatComplet") {

    $query = "SELECT a.id, dp.cognom1, dp.cognom2, dp.nom, a.copia_exp, dp.data_naixement, dp.edat, dp.data_defuncio,
            e1.ciutat, e1.comarca, e1.provincia, e1.comunitat, e1.pais, e2.ciutat AS ciutat2, e2.comarca AS comarca2, e2.provincia AS provincia2, e2.comunitat AS comunitat2, e2.pais AS pais2, dp.categoria
            FROM db_afusellats AS a
            LEFT JOIN db_dades_personals AS dp ON a.idPersona = dp.id
            LEFT JOIN aux_dades_municipis AS e1 ON dp.municipi_naixement = e1.id
            LEFT JOIN aux_dades_municipis AS e2 ON dp.municipi_defuncio = e2.id
            ORDER BY dp.cognom1 ASC";

    $result = getData($query);
    echo json_encode($result);

    // 2) Pagina informacio fitxa deportat
    // ruta GET => "/api/deportats/get/fitxaRepresaliat?id=35"
} else if ($slug === "fitxaRepresaliat") {
    $id = $_GET['id'] ?? null;

    $query = "SELECT 
             	d.id,
                d.idPersona,
                d.data_alliberament,
                d.preso_nom,
                d.preso_data_sortida,
                d.preso_num_matricula,
                d.deportacio_nom_camp,
                d.deportacio_data_entrada,
                d.deportacio_num_matricula,
                d.deportacio_nom_subcamp,
                d.deportacio_data_entrada_subcamp,
                d.deportacio_nom_matricula_subcamp,
                sd.situacio_ca AS situacio,
                m1.ciutat AS ciutat_mort_alliberament,
                m2.ciutat AS preso_localitat,
                tp.tipus_preso_ca AS preso_tipus
            FROM db_deportats AS d
            LEFT JOIN aux_situacions_deportats AS sd ON d.situacio = sd.id
            LEFT JOIN aux_dades_municipis AS m1 ON d.lloc_mort_alliberament = m1.id
            LEFT JOIN aux_dades_municipis AS m2 ON d.preso_localitat = m2.id
            LEFT JOIN aux_tipus_presons AS tp ON d.preso_tipus = tp.id
            WHERE a.idPersona = :idPersona";

    $result = getData($query, ['idPersona' => $id], true);
    echo json_encode($result);

    // 3) Pagina fitxa repressió deportat
    // ruta GET => "/api/deportats/get/fitxaRepressio?id=35"
} else if ($slug === "fitxaRepressio") {
    $id = $_GET['id'] ?? null;

    $query = "SELECT 
            id,
            idPersona,
            situacio,
            data_alliberament,
            lloc_mort_alliberament,
            preso_tipus,
            preso_nom,
            preso_data_sortida,
            preso_localitat,
            preso_num_matricula,
            deportacio_nom_camp,
            deportacio_data_entrada,
            deportacio_num_matricula,
            deportacio_nom_subcamp,
            deportacio_data_entrada_subcamp,
            deportacio_nom_matricula_subcamp
            FROM db_deportats
            WHERE idPersona = :idPersona";

    $result = getData($query, ['idPersona' => $id], true);
    echo json_encode($result);
} else {
    // Si 'type', 'id' o 'token' están ausentes o 'type' no es 'user' en la URL
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Something get wrong']);
    exit();
}
