<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">

    <div class="container">
        <h2>Tipus de repressió: Mort / desaparegut en combat (Cost humà de la Guerra civil)</h2>
        <div id="fitxaNomCognoms"></div>

        <div class="alert alert-success" role="alert" id="okMessage" style="display:none">
            <div id="okText"></div>
        </div>

        <div class="alert alert-danger" role="alert" id="errMessage" style="display:none">
            <div id="errText"></div>
        </div>

        <form id="mortCombatForm">
            <div class="row g-3">
                <input type="hidden" name="idPersona" id="idPersona" value="">
                <input type="hidden" name="id" id="id" value="">

                <div class="col-md-4 mb-4">
                    <label for="condicio" class="form-label negreta">Condició:</label>
                    <select class="form-select" name="condicio" id="condicio" value="">
                    </select>
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="bandol" class="form-label negreta">Bàndol durant la guerra:</label>
                    <select class="form-select" name="bandol" id="bandol" value="">
                    </select>
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="any_lleva" class="form-label negreta">Any lleva:</label>
                    <input type="text" class="form-control" id="any_lleva" name="any_lleva" value="">
                </div>

                <div class="col-md-4 mb-4">
                    <label for="unitat_inicial" class="form-label negreta">Unitat inicial:</label>
                    <input type="text" class="form-control" id="unitat_inicial" name="unitat_inicial" value="">
                </div>

                <div class="col-md-4 mb-4">
                    <label for="cos" class="form-label negreta">Cos militar:</label>
                    <select class="form-select" name="cos" id="cos" value="">
                    </select>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="unitat_final" class="form-label negreta">Unitat final:</label>
                    <input type="text" class="form-control" id="unitat_final" name="unitat_final" value="">
                </div>

                <div class="col-md-4 mb-4">
                    <label for="graduacio_final" class="form-label negreta">Graduació final:</label>
                    <input type="text" class="form-control" id="graduacio_final" name="graduacio_final" value="">
                </div>

                <div class="col-md-12 mb-4">
                    <label for="periple_militar" class="form-label negreta">Periple militar i altres observacions:</label>
                    <textarea class="form-control" id="periple_militar" name="periple_militar" rows="5"></textarea>
                </div>

                <hr>

                <h4>Dades conegudes sobre la defunció/desaparació:</h4>

                <div class="col-md-4 mb-4">
                    <label for="circumstancia_mort" class="form-label negreta">Circumstància mort/desaparació:</label>
                    <select class="form-select" name="circumstancia_mort" id="circumstancia_mort" value="">
                    </select>
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <hr>

                <h4>Si el combatent és donat per desaparegut:</h4>

                <div class="col-md-4 mb-4">
                    <label for="desaparegut_data" class="form-label negreta">Data de la desaparació:</label>
                    <input type="text" class="form-control" id="desaparegut_data" name="desaparegut_data" value="">
                    <div class="avis-form">
                        * Format data: dia/mes/any
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="desaparegut_lloc" class="form-label negreta">Lloc de desaparació:</label>
                    <select class="form-select" name="desaparegut_lloc" id="desaparegut_lloc" value="">
                    </select>

                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/auxiliars/nou-municipi" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi1">Afegir municipi</a>
                        <button id="refreshButton1" class="btn btn-primary btn-sm">Actualitzar llistat Municipis</button>
                    </div>
                </div>

                <hr>

                <h4>Dades conegudes sobre l'aparició posterior del desaparegut:</h4>

                <div class="col-md-4 mb-4">
                    <label for="reaparegut" class="form-label negreta">És un reaparegut?:</label>
                    <select class="form-select" name="reaparegut" id="reaparegut" value="">
                    </select>
                </div>


                <div class="col-md-4 mb-4">
                    <label for="desaparegut_data_aparicio" class="form-label negreta">Data d'aparació del desaparegut:</label>
                    <input type="text" class="form-control" id="desaparegut_data_aparicio" name="desaparegut_data_aparicio" value="">
                    <div class="avis-form">
                        * Format data: dia/mes/any
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="desaparegut_lloc_aparicio" class="form-label negreta">Lloc d'aparació del desaparegut:</label>
                    <select class="form-select" name="desaparegut_lloc_aparicio" id="desaparegut_lloc_aparicio" value="">
                    </select>

                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/auxiliars/nou-municipi" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi1">Afegir municipi</a>
                        <button id="refreshButton2" class="btn btn-primary btn-sm">Actualitzar llistat Municipis</button>
                    </div>
                </div>

                <div class="col-md-12 mb-4">
                    <label for="aparegut_observacions" class="form-label negreta">Observacions sobre l'aparició:</label>
                    <textarea class="form-control" id="aparegut_observacions" name="aparegut_observacions" rows="4"></textarea>
                </div>



                <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
                    <div class="col"></div>

                    <div class="col d-flex justify-content-end align-items-center">
                        <button class="btn btn-primary" id="btnMortsCombat" type="submit">Inserir dades</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>