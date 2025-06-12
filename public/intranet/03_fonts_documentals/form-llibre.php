<?php
require_once APP_ROOT . '/public/intranet/includes/header.php';
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="bibliografiaForm">
        <div class="container">
            <div class="row g-4">
                <div id="titolForm"></div>

                <div class="alert alert-success" role="alert" id="okMessage" style="display:none">
                    <div id="okText"></div>
                </div>

                <div class="alert alert-danger" role="alert" id="errMessage" style="display:none">
                    <div id="errText"></div>
                </div>

                <input type="hidden" id="id" name="id" value="">

                <div class="col-md-12 mb-4">
                    <label for="llibre" class="form-label negreta">Nom del llibre:</label>
                    <input type="text" class="form-control" name="llibre" id="llibre" value="">
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="autor" class="form-label negreta">Nom i cognoms de l'autor (COGNOMS, Nom):</label>
                    <input type="text" class="form-control" name="autor" id="autor" value="">
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="any" class="form-label negreta">Any de publicació:</label>
                    <input type="text" class="form-control" name="any" id="any" value="">
                </div>

                <div class="col-md-4 mb-4">
                    <label for="volum" class="form-label negreta">Volum o número de l'obra (opcional):</label>
                    <input type="text" class="form-control" name="volum" id="volum" value="">
                </div>

                <div class="col-md-4 mb-4">
                    <label for="editorial" class="form-label negreta">Editorial:</label>
                    <input type="text" class="form-control" name="editorial" id="editorial" value="">
                </div>

                <div class="col-md-4 mb-4">
                    <label for="ciutat" class="form-label negreta">Ciutat edició llibre:</label>
                    <select class="form-select" name="ciutat" id="ciutat" value="">
                    </select>
                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/auxiliars/nou-municipi/" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi">Afegir municipi</a>
                        <button id="refreshButton" class="btn btn-primary btn-sm">Actualitzar llistat Municipis</button>
                    </div>
                </div>

                <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
                    <div class="col">
                    </div>
                    <div class="col d-flex justify-content-end align-items-center">
                        <button class="btn btn-primary" type="submit" id="btnBibliografia">Modificar dades</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>