<?php
require_once APP_ROOT . '/public/intranet/includes/header.php';
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="empresaForm" action="" type="post">
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
                    <label for="empresa_ca" class="form-label negreta">Nom empresa o institució pública (català):</label>
                    <input type="text" class="form-control" id="empresa_ca" name="empresa_ca" value="">
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <?php if (isUserAdmin()) : ?>
                    <hr>

                    <div class="col-md-4 mb-4">
                        <label for="empresa_es" class="form-label negreta">Nom empresa o institució pública (castellà):</label>
                        <input type="text" class="form-control" id="empresa_es" name="empresa_es" value="">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="empresa_en" class="form-label negreta">Nom empresa o institució pública (anglès):</label>
                        <input type="text" class="form-control" id="empresa_en" name="empresa_en" value="">

                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="empresa_fr" class="form-label negreta">Nom empresa o institució pública (francès):</label>
                        <input type="text" class="form-control" id="empresa_fr" name="empresa_fr" value="">

                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="empresa_it" class="form-label negreta">Nom empresa o institució pública (italià):</label>
                        <input type="text" class="form-control" id="empresa_it" name="empresa_it" value="">

                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="empresa_pt" class="form-label negreta">Nom empresa o institució pública (portuguès):</label>
                        <input type="text" class="form-control" id="empresa_pt" name="empresa_pt" value="">
                    </div>

                <?php endif; ?>

                <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
                    <div class="col"></div>

                    <div class="col d-flex justify-content-end align-items-center">
                        <button class="btn btn-primary" id="btnSubmitEmpresa" type="submit">Modificar dades</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>