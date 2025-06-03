<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="mortCivilsForm">
        <div class="container">
            <h2>Tipus de repressió: Morts civils (Cost humà de la Guerra civil)</h2>
            <div id="fitxaNomCognoms"></div>
            <div class="row g-3">
                <div class="alert alert-success" role="alert" id="okMessage" style="display:none">
                    <h4 class="alert-heading"><strong>Enviament de dades correcte!</strong></h4>
                    <div id="okText"></div>
                </div>

                <div class="alert alert-danger" role="alert" id="errMessage" style="display:none">
                    <h4 class="alert-heading"><strong>Error en les dades!</strong></h4>
                    <div id="errText"></div>
                </div>

                <input type="hidden" name="idPersona" id="idPersona" value="">
                <input type="hidden" name="id" id="id" value="">

                <div class="col-md-4 mb-4">
                    <label for="cirscumstancies_mort" class="form-label negreta">Circumstàncies de la mort:</label>
                    <select class="form-select" name="cirscumstancies_mort" id="cirscumstancies_mort" value="">
                    </select>
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="data_trobada_cadaver" class="form-label negreta">Data trobada del càdaver:</label>
                    <input type="text" class="form-control" id="data_trobada_cadaver" name="data_trobada_cadaver" value="">
                    <div class="avis-form">
                        * Format data: dia/mes/any
                    </div>
                </div>


                <div class="col-md-4 mb-4">
                    <label for="lloc_trobada_cadaver" class="form-label negreta">Lloc afusellament / assassinat extra-judicial / bombardeig / altres causes</label>
                    <select class="form-select" name="lloc_trobada_cadaver" id="lloc_trobada_cadaver" value="">
                    </select>

                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/auxiliars/nou-municipi" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi1">Afegir municipi</a>
                        <button id="refreshButtonMunicipi1" class="btn btn-primary btn-sm">Actualitzar llistat Municipis</button>
                    </div>
                </div>

                <hr>

                <h4>Causa de la mort: Afusellat</h4>
                <div class="col-md-4 mb-4">
                    <label for="qui_ordena_afusellat" class="form-label negreta">Qui ordena l'afusellament</label>
                    <input type="text" class="form-control" id="qui_ordena_afusellat" name="qui_ordena_afusellat" value="">
                </div>

                <div class="col-md-4 mb-4">
                    <label for="qui_executa_afusellat" class="form-label negreta">Qui l'executa?</label>
                    <input type="text" class="form-control" id="qui_executa_afusellat" name="qui_executa_afusellat" value="">
                </div>

                <hr>
                <h4>Causa de la mort: Extra-judicial (assassinat)</h4>
                <div class="col-md-4 mb-4">
                    <label for="data_detencio" class="form-label negreta">Data de la detenció:</label>
                    <input type="text" class="form-control" id="data_detencio" name="data_detencio" value="">
                    <div class="avis-form">
                        * Format data: dia/mes/any
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="lloc_detencio" class="form-label negreta">Lloc de la detenció:</label>
                    <select class="form-select" name="lloc_detencio" id="lloc_detencio" value="">
                    </select>

                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/auxiliars/nou-municipi" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi2">Afegir municipi</a>
                        <button id="refreshButtonMunicipi2" class="btn btn-primary btn-sm">Actualitzar llistat Municipis</button>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="qui_detencio" class="form-label negreta">Qui el deté?</label>
                    <input type="text" class="form-control" id="qui_detencio" name="qui_detencio" value="">
                </div>

                <hr>

                <h4>Causa de la mort: Bombardeig</h4>
                <div class="col-md-4 mb-4">
                    <label for="data_bombardeig" class="form-label negreta">Data del bombardeig:</label>
                    <input type="text" class="form-control" id="data_bombardeig" name="data_bombardeig" value="">
                    <div class="avis-form">
                        * Format data: dia/mes/any
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="municipi_bombardeig" class="form-label negreta">Municipi del bombardeig:</label>
                    <select class="form-select" name="municipi_bombardeig" id="municipi_bombardeig" value="">
                    </select>

                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/auxiliars/nou-municipi" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi3">Afegir municipi</a>
                        <button id="refreshButtonMunicipi3" class="btn btn-primary btn-sm">Actualitzar llistat Municipis</button>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="lloc_bombardeig" class="form-label negreta">Tipus d'espai del bombardeig:</label>
                    <select class="form-select" name="lloc_bombardeig" id="lloc_bombardeig" value="">
                    </select>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="responsable_bombardeig" class="form-label negreta">Responsable del bombardeig:</label>
                    <select class="form-select" id="responsable_bombardeig" name="responsable_bombardeig">
                        <option value="">Selecciona una opció:</option>
                        <option value="1">Aviació feixista italiana</option>
                        <option value="2">Aviació nazista alemanya</option>
                        <option value="3">Aviació franquista</option>
                    </select>
                </div>

                <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
                    <div class="col"></div>

                    <div class="col d-flex justify-content-end align-items-center">
                        <button class="btn btn-primary" id="btnMortsCivils" type="submit">Inserir dades</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>