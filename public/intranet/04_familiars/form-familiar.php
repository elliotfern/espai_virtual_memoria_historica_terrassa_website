<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <div class="container">

        <div id="titolForm"></div>
        <div class="alert alert-success" role="alert" id="okMessage" style="display:none">
            <div id="okText"></div>
        </div>

        <div class="alert alert-danger" role="alert" id="errMessage" style="display:none">
            <div id="errText"></div>
        </div>

        <form id="familiarForm">
            <div class="row g-4">

                <input type="hidden" name="id" id="id" value="">

                <div class="col-md-4 mb-4">
                    <label for="nom" class="form-label negreta">Nom:</label>
                    <input type="text" class="form-control" id="nom" name="nom" value="">
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="cognom1" class="form-label negreta">Primer cognom:</label>
                    <input type="text" class="form-control" id="cognom1" name="cognom1" value="">
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <div class="col-md-4">
                    <label for="cognom2" class="form-label negreta">Segon cognom:</label>
                    <input type="text" class="form-control" id="cognom2" name="cognom2" value="">
                </div>

                <div class="col-md-4 mb-4"> <label for="anyNaixement" class="form-label negreta">Any de naixement:</label>
                    <input type="text" class="form-control" id="anyNaixement" name="anyNaixement" value="">
                </div>

                <div class="col-md-4 mb-4"> <label for="relacio_parentiu_ca" class="form-label negreta">Relació de parentiu:</label>
                    <select class="form-select" name="relacio_parentiu_ca" id="relacio_parentiu_ca" value="">
                    </select>
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <div class="col-md-4 mb-4"> <label for="idParent" class="form-label negreta">Familiar represaliat:</label>
                    <select class="form-select" name="idParent" id="idParent" value="">
                    </select>
                </div>

                <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
                    <div class="col">
                        <a class="btn btn-secondary" role="button" aria-disabled="true" onclick="goBack()">Tornar enrere</a>
                    </div>
                    <div class="col d-flex justify-content-end align-items-center">

                        <button class="btn btn-primary" id="btnFamiliars" type="submit">Inserir dades</button>

                    </div>
                </div>
            </div>
        </form>
    </div>
</div>