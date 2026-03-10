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
    <span class="titol gran lora negreta">Un recorregut per història del projecte</span>
    <span class="titol italic-text gran lora">Explora els moments clau en la creació i evolució d'aquest<br> espai dedicat a la memòria històrica de Terrassa.</span>

    <!-- Aquí renderiza TS -->
    <div id="blocAntecedents"></div>
</div>