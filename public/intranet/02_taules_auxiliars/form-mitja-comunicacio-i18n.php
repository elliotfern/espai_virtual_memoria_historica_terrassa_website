<?php
require_once APP_ROOT . '/public/intranet/includes/header.php';
?>

<div class="container mb-5 border rounded p-4" style="background-color:#eaeaea">
    <div class="container">
        <div class="row">
            <h2>Gestió base de dades auxiliars: mitjans de comunicació</h2>

            <div id="titolForm" class="mb-3"></div>

            <?php if (isUserAdmin()) : ?>

                <form id="mitjaI18nForm" novalidate>
                    <div class="row g-4">

                        <div class="alert alert-success" role="alert" id="okMessage" style="display:none">
                            <div id="okText"></div>
                        </div>

                        <div class="alert alert-danger" role="alert" id="errMessage" style="display:none">
                            <div id="errText"></div>
                        </div>

                        <!-- Identificador del medio -->
                        <input type="hidden" name="id" id="id" value="">
                        <input type="hidden" name="slug" id="slug" value="">

                        <!-- Info rápida del medio (solo lectura, lo rellena TS) -->
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <div class="text-muted small">Slug</div>
                                            <div class="fw-semibold" id="infoSlug">—</div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-muted small">Tipus</div>
                                            <div class="fw-semibold" id="infoTipus">—</div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-muted small">Web</div>
                                            <div class="fw-semibold" id="infoWeb">—</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabs idiomas -->
                        <div class="col-12">
                            <ul class="nav nav-tabs" id="mitjaLangTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="tab-ca" data-bs-toggle="tab" data-bs-target="#pane-ca" type="button" role="tab" aria-controls="pane-ca" aria-selected="true">
                                        CA
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="tab-es" data-bs-toggle="tab" data-bs-target="#pane-es" type="button" role="tab" aria-controls="pane-es" aria-selected="false">
                                        ES
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="tab-en" data-bs-toggle="tab" data-bs-target="#pane-en" type="button" role="tab" aria-controls="pane-en" aria-selected="false">
                                        EN
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="tab-fr" data-bs-toggle="tab" data-bs-target="#pane-fr" type="button" role="tab" aria-controls="pane-fr" aria-selected="false">
                                        FR
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="tab-it" data-bs-toggle="tab" data-bs-target="#pane-it" type="button" role="tab" aria-controls="pane-it" aria-selected="false">
                                        IT
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="tab-pt" data-bs-toggle="tab" data-bs-target="#pane-pt" type="button" role="tab" aria-controls="pane-pt" aria-selected="false">
                                        PT
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content border border-top-0 rounded-bottom p-3 bg-white" id="mitjaLangTabContent">

                                <!-- CA -->
                                <div class="tab-pane fade show active" id="pane-ca" role="tabpanel" aria-labelledby="tab-ca" tabindex="0">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="nom_ca" class="form-label fw-bold">Nom (CA)</label>
                                            <input type="text" class="form-control" id="nom_ca" name="nom_ca" value="" required>
                                        </div>
                                        <div class="col-12">
                                            <label for="descripcio_ca" class="form-label fw-bold">Descripció (CA)</label>
                                            <textarea class="form-control" id="descripcio_ca" name="descripcio_ca" rows="3"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- ES -->
                                <div class="tab-pane fade" id="pane-es" role="tabpanel" aria-labelledby="tab-es" tabindex="0">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="nom_es" class="form-label fw-bold">Nom (ES)</label>
                                            <input type="text" class="form-control" id="nom_es" name="nom_es" value="">
                                        </div>
                                        <div class="col-12">
                                            <label for="descripcio_es" class="form-label fw-bold">Descripció (ES)</label>
                                            <textarea class="form-control" id="descripcio_es" name="descripcio_es" rows="3"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- EN -->
                                <div class="tab-pane fade" id="pane-en" role="tabpanel" aria-labelledby="tab-en" tabindex="0">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="nom_en" class="form-label fw-bold">Nom (EN)</label>
                                            <input type="text" class="form-control" id="nom_en" name="nom_en" value="">
                                        </div>
                                        <div class="col-12">
                                            <label for="descripcio_en" class="form-label fw-bold">Descripció (EN)</label>
                                            <textarea class="form-control" id="descripcio_en" name="descripcio_en" rows="3"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- FR -->
                                <div class="tab-pane fade" id="pane-fr" role="tabpanel" aria-labelledby="tab-fr" tabindex="0">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="nom_fr" class="form-label fw-bold">Nom (FR)</label>
                                            <input type="text" class="form-control" id="nom_fr" name="nom_fr" value="">
                                        </div>
                                        <div class="col-12">
                                            <label for="descripcio_fr" class="form-label fw-bold">Descripció (FR)</label>
                                            <textarea class="form-control" id="descripcio_fr" name="descripcio_fr" rows="3"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- IT -->
                                <div class="tab-pane fade" id="pane-it" role="tabpanel" aria-labelledby="tab-it" tabindex="0">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="nom_it" class="form-label fw-bold">Nom (IT)</label>
                                            <input type="text" class="form-control" id="nom_it" name="nom_it" value="">
                                        </div>
                                        <div class="col-12">
                                            <label for="descripcio_it" class="form-label fw-bold">Descripció (IT)</label>
                                            <textarea class="form-control" id="descripcio_it" name="descripcio_it" rows="3"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- PT -->
                                <div class="tab-pane fade" id="pane-pt" role="tabpanel" aria-labelledby="tab-pt" tabindex="0">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="nom_pt" class="form-label fw-bold">Nom (PT)</label>
                                            <input type="text" class="form-control" id="nom_pt" name="nom_pt" value="">
                                        </div>
                                        <div class="col-12">
                                            <label for="descripcio_pt" class="form-label fw-bold">Descripció (PT)</label>
                                            <textarea class="form-control" id="descripcio_pt" name="descripcio_pt" rows="3"></textarea>
                                        </div>
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
                                <button class="btn btn-primary" id="btnMitjaI18n" type="submit">
                                    Desar traduccions
                                </button>
                            </div>
                        </div>

                    </div>
                </form>

            <?php endif; ?>
        </div>
    </div>
</div>