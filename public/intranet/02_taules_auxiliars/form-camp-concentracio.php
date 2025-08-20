<?php
require_once APP_ROOT . '/public/intranet/includes/header.php';
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="campForm" action="" type="post">
        <div class="container">
            <div class="row g-3">
                <div id="titolForm"></div>
                <div class="alert alert-success" role="alert" id="okMessage" style="display:none">
                    <div id="okText"></div>
                </div>

                <div class="alert alert-danger" role="alert" id="errMessage" style="display:none">
                    <div id="errText"></div>
                </div>

                <input type="hidden" name="id" id="id" value="">


                <div class="col-md-4 mb-4">
                    <label for="tipus" class="form-label negreta">Tipus de camp:</label>
                    <select class="form-select" name="tipus" id="tipus" value="">
                    </select>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="nom" class="form-label negreta">Nom del camp de concentració:</label>
                    <input type="text" class="form-control" id="nom" name="nom" value="">
                </div>

                <div class="col-md-4 mb-4">
                    <label for="municipi" class="form-label negreta">Municipi:</label>
                    <select class="form-select" id="municipi" value="" name="municipi">
                    </select>
                    <div class="mt-2">
                        <a href="<?php echo APP_WEB . APP_INTRANET . $url['auxiliars'] ?>/nou-municipi" target="_blank" class="btn btn-secondary btn-sm" id="afegir">Afegir municipi</a>
                        <button id="refreshButton" class="btn btn-primary btn-sm">Actualitzar llistat</button>
                    </div>
                </div>

                <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
                    <div class="col"></div>

                    <div class="col d-flex justify-content-end align-items-center">
                        <button class="btn btn-primary" id="btnSubmitCamp" type="submit">Modificar dades</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>