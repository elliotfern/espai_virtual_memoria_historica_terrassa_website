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
        transition: all 0.25s ease;
    }

    .timeline-item:nth-child(even)::before {
        left: auto;
        right: 100%;
        transform: translateX(50%);
    }

    .timeline-item:hover::before {
        background-color: #B39B7C;
        transform: scale(1.2) translateX(-50%);
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

    .timeline .card {
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .timeline .timeline-item:hover .card {
        transform: translateY(-4px);
        box-shadow: 0 12px 26px rgba(0, 0, 0, 0.18);
    }

    .antecedent-detail {
        opacity: 1;
        transform: translateY(0);
        transition: opacity 0.3s ease, transform 0.3s ease;
    }

    .antecedent-detail.d-none {
        opacity: 0;
        transform: translateY(12px);
    }

    /* estado activo del punto del timeline */
    .timeline-item.active::before {
        background-color: #B39B7C;
        transform: scale(1.25) translateX(-50%);
        box-shadow: 0 0 0 4px rgba(179, 155, 124, 0.25);
    }

    /* animación suave */
    .timeline-item::before {
        transition: all 0.25s ease;
    }

    /* ===== Timeline en móvil ===== */
    @media (max-width: 768px) {

        /* quitar la línea central */
        .timeline::before {
            display: none;
        }

        /* cada item ocupa todo el ancho */
        .timeline-item {
            width: 100%;
            align-self: center !important;
            text-align: left !important;
            padding: 15px 0;
            margin-bottom: 10px;
        }

        /* quitar los puntos laterales */
        .timeline-item::before {
            display: none;
        }

        /* card ocupa todo el ancho */
        .timeline .card {
            max-width: 100%;
        }
    }
</style>