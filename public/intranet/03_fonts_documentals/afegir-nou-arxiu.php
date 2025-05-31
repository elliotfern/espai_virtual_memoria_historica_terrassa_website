<?php
require_once APP_ROOT . '/public/intranet/includes/header.php';
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="arxiuForm">
        <div class="container">
            <div class="row g-5">
                <h2>Bibliografia: creació de nou arxiu/fonts documental</h2>

                <div class="alert alert-success" role="alert" id="okMessage" style="display:none">
                    <h4 class="alert-heading"><strong>Modificació correcte!</strong></h4>
                    <div id="okText"></div>
                </div>

                <div class="alert alert-danger" role="alert" id="errMessage" style="display:none">
                    <h4 class="alert-heading"><strong>Error en les dades!</strong></h4>
                    <div id="errText"></div>
                </div>

                <div class="col-md-4">
                    <label for="arxiu" class="form-label negreta">Nom Arxiu:</label>
                    <input type="text" class="form-control" name="arxiu" id="arxiu" value="">
                </div>

                <div class="col-md-4">
                    <label for="codi" class="form-label negreta">Codi arxiu:</label>
                    <input type="text" class="form-control" name="codi" id="codi" value="">
                </div>

                <div class="col-md-4">
                    <label for="descripcio" class="form-label negreta">Descripció arxiu/fonts:</label>
                    <input type="text" class="form-control" name="descripcio" id="descripcio" value="">
                </div>

                <div class="col-md-4">
                    <label for="web" class="form-label negreta">Web:</label>
                    <input type="text" class="form-control" name="web" id="web" value="">
                </div>

                <div class="col-md-4">
                    <label for="ciutat" class="form-label negreta">Ciutat arxiu:</label>
                    <select class="form-select" name="ciutat" id="ciutat" value="">
                    </select>

                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/municipi/nou" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi">Afegir municipi</a>
                        <button type="button" id="refreshButton" class="btn btn-primary btn-sm">Actualitzar llistat Municipis</button>
                    </div>
                </div>

                <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
                    <div class="col">
                        <a class="btn btn-secondary" role="button" aria-disabled="true" onclick="goBack()">Tornar enrere</a>
                    </div>
                    <div class="col d-flex justify-content-end align-items-center">
                        <button type="submit" class="btn btn-primary">Inserir dades</button>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>


<script>
    function goBack() {
        window.history.back();
    }

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

    auxiliarSelect("", "municipis", "ciutat", "ciutat");
    document.getElementById('refreshButton').addEventListener('click', function(event) {
        event.preventDefault();
        auxiliarSelect("", "municipis", "ciutat", "ciutat");
    });
</script>