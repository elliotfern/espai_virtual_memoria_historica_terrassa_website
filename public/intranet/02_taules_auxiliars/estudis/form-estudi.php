<?php
require_once APP_ROOT . '/public/intranet/includes/header.php';
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="estudiForm" action="" method="post">
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
                    <label for="slug" class="form-label negreta">Slug:</label>
                    <input type="text" class="form-control" id="slug" name="slug" value="">
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <div class="col-md-2 mb-4">
                    <label for="any_publicacio" class="form-label negreta">Any de publicació:</label>
                    <input type="number" class="form-control" id="any_publicacio" name="any_publicacio" value="">
                </div>

                <div class="col-md-2 mb-4">
                    <label for="periode_id" class="form-label negreta">Període:</label>
                    <select class="form-select" id="periode_id" name="periode_id">
                    </select>
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <div class="col-md-2 mb-4">
                    <label for="territori_id" class="form-label negreta">Territori:</label>
                    <select class="form-select" id="territori_id" name="territori_id">
                    </select>
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <div class="col-md-2 mb-4">
                    <label for="tipus_id" class="form-label negreta">Tipus:</label>
                    <select class="form-select" id="tipus_id" name="tipus_id">
                    </select>
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <div class="col-md-12 mb-4">
                    <label for="autors" class="form-label negreta">Autor/s:</label>
                    <select class="form-select" id="autors" name="autors[]" multiple>
                    </select>
                    <div class="avis-form">
                        * Selecciona un o més autors
                    </div>
                </div>

                <hr>

                <div class="col-md-12 mb-2">
                    <h4>Dades en català</h4>
                </div>

                <div class="col-md-12 mb-4">
                    <label for="titol_ca" class="form-label negreta">Títol (català):</label>
                    <input type="text" class="form-control" id="titol_ca" name="titol_ca" value="">
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <div class="col-md-12 mb-4">
                    <label for="resum_ca" class="form-label negreta">Resum (català):</label>
                    <textarea class="form-control" id="resum_ca" name="resum_ca" rows="5"></textarea>
                </div>

                <div class="col-md-12 mb-4">
                    <label for="url_document_ca" class="form-label negreta">URL document (català):</label>
                    <input type="text" class="form-control" id="url_document_ca" name="url_document_ca" value="">
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <?php if (isUserAdmin()) : ?>
                    <hr>

                    <div class="col-md-12 mb-2">
                        <h4>Traduccions</h4>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label for="titol_es" class="form-label negreta">Títol (castellà):</label>
                        <input type="text" class="form-control" id="titol_es" name="titol_es" value="">
                    </div>

                    <div class="col-md-6 mb-4">
                        <label for="url_document_es" class="form-label negreta">URL document (castellà):</label>
                        <input type="text" class="form-control" id="url_document_es" name="url_document_es" value="">
                    </div>

                    <div class="col-md-12 mb-4">
                        <label for="resum_es" class="form-label negreta">Resum (castellà):</label>
                        <textarea class="form-control" id="resum_es" name="resum_es" rows="4"></textarea>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label for="titol_en" class="form-label negreta">Títol (anglès):</label>
                        <input type="text" class="form-control" id="titol_en" name="titol_en" value="">
                    </div>

                    <div class="col-md-6 mb-4">
                        <label for="url_document_en" class="form-label negreta">URL document (anglès):</label>
                        <input type="text" class="form-control" id="url_document_en" name="url_document_en" value="">
                    </div>

                    <div class="col-md-12 mb-4">
                        <label for="resum_en" class="form-label negreta">Resum (anglès):</label>
                        <textarea class="form-control" id="resum_en" name="resum_en" rows="4"></textarea>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label for="titol_fr" class="form-label negreta">Títol (francès):</label>
                        <input type="text" class="form-control" id="titol_fr" name="titol_fr" value="">
                    </div>

                    <div class="col-md-6 mb-4">
                        <label for="url_document_fr" class="form-label negreta">URL document (francès):</label>
                        <input type="text" class="form-control" id="url_document_fr" name="url_document_fr" value="">
                    </div>

                    <div class="col-md-12 mb-4">
                        <label for="resum_fr" class="form-label negreta">Resum (francès):</label>
                        <textarea class="form-control" id="resum_fr" name="resum_fr" rows="4"></textarea>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label for="titol_it" class="form-label negreta">Títol (italià):</label>
                        <input type="text" class="form-control" id="titol_it" name="titol_it" value="">
                    </div>

                    <div class="col-md-6 mb-4">
                        <label for="url_document_it" class="form-label negreta">URL document (italià):</label>
                        <input type="text" class="form-control" id="url_document_it" name="url_document_it" value="">
                    </div>

                    <div class="col-md-12 mb-4">
                        <label for="resum_it" class="form-label negreta">Resum (italià):</label>
                        <textarea class="form-control" id="resum_it" name="resum_it" rows="4"></textarea>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label for="titol_pt" class="form-label negreta">Títol (portuguès):</label>
                        <input type="text" class="form-control" id="titol_pt" name="titol_pt" value="">
                    </div>

                    <div class="col-md-6 mb-4">
                        <label for="url_document_pt" class="form-label negreta">URL document (portuguès):</label>
                        <input type="text" class="form-control" id="url_document_pt" name="url_document_pt" value="">
                    </div>

                    <div class="col-md-12 mb-4">
                        <label for="resum_pt" class="form-label negreta">Resum (portuguès):</label>
                        <textarea class="form-control" id="resum_pt" name="resum_pt" rows="4"></textarea>
                    </div>
                <?php endif; ?>

                <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
                    <div class="col"></div>
                    <div class="col d-flex justify-content-end align-items-center">
                        <button class="btn btn-primary" id="btnSubmitEstudi" type="submit">Desa les dades</button>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>