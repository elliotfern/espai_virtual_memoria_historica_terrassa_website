<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="detingutGUForm">
        <div class="container">
            <h2>Tipus de repressió: Detingut Guàrdia Urbana</h2>
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
                    <label for="data_empresonament" class="form-label negreta">Data d'empresonament:</label>
                    <input type="text" class="form-control" id="data_empresonament" name="data_empresonament" value="">
                    <div class="avis-form">
                        * Format de la data: DD/MM/AAAA
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="data_sortida" class="form-label negreta">Data de sortida:</label>
                    <input type="text" class="form-control" id="data_sortida" name="data_sortida" value="">
                    <div class="avis-form">
                        * Format de la data: DD/MM/AAAA
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="motiu_empresonament" class="form-label negreta">Motiu de la detenció:</label>
                    <select class="form-select" id="motiu_empresonament" name="motiu_empresonament">
                    </select>

                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/auxiliars/nou-motiu-detencio" target="_blank" class="btn btn-secondary btn-sm" id="afegir1">Afegir motiu detenció</a>
                        <button id="refreshButton1" class="btn btn-primary btn-sm">Actualitzar llistat</button>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="qui_ordena_detencio" class="form-label negreta">Qui ordena la detenció:</label>
                    <select class="form-select" id="qui_ordena_detencio" name="qui_ordena_detencio">
                    </select>

                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/auxiliars/nou-grup-repressio" target="_blank" class="btn btn-secondary btn-sm" id="afegir2">Afegir grup repressió</a>
                        <button id="refreshButton2" class="btn btn-primary btn-sm">Actualitzar llistat</button>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="top" class="form-label negreta">Detenció ordenada pel TOP?:</label>
                    <select class="form-select" id="top" name="top">
                    </select>
                </div>

                <div class="col-md-12 mb-4">
                    <label for="observacions" class="form-label negreta">Observacions:</label>
                    <textarea class="form-control" id="observacions" name="observacions" rows="4"></textarea>
                </div>

                <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
                    <div class="col"></div>

                    <div class="col d-flex justify-content-end align-items-center">
                        <button class="btn btn-primary" type="submit" id="btndetingutGU">Modificar dades</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>