<?php
// Obtener traducciones generales
$translate = $translations['general'] ?? [];
$translate2 = $translations['cerca-avan'] ?? [];
?>

<!-- Sección con container-fluid -->
<div class="container-fluid background-image-cap">

    <div class="container px-4">
        <span class="negreta gran italic-text cap">Agenda d'activitats<br> de l'Espai Virtual de la Memòria Històrica de Terrassa</span>

    </div>
</div>


<div class="container d-flex flex-column" style="padding-top: 50px;padding-bottom:50px;">

    <div id="agendaActivitat" class="container py-5">
    </div>

</div>



<style>
    .btn-div {
        height: 200px;
        background-color: #EEEAD9;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        font-weight: bold;
        cursor: pointer;
        border-radius: 5px;
        transition: background 0.3s;
        color: #426296 !important;
    }

    .btn-div:hover {
        background-color: #B39B7C;
        color: white !important;
    }
</style>

</div>