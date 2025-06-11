<?php
require_once APP_ROOT . '/public/intranet/includes/header.php';
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="categoriesForm" action="" type="post">
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
                    <label for="categoria_cat" class="form-label negreta">Categoria repressió (català):</label>
                    <input type="text" class="form-control" id="categoria_cat" name="categoria_cat" value="">
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="grup" class="form-label negreta">Grup de repressió:</label>
                    <select class="form-select" id="grup" name="grup" value="">
                    </select>
                </div>

                <?php if (isUserAdmin()) : ?>
                    <hr>

                    <div class="col-md-4 mb-4">
                        <label for="categoria_cast" class="form-label negreta">Categoria repressió (castellà):</label>
                        <input type="text" class="form-control" id="categoria_cast" name="categoria_cast" value="">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="categoria_eng" class="form-label negreta">Categoria repressió (anglès):</label>
                        <input type="text" class="form-control" id="categoria_eng" name="categoria_eng" value="">

                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="categoria_fr" class="form-label negreta">Categoria repressió (francès):</label>
                        <input type="text" class="form-control" id="categoria_fr" name="categoria_fr" value="">

                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="categoria_it" class="form-label negreta">Categoria repressió (italià):</label>
                        <input type="text" class="form-control" id="categoria_it" name="categoria_it" value="">

                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="categoria_pt" class="form-label negreta">Categoria repressió (portuguès):</label>
                        <input type="text" class="form-control" id="categoria_pt" name="categoria_pt" value="">
                    </div>

                <?php endif; ?>

                <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
                    <div class="col"></div>

                    <div class="col d-flex justify-content-end align-items-center">
                        <button class="btn btn-primary" id="btnSubmitCategoria" type="submit">Modificar dades</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>