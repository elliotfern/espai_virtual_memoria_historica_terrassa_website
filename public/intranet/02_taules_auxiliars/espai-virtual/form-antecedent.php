<?php
require_once APP_ROOT . '/public/intranet/includes/header.php';
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="antecedentForm" action="" method="post">
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

                <div class="col-md-2 mb-4">
                    <label for="ordre" class="form-label negreta">Ordre:</label>
                    <input type="number" class="form-control" id="ordre" name="ordre" value="">
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <div class="col-md-3 mb-4">
                    <label for="image_id" class="form-label negreta">Imatge:</label>
                    <select class="form-select" id="image_id" name="image_id">
                    </select>
                </div>

                <div class="col-md-3 mb-4">
                    <label for="layout_image_left" class="form-label negreta">Posició imatge:</label>
                    <select class="form-select" id="layout_image_left" name="layout_image_left">
                        <option value="0">Imatge a la dreta</option>
                        <option value="1">Imatge a l'esquerra</option>
                    </select>
                </div>

                <div class="col-md-3 mb-4">
                    <label for="show_in_timeline" class="form-label negreta">Mostrar a la timeline:</label>
                    <select class="form-select" id="show_in_timeline" name="show_in_timeline">
                        <option value="1">Sí</option>
                        <option value="0">No</option>
                    </select>
                </div>

                <hr>

                <div class="col-md-12 mb-2">
                    <h4>Dades en català</h4>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="any_text_ca" class="form-label negreta">Data / període (català):</label>
                    <input type="text" class="form-control" id="any_text_ca" name="any_text_ca" value="">
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <div class="col-md-8 mb-4">
                    <label for="titol_ca" class="form-label negreta">Títol (català):</label>
                    <input type="text" class="form-control" id="titol_ca" name="titol_ca" value="">
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <div class="col-md-12 mb-4">
                    <label for="resum_timeline_ca" class="form-label negreta">Resum timeline (català):</label>
                    <textarea class="form-control" id="resum_timeline_ca" name="resum_timeline_ca" rows="3"></textarea>
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <div class="col-md-12 mb-4">
                    <label for="link_url_ca" class="form-label negreta">URL enllaç (català):</label>
                    <input type="text" class="form-control" id="link_url_ca" name="link_url_ca" value="">
                </div>

                <div class="col-md-12 mb-4">
                    <label for="contingut_html_ca" class="form-label negreta">Contingut HTML (català):</label>
                    <!-- Campo oculto que almacena el valor de Trix -->
                    <input id="contingut_html_ca" type="hidden" name="contingut_html_ca" value="">

                    <!-- Editor Trix -->
                    <trix-editor class="form-control" input="contingut_html_ca"></trix-editor>
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <?php if (isUserAdmin()) : ?>
                    <hr>

                    <div class="col-md-12 mb-2">
                        <h4>Traduccions</h4>
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="any_text_es" class="form-label negreta">Data / període (castellà):</label>
                        <input type="text" class="form-control" id="any_text_es" name="any_text_es" value="">
                    </div>

                    <div class="col-md-8 mb-4">
                        <label for="titol_es" class="form-label negreta">Títol (castellà):</label>
                        <input type="text" class="form-control" id="titol_es" name="titol_es" value="">
                    </div>

                    <div class="col-md-12 mb-4">
                        <label for="resum_timeline_es" class="form-label negreta">Resum timeline (castellà):</label>
                        <textarea class="form-control" id="resum_timeline_es" name="resum_timeline_es" rows="3"></textarea>
                    </div>

                    <div class="col-md-12 mb-4">
                        <label for="link_url_es" class="form-label negreta">URL enllaç (castellà):</label>
                        <input type="text" class="form-control" id="link_url_es" name="link_url_es" value="">
                    </div>

                    <div class="col-md-12 mb-4">
                        <label for="contingut_html_es" class="form-label negreta">Contingut HTML (castellà):</label>
                        <!-- Campo oculto que almacena el valor de Trix -->
                        <input id="contingut_html_es" type="hidden" name="contingut_html_es" value="">

                        <!-- Editor Trix -->
                        <trix-editor class="form-control" input="contingut_html_es"></trix-editor>
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="any_text_en" class="form-label negreta">Data / període (anglès):</label>
                        <input type="text" class="form-control" id="any_text_en" name="any_text_en" value="">
                    </div>

                    <div class="col-md-8 mb-4">
                        <label for="titol_en" class="form-label negreta">Títol (anglès):</label>
                        <input type="text" class="form-control" id="titol_en" name="titol_en" value="">
                    </div>

                    <div class="col-md-12 mb-4">
                        <label for="resum_timeline_en" class="form-label negreta">Resum timeline (anglès):</label>
                        <textarea class="form-control" id="resum_timeline_en" name="resum_timeline_en" rows="3"></textarea>
                    </div>

                    <div class="col-md-12 mb-4">
                        <label for="link_url_en" class="form-label negreta">URL enllaç (anglès):</label>
                        <input type="text" class="form-control" id="link_url_en" name="link_url_en" value="">
                    </div>

                    <div class="col-md-12 mb-4">
                        <label for="contingut_html_en" class="form-label negreta">Contingut HTML (anglès):</label>
                        <!-- Campo oculto que almacena el valor de Trix -->
                        <input id="contingut_html_en" type="hidden" name="contingut_html_en" value="">

                        <!-- Editor Trix -->
                        <trix-editor class="form-control" input="contingut_html_en"></trix-editor>
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="any_text_fr" class="form-label negreta">Data / període (francès):</label>
                        <input type="text" class="form-control" id="any_text_fr" name="any_text_fr" value="">
                    </div>

                    <div class="col-md-8 mb-4">
                        <label for="titol_fr" class="form-label negreta">Títol (francès):</label>
                        <input type="text" class="form-control" id="titol_fr" name="titol_fr" value="">
                    </div>

                    <div class="col-md-12 mb-4">
                        <label for="resum_timeline_fr" class="form-label negreta">Resum timeline (francès):</label>
                        <textarea class="form-control" id="resum_timeline_fr" name="resum_timeline_fr" rows="3"></textarea>
                    </div>

                    <div class="col-md-12 mb-4">
                        <label for="link_url_fr" class="form-label negreta">URL enllaç (francès):</label>
                        <input type="text" class="form-control" id="link_url_fr" name="link_url_fr" value="">
                    </div>

                    <div class="col-md-12 mb-4">
                        <label for="contingut_html_fr" class="form-label negreta">Contingut HTML (francès):</label>
                        <!-- Campo oculto que almacena el valor de Trix -->
                        <input id="contingut_html_fr" type="hidden" name="contingut_html_fr" value="">

                        <!-- Editor Trix -->
                        <trix-editor class="form-control" input="contingut_html_fr"></trix-editor>
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="any_text_it" class="form-label negreta">Data / període (italià):</label>
                        <input type="text" class="form-control" id="any_text_it" name="any_text_it" value="">
                    </div>

                    <div class="col-md-8 mb-4">
                        <label for="titol_it" class="form-label negreta">Títol (italià):</label>
                        <input type="text" class="form-control" id="titol_it" name="titol_it" value="">
                    </div>

                    <div class="col-md-12 mb-4">
                        <label for="resum_timeline_it" class="form-label negreta">Resum timeline (italià):</label>
                        <textarea class="form-control" id="resum_timeline_it" name="resum_timeline_it" rows="3"></textarea>
                    </div>

                    <div class="col-md-12 mb-4">
                        <label for="link_url_it" class="form-label negreta">URL enllaç (italià):</label>
                        <input type="text" class="form-control" id="link_url_it" name="link_url_it" value="">
                    </div>

                    <div class="col-md-12 mb-4">
                        <label for="contingut_html_it" class="form-label negreta">Contingut HTML (italià):</label>
                        <!-- Campo oculto que almacena el valor de Trix -->
                        <input id="contingut_html_it" type="hidden" name="contingut_html_it" value="">

                        <!-- Editor Trix -->
                        <trix-editor class="form-control" input="contingut_html_it"></trix-editor>
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="any_text_pt" class="form-label negreta">Data / període (portuguès):</label>
                        <input type="text" class="form-control" id="any_text_pt" name="any_text_pt" value="">
                    </div>

                    <div class="col-md-8 mb-4">
                        <label for="titol_pt" class="form-label negreta">Títol (portuguès):</label>
                        <input type="text" class="form-control" id="titol_pt" name="titol_pt" value="">
                    </div>

                    <div class="col-md-12 mb-4">
                        <label for="resum_timeline_pt" class="form-label negreta">Resum timeline (portuguès):</label>
                        <textarea class="form-control" id="resum_timeline_pt" name="resum_timeline_pt" rows="3"></textarea>
                    </div>

                    <div class="col-md-12 mb-4">
                        <label for="link_url_pt" class="form-label negreta">URL enllaç (portuguès):</label>
                        <input type="text" class="form-control" id="link_url_pt" name="link_url_pt" value="">
                    </div>

                    <div class="col-md-12 mb-4">
                        <label for="contingut_html_pt" class="form-label negreta">Contingut HTML (portuguès):</label>
                        <!-- Campo oculto que almacena el valor de Trix -->
                        <input id="contingut_html_pt" type="hidden" name="contingut_html_pt" value="">

                        <!-- Editor Trix -->
                        <trix-editor class="form-control" input="contingut_html_pt"></trix-editor>
                    </div>
                <?php endif; ?>

                <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
                    <div class="col"></div>
                    <div class="col d-flex justify-content-end align-items-center">
                        <button class="btn btn-primary" id="btnSubmitAntecedent" type="submit">Desa les dades</button>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>