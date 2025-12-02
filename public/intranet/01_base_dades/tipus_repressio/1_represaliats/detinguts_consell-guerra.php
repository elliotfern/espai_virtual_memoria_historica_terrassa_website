<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">

    <form id="processatForm">
        <div class="container">
            <h2>Tipus de repressió: Detingut/Processat</h2>
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

                <h4>1) Detenció</h4>
                <div class="col-md-4 mb-4">
                    <label for="data_detencio" class="form-label negreta">Data de detenció:</label>
                    <input type="text" class="form-control" id="data_detencio" name="data_detencio" value="">
                    <div class="avis-form">
                        * Format de la data: DD/MM/AAAA
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="lloc_detencio" class="form-label negreta">Ciutat de detenció:</label>
                    <select class="form-select" aria-label="Default select example" name="lloc_detencio" id="lloc_detencio">
                    </select>
                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/auxiliars/nou-municipi" target="_blank" class="btn btn-secondary btn-sm" id="afegirMun10">Afegir municipi</a>
                        <button id="refreshButton10" class="btn btn-primary btn-sm">Actualitzar llistat</button>
                    </div>
                </div>

                <h4>2) Procés judicial</h4>
                <div class="col-md-4 mb-4">
                    <label for="tipus_procediment" class="form-label negreta">Tipus de procediment:</label>
                    <select class="form-select" aria-label="Default select example" name="tipus_procediment" id="tipus_procediment">
                    </select>
                    <div class="avis-form">
                        * Camp obligatori.
                    </div>
                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/auxiliars/nou-tipus-procediment-judicial" target="_blank" class="btn btn-secondary btn-sm" id="afegir1">Afegir procediment</a>
                        <button id="refreshButton1" class="btn btn-primary btn-sm">Actualitzar llistat</button>
                    </div>

                </div>

                <div class="col-md-4 mb-4">
                    <label for="tipus_judici" class="form-label negreta">Tipus de judici:</label>
                    <select class="form-select" aria-label="Default select example" name="tipus_judici" id="tipus_judici">
                    </select>
                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/auxiliars/nou-tipus-judici" target="_blank" class="btn btn-secondary btn-sm" id="afegir2">Afegir tipus judici</a>
                        <button id="refreshButton2" class="btn btn-primary btn-sm">Actualitzar llistat</button>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="num_causa" class="form-label negreta">Número de causa:</label>
                    <input type="text" class="form-control" id="num_causa" name="num_causa" value="">
                </div>

                <div class="col-md-4 mb-4">
                    <label for="anyDetingut" class="form-label negreta">Anys en ser detingut o investigat:</label>
                    <input type="text" class="form-control" id="anyDetingut" name="anyDetingut" value="">
                </div>

                <div class="col-md-4 mb-4">
                    <label for="any_inicial" class="form-label negreta">Any inici procés:</label>
                    <input type="text" class="form-control" id="any_inicial" name="any_inicial" value="">
                </div>

                <div class="col-md-4 mb-4">
                    <label for="any_final" class="form-label negreta">Any final del procés:</label>
                    <input type="text" class="form-control" id="any_final" name="any_final" value="">
                </div>

                <div class="col-md-4 mb-4">
                    <label for="sentencia_data" class="form-label negreta">Data sentència:</label>
                    <input type="text" class="form-control" id="sentencia_data" name="sentencia_data" value="">
                    <div class="avis-form">
                        * Format de la data: DD/MM/AAAA
                    </div>
                </div>

                <h4>3) Detalls de la conclusió del procés</h4>

                <div class="alert alert-info">
                    <h5>Sobre la sentència i la pena</h5>
                    <ul>
                        <li>1. La sentència és la decició final emitida per un jutjat o tribunal en un cas legal. Aquesta pot incloure la imposició d'una pena, però també pot ordenar l'absolució de l'acusat si no se'l troba culpable del delicte.</li>
                        <li>2. La pena és una de les possibles conseqüències que pot incloure la sentència.</li>
                        <li>3. La sentència pot incloure la imposició d'una pena si es considera que l'acusat és culpable del delicte</li>
                    </ul>
                </div>

                <div class="alert alert-info">
                    <h5>Codi Penal que s'aplicava durant els consells de guerra: determinació de les sentències i les penes</h5>
                    * Més greu: Adhesión a la rebelión:
                    <ul>
                        <li>Pena de mort</li>
                        <li>Entre 30 i 20 anys i 1 día de reclusió major</li>
                    </ul>

                    * Auxili a la rebelió:
                    <ul>
                        <li>20 anys i 1 dia de reclusió menor</li>
                        <li>Penes d'1 any de presó menor</li>
                    </ul>

                    * Excitació a la rebelió:
                    <ul>
                        <li>12 anys de presó major</li>
                        <li>Penes d'1 any de presó menor</li>
                        <li>Fins a 6 mesos de presó</li>
                    </ul>
                </div>

                <div class="col-md-6 mb-4">
                    <label for="sentencia" class="form-label negreta">Sentència:</label>
                    <select class="form-select" aria-label="Default select example" name="sentencia" id="sentencia">
                    </select>
                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/auxiliars/nova-sentencia" target="_blank" class="btn btn-secondary btn-sm" id="afegir3">Afegir sentència</a>
                        <button id="refreshButton3" class="btn btn-primary btn-sm">Actualitzar llistat</button>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <label for="pena" class="form-label negreta">Pena:</label>
                    <select class="form-select" aria-label="Default select example" name="pena" id="pena">
                    </select>
                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/auxiliars/nova-pena" target="_blank" class="btn btn-secondary btn-sm" id="afegir4">Afegir pena</a>
                        <button id="refreshButton4" class="btn btn-primary btn-sm">Actualitzar llistat</button>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <label for="commutacio" class="form-label negreta">Commutació o indult:</label>
                    <input type="text" class="form-control" id="commutacio" name="commutacio" value="">
                </div>

                <h4>4) Detalls del procediment judicial: Consell de Guerra</h4>
                <div class="col-md-4 mb-4">
                    <label for="copia_exp" class="form-label negreta">Còpia expedient:</label>
                    <input type="text" class="form-control" id="copia_exp" name="copia_exp" value="">
                </div>

                <div class="col-md-4 mb-4">
                    <label for="data_inici_proces" class="form-label negreta">Data inici del procés judicial:</label>
                    <input type="text" class="form-control" id="data_inici_proces" name="data_inici_proces" value="">
                    <div class="avis-form">
                        * Format de la data: DD/MM/AAAA
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="jutjat" class="form-label negreta">Jutjat:</label>
                    <select class="form-select" aria-label="Default select example" name="jutjat" id="jutjat">
                    </select>
                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/auxiliars/nou-jutjat" target="_blank" class="btn btn-secondary btn-sm" id="afegir5">Afegir jutjat</a>
                        <button id="refreshButton5" class="btn btn-primary btn-sm">Actualitzar llistat</button>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="jutge_instructor" class="form-label negreta">Jutge instructor:</label>
                    <input type="text" class="form-control" id="jutge_instructor" name="jutge_instructor" value="">
                </div>

                <div class="col-md-4 mb-4">
                    <label for="secretari_instructor" class="form-label negreta">Secretari instructor:</label>
                    <input type="text" class="form-control" id="secretari_instructor" name="secretari_instructor" value="">
                </div>

                <div class="col-md-4 mb-4">
                    <label for="consell_guerra_data" class="form-label negreta">Data Consell de guerra:</label>
                    <input type="text" class="form-control" id="consell_guerra_data" name="consell_guerra_data" value="">
                    <div class="avis-form">
                        * Format de la data: DD/MM/AAAA
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="lloc_consell_guerra" class="form-label negreta">Ciutat Consell de guerra:</label>
                    <select class="form-select" aria-label="Default select example" name="lloc_consell_guerra" id="lloc_consell_guerra">
                    </select>
                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/auxiliars/nou-municipi" target="_blank" class="btn btn-secondary btn-sm" id="afegir6">Afegir municipi</a>
                        <button id="refreshButton6" class="btn btn-primary btn-sm">Actualitzar llistat</button>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="president_tribunal" class="form-label negreta">President Tribunal:</label>
                    <input type="text" class="form-control" id="president_tribunal" name="president_tribunal" value="">
                </div>

                <div class="col-md-4 mb-4">
                    <label for="defensor" class="form-label negreta">Defensor:</label>
                    <input type="text" class="form-control" id="defensor" name="defensor" value="">
                </div>

                <div class="col-md-4 mb-4">
                    <label for="fiscal" class="form-label negreta">Fiscal:</label>
                    <input type="text" class="form-control" id="fiscal" name="fiscal" value="">
                </div>

                <div class="col-md-4 mb-4">
                    <label for="ponent" class="form-label negreta">Ponent:</label>
                    <input type="text" class="form-control" id="ponent" name="ponent" value="">
                </div>

                <div class="col-md-4 mb-4">
                    <label for="tribunal_vocals" class="form-label negreta">Vocals del tribunal:</label>
                    <input type="text" class="form-control" id="tribunal_vocals" name="tribunal_vocals" value="">
                </div>

                <h4>5) Detalls de l'acusació fiscalia</h4>
                <div class="col-md-6 mb-4">
                    <label for="acusacio" class="form-label negreta">Acusació fiscalia 1:</label>
                    <select class="form-select" aria-label="Default select example" name="acusacio" id="acusacio">
                    </select>
                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/auxiliars/nova-acusacio" target="_blank" class="btn btn-secondary btn-sm" id="afegir7">Afegir acusació</a>
                        <button id="refreshButton7" class="btn btn-primary btn-sm">Actualitzar llistat</button>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <label for="acusacio_2" class="form-label negreta">Acusació fiscalia 2:</label>
                    <select class="form-select" aria-label="Default select example" name="acusacio_2" id="acusacio_2">
                    </select>
                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/auxiliars/nova-acusacio" target="_blank" class="btn btn-secondary btn-sm" id="afegir8">Afegir acusació</a>
                        <button id="refreshButton8" class="btn btn-primary btn-sm">Actualitzar llistat</button>
                    </div>
                </div>

                <div class="col-md-12 mb-4">
                    <label for="testimoni_acusacio" class="form-label negreta">Testimoni acusació:</label>
                    <input type="text" class="form-control" id="testimoni_acusacio" name="testimoni_acusacio" value="">
                </div>

                <div class="col-md-12 mb-4">
                    <label for="observacions" class="form-label negreta">Observacions:</label>
                    <textarea class="form-control" id="observacions" name="observacions" rows="4"></textarea>
                </div>

                <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
                    <div class="col"></div>

                    <div class="col d-flex justify-content-end align-items-center">
                        <button class="btn btn-primary" type="submit" id="btnProcessat">Modificar dades</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>