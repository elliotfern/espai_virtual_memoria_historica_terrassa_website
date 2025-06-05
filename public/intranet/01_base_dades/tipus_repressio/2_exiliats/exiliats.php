<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="exiliatForm">
        <div class="container">
            <div class="row g-3">
                <h2>Tipus de repressió: Exiliat </h2>
                <div id="fitxaNomCognoms"></div>

                <div class="alert alert-success" role="alert" id="okMessage" style="display:none">
                    <div id="okText"></div>
                </div>

                <div class="alert alert-danger" role="alert" id="errMessage" style="display:none">
                    <div id="errText"></div>
                </div>

                <input type="hidden" name="idPersona" id="idPersona" value="">
                <input type="hidden" name="id" id="id" value="">

                <hr>

                <h3>1) Sortida de Catalunya:</h3>
                <div class="col-md-4 mb-4">
                    <label for="data_exili" class="form-label negreta">Data d'exili:</label>
                    <input type="text" class="form-control" id="data_exili" name="data_exili" value="">
                    <div class="avis-form">
                        * Format data pot ser "any" o "dia/mes/any". (La majoria dels casos es l'any 1939)
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="lloc_partida" class="form-label negreta">Lloc partida exili (normalment és Terrassa):</label>
                    <select class="form-select" name="lloc_partida" id="lloc_partida" value="">
                    </select>

                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/auxiliars/nou-municipi" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi1">Afegir municipi</a>
                        <button id="refreshButton1" class="btn btn-primary btn-sm">Actualitzar llistat Municipis</button>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="lloc_pas_frontera" class="form-label negreta">Lloc pas de la frontera (pot ser Portbou, La Junquera, Coll de Lli, etc.):</label>
                    <select class="form-select" name="lloc_pas_frontera" id="lloc_pas_frontera" value="">
                    </select>

                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/auxiliars/nou-municipi" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi1">Afegir municipi</a>
                        <button id="refreshButton2" class="btn btn-primary btn-sm">Actualitzar llistat Municipis</button>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="amb_qui_passa_frontera" class="form-label negreta">Amb qui pasa a l'exili: (podria ser la parella, fills, germans, companyia militar...):</label>
                    <input type="text" class="form-control" id="amb_qui_passa_frontera" name="amb_qui_passa_frontera" value="">
                </div>

                <hr>
                <h3>2) Arribada al lloc d'exili (normalment França):</h3>
                <div class="col-md-4 mb-4">
                    <label for="primer_desti_exili" class="form-label negreta">Primer municipi de destí a l'exili (primer lloc on s'instal·la l'exiliat):</label>
                    <select class="form-select" name="primer_desti_exili" id="primer_desti_exili" value="">
                    </select>

                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/auxiliars/nou-municipi" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi3">Afegir municipi</a>
                        <button id="refreshButton3" class="btn btn-primary btn-sm">Actualitzar llistat Municipis</button>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="primer_desti_data" class="form-label negreta">Data del primer destí de l'exili:</label>
                    <input type="text" class="form-control" id="primer_desti_data" name="primer_desti_data" value="">
                    <div class="avis-form">
                        * Format data pot ser "any" o "dia/mes/any"
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="tipologia_primer_desti" class="form-label negreta">Tipologia del primer destí a l'exili:</label>
                    <select class="form-select" name="tipologia_primer_desti" id="tipologia_primer_desti" value="">
                    </select>
                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/auxiliars/nova-tipologia-espai" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi77">Afegir tipologia espai</a>
                        <button id="refreshButtonTipologia22" class="btn btn-primary btn-sm">Actualitzar llistat espais</button>
                    </div>
                </div>

                <div class="col-md-12 mb-4">
                    <label for="dades_lloc_primer_desti" class="form-label negreta">Dades del primer destí de l'exili:</label>
                    <textarea class="form-control" id="dades_lloc_primer_desti" name="dades_lloc_primer_desti" rows="3"></textarea>
                </div>

                <h3>2) Periple exili:</h3>

                <div class="col-md-12 mb-4">
                    <label for="periple_recorregut" class="form-label negreta">Periple del recorregut a l'exili:</label>
                    <textarea class="form-control" id="periple_recorregut" name="periple_recorregut" rows="3"></textarea>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="deportat" class="form-label negreta">Deportat:</label>
                    <select class="form-select" id="deportat" name="deportat">
                        <option value="">Selecciona una opció:</option>
                        <option value="1">Si</option>
                        <option value="2">No</option>
                    </select>
                </div>

                <h3>4) Final del període d'exili:</h3>

                <div class="col-md-4 mb-4">
                    <label for="ultim_desti_exili" class="form-label negreta">Darrer municipi de destí a l'exili:</label>
                    <select class="form-select" name="ultim_desti_exili" id="ultim_desti_exili" value="">
                    </select>
                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/auxiliars/nou-municipi" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi3">Afegir municipi</a>
                        <button id="refreshButton4" class="btn btn-primary btn-sm">Actualitzar llistat Municipis</button>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="tipologia_ultim_desti" class="form-label negreta">Tipologia del darrer destí a l'exili:</label>
                    <select class="form-select" name="tipologia_ultim_desti" id="tipologia_ultim_desti" value="">
                    </select>

                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/auxiliars/nova-tipologia-espai" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi778">Afegir tipologia espai</a>
                        <button id="refreshButtonTipologia2" class="btn btn-primary btn-sm">Actualitzar llistat espais</button>
                    </div>

                </div>

                <hr>
                <h3>5) Activitat política i sindical durant l'exili</h3>

                <div class="col-md-4 mb-4">
                    <label for="participacio_resistencia" class="form-label negreta">Participació a la Resistència:</label>
                    <select class="form-select" id="participacio_resistencia" name="participacio_resistencia">
                        <option value="">Selecciona una opció:</option>
                        <option value="1">Si</option>
                        <option value="2">No</option>
                    </select>
                </div>

                <div class="col-md-12 mb-4">
                    <label for="dades_resistencia" class="form-label negreta">Dades de la Resistència:</label>
                    <textarea class="form-control" id="dades_resistencia" name="dades_resistencia" rows="3"></textarea>
                </div>

                <div class="col-md-12 mb-4">
                    <label for="activitat_politica_exili" class="form-label negreta">Activitat política a l'exili:</label>
                    <textarea class="form-control" id="activitat_politica_exili" name="activitat_politica_exili" rows="3"></textarea>
                </div>

                <div class="col-md-12 m4-4">
                    <label for="activitat_sindical_exili" class="form-label negreta">Activitat sindical a l'exili:</label>
                    <textarea class="form-control" id="activitat_sindical_exili" name="activitat_sindical_exili" rows="3"></textarea>
                </div>

                <div class="col-md-12 mb-4">
                    <label for="situacio_legal_espanya" class="form-label negreta">Situació legal a Espanya:</label>
                    <textarea class="form-control" id="situacio_legal_espanya" name="situacio_legal_espanya" rows="3"></textarea>
                </div>

                <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
                    <div class="col"></div>
                    <div class="col d-flex justify-content-end align-items-center">
                        <button class="btn btn-primary" id="btnExiliats" type="submit">Inserir dades</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>