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
  $query = "SELECT u.id, u.email, u.password, u.user_type, u.nom
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
    $userType = $row['user_type'];
    $nom = $row['nom'];

    if (password_verify($password, $hash) && in_array($userType, [1, 2, 3])) {
      session_start();
      $idUser = $id;

      $key = $jwtSecret;
      $algorithm = "HS256";  // Elige el algoritmo adecuado para tu aplicación
      $payload = array(
        "user_id" =>  $id,
        "username" => $nom,
        "user_type" => $userType,
        'iat' => time(),
        'exp' => time() + 604800,
        "kid" => "key_api"
      );

      // Encode headers in the JWT string
      $jwt = JWT::encode($payload, $key, $algorithm);

      // Preparar la respuesta
      $response = array(
        "status" => "success"
      );

      $cookie_options = array(
        'expires' => time() + 604800,
        'path' => '/',
        'domain' => 'memoriaterrassa.cat',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict'
      );

      // Establecer las cookies
      setcookie('token', $jwt, $cookie_options);

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
