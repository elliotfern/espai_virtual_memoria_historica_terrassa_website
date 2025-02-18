<?php
// Obtener traducciones generales
$translate = $translations['general'] ?? [];
$translate2 = $translations['cerca-avan'] ?? [];
?>

<!-- Sección con container-fluid -->
<div class="container-fluid background-image-cap">

    <div class="container px-4">
        <span class="negreta gran italic-text cap">Què és<br> l'Espai Virtual de la Memòria Històrica de Terrassa</span>

    </div>
</div>


<div class="container" style="margin-top: 50px;margin-bottom:50px;">

    <?php echo $translate['no-disponible'] ?>
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
        width: 4px;
        background-color: #007bff;
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
        top: 20px;
        width: 20px;
        height: 20px;
        background-color: #fff;
        border: 4px solid #007bff;
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
    }

    /* En pantallas grandes, hacer las tarjetas más anchas */
    @media (min-width: 992px) {
        .card {
            max-width: 500px;
        }
    }
</style>

<div class="container py-5">
    <h2 class="text-center mb-4">Projecte</h2>
    <div class="timeline">

        <!-- Evento 1 -->
        <div class="timeline-item">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Evento 1</h5>
                    <p class="card-text">Descripción del evento 1.</p>
                </div>
            </div>
        </div>

        <!-- Evento 2 -->
        <div class="timeline-item">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Evento 2</h5>
                    <p class="card-text">Descripción del evento 2.</p>
                </div>
            </div>
        </div>

        <!-- Evento 3 -->
        <div class="timeline-item">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Evento 3</h5>
                    <p class="card-text">Descripción del evento 3.</p>
                </div>
            </div>
        </div>

    </div>
</div>