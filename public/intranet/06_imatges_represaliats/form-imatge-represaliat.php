<?php
require_once APP_ROOT . '/public/intranet/includes/header.php';
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="imatgePerfilForm" action="" type="post">
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

                <!-- Campo oculto tipus -->
                <input type="hidden" name="tipus" value="1">

                <div class="col-md-4 mb-4">
                    <label for="nomImatge" class="form-label">Nom imatge</label>
                    <input type="text" class="form-control" id="nomImatge" name="nomImatge" required maxlength="120">
                </div>

                <div class="col-md-4 mb-4">
                    <label for="nomArxiu" class="form-label">Selecciona la imagen</label>
                    <input class="form-control" type="file" id="nomArxiu" name="nomArxiu" accept="image/jpg" required>
                    <div class="form-text">Formatos permitidos: JPG, PNG, WebP. Tamaño máx. según servidor.</div>
                </div>

                <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
                    <div class="col"></div>

                    <div class="col d-flex justify-content-end align-items-center">
                        <button class="btn btn-primary" id="btnImatgePerfil" type="submit">Modificar dades</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>