<?php
// Obtener traducciones generales
$translate = $translations['general'] ?? [];
$translate2 = $translations['cerca-avan'] ?? [];
?>

<!-- Sección con container-fluid -->
<div class="container-fluid background-image-cap">

    <div class="container px-4">
        <span class="negreta gran italic-text cap">Crèdits del web<br> de l'Espai Virtual de la Memòria Històrica de Terrassa</span>

    </div>
</div>

<div class="container" style="margin-top: 50px;margin-bottom:50px;">

    <span class="text1 mitja raleway" style="margin-top:20px">Aquesta pàgina web ha estat possible gràcies a un equip de persones multidisciplinar, que inclou: el disseny gràfic, la programació web i la investigació històrica:</span>

    <div class="container my-5">
        <div class="row mt-4 gy-4 gx-4">
            <!-- Primera fila -->
            <div class="col-md-6 d-flex align-items-center px-3">
                <div class="col-md-6 d-flex flex-column g-3">
                    <h3 class="fw-bold lora gran blau1">Manuel Màrquez <br>Berrocal</h3>
                    <span class="marro1 lora italic-text">Historiador i<br> responsable del projecte</span>
                    <a href="../<?php echo empty($language) ? '' : $language . '/'; ?>equip/manel-marquez" class="btn btn-primary btn-custom-2 w-auto align-self-start" style="margin-top:15px">Veure biografia</a>
                </div>
                <div class="col-md-4 d-flex align-items-center">
                    <div class="col-md-10">
                        <img src="<?php echo IMG_DOMAIN; ?>/assets_usuaris/manel_marquez.jpg" class="rounded-circle img-petita" alt="Foto">
                    </div>
                    <div class="col-md-2">
                        <img src="<?php echo IMG_DOMAIN; ?>/assets_web/vector.png" class="img-s" alt="Foto">
                    </div>
                </div>
            </div>

            <!-- segona fila -->
            <div class="col-md-6 d-flex align-items-center px-3 border-start">
                <div class="col-md-6 d-flex flex-column g-3">
                    <h3 class="fw-bold lora gran blau1">Juan Antonio <br>Olivares Abad</h3>
                    <span class="marro1 lora italic-text">Historiador i<br> divulgador.</span>
                    <a href="../<?php echo empty($language) ? '' : $language . '/'; ?>equip/juan-antonio-olivares" class="btn btn-primary btn-custom-2 w-auto align-self-start" style="margin-top:15px">Veure biografia</a>
                </div>
                <div class="col-md-4 d-flex align-items-center">
                    <div class="col-md-10">
                        <img src="<?php echo IMG_DOMAIN; ?>/assets_usuaris/jose_antonio_olivares.jpg" class="rounded-circle img-petita" alt="Foto">
                    </div>
                    <div class="col-md-2">
                        <img src="<?php echo IMG_DOMAIN; ?>/assets_web/vector.png" class="img-s" alt="Foto">
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4 gy-4 gx-4 border-top">
            <!-- tercera fila -->
            <div class="col-md-6 d-flex align-items-center px-3">
                <div class="col-md-6 d-flex flex-column g-3">
                    <h3 class="fw-bold lora gran blau1">Josep Lluís<br>Lacueva Moreno</h3>
                    <span class="marro1 lora italic-text">Historiador i<br> autor de diversos llibres d'història de Terrassa.</span>
                    <a href="../<?php echo empty($language) ? '' : $language . '/'; ?>equip/josep-lluis-lacueva" class="btn btn-primary btn-custom-2 w-auto align-self-start" style="margin-top:15px">Veure biografia</a>
                </div>
                <div class="col-md-4 d-flex align-items-center">
                    <div class="col-md-10">
                        <img src="<?php echo IMG_DOMAIN; ?>/assets_usuaris/josep_lluis_lacueva.jpg" class="rounded-circle img-petita" alt="Foto">
                    </div>
                    <div class="col-md-2">
                        <img src="<?php echo IMG_DOMAIN; ?>/assets_web/vector.png" class="img-s" alt="Foto">
                    </div>
                </div>
            </div>


            <!-- quarta fila -->
            <div class="col-md-6 d-flex align-items-center px-3  border-start">
                <div class="col-md-6 d-flex flex-column g-3">
                    <h3 class="fw-bold lora gran blau1">Elliot Fernández <br>Hernández</h3>
                    <span class="marro1 lora italic-text">Historiador i<br> responsable tècnic del web.</span>
                    <a href="../<?php echo empty($language) ? '' : $language . '/'; ?>equip/elliot-fernandez" class="btn btn-primary btn-custom-2 w-auto align-self-start" style="margin-top:15px">Veure biografia</a>
                </div>
                <div class="col-md-4 d-flex align-items-center">
                    <div class="col-md-10">
                        <img src="<?php echo IMG_DOMAIN; ?>/assets_usuaris/elliot_fernandez.jpg" class="rounded-circle img-petita" alt="Foto">
                    </div>
                    <div class="col-md-2">
                        <img src="<?php echo IMG_DOMAIN; ?>/assets_web/vector.png" class="img-s" alt="Foto">
                    </div>
                </div>
            </div>


        </div>
    </div>

</div>