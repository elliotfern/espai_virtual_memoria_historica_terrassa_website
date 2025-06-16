<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="afusellatForm">
        <div class="container">
            <h2>Tipus de repressió: Afusellat</h2>
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

                <!-- data_execucio -->
                <div class="col-md-4 mb-4">
                    <label for="data_execucio" class="form-label negreta">Data execució:</label>
                    <input type="text" class="form-control" id="data_execucio" name="data_execucio" value="">
                </div>

                <!-- lloc_execucio_enterrament (int) -->
                <div class="col-md-4 mb-4">
                    <label for="lloc_execucio_enterrament" class="form-label negreta">Lloc execució:</label>
                    <select class="form-select" id="lloc_execucio_enterrament" name="lloc_execucio_enterrament">
                    </select>
                </div>

                <!-- enterrament_lloc (int) -->
                <div class="col-md-4 mb-4">
                    <label for="enterrament_lloc" class="form-label negreta">Lloc enterrament:</label>
                    <select class="form-select" id="enterrament_lloc" name="enterrament_lloc">

                    </select>
                </div>


                <!-- observacions -->
                <div class="col-md-12 mb-4">
                    <label for="observacions" class="form-label negreta">Observacions:</label>
                    <input type="text" class="form-control" id="observacions" name="observacions" value="">
                </div>

                <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
                    <div class="col"></div>

                    <div class="col d-flex justify-content-end align-items-center">
                        <button class="btn btn-primary" type="submit" id="btnAfusellat">Modificar dades</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>