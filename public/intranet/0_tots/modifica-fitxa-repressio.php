<?php
$categoriaId = $routeParams[0]; // Primero, por ejemplo, 1
$idPersona = $routeParams[1]; // Segundo, por ejemplo, 2
$categoriaId = (int) $categoriaId;

// Verificar si es un número entero válido
if (!is_int($categoriaId) || $categoriaId >= 11) {
    // Si no es un número entero o es menor o igual a cero, detener la ejecución
    header("Location: /404");
    exit();
}

require_once APP_ROOT . '/public/intranet/includes/header.php';

if ($categoriaId === 1) {
    echo "afusellat";
} else if ($categoriaId === 2) {
    require_once APP_ROOT . '/public/intranet/0_tots/modifica/deportats.php';
} else if ($categoriaId === 3) {
    require_once APP_ROOT . '/public/intranet/0_tots/modifica/morts_combat.php';
} else if ($categoriaId === 4) {
    require_once APP_ROOT . '/public/intranet/0_tots/modifica/morts_civils.php';
} else if ($categoriaId === 5) {
    echo "Represàlia republicana";
} else if ($categoriaId === 6) {
    echo "Processat/Empresonat";
} else if ($categoriaId === 7) {
    echo "Depurat";
} else if ($categoriaId === 8) {
    echo "Dona";
} else if ($categoriaId === 9) {
    echo "";
} else if ($categoriaId === 10) {
    require_once APP_ROOT . '/public/intranet/0_tots/modifica/exiliats.php';
}
