<?php
require_once APP_ROOT . '/public/intranet/includes/header.php';
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="arxiuForm">
        <div class="container">
            <div class="row g-5">
                <div id="titolForm"></div>

                <div class="alert alert-success" role="alert" id="okMessage" style="display:none">
                    <div id="okText"></div>
                </div>

                <div class="alert alert-danger" role="alert" id="errMessage" style="display:none">
                    <div id="errText"></div>
                </div>

                <input type="hidden" id="id" name="id" value="">

                <div class="col-md-8 mb-4">
                    <label for="arxiu" class="form-label negreta">Nom Arxiu:</label>
                    <input type="text" class="form-control" name="arxiu" id="arxiu" value="">
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="codi" class="form-label negreta">Codi arxiu:</label>
                    <input type="text" class="form-control" name="codi" id="codi" value="">
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <div class="col-md-12 mb-4">
                    <label for="descripcio" class="form-label negreta">Descripció arxiu/fonts:</label>
                    <input type="text" class="form-control" name="descripcio" id="descripcio" value="">
                </div>

                <div class="col-md-8 mb-4">
                    <label for="web" class="form-label negreta">Web:</label>
                    <input type="text" class="form-control" name="web" id="web" value="">
                </div>

                <div class="col-md-4 mb-4">
                    <label for="ciutat" class="form-label negreta">Ciutat arxiu:</label>
                    <select class="form-select" name="ciutat" id="ciutat" value="">
                    </select>

                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/auxiliars/nou-municipi" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi">Afegir municipi</a>
                        <button type="button" id="refreshButton" class="btn btn-primary btn-sm">Actualitzar llistat Municipis</button>
                    </div>
                </div>

                <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
                    <div class="col">
                    </div>
                    <div class="col d-flex justify-content-end align-items-center">
                        <button class="btn btn-primary" type="submit" id="btnArxiu">Modificar dades</button>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>