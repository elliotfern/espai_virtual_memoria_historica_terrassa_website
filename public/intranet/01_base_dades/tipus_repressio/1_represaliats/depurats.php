<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="depuratForm">
        <div class="container">
            <h2>Tipus de repressió: Depurat</h2>
            <h4 id="fitxaNomCognoms">Fitxa:</a></h4>
            <div class="row g-4">
                <div class="alert alert-success" role="alert" id="okMessage" style="display:none">
                    <div id="okText"></div>
                </div>

                <div class="alert alert-danger" role="alert" id="errMessage" style="display:none">
                    <div id="errText"></div>
                </div>

                <input type="hidden" name="idPersona" id="idPersona" value="">
                <input type="hidden" name="id" id="id" value="">

                <div class="col-md-4 mb-4">
                    <label for="tipus_professional" class="form-label negreta">Tipus d'empleat:</label>
                    <select class="form-select" id="tipus_professional" name="tipus_professional">
                    </select>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="professio" class="form-label negreta">Professió:</label>
                    <select class="form-select" id="professio" name="professio">
                    </select>
                    <div class="mt-2">
                        <a href="<?php echo APP_WEB . APP_INTRANET . $url['auxiliars'] ?>/nou-ofici" target="_blank" class="btn btn-secondary btn-sm" id="afegirOfici1">Afegir ofici</a>
                        <button id="refreshButton1" class="btn btn-primary btn-sm">Actualitzar llistat</button>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="empresa" class="form-label negreta">Empresa / Organisme públic:</label>
                    <select class="form-select" id="empresa" value="" name="empresa">
                    </select>
                    <div class="avis" style="font-size:14px">
                        * En cas d'absència d'informació marqueu "Desconeguda".
                    </div>
                    <div class="mt-2">
                        <a href="<?php echo APP_WEB . APP_INTRANET . $url['auxiliars'] ?>/nova-empresa" target="_blank" class="btn btn-secondary btn-sm" id="afegirEmpresa">Afegir empresa</a>
                        <button id="refreshButton2" class="btn btn-primary btn-sm">Actualitzar llistat</button>
                    </div>
                </div>

                <div class="col-md-12 mb-4">
                    <label for="sancio" class="form-label negreta">Sanció:</label>
                    <textarea class="form-control" id="sancio" name="sancio" rows="2"></textarea>
                </div>

                <div class="col-md-12 mb-4">
                    <label for="observacions" class="form-label negreta">Observacions:</label>
                    <textarea class="form-control" id="observacions" name="observacions" rows="4"></textarea>
                </div>


                <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
                    <div class="col"></div>

                    <div class="col d-flex justify-content-end align-items-center">
                        <button class="btn btn-primary" type="submit" id="btnDepurat">Modificar dades</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>