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

                <div class="col-md-12 mb-4">
                    <label for="espai_cat" class="form-label negreta">Descripció espai (català):</label>
                    <textarea class="form-control" id="descripcio_espai" name="descripcio_espai" rows="4"></textarea>
                    <div class="avis-form">
                        * Opcional
                    </div>
                </div>

                <?php if (isUserAdmin()) : ?>
                    <hr>

                    <div class="col-md-4 mb-4">
                        <label for="espai_es" class="form-label negreta">Espai (castellano):</label>
                        <input type="text" class="form-control" id="espai_es" name="espai_es" value="<?php echo $espai_es_old ?? ''; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="espai_en" class="form-label negreta">Space (English):</label>
                        <input type="text" class="form-control" id="espai_en" name="espai_en" value="<?php echo $espai_en_old ?? ''; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="espai_fr" class="form-label negreta">Espace (français):</label>
                        <input type="text" class="form-control" id="espai_fr" name="espai_fr" value="<?php echo $espai_fr_old ?? ''; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="espai_it" class="form-label negreta">Spazio (italiano):</label>
                        <input type="text" class="form-control" id="espai_it" name="espai_it" value="<?php echo $espai_it_old ?? ''; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="espai_pt" class="form-label negreta">Espaço (português):</label>
                        <input type="text" class="form-control" id="espai_pt" name="espai_pt" value="<?php echo $espai_pt_old ?? ''; ?>">
                    </div>

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