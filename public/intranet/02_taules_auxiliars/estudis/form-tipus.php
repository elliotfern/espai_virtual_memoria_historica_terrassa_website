<?php
require_once APP_ROOT . '/public/intranet/includes/header.php';
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="tipusForm" action="" method="post">
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

                <div class="col-md-3 mb-4">
                    <label for="sort_order" class="form-label negreta">Ordre:</label>
                    <input type="number" class="form-control" id="sort_order" name="sort_order" value="">
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <div class="col-md-9 mb-4">
                    <label for="nom_ca" class="form-label negreta">Tipus (català):</label>
                    <input type="text" class="form-control" id="nom_ca" name="nom_ca" value="">
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <?php if (isUserAdmin()) : ?>
                    <hr>

                    <div class="col-md-6 mb-4">
                        <label for="nom_es" class="form-label negreta">Tipus (castellà):</label>
                        <input type="text" class="form-control" id="nom_es" name="nom_es" value="">
                    </div>

                    <div class="col-md-6 mb-4">
                        <label for="nom_en" class="form-label negreta">Tipus (anglès):</label>
                        <input type="text" class="form-control" id="nom_en" name="nom_en" value="">
                    </div>

                    <div class="col-md-6 mb-4">
                        <label for="nom_fr" class="form-label negreta">Tipus (francès):</label>
                        <input type="text" class="form-control" id="nom_fr" name="nom_fr" value="">
                    </div>

                    <div class="col-md-6 mb-4">
                        <label for="nom_it" class="form-label negreta">Tipus (italià):</label>
                        <input type="text" class="form-control" id="nom_it" name="nom_it" value="">
                    </div>

                    <div class="col-md-6 mb-4">
                        <label for="nom_pt" class="form-label negreta">Tipus (portuguès):</label>
                        <input type="text" class="form-control" id="nom_pt" name="nom_pt" value="">
                    </div>
                <?php endif; ?>

                <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
                    <div class="col"></div>

                    <div class="col d-flex justify-content-end align-items-center">
                        <button class="btn btn-primary" id="btnSubmitTipus" type="submit">Desa les dades</button>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>