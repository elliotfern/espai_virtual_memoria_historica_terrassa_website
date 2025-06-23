<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="topForm">
        <div class="container">
            <h2>Tipus de repressió: Tribunal de Orden Público</h2>
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
                    <label for="preso" class="form-label negreta">Lloc d'empresonament:</label>
                    <select class="form-select" id="preso" name="preso">
                    </select>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="data_sentencia" class="form-label negreta">Data sentència:</label>
                    <input type="text" class="form-control" id="data_sentencia" name="data_sentencia" value="">
                </div>

                <div class="col-md-4 mb-4">
                    <label for="num_causa" class="form-label negreta">Número de la causa:</label>
                    <input type="text" class="form-control" id="num_causa" name="num_causa" value="">
                </div>

                <div class="col-md-12 mb-4">
                    <label for="sentencia" class="form-label negreta">Sentència:</label>
                    <input type="text" class="form-control" id="sentencia" name="sentencia" value="">
                </div>

                <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
                    <div class="col"></div>

                    <div class="col d-flex justify-content-end align-items-center">
                        <button class="btn btn-primary" type="submit" id="btnTop">Modificar dades</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>