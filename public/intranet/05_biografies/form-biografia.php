<?php

// Obtener la URL completa
$url = $_SERVER['REQUEST_URI'];

// Dividir la URL en partes usando '/' como delimitador
$urlParts = explode('/', $url);

// Obtener la parte deseada (en este caso, la cuarta parte)
$categoriaId = $urlParts[3] ?? '';

require_once APP_ROOT . '/public/intranet/includes/header.php';

$modificaBtn = "";
$idPersona = "";
$idBiografia_old = "";
$biografiaCa_old = "";
$biografiaEs_old = "";

if ($categoriaId === "modifica-biografia") {
    $modificaBtn = 1;
    $idPersona = $routeParams[0];
} else {
    $modificaBtn = 2;
    $idPersona = $routeParams[0];
}

if ($modificaBtn === 1) {
    // Verificar si la ID existe en la base de datos
    $query = "SELECT 
    d.nom,
    d.cognom1,
    d.cognom2,
    b.biografiaCa,
    b.biografiaEs,
    b.biografiaEn,
    b.id AS idBiografia
    FROM db_dades_personals AS d
    LEFT JOIN db_biografies AS b ON d.id = b.idRepresaliat
    WHERE d.id = :id";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $idPersona, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Acceder a las variables de la consulta
            $idBiografia_old = $row['idBiografia'] ?? "";
            $nom = $row['nom'] ?? "";
            $cognom1 = $row['cognom1'] ?? "";
            $cognom2 = $row['cognom2'] ?? "";
            $biografiaCa_old = $row['biografiaCa'] ?? "";
            $biografiaEs_old = $row['biografiaEs'] ?? "";
            $biografiaEn_old = $row['biografiaEn'] ?? "";
        }
    }
} else {
    $query = "SELECT d.id,
    d.nom,
    d.cognom1,
    d.cognom2
    FROM db_dades_personals AS d
    WHERE d.id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $idPersona, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $nom = $row['nom'] ?? "";
            $cognom1 = $row['cognom1'] ?? "";
            $cognom2 = $row['cognom2'] ?? "";
            $idPersona = $idPersona;
        }
    }
}
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
    <form id="familiarForm">
        <div class="container">
            <div class="row">
                <?php if ($modificaBtn === 1) { ?>
                    <h2>Modificació biografia</h2>
                    <h4 id="fitxaNomCognoms">Fitxa represaliat: <a href="https://memoriaterrassa.cat/fitxa/<?php echo $idPersona; ?>" target="_blank"><?php echo $nom . " " . $cognom1 . " " . $cognom2; ?></a></h4>
                <?php } else { ?>
                    <h2>Inserció biografia</h2>
                    <h4 id="fitxaNomCognoms">Fitxa represaliat: <a href="https://memoriaterrassa.cat/fitxa/<?php echo $idPersona; ?>" target="_blank"><?php echo $nom . " " . $cognom1 . " " . $cognom2; ?></a></h4>

                <?php } ?>

                <div class="alert alert-success" role="alert" id="okMessage" style="display:none">
                    <h4 class="alert-heading"><strong>Modificació correcte!</strong></h4>
                    <div id="okText"></div>
                </div>

                <div class="alert alert-danger" role="alert" id="errMessage" style="display:none">
                    <h4 class="alert-heading"><strong>Error en les dades!</strong></h4>
                    <div id="errText"></div>
                </div>

                <input type="hidden" name="idRepresaliat" id="idRepresaliat" value="<?php echo $idPersona; ?>">
                <input type="hidden" name="id" id="id" value="<?php echo $idBiografia_old; ?>">

                <!-- Crear el editor de texto -->
                <div class="col-md-12">
                    <label for="tema" class="form-label negreta">Biografia (català):</label>
                    <!-- Campo oculto que almacena el valor de Trix -->
                    <input id="biografiaCa" type="hidden" name="biografiaCa" value="<?php echo htmlspecialchars($biografiaCa_old); ?>">

                    <!-- Editor Trix -->
                    <trix-editor input="biografiaCa"></trix-editor>
                </div>

                <!-- Crear el editor de texto -->
                <div class="col-md-12">
                    <label for="tema" class="form-label negreta">Biografia (castellà):</label>
                    <!-- Campo oculto que almacena el valor de Trix -->
                    <input id="biografiaEs" type="hidden" name="biografiaEs" value="<?php echo htmlspecialchars($biografiaEs_old); ?>">

                    <!-- Editor Trix -->
                    <trix-editor input="biografiaEs"></trix-editor>
                </div>

                <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
                    <div class="col">
                        <a class="btn btn-secondary" role="button" aria-disabled="true" onclick="goBack()">Tornar enrere</a>
                    </div>
                    <div class="col d-flex justify-content-end align-items-center">

                        <?php
                        if ($modificaBtn === 1) {
                            echo '<button class="btn btn-primary" id="btnModificarDades" type="submit">Modificar dades</button>';
                        } else {
                            echo '<button class="btn btn-primary" id="btnInserirDades" type="submit">Inserir dades</button>';
                        }
                        ?>
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
    }
</script>