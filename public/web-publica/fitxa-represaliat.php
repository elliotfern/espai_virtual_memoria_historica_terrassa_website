<?php
$id = $routeParams[0];
?>

<div class="container fitxaRepresaliat">
    <div class="tab row" id="botons1"></div>
    <div class="container" style="padding: 100px">
        <div id="info"> </div>

        <div id="fitxa" class="fitxa-persona"> </div>

        <h6 class="titolSeccio" style="margin-top:25px"><strong>Tipus de repressió:</strong></h6>
        <div class="tab" id="botons2"></div>
        <div id="fitxa-categoria" class="fitxa-persona" style="margin-top:50px;margin-bottom:50px;display:none"> </div>
    </div>
</div>

<style>
    .tablinks {
        border-top-left-radius: 10px !important;
        font-size: 16px !important;
    }

    .colorBtn1 {
        background-color: #133B7C !important;
        color: #C2AF96 !important;
        font-weight: 500;
        font-style: italic;
        font-family: "Lora", serif;
        font-optical-sizing: auto;
    }

    .colorBtn2 {
        background-color: #C2AF96 !important;
        color: #133B7C !important;
        font-weight: 500;
        font-style: italic;
        font-family: "Lora", serif;
        font-optical-sizing: auto;
    }

    .colorBtn1:active,
    .colorBtn2:active {
        background-color: rgb(0, 0, 0) !important;
        color: #C2AF96 !important;
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
        border-left: 1px solid #133B7C;
        border-right: 1px solid #133B7C;
        border-bottom: 1px solid #133B7C;
        padding-right: 0px !important;
        padding-left: 0px !important;
        border-top-left-radius: 10px;
    }
</style>