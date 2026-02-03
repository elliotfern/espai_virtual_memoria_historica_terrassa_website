<?php
require_once APP_ROOT . '/public/intranet/includes/header.php';
?>

<div class="container mb-5 border rounded p-4" style="background-color:#eaeaea">
    <div class="container">
        <div class="row">
            <h2>Gestió base de dades auxiliars: aparició a mitjans de comunicació</h2>

            <div id="titolForm" class="mb-3"></div>

            <?php if (isUserAdmin()) : ?>

                <form id="aparicioForm" novalidate>
                    <div class="row g-4">

                        <!-- OK / ERR -->
                        <div class="alert alert-success" role="alert" id="okMessage" style="display:none">
                            <div id="okText"></div>
                        </div>

                        <div class="alert alert-danger" role="alert" id="errMessage" style="display:none">
                            <div id="errText"></div>
                        </div>

                        <!-- ID (create: vacío, pero dejamos hidden por consistencia con update) -->
                        <input type="hidden" name="id" id="id" value="">

                        <!-- Datos base -->
                        <div class="col-md-4">
                            <label for="data_aparicio" class="form-label fw-bold">Data aparició en premsa *</label>
                            <input type="date" class="form-control" id="data_aparicio" name="data_aparicio" required>
                        </div>

                        <div class="col-md-4">
                            <label for="tipus_aparicio" class="form-label fw-bold">Tipus aparició *</label>
                            <select class="form-select" id="tipus_aparicio" name="tipus_aparicio" required>
                                <!-- ho omple TS -->
                                <option value="">Selecciona…</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label for="titol_ca" class="form-label fw-bold">Títol (català) *</label>
                            <input
                                type="text"
                                class="form-control"
                                id="titol_ca"
                                name="titol_ca"
                                required
                                maxlength="255"
                                placeholder="Escriu el títol en català…">
                            <div class="form-text">Obligatori. Es guardarà a la taula i18n amb lang=ca.</div>
                        </div>

                        <div class="col-md-4">
                            <label for="mitja_id" class="form-label fw-bold">Mitjà *</label>
                            <select class="form-select" id="mitja_id" name="mitja_id" required>
                                <!-- ho omple TS -->
                                <option value="">Selecciona…</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label for="url_noticia" class="form-label fw-bold">URL notícia</label>
                            <input type="url" class="form-control" id="url_noticia" name="url_noticia" placeholder="https://…">
                            <div class="form-text">Opcional. Enllaç a la notícia/publicació.</div>
                        </div>

                        <div class="col-md-4">
                            <label for="image_id" class="form-label fw-bold">Imatge</label>
                            <select class="form-select" id="image_id" name="image_id">
                                <!-- ho omple TS -->
                                <option value="">Selecciona…</option>
                            </select>

                        </div>

                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="destacat" name="destacat" value="1">
                                <label class="form-check-label fw-bold" for="destacat">Destacat</label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="estat" class="form-label fw-bold">Estat *</label>
                            <select class="form-select" id="estat" name="estat" required>
                                <option value="publicat" selected>Publicat</option>
                                <option value="draft">Esborrany</option>
                            </select>
                        </div>

                        <!-- Botonera -->
                        <div class="row pt-3">
                            <div class="col">
                                <a class="btn btn-secondary" role="button" onclick="goBack()">Tornar enrere</a>
                            </div>
                            <div class="col d-flex justify-content-end align-items-center gap-2">
                                <button class="btn btn-primary" id="btnAparicio" type="submit">
                                    Desa aparició
                                </button>
                            </div>
                        </div>

                    </div>
                </form>

            <?php endif; ?>
        </div>
    </div>
</div>