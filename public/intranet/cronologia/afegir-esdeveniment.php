<?php
require_once APP_ROOT . '/public/intranet/includes/header.php';
?>



<!-- Incluir el CSS de Trix -->
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.css">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.js"></script>
<style>
    trix-toolbar [data-trix-button-group="file-tools"] {
        display: none;
    }

    trix-editor {
        min-height: 300px;
        /* Ajusta la altura según tus necesidades */
        max-height: 600px;
        /* Opcional: establece una altura máxima */
        overflow-y: auto;
        background-color: #fff !important;
    }
</style>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="familiarForm">
        <div class="container">
            <div class="row g-4">
                <h2>Cronologia: creació de nou esdeveniment històric</h2>

                <div class="alert alert-success" role="alert" id="okMessage" style="display:none">
                    <h4 class="alert-heading"><strong>Modificació correcte!</strong></h4>
                    <div id="okText"></div>
                </div>

                <div class="alert alert-danger" role="alert" id="errMessage" style="display:none">
                    <h4 class="alert-heading"><strong>Error en les dades!</strong></h4>
                    <div id="errText"></div>
                </div>

                <div class="col-md-3">
                    <label for="any" class="form-label negreta">Selecciona un any:</label>
                    <select class="form-select" id="any" name="any">
                        <!-- Generar opciones para los años de 1910 a 1989 -->
                        <script>
                            const select = document.getElementById('any');
                            for (let year = 1910; year <= 1989; year++) {
                                const option = document.createElement('option');
                                option.value = year;
                                option.textContent = year;
                                select.appendChild(option);
                            }
                        </script>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="monthSelect" class="form-label negreta">Selecciona un mes:</label>
                    <select class="form-select" id="mes" name="mes">
                        <option value="1">Gener</option>
                        <option value="2">Febrer</option>
                        <option value="3">Març</option>
                        <option value="4">Abril</option>
                        <option value="5">Maig</option>
                        <option value="6">Juny</option>
                        <option value="7">Juliol</option>
                        <option value="8">Agost</option>
                        <option value="9">Setembre</option>
                        <option value="10">Octubre</option>
                        <option value="11">Novembre</option>
                        <option value="12">Desembre</option>
                    </select>
                </div>


                <div class="col-md-3">
                    <label for="diaInici" class="form-label negreta">Dia inici:</label>
                    <input type="text" class="form-control" name="diaInici" id="diaInici" value="">
                </div>

                <div class="col-md-3">
                    <label for="diaFi" class="form-label negreta">Dia fi (opcional):</label>
                    <input type="text" class="form-control" name="diaFi" id="diaFi" value="">
                </div>

                <div class="col-md-3">
                    <label for="area" class="form-label negreta">Selecciona una àrea geogràfica:</label>
                    <select class="form-select" id="area" name="area">
                        <option value="1">Terrassa</option>
                        <option value="2">Catalunya</option>
                        <option value="3">Espanya</option>
                        <option value="4">Europa</option>
                        <option value="5">Món</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="tema" class="form-label negreta">Selecciona un tema:</label>
                    <select class="form-select" id="tema" name="tema">
                        <option value="1">Econòmic i laboral</option>
                        <option value="2">Polític i social</option>
                        <option value="3">Moviment obrer</option>
                    </select>
                </div>

                <!-- Crear el editor de texto -->
                <div class="col-md-12">
                    <label for="tema" class="form-label negreta">Esdeveniment (català):</label>
                    <input id="textCa" type="hidden" name="textCa">
                    <trix-editor></trix-editor>
                </div>


                <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
                    <div class="col">
                        <a class="btn btn-secondary" role="button" aria-disabled="true" onclick="goBack()">Tornar enrere</a>
                    </div>
                    <div class="col d-flex justify-content-end align-items-center">
                        <button class="btn btn-primary" id="btnInserirDadesFamiliar" type="submit">Inserir dades</button>

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

    document.getElementById("familiarForm").addEventListener("submit", enviarFormularioPost);

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

        // Obtener el contenido del editor Trix
        const trixEditor = document.querySelector("trix-editor");
        if (trixEditor) {
            formData['textCa'] = trixEditor.innerHTML;

        }

        // Obtener el user_id de localStorage
        const userId = localStorage.getItem('user_id');
        if (userId) {
            formData['userId'] = userId;
        }

        // Convertir los datos del formulario a JSON
        const jsonData = JSON.stringify(formData);
        const devDirectory = `https://${window.location.hostname}`;
        let urlAjax = devDirectory + "/api/cronologia/post";
        console.log("Formulario enviado:", formData);
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
            console.log(data);

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