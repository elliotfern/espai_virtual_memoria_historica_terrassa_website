<?php
require_once APP_ROOT . '/public/intranet/includes/header.php';
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <div class="container">
        <div class="row">
            <h2>Gestió base de dades auxiliars: imatges / pdf</h2>
            <div id="titolForm"></div>
            <?php if (isUserAdmin()) : ?>

                <form id="imatgeForm" novalidate enctype="multipart/form-data">
                    <div class="row g-4">

                        <!-- OK / ERR -->
                        <div class="alert alert-success" role="alert" id="okMessage" style="display:none">
                            <div id="okText"></div>
                        </div>

                        <div class="alert alert-danger" role="alert" id="errMessage" style="display:none">
                            <div id="errText"></div>
                        </div>

                        <!-- ID (create: vacío, por consistencia) -->
                        <input type="hidden" name="id" id="id" value="">

                        <!-- Tipo -->
                        <div class="col-md-4">
                            <label for="tipus" class="form-label fw-bold">Tipus *</label>
                            <select class="form-select" id="tipus" name="tipus" required>
                                <option value="">Selecciona…</option>
                                <option value="1">Represaliat</option>
                                <option value="2">Usuari web</option>
                                <option value="3">Galeria multimèdia</option>
                                <option value="4">Aparició Premsa</option>
                            </select>
                        </div>

                        <!-- Persona (opcional) -->
                        <div class="col-md-4">
                            <label for="idPersona" class="form-label fw-bold">Persona (ID)</label>
                            <select class="form-select" id="idPersona" name="idPersona">
                                <option value="">Selecciona…</option>
                            </select>
                            <div class="form-text">Només si la imatge està associada a una fitxa de persona.</div>
                        </div>

                        <!-- Nom imatge -->
                        <div class="col-md-4">
                            <label for="nomImatge" class="form-label fw-bold">Nom / títol imatge *</label>
                            <input type="text" class="form-control" id="nomImatge" name="nomImatge" required>
                        </div>

                        <!-- Archivo -->
                        <div class="col-12">
                            <label for="file" class="form-label fw-bold">Arxiu *</label>
                            <input type="file" class="form-control" id="file" name="file"
                                accept="image/jpeg,image/png,application/pdf">
                            <div class="form-text">Formats admesos: JPG, PNG, PDF.</div>
                        </div>

                        <!-- Preview -->
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="text-muted small mb-2">Previsualització</div>
                                    <div id="previewBox" class="d-flex align-items-center gap-3">
                                        <div id="previewInner" class="text-muted">—</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botonera -->
                        <div class="row pt-3">
                            <div class="col">
                                <a class="btn btn-secondary" role="button" onclick="goBack()">Tornar enrere</a>
                            </div>
                            <div class="col d-flex justify-content-end align-items-center gap-2">
                                <button class="btn btn-primary" id="btnImatge" type="submit">
                                    Pujar imatge
                                </button>
                            </div>
                        </div>

                    </div>
                </form>

            <?php else : ?>

                <div class="alert alert-warning" role="alert">
                    No tens permisos d'administració per modificar auxiliars.
                </div>

            <?php endif; ?>
        </div>
    </div>
</div>