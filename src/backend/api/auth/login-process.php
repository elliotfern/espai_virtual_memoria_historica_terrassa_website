<?php

// Configuración de cabeceras para aceptar JSON y responder JSON
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: https://memoriaterrassa.cat");
header("Access-Control-Allow-Methods: POST");

require 'vendor/autoload.php'; // Assegura't que tens la biblioteca JWT instal·lada
use Firebase\JWT\JWT;

$jwtSecret = $_ENV['TOKEN'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Obtener el cuerpo de la solicitud
  $data = json_decode(file_get_contents('php://input'), true); // Decodifica el JSON

  // Asegúrate de que las variables están definidas
  $username = isset($data['userName']) ? $data['userName'] : null;
  $password = isset($data['password']) ? $data['password'] : null;

  if (empty($username) || empty($password)) {
    $response['status'] = 'error';
    $response['message'] = 'El camp email i password són obligatoris.';
    echo json_encode($response);
    exit;
  }

  global $conn;
  /** @var PDO $conn */
  $query = "SELECT u.id, u.email, u.password
              FROM auth_users AS u
              WHERE u.email = :email";
  $stmt = $conn->prepare($query);
  $stmt->execute(['email' => $username]);

  if ($stmt->rowCount() === 0) {
    $response['status'] = 'error';
    $response['message'] = 'Usuari no trobat.';
    echo json_encode($response);
    exit;
  } else {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $hash = $row['password'];
    $id = $row['id'];

    if (password_verify($password, $hash) && in_array($id, [1, 2, 3, 4, 6])) {
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

      // Encode headers in the JWT string
      $jwt = JWT::encode($payload, $key, $algorithm);

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

      // Si la inserció té èxit, cal registrar acces usuari en la base de control de acces
      $dataAcces = date('Y-m-d H:i:s');
      $idUser = $idUser;
      $tipusOperacio = 1;

      // Crear la consulta SQL
      $sql2 = "INSERT INTO auth_users_control_acces (
        idUser, dataAcces, tipusOperacio
        ) VALUES (
        :idUser, :dataAcces, :tipusOperacio
        )";

      // Preparar la consulta
      $stmt = $conn->prepare($sql2);

      // Enlazar los parámetros con los valores de las variables PHP
      $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
      $stmt->bindParam(':tipusOperacio', $tipusOperacio, PDO::PARAM_INT);
      $stmt->bindParam(':dataAcces', $dataAcces, PDO::PARAM_STR);

      // Ejecutar la consulta
      $stmt->execute();

      // Devolver la respuesta JSON
      echo json_encode($response);

      exit;
    } else {

      // Si la inserció té èxit, cal registrar acces usuari en la base de control de acces
      $dataAcces = date('Y-m-d H:i:s');
      $idUser = $id;
      $tipusOperacio = 2;

      // Crear la consulta SQL
      $sql2 = "INSERT INTO auth_users_control_acces (
      idUser, dataAcces, tipusOperacio
      ) VALUES (
      :idUser, :dataAcces, :tipusOperacio
      )";

      // Preparar la consulta
      $stmt = $conn->prepare($sql2);

      // Enlazar los parámetros con los valores de las variables PHP
      $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
      $stmt->bindParam(':tipusOperacio', $tipusOperacio, PDO::PARAM_INT);
      $stmt->bindParam(':dataAcces', $dataAcces, PDO::PARAM_STR);

      // Ejecutar la consulta
      $stmt->execute();

      $response['status'] = 'error';
      $response['message'] = 'Usuari no autoritzat o contrasenya incorrecta.';
      echo json_encode($response);
      exit;
    }
  }
} else {
  $response['status'] = 'error';
  $response['message'] = 'Método no permitido.';
  echo json_encode($response);
  exit;
}
