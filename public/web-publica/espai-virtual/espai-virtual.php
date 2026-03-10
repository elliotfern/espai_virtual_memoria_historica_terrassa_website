<?php
// Obtener traducciones generales
$translate = $translations['general'] ?? [];
$translate2 = $translations['cerca-avan'] ?? [];
?>

<!-- Sección con container-fluid -->
<div class="container-fluid background-image-cap">

    <div class="container px-4">
        <span class="negreta gran italic-text cap">Què és<br> l'Espai Virtual de la Memòria Històrica de Terrassa?</span>

    </div>
</div>


<div class="container d-flex flex-column" style="padding-top: 50px;padding-bottom:50px;">
    <span class="titol gran lora negreta">Espai virtual</span>
    <span class="titol italic-text gran lora">Preservant la memòria,<br> construint el futur.</span>
    <span class="text1 mitja raleway" style="margin-top:20px">
        <p>
            L’Espai Virtual de la Memòria Històrica de Terrassa (EVMHT) és un espai virtual històric, documental, visual, sonor, divulgatiu, educatiu i d’investigació sobre el cost humà de la lluita per la llibertat dels terrassencs i terrassenques durant el període 1936-1983.
        </p>
        <p>
            L’EVMHT recull el cost humà provocat pel <strong>cop d’estat franquista</strong> i la <strong>guerra civil</strong>; l’exili (<em>deportats</em>) i per la repressió franquista a Terrassa (<em>afusellats</em>), entre els anys, 1936 i el 1983. És a dir, recollirà els períodes històrics: <strong>República</strong>, <strong>Guerra Civil</strong>, <strong>Dictadura Franquista</strong> i <strong>Transició</strong>.
        </p>
        <p>
            Els continguts que trobareu a l’espai virtual, són el resultat d’anys d’investigació i recerca de diversos historiadors i historiadores locals i de la Catalunya i el món. Recerques que continuen encara avui i que s’incorporen de forma contínua i sistemàtica a l’EVMHT.
        </p>
        <p>
            El projecte de l’EVMHT és una iniciativa del <strong>Centre d’Estudis Històrics de Terrassa (CEHT)</strong>, associació cultural, amb 40 anys d’història, que reuneix tots els historiadors i historiadores de Terrassa i que col·labora amb les institucions públiques –i privades– de la ciutat i el país.
        </p>

    </span>

    <div class="container py-5">
        <div class="row row-cols-4 g-4 text-center">
            <div class="col">
                <div class=" p-4">
                    <img src="<?php echo IMG_DOMAIN; ?>/assets_web/icon1.png" class="mb-3 w-6" alt="Imagen 1">
                    <h5 class="lora blau2">Històries personals de les represaliades <br>i represaliats</h5>
                </div>
            </div>
            <div class="col">
                <div class="p-4">
                    <img src="<?php echo IMG_DOMAIN; ?>/assets_web/icon2.png" class="mb-3 w-6" alt="Imagen 2">
                    <h5 class="lora blau2">Eines de cerca avançada<br> per explorar arxius</h5>
                </div>
            </div>
            <div class="col">
                <div class="p-4">
                    <img src="<?php echo IMG_DOMAIN; ?>/assets_web/icon3.png" class="mb-3 w-6" alt="Imagen 3">
                    <h5 class="lora blau2">Bases de dades detallades<br> segons el tipus de repressió</h5>
                </div>
            </div>
            <div class="col">
                <div class="p-4">
                    <img src="<?php echo IMG_DOMAIN; ?>/assets_web/icon4.png" class="mb-3 w-6" alt="Imagen 4">
                    <h5 class="lora blau2">Fonts documentals<br> verificades i accessibles</h5>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="container d-flex flex-column" style="padding-top: 50px;padding-bottom:50px;">
    <span class="titol gran lora negreta">Què trobareu a l'Espai Virtual?</span>
    <span class="titol italic-text gran lora">Les dades es presenten en forma de taules interactives i <br>fitxes individuals que inclouen biografies, documents i <br>testimonis audiovisuals</span>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12 col-md-3">
                <div class="btn-div d-flex flex-column">
                    <img class="img-m" src="<?php echo IMG_DOMAIN; ?>/assets_web/icon5.png" alt="Icono 1">
                    <span class="lora mitja">Fitxes <br>detallades</span>
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="btn-div d-flex flex-column">
                    <img class="img-m" src="<?php echo IMG_DOMAIN; ?>/assets_web/icon6.png" alt="Icono 1">
                    <span class="lora mitja">Base de dades <br>relacionals</span>
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="btn-div d-flex flex-column">
                    <img class="img-m" src="<?php echo IMG_DOMAIN; ?>/assets_web/icon7.png" alt="Icono 1">
                    <span class="lora mitja">Documentació <br>històrica</span>
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="btn-div d-flex flex-column">
                    <img class="img-m" src="<?php echo IMG_DOMAIN; ?>/assets_web/icon8.png" alt="Icono 1">
                    <span class="lora mitja">Testimonis <br>audiovisuals</span>
                </div>
            </div>
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