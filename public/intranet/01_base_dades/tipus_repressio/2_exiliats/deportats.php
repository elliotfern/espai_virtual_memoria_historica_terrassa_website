<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="deportatForm">
        <div class="container">
            <h2>Tipus de repressió: Deportat</h2>
            <div id="fitxaNomCognoms"></div>

            <div class="alert alert-success" role="alert" id="okMessage" style="display:none">
                <div id="okText"></div>
            </div>

            <div class="alert alert-danger" role="alert" id="errMessage" style="display:none">
                <div id="errText"></div>
            </div>
            <div class="row g-3">
                <input type="hidden" name="idPersona" id="idPersona" value="">
                <input type="hidden" name="id" id="id" value="">

                <div class="col-md-4 mb-4">
                    <label for="situacio" class="form-label negreta">Situació del deportat:</label>
                    <select class="form-select" name="situacio" id="situacio" value="">
                    </select>
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="data_alliberament" class="form-label negreta">Data alliberament o mort:</label>
                    <input type="text" class="form-control" id="data_alliberament" name="data_alliberament" value="">
                    <div class="avis-form">
                        * Camp obligatori - Format data: dia/mes/any
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="lloc_mort_alliberament" class="form-label negreta">Municipi de mort o alliberament:</label>
                    <select class="form-select" name="lloc_mort_alliberament" id="lloc_mort_alliberament" value="">
                    </select>
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/auxiliars/nou-municipi" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi1">Afegir municipi</a>
                        <button id="refreshButton1" class="btn btn-primary btn-sm">Actualitzar llistat Municipis</button>
                    </div>
                </div>

                <hr style="margin-top:25px">
                <h4>Empresonament:</h4>

                <div class="col-md-4 mb-4">
                    <label for="preso_tipus" class="form-label negreta">Tipus de presó:</label>
                    <select class="form-select" name="preso_tipus" id="preso_tipus" value="">
                    </select>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="preso_nom" class="form-label negreta">Nom de la presó:</label>
                    <input type="text" class="form-control" id="preso_nom" name="preso_nom" value="">
                </div>

                <div class="col-md-4 mb-4">
                    <label for="preso_data_sortida" class="form-label negreta">Data de la sortida de la presó:</label>
                    <input type="text" class="form-control" id="preso_data_sortida" name="preso_data_sortida" value="">
                </div>

                <div class="col-md-4 mb-4">
                    <label for="preso_localitat" class="form-label negreta">Municipi de la presó:</label>
                    <select class="form-select" name="preso_localitat" id="preso_localitat" value="">
                    </select>
                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/auxiliars/nou-municipi" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi2">Afegir municipi</a>
                        <button id="refreshButton2" class="btn btn-primary btn-sm">Actualitzar llistat Municipis</button>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="preso_num_matricula" class="form-label negreta">Número de matrícula presó:</label>
                    <input type="text" class="form-control" id="preso_num_matricula" name="preso_num_matricula" value="">
                </div>

                <hr style="margin-top:25px">
                <h4>Deportació:</h4>

                <div class="col-md-4 mb-4">
                    <label for="deportacio_nom_camp" class="form-label negreta">Nom cap de deportació</label>
                    <input type="text" class="form-control" id="deportacio_nom_camp" name="deportacio_nom_camp" value="">
                </div>

                <div class="col-md-4 mb-4">
                    <label for="deportacio_data_entrada" class="form-label negreta">Data d'entrada</label>
                    <input type="text" class="form-control" id="deportacio_data_entrada" name="deportacio_data_entrada" value="">
                </div>


                <div class="col-md-4 mb-4">
                    <label for="deportacio_num_matricula" class="form-label negreta">Número de matrícula</label>
                    <input type="text" class="form-control" id="deportacio_num_matricula" name="deportacio_num_matricula" value="">
                </div>

                <div class="col-md-4 mb-4">
                    <label for="deportacio_nom_subcamp" class="form-label negreta">Nom del subcamp</label>
                    <input type="text" class="form-control" id="deportacio_nom_subcamp" name="deportacio_nom_subcamp" value="">
                </div>

                <div class="col-md-4 mb-4">
                    <label for="deportacio_data_entrada_subcamp" class="form-label negreta">Data d'entrada al subcamp</label>
                    <input type="text" class="form-control" id="deportacio_data_entrada_subcamp" name="deportacio_data_entrada_subcamp" value="">
                </div>

                <div class="col-md-4 mb-4">
                    <label for="deportacio_nom_matricula_subcamp" class="form-label negreta">Número de matrícula del subcamp</label>
                    <input type="text" class="form-control" id="deportacio_nom_matricula_subcamp" name="deportacio_nom_matricula_subcamp" value="">
                </div>

                <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
                    <div class="col"></div>
                    <div class="col d-flex justify-content-end align-items-center">
                        <button class="btn btn-primary" id="btnDeportats" type="submit">Inserir dades</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>