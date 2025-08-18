<?php
require_once APP_ROOT . '/public/intranet/includes/header.php';
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <div class="container">
        <div class="row">
            <h2>Gestió base de dades auxiliars: usuaris</h2>
            <div id="titolForm"></div>
            <?php if (isUserAdmin()) : ?>

                <form id="usuariForm">
                    <div class="row g-5">
                        <div class="alert alert-success" role="alert" id="okMessage" style="display:none">
                            <div id="okText"></div>
                        </div>

                        <div class="alert alert-danger" role="alert" id="errMessage" style="display:none">
                            <div id="errText"></div>
                        </div>

                        <input type="hidden" name="id" id="id" value="">

                        <div class="col-md-4">
                            <label for="nom" class="form-label negreta">Nom usuari:</label>
                            <input type="text" class="form-control" id="nom" name="nom" value="">
                        </div>

                        <div class="col-md-4">
                            <label for="email" class="form-label negreta">Email:</label>
                            <input type="text" class="form-control" id="email" name="email" value="">
                        </div>

                        <div class="col-md-4">
                            <label for="email" class="form-label negreta">Password:</label>
                            <input type="password" class="form-control" id="password" name="password" value="">
                        </div>

                        <div class="col-md-4">
                            <label for="user_type" class="form-label negreta">Tipus d'usuari:</label>
                            <select class="form-select" id="user_type" name="user_type" value="">
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="avatar" class="form-label negreta">Avatar:</label>
                            <select class="form-select" id="avatar" name="avatar" value="">
                            </select>
                            <div class="mt-2">
                                <a href="https://memoriaterrassa.cat/gestio/auxiliars/nou-avatar-usuari" target="_blank" class="btn btn-secondary btn-sm" id="afegirAvatar">Afegir avatar</a>
                                <button type="button" id="refreshButtonAvatar" class="btn btn-primary btn-sm">Actualitzar llistat</button>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label for="biografia_cat" class="form-label negreta">Descripcio (català):</label>
                            <textarea id="biografia_cat" name="biografia_cat" class="form-control" rows="4"></textarea>
                        </div>

                        <div class="row espai-superior" style="padding-top:25px">
                            <div class="col">
                                <a class="btn btn-secondary" role="button" aria-disabled="true" onclick="goBack()">Tornar enrere</a>
                            </div>
                            <div class="col d-flex justify-content-end align-items-center">

                                <button class="btn btn-primary" id="btnUsuari" type="submit">Modificar dades</button>
                            </div>
                        </div>
                    </div>
                </form>

            <?php endif; ?>
        </div>
    </div>
</div>