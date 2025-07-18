<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="presoModelForm">
        <div class="container">
            <h2>Tipus de repressió: Empresonat Presó Model de Barcelona</h2>
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

                <h4>1) Dades empresonament</h4>

                <div class="col-md-4 mb-4">
                    <label for="data_empresonament" class="form-label negreta">Data d'empresonament:</label>
                    <input type="text" class="form-control" id="data_empresonament" name="data_empresonament" value="">
                    <div class="avis-form">
                        * Format de la data: DD/MM/AAAA
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="modalitat" class="form-label negreta">Modalitat de presó:</label>
                    <select class="form-select" id="modalitat" name="modalitat">
                    </select>

                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/auxiliars/nova-modalitat-preso" target="_blank" class="btn btn-secondary btn-sm" id="afegir1">Afegir modalitat presó</a>
                        <button id="refreshButton1" class="btn btn-primary btn-sm">Actualitzar llistat</button>
                    </div>
                </div>

                <hr>
                <h4>2) Trasllats</h4>

                <div class="col-md-4 mb-4">
                    <label for="trasllats" class="form-label negreta">Trasllats:</label>
                    <select class="form-select" id="trasllats" name="trasllats">
                        <option value="" selected>Selecciona una opció:</option>
                        <option value="1">Sí</option>
                        <option value="2">No</option>
                        <option value="3">Sense dades</option>
                    </select>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="lloc_trasllat" class="form-label negreta">Lloc trasllat:</label>
                    <input type="text" class="form-control" id="lloc_trasllat" name="lloc_trasllat" value="">
                </div>

                <div class="col-md-4 mb-4">
                    <label for="data_trasllat" class="form-label negreta">Data trasllat:</label>
                    <input type="text" class="form-control" id="data_trasllat" name="data_trasllat" value="">
                    <div class="avis-form">
                        * Format de la data: DD/MM/AAAA
                    </div>
                </div>

                <hr>
                <h4>3) Dades alliberament</h4>
                <div class="col-md-4 mb-4">
                    <label for="llibertat" class="form-label negreta">Llibertat:</label>
                    <select class="form-select" id="llibertat" name="llibertat">
                        <option value="" selected>Selecciona una opció:</option>
                        <option value="1">Sí</option>
                        <option value="2">No</option>
                        <option value="3">Sense dades</option>
                    </select>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="data_llibertat" class="form-label negreta">Data llibertat:</label>
                    <input type="text" class="form-control" id="data_llibertat" name="data_llibertat" value="">
                    <div class="avis-form">
                        * Format de la data: DD/MM/AAAA
                    </div>
                </div>

                <hr>

                <h4>4) Altres dades</h4>

                <div class="col-md-12 mb-4">
                    <label for="vicissituds" class="form-label negreta">Vicissituds:</label>
                    <textarea class="form-control" id="vicissituds" name="vicissituds" rows="4"></textarea>
                </div>

                <div class="col-md-12 mb-4">
                    <label for="observacions" class="form-label negreta">Observacions:</label>
                    <textarea class="form-control" id="observacions" name="observacions" rows="4"></textarea>
                </div>

                <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
                    <div class="col"></div>

                    <div class="col d-flex justify-content-end align-items-center">
                        <button class="btn btn-primary" type="submit" id="btnPresoModel">Modificar dades</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>