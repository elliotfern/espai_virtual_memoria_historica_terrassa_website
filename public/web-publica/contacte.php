<?php
// Obtener traducciones generales
$translate = $translations['general'] ?? [];
$translate2 = $translations['cerca-avan'] ?? [];
?>

<!-- Sección con container-fluid -->
<div class="container-fluid background-image-cap">

    <div class="container px-4">
        <span class="negreta gran italic-text cap">Contacta'ns</span>

    </div>
</div>


<div class="container d-flex justify-content-center" style="margin-top: 50px; margin-bottom: 50px;">
    <div class="w-100" style="max-width: 60%;">

        <div class="alert alert-info" role="alert">
            Si vols contactar amb nosaltres, omple aquest formulari amb les teves dades i el teu missatge. Ens posarem en contacte amb tu el més aviat possible.
        </div>

        <div class="alert alert-success" role="alert" id="okMessage" style="display:none">
            <h4 class="alert-heading"><strong>Formulari de contacte enviat correctament!</strong></h4>
            <div id="okText"></div>
        </div>

        <div class="alert alert-danger" role="alert" id="errMessage" style="display:none">
            <h4 class="alert-heading"><strong>Error en les dades!</strong></h4>
            <div id="errText"></div>
        </div>

        <form action="" id="form">

            <input type="text" name="extra_field" id="extra_field" style="display:none" autocomplete="off" tabindex="-1" />
            <input type="hidden" name="form_timestamp" id="form_timestamp" value="">

            <div class="mb-3">
                <label for="nomCognoms" class="form-label negreta">Nom i Cognoms</label>
                <input type="text" class="form-control" id="nomCognoms" name="nomCognoms" required>
                <div class="avis-form">
                    * Camp obligatori
                </div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label negreta">Correu electrònic</label>
                <input type="email" class="form-control" id="email" name="email" required>
                <div class="avis-form">
                    * Camp obligatori
                </div>
            </div>

            <div class="mb-3">
                <label for="telefon" class="form-label negreta">Telèfon</label>
                <input type="text" class="form-control" id="telefon" name="telefon">
            </div>

            <div class="mb-3">
                <label for="missatge" class="form-label negreta">Missatge</label>
                <textarea class="form-control" id="missatge" name="missatge" rows="8" required></textarea>
                <div class="avis-form">
                    * Camp obligatori
                </div>
            </div>

            <input type="hidden" name="form_ip" value="">
            <input type="hidden" name="form_user_agent" value="">

            <button type="submit" class="btn btn-primary" onclick="enviarFormularioPost(event)">Envia</button>
        </form>

    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const now = Math.floor(Date.now() / 1000); // en segundos
        document.getElementById("form_timestamp").value = now;
    });

    // Función para manejar el envío del formulario
    async function enviarFormularioPost(event) {
        event.preventDefault(); // Prevenir el envío por defecto

        document.getElementById("okMessage").style.display = "none";
        document.getElementById("errMessage").style.display = "none";

        // Obtener el formulario
        const form = document.getElementById("form");

        // Crear un objeto para almacenar los datos del formulario
        const formData = {};
        new FormData(form).forEach((value, key) => {
            formData[key] = value; // Agregar cada campo al objeto formData
        });

        // Convertir los datos del formulario a JSON
        const jsonData = JSON.stringify(formData);
        const devDirectory = `https://${window.location.hostname}`;
        let urlAjax = devDirectory + "/api/form_contacte/post";

        // Hacer la solicitud con fetch y await
        const response = await fetch(urlAjax, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: jsonData,
        });

        const data = await response.json(); // 👈 Importante: parsear antes de usar

        if (!response.ok) {
            const errMessageDiv = document.getElementById("errMessage");
            const errTextDiv = document.getElementById("errText");

            if (errMessageDiv && errTextDiv) {
                errMessageDiv.style.display = "block";

                // Mostrar errores múltiples si vienen en array
                if (Array.isArray(data.errors)) {
                    errTextDiv.innerHTML = `<ul>${data.errors.map(e => `<li>${e}</li>`).join("")}</ul>`;
                } else {
                    errTextDiv.textContent = data.message || "S'ha produït un error a la base de dades.";
                }
            }

            return; // Salir de la función después de mostrar el error
        }

        // Si la respuesta es exitosa y status es success
        if (data.status === "success") {
            const okMessageDiv = document.getElementById("okMessage");
            const okTextDiv = document.getElementById("okText");
            const errMessageDiv = document.getElementById("errMessage");

            if (okMessageDiv && okTextDiv && errMessageDiv) {
                okMessageDiv.style.display = "block";
                okTextDiv.textContent = data.message || "Formulari enviat correctament.";
                errMessageDiv.style.display = "none";
            }

            form.reset(); // Opcional: limpiar el formulario
        }

    }
</script>