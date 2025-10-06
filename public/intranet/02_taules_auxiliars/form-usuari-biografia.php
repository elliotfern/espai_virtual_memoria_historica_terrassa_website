<?php
require_once APP_ROOT . '/public/intranet/includes/header.php';
?>


<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <div class="container">
        <h2>Gestió base de dades auxiliars: biografies dels usuaris</h2>
        <div id="titolForm"></div>
        <?php if (isUserAdmin()) : ?>

            <div class="alert alert-success" role="alert" id="okMessage" style="display:none">
                <div id="okText"></div>
            </div>

            <div class="alert alert-danger" role="alert" id="errMessage" style="display:none">
                <div id="errText"></div>
            </div>

            <form id="usuariBioForm">
                <div class="row g-3">

                    <input type="hidden" name="id" id="id" value="">
                    <input type="hidden" name="id_user" id="id_user" value="">

                    <div class="col-md-6">
                        <label for="bio_curta_ca" class="form-label negreta">Descripció curta (català):</label>
                        <input type="text" class="form-control" id="bio_curta_ca" name="bio_curta_ca" value="">
                    </div>

                    <div class="col-md-6">
                        <label for="bio_curta_es" class="form-label negreta">Descripció curta (castellà):</label>
                        <input type="text" class="form-control" id="bio_curta_es" name="bio_curta_es" value="">
                    </div>

                    <div class="col-md-6">
                        <label for="bio_curta_en" class="form-label negreta">Descripció curta (anglès):</label>
                        <input type="text" class="form-control" id="bio_curta_en" name="bio_curta_en" value="">
                    </div>

                    <div class="col-md-6">
                        <label for="bio_curta_fr" class="form-label negreta">Descripció curta (francès):</label>
                        <input type="text" class="form-control" id="bio_curta_fr" name="bio_curta_fr" value="">
                    </div>

                    <div class="col-md-6">
                        <label for="bio_curta_it" class="form-label negreta">Descripció curta (italià):</label>
                        <input type="text" class="form-control" id="bio_curta_it" name="bio_curta_it" value="">
                    </div>

                    <div class="col-md-6">
                        <label for="bio_curta_pt" class="form-label negreta">Descripció curta (portuguès):</label>
                        <input type="text" class="form-control" id="bio_curta_pt" name="bio_curta_pt" value="">
                    </div>

                    <!-- Crear el editor de texto -->
                    <div class="col-md-12">
                        <label for="bio_ca" class="form-label negreta">Biografia (català):</label>
                        <!-- Campo oculto que almacena el valor de Trix -->
                        <input id="bio_ca" type="hidden" name="bio_ca" value="">

                        <!-- Editor Trix -->
                        <trix-editor input="bio_ca"></trix-editor>
                    </div>

                    <div class="col-md-12">
                        <label for="bio_es" class="form-label negreta">Biografia (castella):</label>
                        <!-- Campo oculto que almacena el valor de Trix -->
                        <input id="bio_es" type="hidden" name="bio_es" value="">

                        <!-- Editor Trix -->
                        <trix-editor input="bio_es"></trix-editor>
                    </div>

                    <div class="col-md-12">
                        <label for="bio_en" class="form-label negreta">Biografia (anglès):</label>
                        <!-- Campo oculto que almacena el valor de Trix -->
                        <input id="bio_en" type="hidden" name="bio_en" value="">

                        <!-- Editor Trix -->
                        <trix-editor input="bio_en"></trix-editor>
                    </div>

                    <div class="col-md-12">
                        <label for="bio_fr" class="form-label negreta">Biografia (francès):</label>
                        <!-- Campo oculto que almacena el valor de Trix -->
                        <input id="bio_fr" type="hidden" name="bio_fr" value="">

                        <!-- Editor Trix -->
                        <trix-editor input="bio_fr"></trix-editor>
                    </div>

                    <div class="col-md-12">
                        <label for="bio_it" class="form-label negreta">Biografia (italià):</label>
                        <!-- Campo oculto que almacena el valor de Trix -->
                        <input id="bio_it" type="hidden" name="bio_it" value="">

                        <!-- Editor Trix -->
                        <trix-editor input="bio_it"></trix-editor>
                    </div>

                    <div class="col-md-12">
                        <label for="bio_pt" class="form-label negreta">Biografia (portuguès):</label>
                        <!-- Campo oculto que almacena el valor de Trix -->
                        <input id="bio_pt" type="hidden" name="bio_pt" value="">

                        <!-- Editor Trix -->
                        <trix-editor input="bio_pt"></trix-editor>
                    </div>

                    <div class="row espai-superior" style="padding-top:25px">
                        <div class="col">
                            <a class="btn btn-secondary" role="button" aria-disabled="true" onclick="goBack()">Tornar enrere</a>
                        </div>
                        <div class="col d-flex justify-content-end align-items-center">

                            <button class="btn btn-primary" id="btnUsuariBio" type="submit">Modificar dades</button>
                        </div>
                    </div>
                </div>
            </form>

        <?php endif; ?>
    </div>
</div>