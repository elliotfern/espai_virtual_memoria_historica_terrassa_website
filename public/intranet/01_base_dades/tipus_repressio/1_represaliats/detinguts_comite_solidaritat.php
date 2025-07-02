<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="comiteSolidaritatForm">
        <div class="container">
            <h2>Tipus de repressió: Detinguts Comitè de Solidaritat (1971-1977)</h2>
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
                    <label for="advocat" class="form-label negreta">Advocat:</label>
                    <input type="text" class="form-control" id="advocat" name="advocat" value="">
                </div>

                <div class="col-md-4 mb-4">
                    <label for="motiu" class="form-label negreta">Motiu de la detenció:</label>
                    <select class="form-select" id="motiu" name="motiu">
                    </select>

                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/auxiliars/nou-motiu-detencio" target="_blank" class="btn btn-secondary btn-sm" id="afegir1">Afegir motiu detenció</a>
                        <button id="refreshButton1" class="btn btn-primary btn-sm">Actualitzar llistat</button>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="any_detencio" class="form-label negreta">Any de detenció:</label>
                    <input type="text" class="form-control" id="any_detencio" name="any_detencio" value="">
                </div>

                <div class="col-md-12 mb-4">
                    <label for="observacions" class="form-label negreta">Observacions:</label>
                    <textarea class="form-control" id="observacions" name="observacions" rows="4"></textarea>
                </div>

                <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
                    <div class="col"></div>

                    <div class="col d-flex justify-content-end align-items-center">
                        <button class="btn btn-primary" type="submit" id="btnCS">Modificar dades</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>