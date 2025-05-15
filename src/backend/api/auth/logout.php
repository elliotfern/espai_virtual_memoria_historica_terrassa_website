<?php

// Configuración de cabeceras para aceptar JSON y responder JSON
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: https://memoriaterrassa.cat");
header("Access-Control-Allow-Methods: GET");

// Verifica que el usuario esté autenticado
session_start();

$arr_cookie_options = array(
    'expires' => time() - 3600,
    'path' => '/',
    'domain' => 'memoriaterrassa.cat',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
);

//Elimina les cookies
setcookie('token', '', $arr_cookie_options);

// Además, puedes destruir la sesión si estás utilizando sesiones en PHP
session_unset();    // Elimina todas las variables de sesión
session_destroy();  // Destruye la sesión

// Respuesta en formato JSON o redirige
echo json_encode(['message' => 'OK']);

exit;
