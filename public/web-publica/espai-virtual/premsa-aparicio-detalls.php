<?php

// Traducciones generales si las usas aquí
$translate = $translations['general'] ?? [];
?>

<!-- Hero -->
<div class="container-fluid background-image-cap">
    <div class="container px-4">
        <span class="negreta gran italic-text cap">
            Aparicions<br>als mitjans
        </span>
    </div>
</div>

<!-- Contenido -->
<div class="container d-flex flex-column" style="padding-top: 50px;padding-bottom:50px;">
    <span class="titol gran lora negreta">Aparicions als mitjans de comunicació</span>
    <span class="titol italic-text gran lora">Notícies, entrevistes, articles de premsa i altres esdeveniments</span>

    <!-- Aquí renderiza TS -->
    <div id="publicAparicioPremsaDetalls" class="mt-4"></div>
</div>