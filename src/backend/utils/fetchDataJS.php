<?php

// funcions per comunicarse amb el backend, provisional, a l'espera d'implementar una solució més acurada
?>
<script>
    // Función para manejar el envío del formulario
    async function enviarFormulario(event) {
        event.preventDefault(); // Prevenir el envío por defecto

        const form = document.getElementById("mortCombatForm");

        const formData = {};
        new FormData(form).forEach((value, key) => {
            formData[key] = value.toString();
        });

        const jsonData = JSON.stringify(formData);
        const devDirectory = `https://${window.location.hostname}`;
        const urlAjax = `${devDirectory}/api/cost_huma_civils/put`;

        try {
            const response = await fetch(urlAjax, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                },
                body: jsonData,
            });

            const okMessageDiv = document.getElementById("okMessage");
            const okTextDiv = document.getElementById("okText");
            const errMessageDiv = document.getElementById("errMessage");
            const errTextDiv = document.getElementById("errText");

            const responseData = await response.json();

            if (response.ok && responseData.status === "success") {
                if (okMessageDiv && okTextDiv && errMessageDiv) {
                    okMessageDiv.style.display = "block";
                    okTextDiv.textContent = responseData.message || "Les dades s'han desat correctament!";
                    errMessageDiv.style.display = "none";

                    // Ocultar el mensaje después de 4 segundos
                    setTimeout(() => {
                        okMessageDiv.style.display = "none";
                    }, 4000);
                }
            } else {
                if (errMessageDiv && errTextDiv) {
                    errMessageDiv.style.display = "block";
                    errTextDiv.textContent = responseData.message || "S'ha produït un error a la base de dades.";

                    // Ocultar el mensaje después de 4 segundos
                    setTimeout(() => {
                        errMessageDiv.style.display = "none";
                    }, 4000);
                }
            }
        } catch (error) {
            console.error("Error:", error);
            const errMessageDiv = document.getElementById("errMessage");
            const errTextDiv = document.getElementById("errText");

            if (errMessageDiv && errTextDiv) {
                errMessageDiv.style.display = "block";
                errTextDiv.textContent = "Error de xarxa o resposta invàlida del servidor.";

                // Ocultar el mensaje después de 4 segundos
                setTimeout(() => {
                    errMessageDiv.style.display = "none";
                }, 4000);
            }
        }
    }

    async function enviarFormularioPost(event) {
        event.preventDefault(); // Prevenir el envío por defecto

        const form = document.getElementById("mortCombatForm");

        const formData = {};
        new FormData(form).forEach((value, key) => {
            formData[key] = value.toString();
        });

        const jsonData = JSON.stringify(formData);
        const devDirectory = `https://${window.location.hostname}`;
        const urlAjax = `${devDirectory}/api/cost_huma_civils/post`;

        try {
            const response = await fetch(urlAjax, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: jsonData,
            });

            const okMessageDiv = document.getElementById("okMessage");
            const okTextDiv = document.getElementById("okText");
            const errMessageDiv = document.getElementById("errMessage");
            const errTextDiv = document.getElementById("errText");

            const responseData = await response.json();

            if (response.ok && responseData.status === "success") {
                if (okMessageDiv && okTextDiv && errMessageDiv) {
                    okMessageDiv.style.display = "block";
                    okTextDiv.textContent = responseData.message || "Les dades s'han desat correctament!";
                    errMessageDiv.style.display = "none";

                    // Ocultar el mensaje después de 4 segundos
                    setTimeout(() => {
                        okMessageDiv.style.display = "none";
                    }, 4000);
                }
            } else {
                if (errMessageDiv && errTextDiv) {
                    errMessageDiv.style.display = "block";
                    errTextDiv.textContent = responseData.message || "S'ha produït un error a la base de dades.";

                    // Ocultar el mensaje después de 4 segundos
                    setTimeout(() => {
                        errMessageDiv.style.display = "none";
                    }, 4000);
                }
            }
        } catch (error) {
            console.error("Error:", error);
            const errMessageDiv = document.getElementById("errMessage");
            const errTextDiv = document.getElementById("errText");

            if (errMessageDiv && errTextDiv) {
                errMessageDiv.style.display = "block";
                errTextDiv.textContent = "Error de xarxa o resposta invàlida del servidor.";

                // Ocultar el mensaje después de 4 segundos
                setTimeout(() => {
                    errMessageDiv.style.display = "none";
                }, 4000);
            }
        }
    }
</script>