<?php

$situacio_id = "";
$data_alliberament = "";
$lloc_mort_alliberament_id = "";
$preso_tipus_id = "";
$preso_nom = "";
$preso_data_sortida = "";
$preso_localitat_id = "";
$preso_num_matricula = "";
$deportacio_nom_camp = "";
$deportacio_data_entrada = "";
$deportacio_num_matricula = "";
$deportacio_nom_subcamp = "";
$deportacio_data_entrada_subcamp = "";
$deportacio_nom_matricula_subcamp = "";
$idPersona;
$nom = "";
$cognom1 = "";
$cognom2 = "";
$id = "";

// Verificar si la ID existe en la base de datos
$query = "SELECT 
d.id,
d.situacio,
d.data_alliberament,
d.lloc_mort_alliberament,
d.preso_tipus,
d.preso_nom,
d.preso_data_sortida,
d.preso_localitat,
d.preso_num_matricula,
d.deportacio_nom_camp,
d.deportacio_data_entrada,
d.deportacio_num_matricula,
d.deportacio_nom_subcamp,
d.deportacio_data_entrada_subcamp,
d.deportacio_nom_matricula_subcamp,
dp.nom,
dp.cognom1,
dp.cognom2
FROM db_deportats AS d
LEFT JOIN db_dades_personals AS dp ON d.idPersona = dp.id
WHERE d.idPersona = :idPersona";
$stmt = $conn->prepare($query);
$stmt->bindParam(':idPersona', $idPersona, PDO::PARAM_INT);
$stmt->execute();

$btnModificar = "";
if ($stmt->rowCount() > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Acceder a las variables de la consulta
        $situacio_id = $row['situacio'] ?? "";
        $data_alliberament = $row['data_alliberament'] ?? "";
        $lloc_mort_alliberament_id = $row['lloc_mort_alliberament'] ?? "";
        $preso_tipus_id = $row['preso_tipus'] ?? "";
        $preso_nom = $row['preso_nom'] ?? "";
        $preso_data_sortida = $row['preso_data_sortida'] ?? "";
        $preso_localitat_id = $row['preso_localitat'] ?? "";
        $preso_num_matricula = $row['preso_num_matricula'] ?? "";
        $deportacio_nom_camp = $row['deportacio_nom_camp'] ?? "";
        $deportacio_data_entrada = $row['deportacio_data_entrada'] ?? "";
        $deportacio_num_matricula = $row['deportacio_num_matricula'] ?? "";
        $deportacio_nom_subcamp = $row['deportacio_nom_subcamp'] ?? "";
        $deportacio_data_entrada_subcamp = $row['deportacio_data_entrada_subcamp'] ?? "";
        $deportacio_nom_matricula_subcamp = $row['deportacio_nom_matricula_subcamp'] ?? "";
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
    <form id="deportatForm">
        <div class="container">
            <div class="row">
                <h2>Tipus de repressió: Deportat</h2>
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
                    <label for="situacio" class="form-label negreta">Situació del deportat:</label>
                    <select class="form-select" name="situacio" id="situacio" value="">
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="data_alliberament" class="form-label negreta">Data alliberament:</label>
                    <input type="text" class="form-control" id="data_alliberament" name="data_alliberament" value="<?php echo $data_alliberament; ?>">
                </div>

                <div class="col-md-4">
                    <label for="lloc_mort_alliberament" class="form-label negreta">Municipi de mort o alliberament:</label>
                    <select class="form-select" name="lloc_mort_alliberament" id="lloc_mort_alliberament" value="">
                    </select>
                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/municipi/nou" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi1">Afegir municipi</a>
                        <button id="refreshButton1" class="btn btn-primary btn-sm">Actualitzar llistat Municipis</button>
                    </div>
                </div>

                <hr style="margin-top:25px">
                <h4>Empresonament:</h4>

                <div class="col-md-4">
                    <label for="preso_tipus" class="form-label negreta">Tipus de presó:</label>
                    <select class="form-select" name="preso_tipus" id="preso_tipus" value="">
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="preso_nom" class="form-label negreta">Nom de la presó:</label>
                    <input type="text" class="form-control" id="preso_nom" name="preso_nom" value="<?php echo $preso_nom; ?>">
                </div>

                <div class="col-md-4">
                    <label for="preso_data_sortida" class="form-label negreta">Data de la sortida de la presó:</label>
                    <input type="text" class="form-control" id="preso_data_sortida" name="preso_data_sortida" value="<?php echo $preso_data_sortida; ?>">
                </div>

                <div class="col-md-4">
                    <label for="preso_localitat" class="form-label negreta">Municipi de la presó:</label>
                    <select class="form-select" name="preso_localitat" id="preso_localitat" value="">
                    </select>
                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/municipi/nou" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi2">Afegir municipi</a>
                        <button id="refreshButton2" class="btn btn-primary btn-sm">Actualitzar llistat Municipis</button>
                    </div>
                </div>

                <div class="col-md-4">
                    <label for="preso_num_matricula" class="form-label negreta">Número de matrícula presó:</label>
                    <input type="text" class="form-control" id="preso_num_matricula" name="preso_num_matricula" value="<?php echo $preso_num_matricula; ?>">
                </div>

                <hr style="margin-top:25px">
                <h4>Deportació:</h4>


                <div class="col-md-4">
                    <label for="deportacio_nom_camp" class="form-label negreta">Nom cap de deportació</label>
                    <input type="text" class="form-control" id="deportacio_nom_camp" name="deportacio_nom_camp" value="<?php echo $deportacio_nom_camp; ?>">
                </div>

                <div class="col-md-4">
                    <label for="deportacio_data_entrada" class="form-label negreta">Data d'entrada</label>
                    <input type="text" class="form-control" id="deportacio_data_entrada" name="deportacio_data_entrada" value="<?php echo $deportacio_data_entrada; ?>">
                </div>


                <div class="col-md-4">
                    <label for="deportacio_num_matricula" class="form-label negreta">Número de matrícula</label>
                    <input type="text" class="form-control" id="deportacio_num_matricula" name="deportacio_num_matricula" value="<?php echo $deportacio_num_matricula; ?>">
                </div>

                <div class="col-md-4">
                    <label for="deportacio_nom_subcamp" class="form-label negreta">Nom del subcamp</label>
                    <input type="text" class="form-control" id="deportacio_nom_subcamp" name="deportacio_nom_subcamp" value="<?php echo $deportacio_nom_subcamp; ?>">
                </div>

                <div class="col-md-4">
                    <label for="deportacio_data_entrada_subcamp" class="form-label negreta">Data d'entrada al subcamp</label>
                    <input type="text" class="form-control" id="deportacio_data_entrada_subcamp" name="deportacio_data_entrada_subcamp" value="<?php echo $deportacio_data_entrada_subcamp; ?>">
                </div>

                <div class="col-md-4">
                    <label for="deportacio_nom_matricula_subcamp" class="form-label negreta">Número de matrícula del subcamp</label>
                    <input type="text" class="form-control" id="deportacio_nom_matricula_subcamp" name="deportacio_nom_matricula_subcamp" value="<?php echo $deportacio_nom_matricula_subcamp; ?>">
                </div>

                <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
                    <div class="col"></div>
                    <div class="col d-flex justify-content-end align-items-center">

                        <?php
                        if ($btnModificar === 1) {
                            echo '<a class="btn btn-primary" role="button" aria-disabled="true" id="btnModificarDadesDeportat" onclick="enviarFormulario(event)">Modificar dades</a>';
                        } else {
                            echo '<a class="btn btn-primary" role="button" aria-disabled="true" id="btnInserirDadesDeportat" onclick="enviarFormularioPost(event)">Inserir dades</a>';
                        }
                        ?>
                    </div>
                </div>
    </form>
</div>
</div>
</div>

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

    auxiliarSelect("<?php echo $situacio_id; ?>", "situacions_deportats", "situacio", "situacio_ca");
    auxiliarSelect("<?php echo $lloc_mort_alliberament_id; ?>", "municipis", "lloc_mort_alliberament", "ciutat");

    document.getElementById('refreshButton1').addEventListener('click', function(event) {
        event.preventDefault();
        auxiliarSelect("<?php echo $lloc_mort_alliberament_id; ?>", "municipis", "lloc_mort_alliberament", "ciutat");
    });



    auxiliarSelect("<?php echo $preso_tipus_id; ?>", "tipus_presons", "preso_tipus", "tipus_preso_ca");
    auxiliarSelect("<?php echo $preso_localitat_id; ?>", "municipis", "preso_localitat", "ciutat");

    document.getElementById('refreshButton1').addEventListener('click', function(event) {
        event.preventDefault();
        auxiliarSelect("<?php echo $preso_localitat_id; ?>", "municipis", "preso_localitat", "ciutat");
    });


    // Función para manejar el envío del formulario
    async function enviarFormulario(event) {
        event.preventDefault(); // Prevenir el envío por defecto

        // Obtener el formulario
        const form = document.getElementById("deportatForm");

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
        let urlAjax = devDirectory + "/api/deportats/put";

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

                const errMessageDiv = document.getElementById("errMessage");
                const errTextDiv = document.getElementById("errText");
                if (errMessageDiv && errTextDiv) {
                    errMessageDiv.style.display = "block";
                    errTextDiv.textContent = response.errors || "S'ha produit un error a la base de dades.";
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


    // Función para manejar el envío del formulario
    async function enviarFormularioPost(event) {
        event.preventDefault(); // Prevenir el envío por defecto

        // Obtener el formulario
        const form = document.getElementById("deportatForm");

        // Crear un objeto para almacenar los datos del formulario
        const formData = {};
        new FormData(form).forEach((value, key) => {
            formData[key] = value; // Agregar cada campo al objeto formData
        });

        // Convertir los datos del formulario a JSON
        const jsonData = JSON.stringify(formData);
        const devDirectory = `https://${window.location.hostname}`;
        let urlAjax = devDirectory + "/api/deportats/post";

        // Obtener el user_id de localStorage
        const userId = localStorage.getItem('user_id');
        if (userId) {
            formData['userId'] = userId;
        }

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