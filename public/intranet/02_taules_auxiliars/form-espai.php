<?php
require_once APP_ROOT . '/public/intranet/includes/header.php';
?>


<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="espaiForm">
        <div class="container">
            <div class="row g-3">
                <div id="titolForm"></div>

                <div class="alert alert-success" role="alert" id="okMessage" style="display:none">
                    <div id="okText"></div>
                </div>

                <div class="alert alert-danger" role="alert" id="errMessage" style="display:none">
                    <div id="errText"></div>
                </div>

                <input type="hidden" name="id" id="id" value="<?php echo $id_old; ?>">

                <div class="col-md-4 mb-4">
                    <label for="espai_cat" class="form-label negreta">Espai (català):</label>
                    <input type="text" class="form-control" id="espai_cat" name="espai_cat" value="">
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="municipi" class="form-label negreta">Municipi de l'espai:</label>
                    <select class="form-select" id="municipi" value="" name="municipi">
                    </select>
                    <div class="mt-2">
                        <a href="<?php echo APP_WEB . APP_INTRANET . $url['auxiliars'] ?>/nou-municipi" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi">Afegir municipi</a>
                        <button id="refreshButtonMunicipi" class="btn btn-primary btn-sm">Actualitzar llistat</button>
                    </div>
                </div>

                <?php if (isUserAdmin()) : ?>
                    <hr>

                <?php endif; ?>

                <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
                    <div class="col"></div>

                    <div class="col d-flex justify-content-end align-items-center">
                        <button class="btn btn-primary" id="btnEspai" type="submit">Modificar dades</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>