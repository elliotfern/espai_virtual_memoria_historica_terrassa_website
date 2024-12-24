<?php

// 1) Informacion usuario
// ruta => "/api/auth/?type=user&id=1"
if (isset($_GET['type']) && $_GET['type'] === 'user' && isset($_GET['id'])) {

    // Asigna valores a las variables después de verificar que no son null
    $id = $_GET['id'];

    global $conn;
    $data = array();
    /** @var PDO $conn */
    $stmt = $conn->prepare(
        "SELECT u.nom
                    FROM auth_users AS u
                    WHERE u.id=?"
    );
    $stmt->execute([$id]);

    if ($stmt->rowCount() === 0) {
        echo json_encode(null);  // Devuelve un objeto JSON nulo si no hay resultados
    } else {
        // Solo obtenemos la primera fila ya que parece ser una búsqueda por ID
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($row);  // Codifica la fila como un objeto JSON
    }
} else {
    // Si 'type', 'id' o 'token' están ausentes o 'type' no es 'user' en la URL
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Something get wrong']);
    exit();
}
