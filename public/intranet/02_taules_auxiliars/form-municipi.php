<?php
require_once APP_ROOT . '/public/intranet/includes/header.php';
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="municipiForm">
        <div class="container">
            <div id="titolFormMunicipi"></div>
            <div class="row g-3">
                <div class="alert alert-success" role="alert" id="okMessage" style="display:none">
                    <h4 class="alert-heading"><strong>Modificació correcte!</strong></h4>
                    <div id="okText"></div>
                </div>

                <div class="alert alert-danger" role="alert" id="errMessage" style="display:none">
                    <div id="errText"></div>
                </div>

                <input type="hidden" id="id" name="id" value="">

                <div class="alert alert-info">
                    <h5>Sobre l'ús general de topònims en català</h5>
                    <ul>

                        <li>1. Els topònims de Catalunya s'utilitzen en la seva forma oficial i, sempre que sigui possible, en la seva forma íntegra.</li>
                        <li>2. Els topònims d'altres territoris de l'àrea lingüística catalana s'utilitzen en la forma en català.</li>
                        <li>3. Els exotopònims, és a dir, els topònims de fora de l'àrea lingüística catalana s'utilitzen en català quan hi ha una forma establerta amb ús tradicional, sens perjudici que hi pugui figurar també la denominació en altres llengües del territori corresponent.</li>
                        <li>4. Els topònims de l'àrea lingüística occitana de fora de Catalunya s'utilitzen en la forma tradicional en català o en occità, tret de l'Aran, on s'utilitza la forma tradicional en occità, i sens perjudici que hi pugui figurar també la denominació en altres llengües del territori corresponent.</li>
                    </ul>
                </div>

                <div class="col-md-6 mb-4">
                    <label for="ciutat" class="form-label negreta">Nom oficial del municipi (a Catalunya, nom sempre en català):</label>
                    <input type="text" class="form-control" id="ciutat" name="ciutat" value="">
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <?php if (isUserAdmin()) : ?>
                    <hr>

                    <div class="col-md-6 mb-4">
                        <label for="ciutat" class="form-label negreta">Nom municipi (nom en català):</label>
                        <input type="text" class="form-control" id="ciutat_ca" name="ciutat_ca" value="">
                        <div class="avis-form">
                            * Omplir en cas que disposem del nom del municipi en català
                        </div>
                    </div>

                <?php endif; ?>
                <hr>

                <div class="col-md-4 mb-4">
                    <label for="comarca" class="form-label negreta">Comarca:</label>
                    <select class="form-select" id="comarca" value="" name="comarca">
                    </select>
                    <div class="mt-2">
                        <a href="<?php echo APP_WEB . APP_INTRANET . $url['auxiliars'] ?>/nova-comarca" target="_blank" class="btn btn-secondary btn-sm" id="afegirComarca">Afegir comarca</a>
                        <button id="refreshButtonComarca" class="btn btn-primary btn-sm">Actualitzar llistat</button>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="provincia" class="form-label negreta">Provincia/Departament:</label>
                    <select class="form-select" id="provincia" value="" name="provincia">
                    </select>
                    <div class="mt-2">
                        <a href="<?php echo APP_WEB . APP_INTRANET . $url['auxiliars'] ?>/nova-provincia" target="_blank" class="btn btn-secondary btn-sm" id="afegirProvincia">Afegir provincia</a>
                        <button id="refreshButtonProvincia" class="btn btn-primary btn-sm">Actualitzar llistat</button>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="comunitat" class="form-label negreta">Comunitat autònoma / Regió:</label>
                    <select class="form-select" id="comunitat" value="" name="comunitat">
                    </select>
                    <div class="mt-2">
                        <a href="<?php echo APP_WEB . APP_INTRANET . $url['auxiliars'] ?>/nova-comunitat" target="_blank" class="btn btn-secondary btn-sm" id="afegirComunitat">Afegir comunitat</a>
                        <button id="refreshButtonComunitat" class="btn btn-primary btn-sm">Actualitzar llistat</button>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="estat" class="form-label negreta">Estat:</label>
                    <select class="form-select" id="estat" value="" name="estat">
                    </select>
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                    <div class="mt-2">
                        <a href="<?php echo APP_WEB . APP_INTRANET . $url['auxiliars'] ?>/nou-estat/" target="_blank" class="btn btn-secondary btn-sm" id="afegirEstat">Afegir estat</a>
                        <button id="refreshButtonEstat" class="btn btn-primary btn-sm">Actualitzar llistat</button>
                    </div>
                </div>

                <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
                    <div class="col">
                    </div>

                    <div class="col d-flex justify-content-end align-items-center">
                        <button class="btn btn-primary" id="btnFormMunicipi" type="submit">Inserir dades</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

</div>