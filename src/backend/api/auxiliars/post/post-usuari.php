<?php

// Configuración de cabeceras para aceptar JSON y responder JSON
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET");

// Definir el dominio permitido
$allowedOrigin = "https://memoriaterrassa.cat";

// Llamar a la función para verificar el referer
checkReferer($allowedOrigin);

// Verificar que el método de la solicitud sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
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
$password = !empty($data['password']) ? data_input($data['password']) : ($hasError = true);

// Si hay algún error de validación
if ($hasError) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['status' => 'error', 'message' => 'Falten dades obligatòries']);
    exit();
}

// Hashear el password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT, ['cost' => 10]);

global $conn;
/** @var PDO $conn */
$query = "INSERT INTO auth_users (nom, email, biografia_cat, user_type, password)
          VALUES (:nom, :email, :biografia_cat, :user_type, :password)";

try {
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':biografia_cat', $biografia_cat, PDO::PARAM_STR);
    $stmt->bindParam(':user_type', $user_type, PDO::PARAM_INT);
    $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);

    $stmt->execute();

    header("Content-Type: application/json");
    echo json_encode(['status' => 'success', 'message' => 'Usuari creat correctament']);
} catch (PDOException $e) {

    // Si no hay resultados, devolver un mensaje de error
    header("Content-Type: application/json");
    echo json_encode(['status' => 'error', 'message' => 'Error en la transmissió de les dades.']);
}
