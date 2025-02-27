<?php
$id = $routeParams[0];
?>

<div class="container fitxaRepresaliat">
    <div class="row" id="botons1"></div>
    <div class="container fitxaRepresaliat2" style="padding: 100px">
        <div id="info"> </div>

        <div class="container mt-5">
            <div class="row">
                <!-- Columna izquierda más pequeña (por ejemplo, 4 columnas) -->
                <div class="col-12 col-md-4">
                    <div class="p-3 border bg-light">
                        <img src="" alt="Foto" class="logoPetit" id="imatgeRepresaliat">
                    </div>
                </div>

                <!-- Columna derecha más grande (por ejemplo, 8 columnas) -->
                <div class="col-12 col-md-8">
                    <div id="fitxa" class="fitxa-persona negreta raleway"> </div>
                </div>
            </div>
        </div>

        <hr>

        <h6 class="titolSeccio" style="margin-top:25px"><strong>Tipus de repressió:</strong></h6>
        <div class="tab" id="botons2"></div>
        <div id="fitxa-categoria" class="fitxa-persona" style="margin-top:50px;margin-bottom:50px;display:none"> </div>
    </div>
</div>

<style>
    .button {
        border: 1px solid #C2AF96 !important;
    }

    #botons1 {
        background-color: #f6f4eb !important;
        display: flex;
        gap: 0.3rem;
    }

    .tablinks {
        border-top-left-radius: 30px !important;
        border-top-right-radius: 30px !important;
        font-size: 16px !important;
        border: 1px solid #C2AF96 !important;
    }

    .colorBtn1 {
        background-color: #F1EEE0 !important;
        color: #133B7C !important;
        font-weight: 500;
        font-style: italic;
        font-family: "Lora", serif;
        font-optical-sizing: auto;
    }

    .colorBtn2 {
        background-color: #F1EEE0 !important;
        color: #133B7C !important;
        font-weight: 500;
        font-style: italic;
        font-family: "Lora", serif;
        font-optical-sizing: auto;
    }

    .colorBtn1:hover,
    .colorBtn2:hover {
        background-color: #C2AF96 !important;
        color: #133B7C !important;
    }

    .active {
        background-color: #C2AF96 !important;
        color: #133B7C !important;
    }

    .row {
        margin-top: 0px !important;
        margin-right: 0px !important;
        margin-left: 0px !important;
    }

    .fitxaRepresaliat {
        margin-top: 50px;
        margin-bottom: 50px;
        background-color: #F1EEE0;
        border: none;
        padding-right: 0px !important;
        padding-left: 0px !important;
        border-top-left-radius: 30px;
    }

    .fitxaRepresaliat2 {

        background-color: #F1EEE0;
        border: none;
        border-left: 1px solid #C2AF96B2;
        border-right: 1px solid #C2AF96B2;
        border-bottom: 1px solid #C2AF96B2;
    }
</style>