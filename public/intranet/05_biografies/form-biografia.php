<?php

require_once APP_ROOT . '/public/intranet/includes/header.php';
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <div class="container">
        <div class="row">
            <div id="titolForm"></div>

            <div class="alert alert-success" role="alert" id="okMessage" style="display:none">
                <div id="okText"></div>
            </div>

            <div class="alert alert-danger" role="alert" id="errMessage" style="display:none">
                <div id="errText"></div>
            </div>

            <form id="BiografiesForm">
                <input type="hidden" name="idRepresaliat" id="idRepresaliat" value="">
                <input type="hidden" name="id" id="id" value="">

                <!-- Crear el editor de texto -->
                <div class="col-md-12">
                    <label for="tema" class="form-label negreta">Biografia (català):</label>
                    <!-- Campo oculto que almacena el valor de Trix -->
                    <input id="biografiaCa" type="hidden" name="biografiaCa" value="">

                    <!-- Editor Trix -->
                    <trix-editor input="biografiaCa"></trix-editor>
                </div>

                <!-- Crear el editor de texto -->
                <div class="col-md-12">
                    <label for="tema" class="form-label negreta">Biografia (castellà):</label>
                    <!-- Campo oculto que almacena el valor de Trix -->
                    <input id="biografiaEs" type="hidden" name="biografiaEs" value="">

                    <!-- Editor Trix -->
                    <trix-editor input="biografiaEs"></trix-editor>
                </div>

                <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
                    <div class="col">

                    </div>
                    <div class="col d-flex justify-content-end align-items-center">
                        <button class="btn btn-primary" id="btnBiografies" type="submit">Modificar dades</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


</div>