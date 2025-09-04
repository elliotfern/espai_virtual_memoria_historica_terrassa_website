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
        min-height: 500px;
        /* Ajusta la altura según tus necesidades */
        max-height: 600px;
        /* Opcional: establece una altura máxima */
        overflow-y: auto;
        background-color: #fff !important;
    }
</style>


<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="BiografiesForm">
        <div class="container">
            <div class="row">
                <div id="titolForm"></div>

                <div class="alert alert-success" role="alert" id="okMessage" style="display:none">
                    <div id="okText"></div>
                </div>

                <div class="alert alert-danger" role="alert" id="errMessage" style="display:none">
                    <div id="errText"></div>
                </div>

                <input type="hidden" name="idRepresaliat" id="idRepresaliat" value="">
                <input type="hidden" name="id" id="id" value="">

                <!-- Crear el editor de texto -->
                <div class="col-md-12">
                    <label for="tema" class="form-label negreta">Biografia (català):</label>
                    <!-- Campo oculto que almacena el valor de Trix -->
                    <input id="biografiaCa" type="hidden" name="biografiaCa" value="">

                    <!-- Editor Trix -->
                    <trix-editor input="biografiaCa"></trix-editor>
                </div>

                <!-- Crear el editor de texto -->
                <div class="col-md-12">
                    <label for="tema" class="form-label negreta">Biografia (castellà):</label>
                    <!-- Campo oculto que almacena el valor de Trix -->
                    <input id="biografiaEs" type="hidden" name="biografiaEs" value="">

                    <!-- Editor Trix -->
                    <trix-editor input="biografiaEs"></trix-editor>
                </div>

                <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
                    <div class="col">

                    </div>
                    <div class="col d-flex justify-content-end align-items-center">
                        <button class="btn btn-primary" id="btnBiografies" type="submit">Modificar dades</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

</div>

<script>
    /*
    https://memoriaterrassa.cat/api/dades_personals/get?type=fitxaRepresaliat&slug=amat-vilaplana-cerda
	https://memoriaterrassa.cat/api/dades_personals/get/nomRepresaliat?type=nomRepresaliat&id=1

    
    function goBack() {
        window.history.back();
    }

    document.getElementById("familiarForm").addEventListener("submit", function(event) {
        event.preventDefault(); // Prevenir el envío del formulario por defecto

        // Verificar cuál botón fue presionado
        const form = event.target;
        const postButton = document.getElementById("btnInserirDades");
        const putButton = document.getElementById("btnModificarDades");

        if (document.activeElement === postButton) {
            // Si el botón de POST fue presionado
            enviarFormularioPost(form);
        } else if (document.activeElement === putButton) {
            // Si el botón de PUT fue presionado
            enviarFormularioPut(form);
        }
    });

    // Función para manejar el envío del formulario
    async function enviarFormularioPost(event) {


        // Obtener el formulario
        const form = document.getElementById("familiarForm");

        // Crear un objeto para almacenar los datos del formulario
        const formData = {};
        new FormData(form).forEach((value, key) => {
            formData[key] = value; // Agregar cada campo al objeto formData
        });

        // Obtener el contenido del editor Trix
        const trixEditorCa = document.querySelector("trix-editor[input='biografiaCa']");
        if (trixEditorCa) {
            formData['biografiaCa'] = trixEditorCa.innerHTML;

        }

        // Obtener el contenido del editor Trix para la biografía en castellano
        const trixEditorEs = document.querySelector("trix-editor[input='biografiaEs']");
        if (trixEditorEs) {
            formData['biografiaEs'] = trixEditorEs.innerHTML;
        }

        // Obtener el user_id de localStorage
        const userId = localStorage.getItem('user_id');
        if (userId) {
            formData['userId'] = userId;
        }

        // Convertir los datos del formulario a JSON
        const jsonData = JSON.stringify(formData);
        const devDirectory = `https://${window.location.hostname}`;
        let urlAjax = devDirectory + "/api/biografia/post";
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


    // Función para manejar el envío del formulario
    async function enviarFormularioPut(event) {


        // Obtener el formulario
        const form = document.getElementById("familiarForm");

        // Crear un objeto para almacenar los datos del formulario
        const formData = {};
        new FormData(form).forEach((value, key) => {
            formData[key] = value; // Agregar cada campo al objeto formData
        });

        // Obtener el contenido del editor Trix
        const trixEditorCa = document.querySelector("trix-editor[input='biografiaCa']");
        if (trixEditorCa) {
            formData['biografiaCa'] = trixEditorCa.innerHTML;

        }

        // Obtener el contenido del editor Trix para la biografía en castellano
        const trixEditorEs = document.querySelector("trix-editor[input='biografiaEs']");
        if (trixEditorEs) {
            formData['biografiaEs'] = trixEditorEs.innerHTML;
        }

        // Obtener el user_id de localStorage
        const userId = localStorage.getItem('user_id');
        if (userId) {
            formData['userId'] = userId;
        }

        // Convertir los datos del formulario a JSON
        const jsonData = JSON.stringify(formData);
        const devDirectory = `https://${window.location.hostname}`;
        let urlAjax = devDirectory + "/api/biografia/put";
        try {
            // Hacer la solicitud con fetch y await
            const response = await fetch(urlAjax, {
                method: "PUT",
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
    }*/
</script>