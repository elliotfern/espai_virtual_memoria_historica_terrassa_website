<?php
require_once APP_ROOT . '/public/intranet/includes/header.php';
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="subSectorForm">
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

                <p>Només els usuaris administradors poden crear nous registres.</p>
                <?php if (isUserAdmin()) : ?>

                    <div class="col-md-4 mb-4">
                        <label for="sub_sector_ca" class="form-label negreta">Sub-sector econòmic (català):</label>
                        <input type="text" class="form-control" id="sub_sector_ca" name="sub_sector_ca" value="">
                        <div class="avis-form">
                            * Camp obligatori
                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="idSector" class="form-label negreta">Sector econòmic</label>
                        <select class="form-select" id="idSector" value="" name="idSector">
                        </select>
                    </div>

                    <?php if (isUserAdmin()) : ?>
                        <hr>

                        <div class="col-md-4 mb-4">
                            <label for="sub_sector_es" class="form-label negreta">Sub-sector econòmic(castellà):</label>
                            <input type="text" class="form-control" id="sub_sector_es" name="sub_sector_es" value="">
                        </div>

                        <div class="col-md-4 mb-4">
                            <label for="sub_sector_en" class="form-label negreta">Sub-sector econòmic (anglès):</label>
                            <input type="text" class="form-control" id="sub_sector_en" name="sub_sector_en" value="">
                        </div>

                        <div class="col-md-4 mb-4">
                            <label for="sub_sector_fr" class="form-label negreta">Sub-sector econòmic (francès):</label>
                            <input type="text" class="form-control" id="sub_sector_fr" name="sub_sector_fr" value="">
                        </div>

                        <div class="col-md-4 mb-4">
                            <label for="sub_sector_pt" class="form-label negreta">Sub-sector econòmic (portuguès):</label>
                            <input type="text" class="form-control" id="sub_sector_pt" name="sub_sector_pt" value="">
                        </div>

                        <div class="col-md-4 mb-4">
                            <label for="sub_sector_it" class="form-label negreta">Sub-sector econòmic (italià):</label>
                            <input type="text" class="form-control" id="sub_sector_it" name="sub_sector_it" value="">
                        </div>
                    <?php endif; ?>

                    <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
                        <div class="col"></div>

                        <div class="col d-flex justify-content-end align-items-center">
                            <button class="btn btn-primary" id="btnSubSector" type="submit">Modificar dades</button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>