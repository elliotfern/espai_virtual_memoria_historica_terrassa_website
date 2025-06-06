<?php

require __DIR__ . '../../Utils/utils.php';

// Definir constantes de configuración
define('BASE_URL', $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']);
define('APP_ROOT', $_SERVER['DOCUMENT_ROOT']);

define('DOMAIN', "https://memoriaterrassa.cat");

$base_url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];
define("APP_WEB", $base_url);
define("APP_SERVER", $base_url);


// Definicio de constants







// Combinar todas las rutas en un solo arreglo
$routes = array_merge(
    require __DIR__ . '/routes/api.php',
    require __DIR__ . '/routes/intranet.php',
    require __DIR__ . '/routes/web_publica.php'
);

return $routes;
