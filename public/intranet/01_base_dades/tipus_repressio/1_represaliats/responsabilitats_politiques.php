<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="responsabilitatsPolitiquesForm">
        <div class="container">
            <h2>Tipus de repressió: Afectat per la Llei de Responsabilitats polítiques</h2>
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
                    <label for="lloc_empresonament" class="form-label negreta">Si va ser empresonat, lloc:</label>
                    <select class="form-select" id="lloc_empresonament" name="lloc_empresonament">
                    </select>
                    <div class="mt-2">
                        <a href="<?php echo APP_WEB . APP_INTRANET . $url['auxiliars'] ?>/nova-preso" target="_blank" class="btn btn-secondary btn-sm" id="afegir1">Afegir presó</a>
                        <button id="refreshButton1" class="btn btn-primary btn-sm">Actualitzar llistat</button>
                    </div>

                </div>

                <div class="col-md-4 mb-4">
                    <label for="lloc_exili" class="form-label negreta">Si va anar a l'exili, país:</label>
                    <select class="form-select" id="lloc_exili" name="lloc_exili">
                    </select>
                    <div class="mt-2">
                        <a href="<?php echo APP_WEB . APP_INTRANET . $url['auxiliars'] ?>/nou-estat" target="_blank" class="btn btn-secondary btn-sm" id="afegir2">Afegir país</a>
                        <button id="refreshButton2" class="btn btn-primary btn-sm">Actualitzar llistat</button>
                    </div>

                </div>

                <div class="col-md-12 mb-4">
                    <label for="condemna" class="form-label negreta">Condemna:</label>
                    <textarea class="form-control" id="condemna" name="condemna" rows="4"></textarea>
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <div class="col-md-12 mb-4">
                    <label for="observacions" class="form-label negreta">Observacions:</label>
                    <textarea class="form-control" id="observacions" name="observacions" rows="4"></textarea>
                </div>

                <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
                    <div class="col"></div>

                    <div class="col d-flex justify-content-end align-items-center">
                        <button class="btn btn-primary" type="submit" id="btnResponsabilitats">Modificar dades</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>