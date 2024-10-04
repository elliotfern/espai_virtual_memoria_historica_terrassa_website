<?php
// Inicia el almacenamiento en buffer de salida
ob_start();

// Comprobar si ya hay una sesión iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();  // Iniciar sesión solo si no está activa
} else {
    // Renueva el tiempo de vida de la cookie de sesión
    if (isset($_SESSION)) {
        $sessionLifetime = 1800; // Tiempo de vida en segundos (30 minutos)
        setcookie(session_name(), session_id(), time() + $sessionLifetime, "/");
    }
}

// Envía la salida acumulada en el buffer al navegador
ob_end_flush();

?>