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

        <hr style="margin-top:30px">

        <h6 class="titolSeccio" style="margin-top:25px"><strong>Tipus de repressió:</strong></h6>

        <div class="fitxa-persona marro2 raleway" style="margin-top:20px;margin-bottom:20px">
            <p>Clica sobre els botons per ampliar la informació sobre el tipus de repressió que va patir:</p>
        </div>

        <div class="tab" id="botons2"></div>
        <div id="fitxa-categoria" class="fitxa-persona" style="margin-top:50px;margin-bottom:50px;display:none"> </div>
    </div>
</div>

<div class="container fitxaRepresaliat_error" style="display:none"> </div>

<style>
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

    /* Para móvil */
    @media (max-width: 767.98px) {

        .fitxaRepresaliat {
            padding-left: 15px !important;
            padding-right: 15px !important;
        }

        .fitxaRepresaliat2 {
            padding-left: 15px !important;
            padding-right: 15px !important;
        }
    }

    /* Para tablets y arriba, quitar si molesta */
    @media (min-width: 768px) {
        .fitxaRepresaliat2 {
            padding-left: 40px !important;
            padding-right: 40px !important;
        }
    }
</style>