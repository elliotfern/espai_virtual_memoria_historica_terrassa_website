<?php

// Combinar todas las rutas en un solo arreglo
$routes = array_merge(
    require __DIR__ . '/intranet.php',
    require __DIR__ . '/web_publica.php'
);

return $routes;
