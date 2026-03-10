<?php
// Obtener traducciones generales
$translate = $translations['general'] ?? [];
$translate2 = $translations['cerca-avan'] ?? [];
?>

<!-- Hero -->
<div class="container-fluid background-image-cap">
    <div class="container px-4">
        <span class="negreta gran italic-text cap">
            Antecedents de<br> l'Espai Virtual de la Memòria Històrica de Terrassa
        </span>
    </div>
</div>

<!-- Contenido -->
<div class="container d-flex flex-column" style="padding-top: 50px;padding-bottom:50px;">

    <!-- Aquí renderiza TS -->
    <div id="blocAntecedents"></div>
</div>


<style>
    /* Contenedor de la línea de tiempo */
    .timeline {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
    }

    /* Línea central */
    .timeline::before {
        content: "";
        position: absolute;
        top: 0;
        bottom: 0;
        left: 50%;
        width: 3px;
        background-color: #B39B7C;
        transform: translateX(-50%);
    }

    /* Estilo de los eventos */
    .timeline-item {
        position: relative;
        width: 50%;
        padding: 20px;
        display: flex;
        justify-content: center;
    }

    /* Alternar los eventos a la izquierda y derecha */
    .timeline-item:nth-child(odd) {
        align-self: flex-start;
        text-align: left;
    }

    .timeline-item:nth-child(even) {
        align-self: flex-end;
        text-align: right;
    }

    /* Puntos en la línea */
    .timeline-item::before {
        content: "";
        position: absolute;
        top: 30%;
        width: 20px;
        height: 20px;
        background-color: #fff;
        border: 3px solid #B39B7C;
        border-radius: 50%;
        left: 100%;
        transform: translateX(-50%);
    }

    .timeline-item:nth-child(even)::before {
        left: auto;
        right: 100%;
        transform: translateX(50%);
    }

    /* Tarjetas: tamaño normal */
    .card {
        width: 100%;
        max-width: 300px;
        background-color: #133B7C;
        border-radius: 25px;
    }

    .blanc {
        color: white;
    }

    /* En pantallas grandes, hacer las tarjetas más anchas */
    @media (min-width: 992px) {
        .card {
            max-width: 100%
        }
    }
</style>