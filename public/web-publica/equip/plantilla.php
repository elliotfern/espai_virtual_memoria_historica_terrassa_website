<?php
// Obtener traducciones generales
$translate = $translations['general'] ?? [];
$translate2 = $translations['cerca-avan'] ?? [];
?>

<!-- Sección con container-fluid -->
<div class="container-fluid background-image-cap">

    <div class="container px-4">
        <span id="nom" class="negreta gran italic-text cap"></span>
        <p><span id="bio_curta" class="italic-text cap" style="font-size: 30px!important;"></span></p>
    </div>
</div>


<div class="container mt-4" style="margin-top: 50px;margin-bottom:50px;">
    <div class="row">
        <!-- Texto a la izquierda -->
        <div class="col-md-8 raleway" id="bio">

        </div>

        <!-- Imagen a la derecha -->
        <div class="col-md-4 text-center">
            <img id="urlImatge" src="" class="img-fluid" alt="Usuari">

        </div>
    </div>
</div>