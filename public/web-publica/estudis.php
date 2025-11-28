<?php
// Obtener traducciones generales
$translate = $translations['general'] ?? [];
$translate2 = $translations['cerca-avan'] ?? [];
?>

<!-- Sección con container-fluid -->
<div class="container-fluid background-image-cap">
    <div class="container px-4">
        <span class="negreta gran italic-text cap">Estudis de la investigació històrica del període 1930-1980</span>
    </div>
</div>

<div class="container" style="margin-top: 50px;margin-bottom:50px;">
    <div class="container my-5">
        <h1 class="text-center mb-4 titol gran lora negreta">Treballs d'investigació de Juan Antonio Olivares Abad</h1>
        <div class="row">
            <!-- Card 1 -->
            <div class="col-md-4 col-sm-6 mb-4">
                <div class="card">
                    <img src="https://media.memoriaterrassa.cat/docs/llibre_1.jpg" class="card-img-top" alt="Inversió del Terror">
                    <div class="card-body">
                        <h5 class="card-title">La inversió del Terror. Terrassa 1939-1941</h5>
                        <p class="card-text">Obra d'anàlisi de la repressió feixista posterior a la ocupació de les tropes franquistes de Terrassa l'any 1939.</p>
                        <a href="documento1.pdf" class="btn btn-download" target="_blank">Descarregar PDF</a>
                    </div>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="col-md-4 col-sm-6 mb-4">
                <div class="card">
                    <img src="portada2.jpg" class="card-img-top" alt="Portada Libro 2">
                    <div class="card-body">
                        <h5 class="card-title">La inversió del Terror. La inversió del Terror. Dones sota jurisdicció militar (1939-1942)</h5>
                        <p class="card-text">Una breve descripción del contenido del libro. Un resumen atractivo que invite a la descarga.</p>
                        <a href="documento2.pdf" class="btn btn-download" target="_blank">Descargar PDF</a>
                    </div>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="col-md-4 col-sm-6 mb-4">
                <div class="card">
                    <img src="portada3.jpg" class="card-img-top" alt="Portada Libro 3">
                    <div class="card-body">
                        <h5 class="card-title">La inversió del Terror. HOMES DE 50 ANYS O MÉS
                            SOTA JURISDICCIÓ MILITAR (1939-1942)</h5>
                        <p class="card-text">Una breve descripción del contenido del libro. Un resumen atractivo que invite a la descarga.</p>
                        <a href="documento3.pdf" class="btn btn-download" target="_blank">Descargar PDF</a>
                    </div>
                </div>
            </div>

            <!-- Agregar más tarjetas aquí -->
        </div>
    </div>
</div>

<style>
    /* Ajuste para el contenedor de la imagen */
    .card-img-top {
        /* Fija la altura de la imagen */
        object-fit: cover;
        /* Mantiene la imagen cubriendo el área del contenedor sin distorsionarse */
    }

    /* Diseño de las tarjetas */
    .card {
        margin-bottom: 20px;
        /* Se puede ajustar para añadir bordes redondeados o sombra si se desea */
    }

    .card-title {
        font-size: 1.2rem;
        font-weight: bold;
    }

    .card-text {
        font-size: 0.9rem;
    }

    /* Botón de descarga */
    .btn-download {
        background-color: #007bff;
        color: white;
    }

    .btn-download:hover {
        background-color: #0056b3;
    }
</style>