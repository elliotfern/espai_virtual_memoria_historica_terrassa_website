<?php

use App\Config\Database;
use App\Utils\Response;
use App\Utils\MissatgesAPI;
use App\Config\DatabaseConnection;

$slug = $routeParams[0];

// Configuración de cabeceras para aceptar JSON y responder JSON
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET");

if ($slug === "slug") {
    // Obtenemos conexión PDO
    $conn = DatabaseConnection::getConnection();
    if (!$conn) {
        http_response_code(500);
        echo json_encode(['error' => 'No se pudo establecer conexión a la base de datos.']);
        exit();
    }

    // Función para generar slug
    function generar_slug($nom, $cognom1, $cognom2)
    {
        $texto = trim("$nom $cognom1 $cognom2");
        $slug = strtolower($texto);
        $slug = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $slug);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);  // Elimina caracteres especiales
        $slug = preg_replace('/[\s-]+/', ' ', $slug);       // Unifica espacios y guiones
        $slug = preg_replace('/\s/', '-', $slug);           // Sustituye espacios por guiones
        return trim($slug, '-');
    }

    try {
        // Selecciona solo registros con ID entre 8107 y 9581
        $stmt = $conn->query("SELECT id, nom, cognom1, cognom2 FROM db_dades_personals WHERE id BETWEEN 8107 AND 9581");

        $updateStmt = $conn->prepare("UPDATE db_dades_personals SET slug = :slug WHERE id = :id");

        $updated = 0;

        foreach ($stmt as $row) {
            $slug = generar_slug($row['nom'], $row['cognom1'], $row['cognom2']);
            $updateStmt->execute([
                ':slug' => $slug,
                ':id'   => $row['id']
            ]);
            $updated++;
        }

        echo json_encode([
            'status' => 'success',
            'message' => "Se actualizaron $updated registros con sus slugs."
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Error en la base de datos: ' . $e->getMessage()
        ]);
    }
}
