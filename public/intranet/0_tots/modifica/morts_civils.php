<?php

$cirscumstancies_mort_id = "";
$data_trobada_cadaver = "";
$lloc_trobada_cadaver_id = "";
$data_detencio = "";
$lloc_detencio_id = "";
$data_bombardeig = "";
$municipi_bombardeig_id = "";
$lloc_bombardeig_id = "";
$responsable_bombardeig_id = "";
$idPersona;
$nom = "";
$cognom1 = "";
$cognom2 = "";
$id = "";

// Verificar si la ID existe en la base de datos
$query = "SELECT 
c.id,
c.cirscumstancies_mort,
c.data_trobada_cadaver,
c.lloc_trobada_cadaver,
c.data_detencio,
c.lloc_detencio,
c.data_bombardeig,
c.municipi_bombardeig,
c.lloc_bombardeig,
c.responsable_bombardeig,
d.nom,
d.cognom1,
d.cognom2
FROM db_cost_huma_morts_civils AS c
LEFT JOIN db_dades_personals AS d ON c.idPersona = d.id
WHERE c.idPersona = :idPersona";
$stmt = $conn->prepare($query);
$stmt->bindParam(':idPersona', $idPersona, PDO::PARAM_INT);
$stmt->execute();

$btnModificar = "";
if ($stmt->rowCount() > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Acceder a las variables de la consulta
        $cirscumstancies_mort_id = $row['cirscumstancies_mort'] ?? "";
        $data_trobada_cadaver = $row['data_trobada_cadaver'] ?? "";
        $lloc_trobada_cadaver_id = $row['lloc_trobada_cadaver'] ?? "";
        $data_detencio = $row['data_detencio'] ?? "";
        $lloc_detencio_id = $row['lloc_detencio'] ?? "";
        $data_bombardeig = $row['data_bombardeig'] ?? "";
        $municipi_bombardeig_id = $row['municipi_bombardeig'] ?? "";
        $lloc_bombardeig_id = $row['lloc_bombardeig'] ?? "";
        $responsable_bombardeig_id = $row['responsable_bombardeig'] ?? "";
        $nom = $row['nom'] ?? "";
        $cognom1 = $row['cognom1'] ?? "";
        $cognom2 = $row['cognom2'] ?? "";
        $id = $row['id'] ?? "";

        // Crear el botón o usar los datos (TIPO PUT)
        $btnModificar = 1;
    }
} else {
    // La ID no existe, realizamos un POST (INSERT)
    $btnModificar = 2;

    $query = "SELECT 
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
        }
    }
}

?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="mortCombatForm">
        <div class="container">
            <div class="row">
                <h2>Tipus de repressió: Morts civils durant la Guerra</h2>
                <h4 id="fitxaNomCognoms">Fitxa: <a href="https://memoriaterrassa.cat/fitxa/<?php echo $idPersona; ?>" target="_blank"><?php echo $nom . " " . $cognom1 . " " . $cognom2; ?></a></h4>

                <div class="alert alert-success" role="alert" id="okMessage" style="display:none">
                    <h4 class="alert-heading"><strong>Modificació correcte!</strong></h4>
                    <div id="okText"></div>
                </div>

                <div class="alert alert-danger" role="alert" id="errMessage" style="display:none">
                    <h4 class="alert-heading"><strong>Error en les dades!</strong></h4>
                    <div id="errText"></div>
                </div>

                <input type="hidden" name="idPersona" id="idPersona" value="<?php echo $idPersona; ?>">
                <input type="hidden" name="id" id="id" value="<?php echo $idPersona; ?>">

                <div class="col-md-4">
                    <label for="cirscumstancies_mort" class="form-label negreta">Circumstàncies de la mort:</label>
                    <select class="form-select" name="cirscumstancies_mort" id="cirscumstancies_mort" value="">
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="data_trobada_cadaver" class="form-label negreta">Data trobada del càdaver:</label>
                    <input type="text" class="form-control" id="data_trobada_cadaver" name="data_trobada_cadaver" value="<?php echo $data_trobada_cadaver; ?>">
                </div>

                <div class="col-md-4">
                    <label for="lloc_trobada_cadaver" class="form-label negreta">Lloc de trobada del cadàver:</label>
                    <select class="form-select" name="lloc_trobada_cadaver" id="lloc_trobada_cadaver" value="">
                    </select>

                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/municipi/nou" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi1">Afegir municipi</a>
                        <button id="refreshButtonMunicipi1" class="btn btn-primary btn-sm">Actualitzar llistat Municipis</button>
                    </div>
                </div>

                <div class="col-md-4">
                    <label for="data_detencio" class="form-label negreta">Data de la detenció:</label>
                    <input type="text" class="form-control" id="data_detencio" name="data_detencio" value="<?php echo $data_detencio; ?>">
                </div>

                <div class="col-md-4">
                    <label for="lloc_detencio" class="form-label negreta">Lloc de la detenció:</label>
                    <select class="form-select" name="lloc_detencio" id="lloc_detencio" value="">
                    </select>

                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/municipi/nou" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi2">Afegir municipi</a>
                        <button id="refreshButtonMunicipi2" class="btn btn-primary btn-sm">Actualitzar llistat Municipis</button>
                    </div>
                </div>

                <div class="col-md-4">
                    <label for="data_bombardeig" class="form-label negreta">Data del bombardeig:</label>
                    <input type="text" class="form-control" id="data_bombardeig" name="data_bombardeig" value="<?php echo $data_bombardeig; ?>">
                </div>

                <div class="col-md-4">
                    <label for="municipi_bombardeig" class="form-label negreta">Municipi del bombardeig:</label>
                    <select class="form-select" name="municipi_bombardeig" id="municipi_bombardeig" value="">
                    </select>

                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/municipi/nou" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi3">Afegir municipi</a>
                        <button id="refreshButtonMunicipi3" class="btn btn-primary btn-sm">Actualitzar llistat Municipis</button>
                    </div>
                </div>

                <div class="col-md-4">
                    <label for="lloc_bombardeig" class="form-label negreta">Lloc del bombardeig:</label>
                    <select class="form-select" name="lloc_bombardeig" id="lloc_bombardeig" value="">
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="responsable_bombardeig" class="form-label negreta">Responsable del bombardeig:</label>
                    <select class="form-select" id="responsable_bombardeig" name="responsable_bombardeig">
                        <option selected disabled>Selecciona una opció:</option>
                        <option value="1">Aviació feixista italiana</option>
                        <option value="2">Aviació nazista alemanya</option>
                        <option value="3">Aviació franquista</option>
                    </select>
                </div>


                <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
                    <div class="col"></div>

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
    valorBombargeig = "<?php echo $responsable_bombardeig_id; ?>";

    // Selecciona el element <select> del DOM
    const selectElement = document.getElementById("responsable_bombardeig");

    // Asigna el valor del select segons fitxa[0].sexe
    if (valorBombargeig) {
        selectElement.value = valorBombargeig; // Canvia el valor seleccionat automàticament
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


    auxiliarSelect("<?php echo $cirscumstancies_mort_id ?>", "causa_defuncio", "cirscumstancies_mort", "causa_defuncio_ca");

    auxiliarSelect("<?php echo $lloc_trobada_cadaver_id; ?>", "municipis", "lloc_trobada_cadaver", "ciutat");

    document.getElementById('refreshButtonMunicipi1').addEventListener('click', function(event) {
        event.preventDefault();
        auxiliarSelect("<?php echo $lloc_trobada_cadaver_id; ?>", "municipis", "lloc_trobada_cadaver", "ciutat");
    });

    auxiliarSelect("<?php echo $lloc_detencio_id; ?>", "municipis", "lloc_detencio", "ciutat");

    document.getElementById('refreshButtonMunicipi2').addEventListener('click', function(event) {
        event.preventDefault();
        auxiliarSelect("<?php echo $lloc_detencio_id; ?>", "municipis", "lloc_detencio", "ciutat");
    });

    auxiliarSelect("<?php echo $municipi_bombardeig_id; ?>", "municipis", "municipi_bombardeig", "ciutat");

    document.getElementById('refreshButtonMunicipi3').addEventListener('click', function(event) {
        event.preventDefault();
        auxiliarSelect("<?php echo $municipi_bombardeig_id; ?>", "municipis", "municipi_bombardeig", "ciutat");
    });

    auxiliarSelect("<?php echo $lloc_bombardeig_id; ?>", "llocs_bombardeig", "lloc_bombardeig", "lloc_bombardeig_ca");

    // Función para manejar el envío del formulario
    async function enviarFormulario(event) {
        event.preventDefault(); // Prevenir el envío por defecto

        // Obtener el formulario
        const form = document.getElementById("mortCombatForm");

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
        let urlAjax = devDirectory + "/api/cost_huma_civils/put";

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

    // Función para manejar el envío del formulario
    async function enviarFormularioPost(event) {
        event.preventDefault(); // Prevenir el envío por defecto

        // Obtener el formulario
        const form = document.getElementById("mortCombatForm");

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
        let urlAjax = devDirectory + "/api/cost_huma_civils/post";

        try {
            // Hacer la solicitud con fetch y await
            const response = await fetch(urlAjax, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json", // Indicar que se envía JSON
                },
                body: jsonData, // Enviar los datos en formato JSON
            });

            // Verificar si la solicitud fue exitosa
            if (!response.ok) {

                const errMessageDiv = document.getElementById("errMessage");
                const errTextDiv = document.getElementById("errText");
                if (errMessageDiv && errTextDiv) {
                    errMessageDiv.style.display = "block";
                    errTextDiv.textContent = data.message || "S'ha produit un error a la base de dades.";
                }
                throw new Error("Error al enviar el formulario.");
            }

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
                    errTextDiv.textContent = data.message || "S'ha produit un error a la base de dades.";
                }
            }
        } catch (error) {
            // Manejar errores
            console.error("Error:", error);
        }
    }
</script>