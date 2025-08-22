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
                    <label for="situacio" class="form-label negreta">Situació final del deportat:</label>
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
                        <a href="https://memoriaterrassa.cat/gestio/auxiliars/nou-municipi" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi">Afegir municipi</a>
                        <button id="refreshButton" class="btn btn-primary btn-sm">Actualitzar llistat Municipis</button>
                    </div>
                </div>

                <hr style="margin-top:25px">
                <h4>Situació a França:</h4>

                <div class="col-md-4 mb-4">
                    <label for="situacioFranca" class="form-label negreta">Presó/camp de detenció/Altres:</label>
                    <select class="form-select" name="situacioFranca" id="situacioFranca" value="">
                    </select>
                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/auxiliars/nou-camp-detencio" target="_blank" class="btn btn-secondary btn-sm" id="afegirCamp1">Afegir presó/camp</a>
                        <button id="refreshButton1" class="btn btn-primary btn-sm">Actualitzar llistat</button>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="situacioFranca_sortida" class="form-label negreta">Data de la sortida de la presó/camp/altres:</label>
                    <input type="text" class="form-control" id="situacioFranca_sortida" name="situacioFranca_sortida" value="">
                    <div class="avis-form">
                        * Format data: dia/mes/any
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="situacioFranca_num_matricula" class="form-label negreta">Número de matrícula presó (si el sabem):</label>
                    <input type="text" class="form-control" id="situacioFranca_num_matricula" name="situacioFranca_num_matricula" value="">
                </div>

                <div class="col-md-12 mb-4">
                    <label for="situacioFrancaObservacions" class="form-label negreta">Descripció situació a França:</label>
                    <textarea class="form-control" id="situacioFrancaObservacions" name="situacioFrancaObservacions" rows="3"></textarea>
                </div>


                <hr style="margin-top:25px">
                <h4>Camp de classificació/detenció/altres previ a la deportació al camp de concentració:</h4>

                <div class="col-md-3 mb-4">
                    <label for="presoClasificacio1" class="form-label negreta">Primera Presó/camp de detenció:</label>
                    <select class="form-select" name="presoClasificacio1" id="presoClasificacio1" value="">
                    </select>
                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/auxiliars/nou-camp-detencio" target="_blank" class="btn btn-secondary btn-sm" id="afegirCamp2">Afegir presó/camp</a>
                        <button id="refreshButton2" class="btn btn-primary btn-sm">Actualitzar llistat</button>
                    </div>
                </div>

                <div class="col-md-3 mb-4">
                    <label for="presoClasificacioDataEntrada1" class="form-label negreta">Data entrada presó:</label>
                    <input type="text" class="form-control" id="presoClasificacioDataEntrada1" name="presoClasificacioDataEntrada1" value="">
                </div>

                <div class="col-md-3 mb-4">
                    <label for="presoClasificacioData1" class="form-label negreta">Data sortida presó:</label>
                    <input type="text" class="form-control" id="presoClasificacioData1" name="presoClasificacioData1" value="">
                </div>

                <div class="col-md-3 mb-4">
                    <label for="presoClasificacioMatr1" class="form-label negreta">Núm. matrícula:</label>
                    <input type="text" class="form-control" id="presoClasificacioMatr1" name="presoClasificacioMatr1" value="">
                </div>


                <div class="col-md-3 mb-4">
                    <label for="presoClasificacio2" class="form-label negreta">Segona Presó/camp de detenció:</label>
                    <select class="form-select" name="presoClasificacio2" id="presoClasificacio2" value="">
                    </select>
                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/auxiliars/nou-camp-detencio" target="_blank" class="btn btn-secondary btn-sm" id="afegirCamp3">Afegir presó/camp</a>
                        <button id="refreshButton3" class="btn btn-primary btn-sm">Actualitzar llistat</button>
                    </div>
                </div>


                <div class="col-md-3 mb-4">
                    <label for="presoClasificacioDataEntrada2" class="form-label negreta">Data entrada presó:</label>
                    <input type="text" class="form-control" id="presoClasificacioDataEntrada2" name="presoClasificacioDataEntrada2" value="">
                </div>

                <div class="col-md-3 mb-4">
                    <label for="presoClasificacioData2" class="form-label negreta">Data sortida presó:</label>
                    <input type="text" class="form-control" id="presoClasificacioData2" name="presoClasificacioData2" value="">
                </div>

                <div class="col-md-3 mb-4">
                    <label for="presoClasificacioMatr2" class="form-label negreta">Núm. matrícula:</label>
                    <input type="text" class="form-control" id="presoClasificacioMatr2" name="presoClasificacioMatr2" value="">
                </div>


                <hr style="margin-top:25px">
                <h4>Deportació. Dades sobre el camp de concentració/extermini:</h4>

                <div class="col-md-4 mb-4">
                    <label for="deportacio_camp" class="form-label negreta">Camp de concentració/extermini de deportació:</label>
                    <select class="form-select" name="deportacio_camp" id="deportacio_camp" value="">
                    </select>
                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/auxiliars/nou-camp-concentracio" target="_blank" class="btn btn-secondary btn-sm" id="afegirCamp4">Afegir camp</a>
                        <button id="refreshButton4" class="btn btn-primary btn-sm">Actualitzar llistat</button>
                    </div>
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
                    <label for="deportacio_subcamp" class="form-label negreta">SubCamp de concentració:</label>
                    <select class="form-select" name="deportacio_subcamp" id="deportacio_subcamp" value="">
                    </select>
                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/auxiliars/nou-camp-concentracio" target="_blank" class="btn btn-secondary btn-sm" id="afegirCamp5">Afegir camp</a>
                        <button id="refreshButton5" class="btn btn-primary btn-sm">Actualitzar llistat</button>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="deportacio_data_entrada_subcamp" class="form-label negreta">Data d'entrada al subcamp</label>
                    <input type="text" class="form-control" id="deportacio_data_entrada_subcamp" name="deportacio_data_entrada_subcamp" value="">
                </div>

                <div class="col-md-4 mb-4">
                    <label for="deportacio_nom_matricula_subcamp" class="form-label negreta">Número de matrícula del subcamp</label>
                    <input type="text" class="form-control" id="deportacio_nom_matricula_subcamp" name="deportacio_nom_matricula_subcamp" value="">
                </div>

                <div class="col-md-12 mb-4">
                    <label for="deportacio_observacions" class="form-label negreta">Altra informació rellevant deportació:</label>
                    <textarea class="form-control" id="deportacio_observacions" name="deportacio_observacions" rows="3"></textarea>
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