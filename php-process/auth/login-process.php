<?php

require_once(APP_ROOT . APP_DEV . '/vendor/autoload.php');
use Dotenv\Dotenv;
use Firebase\JWT\JWT;

// Cargar variables de entorno desde .env
$dotenv = Dotenv::createImmutable(APP_ROOT . APP_DEV . '/');
$dotenv->load();

$jwtSecret = $_ENV['TOKEN'];

if (isset($_POST['userName'])) {
    $username = $_POST['userName'];
    $password = $_POST['password'];
    $hasError = 1;
} else {
    $response['status'] = 'error';

    header( "Content-Type: application/json" );
    echo json_encode($response);
}


global $conn;
$data = array();
$stmt = $conn->prepare(
    "SELECT u.id, u.email, u.password
    FROM auth_users AS u
    WHERE u.email = :email");
    $stmt->execute(
      ['email' => $username]
    );
    if ($stmt->rowCount() === 0) {
      $_SESSION['message'] = array('type'=>'danger', 'msg'=>'Your account has not ben enabled.');
    } else {
      while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $hash = $row['password'];
        $id = $row['id'];
        if(password_verify($password, $hash) && ($id == 1 || $id == 2 || $id == 3 || $id == 4 || $id == 5)) {
          session_start();
          $_SESSION['user']['id'] = $row['id'];
          $_SESSION['user']['username'] = $row['email'];

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
            "status" => "success"
          );

          // Establecer el encabezado como JSON
          header('Content-Type: application/json');

          // Devolver la respuesta JSON
          echo json_encode($response);
        } else {
          // response output
          $response['status'] = 'error';

          header( "Content-Type: application/json" );
          echo json_encode($response);
        }
        
      }
    }
