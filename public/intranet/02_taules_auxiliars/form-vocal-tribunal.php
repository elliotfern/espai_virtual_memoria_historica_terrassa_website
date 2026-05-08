<?php
require_once APP_ROOT . '/public/intranet/includes/header.php';
?>

<div class="container mb-5 border rounded p-4" style="background-color:#eaeaea">
    <div class="container">
        <div class="row">
            <h2>Gestió base de dades auxiliars: Vocals tribunal</h2>

            <div id="titolForm" class="mb-3"></div>

            <form id="vocalForm">
                <div class="row g-4">

                    <div class="alert alert-success" role="alert" id="okMessage" style="display:none">
                        <div id="okText"></div>
                    </div>

                    <div class="alert alert-danger" role="alert" id="errMessage" style="display:none">
                        <div id="errText"></div>
                    </div>

                    <!-- Identificador del medio -->
                    <input type="hidden" name="id" id="id" value="">

                    <div class="col-md-6">
                        <label for="nom" class="form-label fw-bold">Nom</label>
                        <input type="text" class="form-control" id="nom" name="nom" value="" required>
                    </div>

                    <div class="col-md-6">
                        <label for="cognoms" class="form-label fw-bold">Cognoms</label>
                        <input type="text" class="form-control" id="cognoms" name="cognoms" value="" required>
                    </div>

                    <div class="col-md-6">
                        <label for="carrec" class="form-label fw-bold">Càrrec (opcional)</label>
                        <input type="text" class="form-control" id="carrec" name="carrec" value="">
                    </div>

                    <!-- Botonera -->
                    <div class="row pt-3">
                        <div class="col">
                            <a class="btn btn-secondary" role="button" onclick="goBack()">Tornar enrere</a>
                        </div>
                        <div class="col d-flex justify-content-end align-items-center gap-2">
                            <button class="btn btn-primary" id="btn" type="submit">
                                Desar
                            </button>
                        </div>
                    </div>

                </div>
            </form>

        </div>
    </div>
</div>