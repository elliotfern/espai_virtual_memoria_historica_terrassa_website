<?php

use App\Config\DatabaseConnection;

$conn = DatabaseConnection::getConnection();

if (!$conn) {
    die("No se pudo establecer conexión a la base de datos.");
}
require_once APP_ROOT . '/public/intranet/includes/header.php';
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <div class="container">
        <div class="row">
            <h2>Gestió base de dades auxiliars: inserció d'avatars d'usuari</h2>
            <?php if (isUserAdmin()) : ?>

                <form id="usuariForm">
                    <div class="row g-5">
                        <div class="alert alert-success" role="alert" id="okMessage" style="display:none">
                            <h4 class="alert-heading"><strong>Modificació correcte!</strong></h4>
                            <div id="okText"></div>
                        </div>

                        <div class="alert alert-danger" role="alert" id="errMessage" style="display:none">
                            <h4 class="alert-heading"><strong>Error en les dades!</strong></h4>
                            <div id="errText"></div>
                        </div>

                        <?php $timestamp = date('Y-m-d'); ?>
                        <input type="hidden" name="dateCreated" id="dateCreated" value="'<?php echo $timestamp; ?>'">

                        <div class="col-md-4">
                            <label>Nom imatge:</label>
                            <input class="form-control" type="text" name="nomImatge" id="nomImatge">
                            <label style="color:#dc3545">* Obligatori </label>
                        </div>

                        <div class="col-md-4">
                            <label>Categoria de la imatge:</label>
                            <select class="form-select" id="tipus" name="tipus">
                                <option selected>Selecciona el tipus d'imatge</option>
                                <option value="2">Usuari web: avatar</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                        </div>

                        <div class="col-md-4">
                            <label>Fitxer:</label>
                            <input class="form-control" type="file" id="fileToUpload" name="fileToUpload">
                        </div>

                        <div class="row espai-superior" style="padding-top:25px">
                            <div class="col">
                                <a class="btn btn-secondary" role="button" aria-disabled="true" onclick="goBack()">Tornar enrere</a>
                            </div>
                            <div class="col d-flex justify-content-end align-items-center">
                                <button class="btn btn-primary" id="btnInserir" type="submit">Inserir imatge</button>
                            </div>
                        </div>
                    </div>
                </form>

            <?php endif; ?>
        </div>
    </div>
</div>