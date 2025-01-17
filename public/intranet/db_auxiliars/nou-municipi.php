<?php
require_once APP_ROOT . '/public/intranet/includes/header.php';

// Obtener la URL completa
$url = $_SERVER['REQUEST_URI'];

// Dividir la URL en partes usando '/' como delimitador
$urlParts = explode('/', $url);

// Obtener la parte deseada (en este caso, la cuarta parte)
$categoriaId = $urlParts[3] ?? '';

$id_old = "";
$ciutat_old = "";
$comarca_old = "";
$provincia_old = "";
$comunitat_old = "";
$estat_old = "";

if ($categoriaId === "modifica") {
    $modificaBtn = 1;
    $btnModificar = 1;
    $idMunicipi = $routeParams[0];

    // Verificar si la ID existe en la base de datos
    $query = "SELECT 
    m.id,
    m.ciutat,
    m.comarca,
    m.provincia,
    m.comunitat,
    m.estat
    FROM aux_dades_municipis AS m
    WHERE m.id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $idMunicipi, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Acceder a las variables de la consulta
            $ciutat_old = $row['ciutat'] ?? "";
            $comarca_old = $row['comarca'] ?? "";
            $provincia_old = $row['provincia'] ?? "";
            $comunitat_old = $row['comunitat'] ?? "";
            $estat_old = $row['estat'] ?? "";
            $id_old = $row['id'] ?? "";
        }
    }
} else {
    $modificaBtn = 2;
    $btnModificar = 2;
    // Pàgina inserció dades
}
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="municipiForm">
        <div class="container">
            <div class="row">
                <?php if ($modificaBtn === 1) { ?>
                    <h2>Modificació dades municipi</h2>
                    <h4 id="fitxaMunicipi">Municipi: <?php echo $ciutat_old; ?></h4>
                <?php } else { ?>
                    <h2>Inserció dades nou municipi</h2>
                <?php } ?>

                <div class="alert alert-success" role="alert" id="okMessage" style="display:none">
                    <h4 class="alert-heading"><strong>Modificació correcte!</strong></h4>
                    <div id="okText"></div>
                </div>

                <div class="alert alert-danger" role="alert" id="errMessage" style="display:none">
                    <h4 class="alert-heading"><strong>Error en les dades!</strong></h4>
                    <div id="errText"></div>
                </div>

                <input type="hidden" id="id" name="id" value="<?php echo $id_old; ?>">

                <div class="col-md-4">
                    <label for="ciutat" class="form-label negreta">Nom municipi:</label>
                    <input type="text" class="form-control" id="ciutat" name="ciutat" value="<?php echo $ciutat_old; ?>">
                </div>

                <div class="col-md-4">
                    <label for="comarca" class="form-label negreta">Comarca:</label>
                    <select class="form-select" id="comarca" value="" name="comarca">
                    </select>
                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/comarca/nou" target="_blank" class="btn btn-secondary btn-sm" id="afegirComarca">Afegir comarca</a>
                        <button id="refreshButtonComarca" class="btn btn-primary btn-sm">Actualitzar llistat comarques</button>
                    </div>
                </div>

                <div class="col-md-4">
                    <label for="provincia" class="form-label negreta">Provincia/Departament:</label>
                    <select class="form-select" id="provincia" value="" name="provincia">
                    </select>
                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/provincia/nou" target="_blank" class="btn btn-secondary btn-sm" id="afegirProvincia">Afegir provincia</a>
                        <button id="refreshButtonProvincia" class="btn btn-primary btn-sm">Actualitzar llistat provincies</button>
                    </div>
                </div>

                <div class="col-md-4">
                    <label for="comunitat" class="form-label negreta">Comunitat autònoma / Regió:</label>
                    <select class="form-select" id="comunitat" value="" name="comunitat">
                    </select>
                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/comunitat/nou" target="_blank" class="btn btn-secondary btn-sm" id="afegirComunitat">Afegir comunitat</a>
                        <button id="refreshButtonComunitat" class="btn btn-primary btn-sm">Actualitzar llistat comunitats</button>
                    </div>
                </div>

                <div class="col-md-4">
                    <label for="estat" class="form-label negreta">Estat:</label>
                    <select class="form-select" id="estat" value="" name="estat">
                    </select>
                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/estat/nou" target="_blank" class="btn btn-secondary btn-sm" id="afegirEstat">Afegir estat</a>
                        <button id="refreshButtonEstat" class="btn btn-primary btn-sm">Actualitzar llistat estats</button>
                    </div>
                </div>

                <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
                    <div class="col">
                        <a class="btn btn-secondary" role="button" aria-disabled="true" onclick="goBack()">Tornar enrere</a>
                    </div>

                    <div class="col d-flex justify-content-end align-items-center">

                        <?php
                        if ($btnModificar === 1) {
                            echo '<a class="btn btn-primary" role="button" aria-disabled="true" id="btnModificarDadesCombat" onclick="enviarFormulario(event)">Modificar dades</a>';
                        } else {
                            echo '<a class="btn btn-primary" role="button" aria-disabled="true" id="btnInserirDadesCombat" onclick="enviarFormularioPost(event)">Inserir dades</a>';
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

    auxiliarSelect("<?php echo $comarca_old; ?>", "comarques", "comarca", "comarca");
    document.getElementById('refreshButtonComarca').addEventListener('click', function(event) {
        event.preventDefault();
        auxiliarSelect("<?php echo $comarca_old; ?>", "comarques", "comarca", "comarca");
    });

    auxiliarSelect("<?php echo $provincia_old; ?>", "provincies", "provincia", "provincia");
    document.getElementById('refreshButtonProvincia').addEventListener('click', function(event) {
        event.preventDefault();
        auxiliarSelect("<?php echo $provincia_old; ?>", "provincies", "provincia", "provincia");
    });

    auxiliarSelect("<?php echo $comunitat_old; ?>", "comunitats", "comunitat", "comunitat");
    document.getElementById('refreshButtonComunitat').addEventListener('click', function(event) {
        event.preventDefault();
        auxiliarSelect("<?php echo $comunitat_old; ?>", "comunitats", "comunitat", "comunitat");
    });

    auxiliarSelect("<?php echo $estat_old; ?>", "estats", "estat", "estat");
    document.getElementById('refreshButtonEstat').addEventListener('click', function(event) {
        event.preventDefault();
        auxiliarSelect("<?php echo $estat_old; ?>", "estats", "estat", "estat");
    });


    // Función para manejar el envío del formulario
    async function enviarFormulario(event) {
        event.preventDefault(); // Prevenir el envío por defecto

        // Obtener el formulario
        const form = document.getElementById("municipiForm");

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
        let urlAjax = devDirectory + "/api/auxiliars/put/?type=municipi";

        try {
            // Hacer la solicitud con fetch y await
            const response = await fetch(urlAjax, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json", // Indicar que se envía JSON
                },
                body: jsonData, // Enviar los datos en formato JSON
            });

            // Verificar si la solicitud fue exitosa
            if (!response.ok) {
                throw new Error("Error al enviar el formulario.");
            }

            // Procesar la respuesta como texto o JSON
            const data = await response.json();

            // Verificar si el status es success
            if (data.status === "success") {
                // Cambiar el display del div con id 'OkMessage' a 'block'
                const okMessageDiv = document.getElementById("okMessage");
                const okTextDiv = document.getElementById("okText");

                if (okMessageDiv && okTextDiv) {
                    okMessageDiv.style.display = "block";
                    okTextDiv.textContent = data.message || "Les dades s'han actualitzat correctament!";
                }

            } else {
                // Si el status no es success, puedes manejar el error aquí
                // Cambiar el display del div con id 'OkMessage' a 'block'
                const errMessageDiv = document.getElementById("errMessage");
                const errTextDiv = document.getElementById("errText");
                if (errMessageDiv && errTextDiv) {
                    errMessageDiv.style.display = "block";
                    errTextDiv.textContent = data.message || "S'ha produit un error a la base de dades.";
                }
            }
        } catch (error) {
            // Manejar errores
            console.error("Error:", error);
        }
    }

    // Asignar la función al botón del formulario
    //document.getElementById("btnModificarDadesCombat").addEventListener("click", enviarFormulario);

    // Función para manejar el envío del formulario
    async function enviarFormularioPost(event) {
        event.preventDefault(); // Prevenir el envío por defecto

        // Obtener el formulario
        const form = document.getElementById("municipiForm");

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
        let urlAjax = devDirectory + "/api/auxiliars/post/?type=municipi";

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

            // Verificar si el status es success
            if (data.status === "success") {
                // Cambiar el display del div con id 'OkMessage' a 'block'
                const okMessageDiv = document.getElementById("okMessage");
                const okTextDiv = document.getElementById("okText");
                const errMessageDiv = document.getElementById("errMessage");

                if (okMessageDiv && okTextDiv && errMessageDiv) {
                    okMessageDiv.style.display = "block";
                    okTextDiv.textContent = data.message || "Les dades s'han desat correctament!";
                    errMessageDiv.style.display = "none";
                }

            } else {
                // Si el status no es success, puedes manejar el error aquí
                // Cambiar el display del div con id 'OkMessage' a 'block'
                const errMessageDiv = document.getElementById("errMessage");
                const errTextDiv = document.getElementById("errText");
                if (errMessageDiv && errTextDiv) {
                    errMessageDiv.style.display = "block";
                    errTextDiv.innerHTML = data.message || "S'ha produit un error a la base de dades.";
                }
            }
        } catch (error) {
            // Manejar errores
            console.error("Error:", error);
        }
    }

    // Asignar la función al botón del formulario
    // document.getElementById("btnInserirDadesCombat").addEventListener("click", enviarFormularioPost);
</script>