<?php
require_once APP_ROOT . '/public/intranet/includes/header.php';
?>

<div class="container mb-5 border rounded p-4" style="background-color:#eaeaea">
    <div class="container">
        <div class="row">
            <h2>Gestió base de dades auxiliars: aparició mitjans i traduccions</h2>

            <div id="titolForm" class="mb-3"></div>

            <?php if (isUserAdmin()) : ?>

                <form id="aparicioI18nForm" novalidate>
                    <div class="row g-4">

                        <!-- OK / ERR -->
                        <div class="alert alert-success" role="alert" id="okMessage" style="display:none">
                            <div id="okText"></div>
                        </div>

                        <div class="alert alert-danger" role="alert" id="errMessage" style="display:none">
                            <div id="errText"></div>
                        </div>

                        <!-- Identificador aparicio -->
                        <input type="hidden" name="id" id="id" value="">

                        <!-- Info rápida (solo lectura, lo rellena TS) -->
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="row g-2">
                                        <div class="col-md-3">
                                            <div class="text-muted small">ID</div>
                                            <div class="fw-semibold" id="infoId">—</div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-muted small">Data aparició</div>
                                            <div class="fw-semibold" id="infoDataAparicio">—</div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-muted small">Tipus</div>
                                            <div class="fw-semibold" id="infoTipusAparicio">—</div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-muted small">Mitjà</div>
                                            <div class="fw-semibold" id="infoNomMitja">—</div>
                                        </div>

                                        <div class="col-12 mt-2">
                                            <div class="text-muted small">URL notícia</div>
                                            <div class="fw-semibold" id="infoUrlNoticia">—</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabs idiomas -->
                        <div class="col-12">
                            <ul class="nav nav-tabs" id="aparicioLangTabs" role="tablist">
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

                            <div class="tab-content border border-top-0 rounded-bottom p-3 bg-white" id="aparicioLangTabContent">

                                <!-- CA -->
                                <div class="tab-pane fade show active" id="pane-ca" role="tabpanel" aria-labelledby="tab-ca" tabindex="0">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label for="titol_ca" class="form-label fw-bold">Títol (CA) *</label>
                                            <input type="text" class="form-control" id="titol_ca" name="titol_ca" value="" required maxlength="255">
                                        </div>

                                        <div class="col-12">
                                            <label for="resum_ca" class="form-label fw-bold">Resum (CA)</label>
                                            <textarea class="form-control" id="resum_ca" name="resum_ca" rows="3"></textarea>
                                        </div>

                                        <div class="col-12">
                                            <label for="notes_ca" class="form-label fw-bold">Notes (CA)</label>
                                            <textarea class="form-control" id="notes_ca" name="notes_ca" rows="3"></textarea>
                                        </div>

                                        <div class="col-12">
                                            <label for="pdf_url_ca" class="form-label fw-bold">PDF URL (CA)</label>
                                            <input type="url" class="form-control" id="pdf_url_ca" name="pdf_url_ca" placeholder="https://…">
                                            <div class="form-text">Opcional. URL del PDF associat a l’aparició.</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- ES -->
                                <div class="tab-pane fade" id="pane-es" role="tabpanel" aria-labelledby="tab-es" tabindex="0">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label for="titol_es" class="form-label fw-bold">Títol (ES)</label>
                                            <input type="text" class="form-control" id="titol_es" name="titol_es" value="" maxlength="255">
                                        </div>

                                        <div class="col-12">
                                            <label for="resum_es" class="form-label fw-bold">Resum (ES)</label>
                                            <textarea class="form-control" id="resum_es" name="resum_es" rows="3"></textarea>
                                        </div>

                                        <div class="col-12">
                                            <label for="notes_es" class="form-label fw-bold">Notes (ES)</label>
                                            <textarea class="form-control" id="notes_es" name="notes_es" rows="3"></textarea>
                                        </div>

                                        <div class="col-12">
                                            <label for="pdf_url_es" class="form-label fw-bold">PDF URL (ES)</label>
                                            <input type="url" class="form-control" id="pdf_url_es" name="pdf_url_es" placeholder="https://…">
                                        </div>
                                    </div>
                                </div>

                                <!-- EN -->
                                <div class="tab-pane fade" id="pane-en" role="tabpanel" aria-labelledby="tab-en" tabindex="0">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label for="titol_en" class="form-label fw-bold">Títol (EN)</label>
                                            <input type="text" class="form-control" id="titol_en" name="titol_en" value="" maxlength="255">
                                        </div>

                                        <div class="col-12">
                                            <label for="resum_en" class="form-label fw-bold">Resum (EN)</label>
                                            <textarea class="form-control" id="resum_en" name="resum_en" rows="3"></textarea>
                                        </div>

                                        <div class="col-12">
                                            <label for="notes_en" class="form-label fw-bold">Notes (EN)</label>
                                            <textarea class="form-control" id="notes_en" name="notes_en" rows="3"></textarea>
                                        </div>

                                        <div class="col-12">
                                            <label for="pdf_url_en" class="form-label fw-bold">PDF URL (EN)</label>
                                            <input type="url" class="form-control" id="pdf_url_en" name="pdf_url_en" placeholder="https://…">
                                        </div>
                                    </div>
                                </div>

                                <!-- FR -->
                                <div class="tab-pane fade" id="pane-fr" role="tabpanel" aria-labelledby="tab-fr" tabindex="0">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label for="titol_fr" class="form-label fw-bold">Títol (FR)</label>
                                            <input type="text" class="form-control" id="titol_fr" name="titol_fr" value="" maxlength="255">
                                        </div>

                                        <div class="col-12">
                                            <label for="resum_fr" class="form-label fw-bold">Resum (FR)</label>
                                            <textarea class="form-control" id="resum_fr" name="resum_fr" rows="3"></textarea>
                                        </div>

                                        <div class="col-12">
                                            <label for="notes_fr" class="form-label fw-bold">Notes (FR)</label>
                                            <textarea class="form-control" id="notes_fr" name="notes_fr" rows="3"></textarea>
                                        </div>

                                        <div class="col-12">
                                            <label for="pdf_url_fr" class="form-label fw-bold">PDF URL (FR)</label>
                                            <input type="url" class="form-control" id="pdf_url_fr" name="pdf_url_fr" placeholder="https://…">
                                        </div>
                                    </div>
                                </div>

                                <!-- IT -->
                                <div class="tab-pane fade" id="pane-it" role="tabpanel" aria-labelledby="tab-it" tabindex="0">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label for="titol_it" class="form-label fw-bold">Títol (IT)</label>
                                            <input type="text" class="form-control" id="titol_it" name="titol_it" value="" maxlength="255">
                                        </div>

                                        <div class="col-12">
                                            <label for="resum_it" class="form-label fw-bold">Resum (IT)</label>
                                            <textarea class="form-control" id="resum_it" name="resum_it" rows="3"></textarea>
                                        </div>

                                        <div class="col-12">
                                            <label for="notes_it" class="form-label fw-bold">Notes (IT)</label>
                                            <textarea class="form-control" id="notes_it" name="notes_it" rows="3"></textarea>
                                        </div>

                                        <div class="col-12">
                                            <label for="pdf_url_it" class="form-label fw-bold">PDF URL (IT)</label>
                                            <input type="url" class="form-control" id="pdf_url_it" name="pdf_url_it" placeholder="https://…">
                                        </div>
                                    </div>
                                </div>

                                <!-- PT -->
                                <div class="tab-pane fade" id="pane-pt" role="tabpanel" aria-labelledby="tab-pt" tabindex="0">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label for="titol_pt" class="form-label fw-bold">Títol (PT)</label>
                                            <input type="text" class="form-control" id="titol_pt" name="titol_pt" value="" maxlength="255">
                                        </div>

                                        <div class="col-12">
                                            <label for="resum_pt" class="form-label fw-bold">Resum (PT)</label>
                                            <textarea class="form-control" id="resum_pt" name="resum_pt" rows="3"></textarea>
                                        </div>

                                        <div class="col-12">
                                            <label for="notes_pt" class="form-label fw-bold">Notes (PT)</label>
                                            <textarea class="form-control" id="notes_pt" name="notes_pt" rows="3"></textarea>
                                        </div>

                                        <div class="col-12">
                                            <label for="pdf_url_pt" class="form-label fw-bold">PDF URL (PT)</label>
                                            <input type="url" class="form-control" id="pdf_url_pt" name="pdf_url_pt" placeholder="https://…">
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
                                <button class="btn btn-primary" id="btnAparicioI18n" type="submit">
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