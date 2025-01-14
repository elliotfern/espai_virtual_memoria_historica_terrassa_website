<?php
require_once APP_ROOT . '/public/intranet/includes/header.php';
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="familiarForm">
        <div class="container">
            <div class="row">
                <h2>Bibliografia: creació de nou arxiu</h2>

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
                    <label for="ciutat" class="form-label negreta">Ciutat arxiu:</label>
                    <select class="form-select" name="ciutat" id="ciutat" value="">
                    </select>
                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/municipi/nou" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi">Afegir municipi</a>
                        <button id="refreshButton" class="btn btn-primary btn-sm">Actualitzar llistat Municipis</button>
                    </div>
                </div>

                <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
                    <div class="col">
                        <a class="btn btn-secondary" role="button" aria-disabled="true" onclick="goBack()">Tornar enrere</a>
                    </div>
                    <div class="col d-flex justify-content-end align-items-center">
                        <a class="btn btn-primary" role="button" aria-disabled="true" id="btnInserirDadesFamiliar" onclick="enviarFormularioPost(event)">Inserir dades</a>
                    </div>
                </div>
    </form>
</div>
</div>
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

    // Función para manejar el envío del formulario
    async function enviarFormularioPost(event) {
        event.preventDefault(); // Prevenir el envío por defecto

        // Obtener el formulario
        const form = document.getElementById("familiarForm");

        // Crear un objeto para almacenar los datos del formulario
        const formData = {};
        new FormData(form).forEach((value, key) => {
            formData[key] = value; // Agregar cada campo al objeto formData
        });

        // Obtener el user_id de localStorage
        const userId = localStorage.getItem('user_id');
        if (userId) {
            formData['userId'] = userId;
        }

        // Convertir los datos del formulario a JSON
        const jsonData = JSON.stringify(formData);
        const devDirectory = `https://${window.location.hostname}`;
        let urlAjax = devDirectory + "/api/fonts_documentals/post/?type=arxiu";

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
            if (!response.ok) {
                const errMessageDiv = document.getElementById("errMessage");
                const errTextDiv = document.getElementById("errText");
                if (errMessageDiv && errTextDiv) {
                    errMessageDiv.style.display = "block";
                    errTextDiv.textContent = data.errors || "S'ha produit un error a la base de dades.";
                }
                throw new Error("Error al enviar el formulario.");
            }

            // Verificar si el status es success
            if (data.status === "success") {
                // Cambiar el display del div con id 'OkMessage' a 'block'
                const okMessageDiv = document.getElementById("okMessage");
                const okTextDiv = document.getElementById("okText");
                const errMessageDiv = document.getElementById("errMessage");

                if (okMessageDiv && okTextDiv && errMessageDiv) {
                    okMessageDiv.style.display = "block";
                    okTextDiv.textContent = "Les dades s'han desat correctament!";
                    errMessageDiv.style.display = "none";
                }

            } else {
                // Si el status no es success, puedes manejar el error aquí
                // Cambiar el display del div con id 'OkMessage' a 'block'
                const errMessageDiv = document.getElementById("errMessage");
                const errTextDiv = document.getElementById("errText");
                if (errMessageDiv && errTextDiv) {
                    errMessageDiv.style.display = "block";
                    errTextDiv.textContent = data.errors || "S'ha produit un error a la base de dades.";
                }
            }
        } catch (error) {
            console.log(data);
            // Manejar errores
            console.error("Error:", error);
        }
    }
</script>