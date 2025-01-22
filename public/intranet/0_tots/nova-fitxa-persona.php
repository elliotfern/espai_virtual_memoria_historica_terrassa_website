<?php
require_once APP_ROOT . '/public/intranet/includes/header.php';
?>


<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">

    <div class="alert alert-success" role="alert" id="okMessage" style="display:none">
        <h4 class="alert-heading"><strong>Fitxa creada correctament!</strong></h4>
        <div id="okText"></div>
    </div>

    <div class="alert alert-danger" role="alert" id="errMessage" style="display:none">
        <h4 class="alert-heading"><strong>Error en les dades!</strong></h4>
        <div id="errText"></div>
    </div>

    <h2>Creació de nova fitxa</h2>


    <div class="tab">
        <button class="tablinks" onclick="openTab(event, 'tab8')">Categoria repressió</button>
        <button class="tablinks" onclick="openTab(event, 'tab1')">Dades personals</button>
        <button class="tablinks" onclick="openTab(event, 'tab2')">Dades familiars</button>
        <button class="tablinks" onclick="openTab(event, 'tab3')">Dades acadèmiques i laborals</button>
        <button class="tablinks" onclick="openTab(event, 'tab4')">Dades polítiques i sindicals</button>
        <button class="tablinks" onclick="openTab(event, 'tab7')">Altres dades</button>
    </div>

    <form id="personalNovaFitxaForm">

        <div id="tab8" class="tabcontent">
            <div class="row">
                <h3>Categoria repressió</h3>

                <div class="container">
                    <div class="row">


                        <div class="col-md-12" style="margin-top:20px;margin-bottom:20px">
                            <h6><strong>Represaliats 1939/1979:</strong></h6>

                            <div id="categoria" class="d-flex flex-wrap">
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="checkbox" id="categoria6" name="categoria" value="categoria6">
                                    <label class="form-check-label" for="categoria6">
                                        Processat/Empresonat
                                    </label>
                                </div>

                                <div class="form-check me-3">
                                    <input class="form-check-input" type="checkbox" id="categoria1" name="categoria" value="categoria1">
                                    <label class="form-check-label" for="categoria1">
                                        Afusellat
                                    </label>
                                </div>

                                <div class="form-check me-3">
                                    <input class="form-check-input" type="checkbox" id="categoria7" name="categoria" value="categoria7">
                                    <label class="form-check-label" for="categoria7">
                                        Depurat
                                    </label>
                                </div>

                            </div>
                        </div> <!-- Fi bloc repressio 1939-79 -->

                        <div class="col-md-12" style="margin-bottom:20px">
                            <h6><strong>Exili:</strong></h6>

                            <div id="categoria" class="d-flex flex-wrap">
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="checkbox" id="categoria10" name="categoria" value="categoria10">
                                    <label class="form-check-label" for="categoria10">
                                        Exiliat
                                    </label>
                                </div>

                                <div class="form-check me-3">
                                    <input class="form-check-input" type="checkbox" id="categoria2" name="categoria" value="categoria2">
                                    <label class="form-check-label" for="categoria2">
                                        Deportat
                                    </label>
                                </div>

                            </div>
                        </div> <!-- Fi bloc exili -->

                        <div class="col-md-12" style="margin-top:20px">
                            <h6><strong>Cost humà de la guerra:</strong></h6>

                            <div id="categoria" class="d-flex flex-wrap">
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="checkbox" id="categoria3" name="categoria" value="categoria3">
                                    <label class="form-check-label" for="categoria3">
                                        Mort o desaparegut en combat
                                    </label>
                                </div>

                                <div class="form-check me-3">
                                    <input class="form-check-input" type="checkbox" id="categoria4" name="categoria" value="categoria4">
                                    <label class="form-check-label" for="categoria4">
                                        Mort civil
                                    </label>
                                </div>

                                <div class="form-check me-3">
                                    <input class="form-check-input" type="checkbox" id="categoria5" name="categoria" value="categoria5">
                                    <label class="form-check-label" for="categoria5">
                                        Represàlia republicana
                                    </label>
                                </div>

                            </div>
                        </div> <!-- Fi bloc cost huma -->

                    </div> <!-- Fi bloc row -->
                </div> <!-- Fi bloc container -->

            </div>
        </div> <!-- Fi tab8 categoria repressio -->

        <div id="tab1" class="tabcontent">
            <div class="row">
                <h3>Dades personals</h3>

                <div class="col-md-4">
                    <label for="nom" class="form-label negreta">Nom:</label>
                    <input type="text" class="form-control" id="nom" name="nom" value="">

                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <div class="col-md-4">
                    <label for="cognom1" class="form-label negreta">Primer cognom:</label>
                    <input type="text" class="form-control" id="cognom1" name="cognom1" value="">

                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <div class="col-md-4">
                    <label for="cognom2" class="form-label negreta">Segon cognom:</label>
                    <input type="text" class="form-control" id="cognom2" name="cognom2" value="">

                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <div class="col-md-4">
                    <label for="sexe" class="form-label negreta">Gènere:</label>
                    <select class="form-select" id="sexe" name="sexe">
                        <option selected disabled>Selecciona una opció:</option>
                        <option value="1">Home</option>
                        <option value="2">Dona</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="data_naixement" class="form-label negreta">Data de naixement:</label>
                    <input type="text" class="form-control" id="data_naixement" name="data_naixement" value="">
                </div>

                <div class="col-md-4">
                    <label for="data_defuncio" class="form-label negreta">Data de defunció:</label>
                    <input type="text" class="form-control" id="data_defuncio" name="data_defuncio" value="">
                </div>

                <div class="col-md-4">
                    <label for="ciutat_naixement" class="form-label negreta">Ciutat de naixement:</label>
                    <select class="form-select" name="municipi_naixement" id="municipi_naixement" value="">
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="ciutat_defuncio" class="form-label negreta">Ciutat de defuncio:</label>
                    <select class="form-select" name="municipi_defuncio" id="municipi_defuncio" value="">
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="ciutat_residencia" class="form-label negreta">Ciutat de residència:</label>
                    <select class="form-select" name="municipi_residencia" id="municipi_residencia" value="">
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="adreca" class="form-label negreta">Adreça residència:</label>
                    <input type="text" class="form-control" id="adreca" name="adreca" value="">
                </div>

                <div class="col-md-4">
                    <label for="tipologia_lloc_defuncio" class="form-label negreta">Tipologia lloc de defunció:</label>
                    <select class="form-select" id="tipologia_lloc_defuncio" value="" name="tipologia_lloc_defuncio">
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="causa_defuncio" class="form-label negreta">Causa de la defunció:</label>
                    <select class="form-select" id="causa_defuncio" value="" name="causa_defuncio">
                    </select>
                </div>

            </div>
        </div> <!-- Fi tab1 -->

        <div id="tab2" class="tabcontent">
            <div class="row">
                <h3>Dades familiars</h3>
                <div class="col-md-4">
                    <label for="estat_civil" class="form-label negreta">Estat civil:</label>
                    <select class="form-select" id="estat_civil" name="estat_civil" value="">
                    </select>
                </div>

            </div>
        </div> <!-- Fi tab2 -->

        <div id="tab3" class="tabcontent">
            <div class="row">
                <h3>Dades laborals i acadèmiques</h3>

                <div class="col-md-4">
                    <label for="estudis" class="form-label negreta">Estudis:</label>
                    <select class="form-select" id="estudis" value="" name="estudis">
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="ofici" class="form-label negreta">Ofici:</label>
                    <select class="form-select" id="ofici" value="" name="ofici">
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="empresa" class="form-label negreta">Empresa:</label>
                    <input type="text" class="form-control" id="empresa" name="empresa" value="">
                </div>

                <div class="col-md-4">
                    <label for="carrec_empresa" class="form-label negreta">Càrrec empresa:</label>
                    <select class="form-select" id="carrec_empresa" value="" name="carrec_empresa">
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="sector" class="form-label negreta">Sector econòmic:</label>
                    <select class="form-select" id="sector" value="" name="sector">
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="sub_sector" class="form-label negreta">Sub-sector econòmic:</label>
                    <select class="form-select" id="sub_sector" value="" name="sub_sector">
                    </select>
                </div>

            </div>
        </div> <!-- Fi tab3 -->

        <div id="tab4" class="tabcontent">
            <div class="row">
                <h3>Dades polítiques/sindicals</h3>

                <div class="container">
                    <div class="row">
                        <div class="col-md-12" style="margin-top:20px;margin-bottom:20px">
                            <h6><strong>Filiació política:</strong></h6>

                            <div id="partit_politic" class="d-flex flex-wrap">

                            </div>
                        </div>
                    </div>
                </div>

                <div class="container">
                    <div class="row">
                        <div class="col-md-12" style="margin-top:20px;margin-bottom:20px">
                            <h6><strong>Filiació sindical:</strong></h6>

                            <div id="sindicat" class="d-flex flex-wrap">

                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <label for="activitat_durant_guerra" class="form-label negreta">Activitat política/sindical durant la guerra:</label>
                    <textarea class="form-control" id="activitat_durant_guerra" name="activitat_durant_guerra" value="" rows="3"></textarea>
                </div>

            </div>
        </div> <!-- Fi tab4 -->

        <div id="tab5" class="tabcontent">
            <div class="row">
                <h3>Biografia</h3>


            </div>
        </div> <!-- Fi tab5 -->

        <div id="tab6" class="tabcontent">
            <div class="row">
                <h3>Fonts documentals</h3>


            </div>
        </div> <!-- Fi tab6 -->

        <div id="tab7" class="tabcontent">
            <div class="row">
                <h3>Altres dades</h3>

                <div class="col-md-12">
                    <label for="observacions" class="form-label negreta">Observacions:</label>
                    <textarea class="form-control" id="observacions" name="observacions" value="" rows="3"></textarea>
                </div>

                <div class="col-md-4">
                    <label for="autor" class="form-label negreta">Autor fitxa:</label>
                    <select class="form-select" id="autor" value="" name="autor">
                    </select>
                </div>

                <div class="form-group">
                    <label>Vols indicar que la fitxa està completada?</label><br>

                    <!-- Botón de opción "Sí" -->
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="completat_si" name="completat" value="2" class="custom-control-input">
                        <label class="custom-control-label" for="completat_si">Si</label>
                    </div>

                    <!-- Botón de opción "No" -->
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="completat_no" name="completat" value="1" class="custom-control-input">
                        <label class="custom-control-label" for="completat_no">No</label>
                    </div>
                </div>
            </div>
        </div> <!-- Fi tab7 -->


        <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
            <div class="col">
                <a class="btn btn-secondary" role="button" aria-disabled="true" onclick="goBack()">Cancel·lar els canvis</a>
            </div>

            <div class="col d-flex justify-content-end align-items-center">
                <a class="btn btn-primary" role="button" aria-disabled="true" id="btnNovesDadesPersonals" onclick="enviarFormulario(event)">Introduir dades</a>
            </div>
        </div>
    </form> <!-- Fi Form -->

    <script>
        function openTab(evt, tabName) {
            // Obtén todos los elementos con la clase tabcontent y ocúltalos
            var tabcontent = document.getElementsByClassName("tabcontent");
            for (var i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }

            // Obtén todos los elementos con la clase tablinks y quítales la clase "active"
            var tablinks = document.getElementsByClassName("tablinks");
            for (var i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }

            // Muestra el div actual y agrega la clase "active" al botón que abrió la pestaña
            document.getElementById(tabName).style.display = "block";
            evt.currentTarget.className += " active";
        }

        // Mostrar la primera pestaña por defecto
        document.getElementById("tab8").style.display = "block";
        document.getElementsByClassName("tablinks")[0].className += " active";
    </script>

    <script>
        // Carregar el select
        async function auxiliarSelect(idAux, api, elementId, valorText) {

            const devDirectory = `https://${window.location.hostname}`;
            let urlAjax = devDirectory + "/api/auxiliars/get/?type=" + api;

            // Obtener el token del localStorage
            let token = localStorage.getItem('token');

            // Configurar las opciones de la solicitud
            const options = {
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json'
                }
            };

            try {
                // Hacer la solicitud fetch y esperar la respuesta
                const response = await fetch(urlAjax, options);

                // Verificar si la respuesta es correcta
                if (!response.ok) {
                    throw new Error('Error en la solicitud');
                }

                // Parsear los datos JSON
                const data = await response.json();


                // Obtener la referencia al elemento select
                var selectElement = document.getElementById(elementId);

                // Limpiar el select por si ya tenía opciones anteriores
                selectElement.innerHTML = "";

                // Agregar una opción predeterminada "Selecciona una opción"
                var defaultOption = document.createElement("option");
                defaultOption.text = "Selecciona una opció:";
                defaultOption.value = ""; // Valor vacío
                selectElement.appendChild(defaultOption);

                // Iterar sobre los datos obtenidos de la API
                data.forEach(function(item) {
                    // Crear una opción y agregarla al select
                    // console.log(item.ciutat)
                    var option = document.createElement("option");
                    option.value = item.id; // Establecer el valor de la opción
                    option.text = item[valorText]; // Establecer el texto visible de la opción
                    selectElement.appendChild(option);
                });

                // Seleccionar automáticamente el valor
                if (idAux) {
                    selectElement.value = idAux;
                }

            } catch (error) {
                console.error('Error al parsear JSON:', error); // Muestra el error de parsing
            }
        }


        function goBack() {
            window.history.back();
        }

        // Función para obtener el listado de partidos políticos desde la API
        const fetchCheckBoxs = async (apiUrl, nodeElement) => {
            try {
                // Simulamos una llamada a la API
                const devDirectory = `https://${window.location.hostname}`;
                let urlAjax = devDirectory + "/api/auxiliars/get/?type=" + apiUrl;

                const response = await fetch(urlAjax); // Cambia la URL a tu API real
                const data = await response.json(); // Convertimos la respuesta en JSON

                // Generar los checkboxes
                renderCheckboxes(data, nodeElement);

            } catch (error) {
                console.error('Error al obtener los partidos políticos:', error);
            }
        };

        // Función para generar los checkboxes dinámicamente
        const renderCheckboxes = (data, nodeElement) => {
            const container = document.getElementById(nodeElement);


            let checkboxName = "";
            let nomElement = "";
            if (nodeElement === "partit_politic") {
                checkboxName = "partido";
                nomElement = "partit_politic";
            } else if (nodeElement === "sindicat") {
                checkboxName = "sindicat";
                nomElement = "sindicat";
            }

            data.forEach((partido) => {
                // Crear el checkbox
                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.id = `${checkboxName}-${partido.id}`;
                checkbox.name = `${checkboxName}`; // Añadir el atributo name
                checkbox.value = partido.id; // El valor debe ser el id del partido
                checkbox.className = 'form-check-input me-2'; // Clases de Bootstrap para estilo

                // Crear la etiqueta
                const label = document.createElement('label');
                label.htmlFor = `${checkboxName}-${partido.id}`; // Corregimos el id de la etiqueta
                label.textContent = partido[nomElement]; // Nombre del partido
                label.className = 'form-check-label me-4'; // Clases para espaciado

                // Agrupar checkbox y label en un div
                const div = document.createElement('div');
                div.className = 'd-flex align-items-center'; // Clases de alineación
                div.appendChild(checkbox);
                div.appendChild(label);

                // Añadir al contenedor principal
                container.appendChild(div);
            });
        };

        // Carregar tota la informacio des de la base de dades


        auxiliarSelect("", "municipis", "municipi_naixement", "ciutat");
        auxiliarSelect("", "municipis", "municipi_defuncio", "ciutat");
        auxiliarSelect("", "municipis", "municipi_residencia", "ciutat");

        auxiliarSelect("", "tipologia_espais", "tipologia_lloc_defuncio", "tipologia_espai_ca");

        auxiliarSelect("", "causa_defuncio", "causa_defuncio", "causa_defuncio_ca");

        // 02. dades familiars:
        auxiliarSelect("", "estats", "estat_civil", "estat_cat");

        // cridar a funcio per carregar API amb les dades familiars

        // 03. dades laborals i academiques:
        auxiliarSelect("", "estudis", "estudis", "estudi_cat")
        auxiliarSelect("", "oficis", "ofici", "ofici_cat");

        auxiliarSelect("", "sectors_economics", "sector", "sector_cat");

        auxiliarSelect("", "sub_sectors_economics", "sub_sector", "sub_sector_cat");

        auxiliarSelect("", "carrecs_empresa", "carrec_empresa", "carrec_cat");

        // 07. Altres dades
        auxiliarSelect("", "autors_fitxa", "autor", "nom");

        // 04. dades politiques
        fetchCheckBoxs("partits", "partit_politic");

        fetchCheckBoxs("sindicats", "sindicat");

        // Función para manejar el envío del formulario
        async function enviarFormulario(event) {
            event.preventDefault(); // Prevenir que el formulario se envíe por defecto

            // Obtener el formulario
            const form = document.getElementById("personalNovaFitxaForm");

            // Crear un objeto para almacenar los datos del formulario
            const formData = {};
            new FormData(form).forEach((value, key) => {
                formData[key] = value; // Agregar cada campo al objeto formData
            });

            // Obtener todos los checkboxes seleccionados de la categoría
            const selectedCategories = [];
            document.querySelectorAll('input[name="categoria"]:checked').forEach((checkbox) => {
                selectedCategories.push(checkbox.value.replace('categoria', ''));
            });

            // Convertir el array de categorías seleccionadas al formato {1,2,3}
            formData['categoria'] = `{${selectedCategories.join(',')}}`;

            // Obtener todos los checkboxes seleccionados del partit
            const selectedPartits = [];
            document.querySelectorAll('input[name="partido"]:checked').forEach((checkbox) => {
                selectedPartits.push(checkbox.value.replace('partido', ''));
            });

            // Convertir el array de categorías seleccionadas al formato {1,2,3}
            formData['filiacio_politica'] = `{${selectedPartits.join(',')}}`;

            // Obtener todos los checkboxes seleccionados del sindicat
            const selectedSindicats = [];
            document.querySelectorAll('input[name="sindicat"]:checked').forEach((checkbox) => {
                selectedSindicats.push(checkbox.value.replace('sindicat', ''));
            });

            // Convertir el array de categorías seleccionadas al formato {1,2,3}
            formData['filiacio_sindical'] = `{${selectedSindicats.join(',')}}`;

            // Obtener el user_id de localStorage
            const userId = localStorage.getItem('user_id');
            if (userId) {
                formData['userId'] = userId;
            }

            // Convertir los datos del formulario a JSON
            const jsonData = JSON.stringify(formData);
            const devDirectory = `https://${window.location.hostname}`;
            let urlAjax = devDirectory + "/api/dades_personals/post";

            try {
                // Hacer la solicitud con fetch y await
                const response = await fetch(urlAjax, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json", // Indicar que se envía JSON
                    },
                    body: jsonData, // Enviar los datos en formato JSON
                });

                // Procesar la respuesta como texto o JSON
                const data = await response.json();

                // Verificar si la solicitud fue exitosa
                if (response.ok) {
                    // Verificar si el status es success
                    if (data.status === "success") {
                        // Cambiar el display del div con id 'OkMessage' a 'block'
                        const okMessageDiv = document.getElementById("okMessage");
                        const okTextDiv = document.getElementById("okText");

                        if (okMessageDiv && okTextDiv) {
                            okMessageDiv.style.display = "block";
                            okTextDiv.textContent = data.message || "Les dades s'han desat correctament!";
                        }
                    } else {
                        // Si el status no es success, manejar el error aquí
                        const errMessageDiv = document.getElementById("errMessage");
                        const errTextDiv = document.getElementById("errText");
                        if (errMessageDiv && errTextDiv) {
                            errMessageDiv.style.display = "block";
                            errTextDiv.innerHTML = data.errors.join('<br>') || "S'ha produit un error a la base de dades.";
                        }
                    }
                } else {
                    // Manejar errores de respuesta del servidor
                    const errMessageDiv = document.getElementById("errMessage");
                    const errTextDiv = document.getElementById("errText");
                    if (errMessageDiv && errTextDiv) {
                        errMessageDiv.style.display = "block";
                        errTextDiv.innerHTML = data.errors.join('<br>') || "S'ha produit un error a la base de dades.";
                    }
                }
            } catch (error) {
                // Manejar errores de red
                const errMessageDiv = document.getElementById("errMessage");
                const errTextDiv = document.getElementById("errText");
                if (errMessageDiv && errTextDiv) {
                    errMessageDiv.style.display = "block";
                    errTextDiv.innerHTML = data.errors.join('<br>') || "S'ha produit un error a la xarxa.";
                }
                console.error("Error:", error);
            }
        }
    </script>
</div>