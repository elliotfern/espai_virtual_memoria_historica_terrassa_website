<div class="container-fluid full-screen bg-image">
    <img src="../public/img/logo-gran.png" alt="Logo" class="logo">
    <a href="./inici"><button class="bottom-right-button">entrar al lloc web</button></a>
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
        background-image: url('../public/img/santpere.jpg');
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
        font-weight: 200px;
        font-style: normal;
        font-size: 1rem;
        position: absolute;
        /* Posiciona el botón de forma independiente al flujo normal */
        bottom: 60px;
        /* Distancia desde el borde inferior */
        right: 60px;
        /* Distancia desde el borde derecho */
        z-index: 3;
        /* Asegura que el botón esté visible por encima de otras capas */
        padding: 10px 20px;
        /* Tamaño del botón */
        background-color: rgb(255, 255, 255);
        /* Color de fondo */
        color: #133B7C;
        /* Color del texto */
        border: none;
        /* Sin bordes */
        border-top-left-radius: 15px;
        /* Bordes redondeados */
        cursor: pointer;
        /* Cambia el cursor al pasar el mouse */
    }

    .bottom-right-button:hover {
        background-color: rgb(0, 0, 0);
        color: rgb(255, 255, 255);
        /* Efecto hover */
    }
</style>