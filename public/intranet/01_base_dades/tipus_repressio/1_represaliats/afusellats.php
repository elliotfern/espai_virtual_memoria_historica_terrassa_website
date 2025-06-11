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

                <!-- copia_exp (text) -->
                <div class="col-md-4 mb-4">
                    <label for="copia_exp" class="form-label negreta">Còpia exp.:</label>
                    <input type="text" class="form-control" id="copia_exp" name="copia_exp" value="">
                </div>

                <!-- procediment (int) -->
                <div class="col-md-4 mb-4">
                    <label for="procediment" class="form-label negreta">Procediment:</label>
                    <select class="form-select" id="procediment" name="procediment">
                    </select>
                </div>

                <!-- num_causa -->
                <div class="col-md-4 mb-4">
                    <label for="num_causa" class="form-label negreta">Número de causa:</label>
                    <input type="text" class="form-control" id="num_causa" name="num_causa" value="">
                </div>

                <!-- data_inici_proces -->
                <div class="col-md-4 mb-4">
                    <label for="data_inici_proces" class="form-label negreta">Data inici procés:</label>
                    <input type="text" class="form-control" id="data_inici_proces" name="data_inici_proces" value="">
                </div>

                <!-- jutge_instructor -->
                <div class="col-md-4 mb-4">
                    <label for="jutge_instructor" class="form-label negreta">Jutge instructor:</label>
                    <input type="text" class="form-control" id="jutge_instructor" name="jutge_instructor" value="">
                </div>

                <!-- secretari_instructor -->
                <div class="col-md-4 mb-4">
                    <label for="secretari_instructor" class="form-label negreta">Secretari instructor:</label>
                    <input type="text" class="form-control" id="secretari_instructor" name="secretari_instructor" value="">
                </div>

                <!-- jutjat -->
                <div class="col-md-4 mb-4">
                    <label for="jutjat" class="form-label negreta">Jutjat:</label>
                    <input type="text" class="form-control" id="jutjat" name="jutjat" value="">
                </div>

                <!-- any_inicial -->
                <div class="col-md-4 mb-4">
                    <label for="any_inicial" class="form-label negreta">Any inicial:</label>
                    <input type="text" class="form-control" id="any_inicial" name="any_inicial" value="">
                </div>

                <!-- consell_guerra_data -->
                <div class="col-md-4 mb-4">
                    <label for="consell_guerra_data" class="form-label negreta">Data consell guerra:</label>
                    <input type="text" class="form-control" id="consell_guerra_data" name="consell_guerra_data" value="">
                </div>

                <!-- lloc_consell_guerra (int) -->
                <div class="col-md-4 mb-4">
                    <label for="lloc_consell_guerra" class="form-label negreta">Lloc consell guerra:</label>
                    <select class="form-select" id="lloc_consell_guerra" name="lloc_consell_guerra">
                    </select>
                </div>

                <!-- president_tribunal -->
                <div class="col-md-4 mb-4">
                    <label for="president_tribunal" class="form-label negreta">President del tribunal:</label>
                    <input type="text" class="form-control" id="president_tribunal" name="president_tribunal" value="">
                </div>

                <!-- defensor -->
                <div class="col-md-4 mb-4">
                    <label for="defensor" class="form-label negreta">Defensor:</label>
                    <input type="text" class="form-control" id="defensor" name="defensor" value="">
                </div>

                <!-- fiscal -->
                <div class="col-md-4 mb-4">
                    <label for="fiscal" class="form-label negreta">Fiscal:</label>
                    <input type="text" class="form-control" id="fiscal" name="fiscal" value="">
                </div>

                <!-- ponent -->
                <div class="col-md-4 mb-4">
                    <label for="ponent" class="form-label negreta">Ponent:</label>
                    <input type="text" class="form-control" id="ponent" name="ponent" value="">
                </div>

                <!-- tribunal_vocals -->
                <div class="col-md-4 mb-4">
                    <label for="tribunal_vocals" class="form-label negreta">Vocals del tribunal:</label>
                    <input type="text" class="form-control" id="tribunal_vocals" name="tribunal_vocals" value="">
                </div>

                <!-- acusacio -->
                <div class="col-md-4 mb-4">
                    <label for="acusacio" class="form-label negreta">Acusació:</label>
                    <input type="text" class="form-control" id="acusacio" name="acusacio" value="">
                </div>

                <!-- acusacio_2 -->
                <div class="col-md-4 mb-4">
                    <label for="acusacio_2" class="form-label negreta">Acusació 2:</label>
                    <input type="text" class="form-control" id="acusacio_2" name="acusacio_2" value="">
                </div>

                <!-- testimoni_acusacio -->
                <div class="col-md-4 mb-4">
                    <label for="testimoni_acusacio" class="form-label negreta">Testimoni acusació:</label>
                    <input type="text" class="form-control" id="testimoni_acusacio" name="testimoni_acusacio" value="">
                </div>

                <!-- sentencia_data -->
                <div class="col-md-4 mb-4">
                    <label for="sentencia_data" class="form-label negreta">Data sentència:</label>
                    <input type="text" class="form-control" id="sentencia_data" name="sentencia_data" value="">
                </div>

                <!-- sentencia -->
                <div class="col-md-4 mb-4">
                    <label for="sentencia" class="form-label negreta">Sentència:</label>
                    <input type="text" class="form-control" id="sentencia" name="sentencia" value="">
                </div>

                <!-- data_sentencia -->
                <div class="col-md-4 mb-4">
                    <label for="data_sentencia" class="form-label negreta">Data de la sentència:</label>
                    <input type="text" class="form-control" id="data_sentencia" name="data_sentencia" value="">
                </div>

                <!-- data_execucio -->
                <div class="col-md-4 mb-4">
                    <label for="data_execucio" class="form-label negreta">Data execució:</label>
                    <input type="text" class="form-control" id="data_execucio" name="data_execucio" value="">
                </div>

                <!-- enterrament_lloc (int) -->
                <div class="col-md-4 mb-4">
                    <label for="enterrament_lloc" class="form-label negreta">Lloc enterrament:</label>
                    <select class="form-select" id="enterrament_lloc" name="enterrament_lloc">

                    </select>
                </div>

                <!-- lloc_execucio_enterrament (int) -->
                <div class="col-md-4 mb-4">
                    <label for="lloc_execucio_enterrament" class="form-label negreta">Lloc execució i enterrament:</label>
                    <select class="form-select" id="lloc_execucio_enterrament" name="lloc_execucio_enterrament">

                    </select>
                </div>

                <!-- ref_num_arxiu -->
                <div class="col-md-4 mb-4">
                    <label for="ref_num_arxiu" class="form-label negreta">Ref. núm. arxiu:</label>
                    <input type="text" class="form-control" id="ref_num_arxiu" name="ref_num_arxiu" value="">
                </div>

                <!-- font_1 -->
                <div class="col-md-4 mb-4">
                    <label for="font_1" class="form-label negreta">Font 1:</label>
                    <input type="text" class="form-control" id="font_1" name="font_1" value="">
                </div>

                <!-- font_2 -->
                <div class="col-md-4 mb-4">
                    <label for="font_2" class="form-label negreta">Font 2:</label>
                    <input type="text" class="form-control" id="font_2" name="font_2" value="">
                </div>


                <!-- observacions -->
                <div class="col-md-12 mb-4">
                    <label for="observacions" class="form-label negreta">Observacions:</label>
                    <input type="text" class="form-control" id="observacions" name="observacions" value="">
                </div>

                <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
                    <div class="col"></div>

                    <div class="col d-flex justify-content-end align-items-center">
                        <a class="btn btn-primary" role="button" aria-disabled="true" id="btnModificarDadesCombat">Modificar dades</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>