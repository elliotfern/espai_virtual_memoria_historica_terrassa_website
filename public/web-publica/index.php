<?php

// Obtener la ruta solicitada
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Normalizar la ruta eliminando barras finales
$lang2 = rtrim($requestUri, '/');

// Obtener traducciones generales
$translate = $translations['benvinguda'] ?? [];
?>

<div class="container-fluid full-screen bg-image">
    <div class="logo-wrapper">
        <a href=".<?php echo $lang2; ?>/inici">
            <picture>
                <!-- Logo SOLO para móvil -->
                <source
                    media="(max-width: 768px)"
                    srcset="<?php echo IMG_DOMAIN; ?>/assets_web/logo_gran.svg">

                <!-- Logo por defecto (desktop / tablet) -->
                <img
                    src="<?php echo IMG_DOMAIN; ?>/assets_web/logo_gran.svg"
                    alt="Logo"
                    class="logo">
            </picture>
        </a>
    </div>

    <a href=".<?php echo $lang2; ?>/inici">
        <button class="bottom-right-button"><?php echo $translate['boto'] ?></button>
    </a>
</div>

<style>
    /* Evitar scroll en esta página */
    html,
    body {
        height: 100%;
        margin: 0;
        overflow: hidden;
        /* no se puede hacer scroll */
    }

    .full-screen {
        height: 100vh;
        min-height: 100vh;
        display: flex;
        align-items: center;
        /* centrado vertical */
        justify-content: center;
        /* centrado horizontal */
        position: relative;
        padding: 0;
    }

    .bg-image {
        position: relative;
        width: 100%;
        height: 100%;
        overflow: hidden;
    }

    /* Fondo con imagen (puedes dejar el blur suave o quitarlo) */
    .bg-image::before {
        content: "";
        position: absolute;
        inset: 0;
        background-image: url('https://media.memoriaterrassa.cat/assets_web/cartells_republica.jpg');
        background-size: cover;
        background-position: center;
        filter: blur(2px);
        /* suave, solo para “romper” un poco */
        transform: scale(1.03);
        z-index: 1;
    }

    /* Capa oscura encima del fondo */
    .bg-image::after {
        content: "";
        position: absolute;
        inset: 0;
        background-color: rgba(5, 6, 13, 0.45);
        z-index: 2;
    }

    /* Enlace del logo sin subrayado */
    .logo-wrapper a {
        text-decoration: none;
    }

    /* Tarjeta detrás del logo: aquí es donde conseguimos legibilidad */
    .logo-wrapper {
        position: relative;
        z-index: 3;
        /* por encima de fondo y capa oscura */
        padding: 20px 26px;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.85);
        /* fondo claro, ideal para azul oscuro + blanco */
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
        backdrop-filter: blur(6px);
        /* efecto cristal (si el navegador lo soporta) */
        -webkit-backdrop-filter: blur(6px);
        max-width: 90%;
    }

    .logo {
        display: block;
        width: 60%;
        height: auto;
        margin: 0 auto;
    }

    .bottom-right-button {
        font-family: "Raleway", serif;
        font-optical-sizing: auto;
        font-weight: bold;
        font-style: normal;
        font-size: 1rem;
        position: absolute;
        bottom: 60px;
        right: 60px;
        z-index: 3;
        padding: 10px 20px;
        background-color: #133B7C;
        color: #fff;
        border: none;
        border-top-left-radius: 15px;
        cursor: pointer;
        transition: transform 0.3s ease, background-color 0.3s ease;
    }

    .bottom-right-button:hover {
        background-color: #000;
        color: #fff;
        transform: scale(1.1);
    }

    /* =========================
         VERSIÓN MÓVIL
       ========================= */
    @media (max-width: 768px) {

        .logo-wrapper {
            max-width: 90%;
            width: 90%;
            padding: 18px 20px;
            border-radius: 16px;
        }

        .logo {
            width: 100%;
            /* más grande en móvil */
            max-width: 100%;
        }

        .bottom-right-button {
            font-size: 0.95rem;
            padding: 10px 24px;

            position: fixed;
            bottom: 80px;
            /* ajusta según la altura real del banner de cookies */
            right: auto;
            left: 50%;
            transform: translateX(-50%);

            border-radius: 15px;
            /* igual en todos los lados en móvil */
        }

        .bottom-right-button:hover {
            background-color: #000;
            color: #fff;
            transform: translateX(-50%);
            /* sin zoom para que no “salte” */
        }
    }
</style>