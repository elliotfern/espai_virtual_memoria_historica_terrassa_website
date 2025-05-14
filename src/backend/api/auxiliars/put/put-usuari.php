<?php

// Configuración de cabeceras para aceptar JSON y responder JSON
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET");

// Definir el dominio permitido
$allowedOrigin = "https://memoriaterrassa.cat";

// Llamar a la función para verificar el referer
checkReferer($allowedOrigin);

// Verificar que el método de la solicitud sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

$inputData = file_get_contents('php://input');
$data = json_decode($inputData, true);

// Verificar si se recibieron datos
if ($data === null) {
    // Error al decodificar JSON
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Error decoding JSON data']);
    exit();
}

// Ahora puedes acceder a los datos como un array asociativo
$hasError = false; // Inicializamos la variable $hasError como false

$nom               = !empty($data['nom']) ? data_input($data['nom']) : ($hasError = true);
$email        = !empty($data['email']) ? data_input($data['email']) : ($hasError = true);
$biografia_cat         = !empty($data['biografia_cat']) ? data_input($data['biografia_cat']) : ($hasError = false);
$user_type          = !empty($data['user_type']) ? data_input($data['user_type']) : ($hasError = true);
$id                  = !empty($data['id']) ? data_input($data['id']) : ($hasError = true);

// Si hay algún error de validación
if ($hasError) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['status' => 'error', 'message' => 'Falten dades obligatòries']);
    exit();
}

global $conn;
/** @var PDO $conn */

// Construcción dinámica del query dependiendo de si se actualiza la contraseña o no
$query = "UPDATE auth_users SET nom = :nom, email = :email, biografia_cat = :biografia_cat, user_type = :user_type";
$params = [
    ':nom' => $nom,
    ':email' => $email,
    ':biografia_cat' => $biografia_cat,
    ':user_type' => $user_type,
];

// Si el password viene lleno, lo incluimos
if (!empty($data['password'])) {
    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT, ['cost' => 10]);
    $query .= ", password = :password";
    $params[':password'] = $hashedPassword;
}

$query .= " WHERE id = :id";
$params[':id'] = $id;

try {
    $stmt = $conn->prepare($query);
    $stmt->execute($params);

    echo json_encode(['status' => 'success', 'message' => 'Usuari actualitzat correctament']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error en l\'actualització de les dades.']);
}
