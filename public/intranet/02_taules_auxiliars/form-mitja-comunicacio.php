<?php
require_once APP_ROOT . '/public/intranet/includes/header.php';
?>

<div class="container mb-5 border rounded p-4" style="background-color:#eaeaea">
    <div class="container">
        <div class="row">
            <h2>Gestió base de dades auxiliars: mitjans de comunicació</h2>

            <div id="titolForm" class="mb-3"></div>

            <?php if (isUserAdmin()) : ?>

                <form id="mitjaForm" novalidate>
                    <div class="row g-4">

                        <div class="alert alert-success" role="alert" id="okMessage" style="display:none">
                            <div id="okText"></div>
                        </div>

                        <div class="alert alert-danger" role="alert" id="errMessage" style="display:none">
                            <div id="errText"></div>
                        </div>

                        <input type="hidden" name="id" id="id" value="">

                        <!-- Nom (Català) -->
                        <div class="col-md-6">
                            <label for="nom_ca" class="form-label fw-bold">Nom del mitjà (CA):</label>
                            <input
                                type="text"
                                class="form-control"
                                id="nom_ca"
                                name="nom_ca"
                                value=""
                                placeholder="Ex: Món Terrassa"
                                required>
                            <div class="form-text">Aquest nom és el que veuràs al llistat de mitjans a la intranet.</div>
                        </div>

                        <!-- Tipus -->
                        <div class="col-md-3">
                            <label for="tipus" class="form-label fw-bold">Tipus:</label>
                            <select class="form-select" id="tipus" name="tipus" required>
                                <option value="">Selecciona...</option>
                                <option value="premsa">Premsa</option>
                                <option value="radio">Ràdio</option>
                                <option value="tv">TV</option>
                                <option value="digital">Digital</option>
                                <option value="xarxa_social">Xarxa social</option>
                                <option value="blog">Blog</option>
                                <option value="podcast">Podcast</option>
                                <option value="altres">Altres</option>
                            </select>
                        </div>

                        <!-- Slug -->
                        <div class="col-md-3">
                            <label for="slug" class="form-label fw-bold">Slug:</label>
                            <input
                                type="text"
                                class="form-control"
                                id="slug"
                                name="slug"
                                value=""
                                placeholder="ex: mon-terrassa"
                                required
                                autocomplete="off">
                            <div class="form-text">Sense espais, millor amb guions.</div>
                        </div>

                        <!-- Web -->
                        <div class="col-md-12">
                            <label for="web_url" class="form-label fw-bold">Web URL:</label>
                            <input
                                type="url"
                                class="form-control"
                                id="web_url"
                                name="web_url"
                                value=""
                                placeholder="https://...">
                        </div>

                        <!-- Descripció (Català) -->
                        <div class="col-md-12">
                            <label for="descripcio_ca" class="form-label fw-bold">Descripció (català):</label>
                            <textarea
                                class="form-control"
                                id="descripcio_ca"
                                name="descripcio_ca"
                                rows="3"
                                placeholder="Opcional"></textarea>
                        </div>

                        <!-- Botonera -->
                        <div class="row pt-3">
                            <div class="col">
                                <a class="btn btn-secondary" role="button" onclick="goBack()">Tornar enrere</a>
                            </div>
                            <div class="col d-flex justify-content-end align-items-center gap-2">
                                <a
                                    class="btn btn-outline-secondary"
                                    id="btnAnarTraduccions"
                                    href="#"
                                    style="display:none">
                                    Editar traduccions
                                </a>

                                <button class="btn btn-primary" id="btnMitja" type="submit">
                                    Desar
                                </button>
                            </div>
                        </div>

                    </div>
                </form>

            <?php endif; ?>
        </div>
    </div>
</div>