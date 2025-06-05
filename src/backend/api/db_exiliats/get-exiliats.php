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

$slug = $routeParams[0];

// GET : Pagina informacio fitxa exiliat
// URL: /api/exiliats/get/fitxaRepresaliat?id=${id}
if ($slug === 'fitxaRepresaliat') {
    $id = $_GET['id'];

    $query = "SELECT 
        e.id,
        e.data_exili,
        m1.ciutat AS lloc_partida,
        m2.ciutat AS lloc_pas_frontera,
        e.amb_qui_passa_frontera,
        m3.ciutat AS primer_desti_exili,
        e.primer_desti_data,
        te.tipologia_espai_ca AS tipologia_primer_desti,
        e.dades_lloc_primer_desti,
        e.periple_recorregut,
        e.deportat,
        m4.ciutat AS ultim_desti_exili,
        te2.tipologia_espai_ca AS tipologia_ultim_desti,
        e.participacio_resistencia,
        e.dades_resistencia,
        e.activitat_politica_exili,
        e.activitat_sindical_exili,
        e.situacio_legal_espanya
        FROM db_exiliats AS e
        LEFT JOIN aux_dades_municipis AS m1 ON e.lloc_partida = m1.id
        LEFT JOIN aux_dades_municipis AS m2 ON e.lloc_pas_frontera = m2.id
        LEFT JOIN aux_dades_municipis AS m3 ON e.primer_desti_exili = m3.id
        LEFT JOIN aux_tipologia_espais AS te ON e.tipologia_primer_desti = te.id
        LEFT JOIN aux_dades_municipis AS m4 ON e.ultim_desti_exili = m4.id
        LEFT JOIN aux_tipologia_espais AS te2 ON e.tipologia_ultim_desti = te2.id
        WHERE e.idPersona = $id";

    $result = getData($query, ['idRepresaliat' => $id], false);
    echo json_encode($result);

    // 2) Fitxa repressió exili
    // ruta GET => "/api/exiliats/get/fitxaRepressio?id=35"
} else if ($slug === "fitxaRepressio") {
    $id = $_GET['id'] ?? null;

    $query = "SELECT
        e.id,
        e.data_exili,
        e.lloc_partida,
        e.lloc_pas_frontera,
        e.amb_qui_passa_frontera,
        e.primer_desti_exili,
        e.primer_desti_data,
        e.tipologia_primer_desti,
        e.dades_lloc_primer_desti,
        e.periple_recorregut,
        e.deportat,
        e.ultim_desti_exili,
        e.tipologia_ultim_desti,
        e.participacio_resistencia,
        e.dades_resistencia,
        e.activitat_politica_exili,
        e.activitat_sindical_exili,
        e.situacio_legal_espanya
        FROM db_exiliats AS e
        WHERE e.idPersona = :idPersona";

    $result = getData($query, ['idPersona' => $id], true);
    echo json_encode($result);
} else {
    // Si 'type', 'id' o 'token' están ausentes o 'type' no es 'user' en la URL
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Something get wrong']);
    exit();
}
