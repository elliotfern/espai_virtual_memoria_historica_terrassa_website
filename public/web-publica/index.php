<?php

// Obtener la ruta solicitada
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Normalizar la ruta eliminando barras finales
$lang2 = rtrim($requestUri, '/');

// Obtener traducciones generales
$translate = $translations['benvinguda'] ?? [];
?>

<div class="container-fluid full-screen bg-image">
    <img src="<?php echo IMG_DOMAIN; ?>/assets_web/logo-gran.png" alt="Logo" class="logo">
    <a href=".<?php echo $lang2; ?>/inici"><button class="bottom-right-button"><?php echo $translate['boto'] ?></button></a>
</div>

<style>
    .full-screen {
        min-height: 100vh;
        /* Ocupa al menos toda la altura de la pantalla */
        display: flex;
        /* Opcional, para centrar el contenido */
        align-items: center;
        /* Opcional, para centrar verticalmente */
        justify-content: center;
        /* Opcional, para centrar horizontalmente */
    }


    .bg-image {
        background-image: url('https://media.memoriaterrassa.cat/assets_web/cartells_republica.jpg');
        /* Cambia 'tu-imagen.jpg' por la URL de tu imagen */
        background-size: cover;
        /* Imagen cubre todo el área */
        background-position: center;
        /* Imagen centrada */
        position: relative;
        /* Necesario para apilar correctamente los elementos */
        background-color: rgba(5, 6, 13, 0.79);
        /* Agrega el color azul con transparencia directamente */
        background-blend-mode: overlay;
        /* Combina la imagen de fondo con el color */
    }

    .logo {
        position: relative;
        /* Asegura que esté encima de la capa azul */
        z-index: 3;
        /* Asegura que el logo esté encima de la capa azul */
        width: 60%;
        /* Ajusta el tamaño del logo */
        height: auto;
        /* Mantiene la proporción del logo */
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
        color: rgb(255, 255, 255);
        border: none;
        border-top-left-radius: 15px;
        cursor: pointer;
        transition: transform 0.3s ease, background-color 0.3s ease;
        /* Transición suave */

    }

    .bottom-right-button:hover {
        background-color: rgb(0, 0, 0);
        color: rgb(255, 255, 255);
        transform: scale(1.1);
    }
</style>