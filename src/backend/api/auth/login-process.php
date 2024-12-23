<?php

// Configuración de cabeceras para aceptar JSON y responder JSON
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: https://memoriaterrassa.cat");
header("Access-Control-Allow-Methods: POST");

use Firebase\JWT\JWT;

$jwtSecret = $_ENV['TOKEN'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Obtener el cuerpo de la solicitud
  $data = json_decode(file_get_contents('php://input'), true); // Decodifica el JSON

  // Asegúrate de que las variables están definidas
  $username = isset($data['userName']) ? $data['userName'] : null;
  $password = isset($data['password']) ? $data['password'] : null;
} else {
  $response['status'] = 'error';
  header("Content-Type: application/json");
  echo json_encode($response);
  exit;
}

global $conn;
/** @var PDO $conn */
$query = "SELECT u.id, u.email, u.password
    FROM auth_users AS u
    WHERE u.email = :email";
$stmt = $conn->prepare($query);

$stmt->execute(
  ['email' => $username]
);

if ($stmt->rowCount() === 0) {
  $response['status'] = 'error';
  // Establecer el encabezado como JSON
  header('Content-Type: application/json');

  // Devolver la respuesta JSON
  echo json_encode($response);
  exit;
} else {

  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $hash = $row['password'];
    $id = $row['id'];
    if (password_verify($password, $hash) && ($id == 1 || $id == 2 || $id == 3 || $id == 4 || $id == 5)) {
      session_start();
      $_SESSION['user']['id'] = $row['id'];
      $_SESSION['user']['username'] = $row['email'];
      $idUser = $row['id'];

      $key = $jwtSecret;
      $algorithm = "HS256";  // Elige el algoritmo adecuado para tu aplicación
      $payload = array(
        "user_id" =>  $row['id'],
        "username" => $row['email'],
        "kid" => "key_api"
      );

      $headers = [
        'x-forwarded-for' => 'localhost'
      ];

      // Encode headers in the JWT string
      $jwt = JWT::encode($payload, $key, $algorithm);

      // Almacenar en localStorage
      // Devolver el token al cliente (puedes enviarlo en una respuesta JSON)

      // Preparar la respuesta
      $response = array(
        "token" => $jwt,
        "idUser" => $idUser,
        "status" => "success"
      );

      // Opciones de configuración de la cookie
      $cookie_options = [
        'expires' => time() + (60 * 60 * 24), // 1 día
        'path' => '/',                       // Disponible en todo el dominio
        'secure' => true,                    // Solo enviar por HTTPS
        'httponly' => true,                  // No accesible por JavaScript
        'samesite' => 'Strict',              // Protección CSRF
      ];

      // Establecer las cookies
      setcookie('token', $jwt, $cookie_options);
      setcookie('user_id', $idUser, $cookie_options);

      // Establecer el encabezado como JSON
      header('Content-Type: application/json');

      // Devolver la respuesta JSON
      echo json_encode($response);
    } else {
      // response output
      $response['status'] = 'error';

      header("Content-Type: application/json");
      echo json_encode($response);
    }
  }
}
