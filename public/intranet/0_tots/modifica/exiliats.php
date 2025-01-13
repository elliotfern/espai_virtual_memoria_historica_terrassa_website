<?php

$data_exili = "";
$lloc_partida_id = "";
$lloc_pas_frontera_id = "";
$amb_qui_passa_frontera = "";
$primer_desti_exili_id = "";
$primer_desti_data = "";
$tipologia_primer_desti_id = "";
$dades_lloc_primer_desti = "";
$periple_recorregut = "";
$deportat = "";
$ultim_desti_exili_id = "";
$tipologia_ultim_desti_id = "";
$participacio_resistencia_id = "";
$dades_resistencia = "";
$activitat_politica_exili = "";
$activitat_sindical_exili = "";
$situacio_legal_espanya = "";
$idPersona;
$nom = "";
$cognom1 = "";
$cognom2 = "";
$id = "";

// Verificar si la ID existe en la base de datos
$query = "SELECT
e.id,
e.data_exili,
e.lloc_partida,
e.lloc_pas_frontera,
e.amb_qui_passa_frontera,
e.primer_desti_exili,
e.primer_desti_data,
e.tipologia_primer_desti,
e.dades_lloc_primer_desti,
e.periple_recorregut,
e.deportat,
e.ultim_desti_exili,
e.tipologia_ultim_desti,
e.participacio_resistencia,
e.dades_resistencia,
e.activitat_politica_exili,
e.activitat_sindical_exili,
e.situacio_legal_espanya,
d.nom,
d.cognom1,
d.cognom2
FROM db_exiliats AS e
LEFT JOIN db_dades_personals AS d ON e.idPersona = d.id
WHERE e.idPersona = :idPersona";
$stmt = $conn->prepare($query);
$stmt->bindParam(':idPersona', $idPersona, PDO::PARAM_INT);
$stmt->execute();

$btnModificar = "";
if ($stmt->rowCount() > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Acceder a las variables de la consulta
        $data_exili = $row['data_exili'] ?? "";
        $lloc_partida_id = $row['lloc_partida'] ?? "";
        $lloc_pas_frontera_id = $row['lloc_pas_frontera'] ?? "";
        $amb_qui_passa_frontera = $row['amb_qui_passa_frontera'] ?? "";
        $primer_desti_exili_id = $row['primer_desti_exili'] ?? "";
        $primer_desti_data = $row['primer_desti_data'] ?? "";
        $tipologia_primer_desti_id = $row['tipologia_primer_desti'] ?? "";
        $dades_lloc_primer_desti = $row['dades_lloc_primer_desti'] ?? "";
        $periple_recorregut = $row['periple_recorregut'] ?? "";
        $deportat_id = $row['deportat'] ?? "";
        $ultim_desti_exili_id = $row['ultim_desti_exili'] ?? "";
        $tipologia_ultim_desti_id = $row['tipologia_ultim_desti'] ?? "";
        $participacio_resistencia_id = $row['participacio_resistencia'] ?? "";
        $dades_resistencia = $row['dades_resistencia'] ?? "";
        $activitat_politica_exili = $row['activitat_politica_exili'] ?? "";
        $activitat_sindical_exili = $row['activitat_sindical_exili'] ?? "";
        $situacio_legal_espanya = $row['situacio_legal_espanya'] ?? "";
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
    <form id="exiliatForm">
        <div class="container">
            <div class="row">
                <h2>Tipus de repressió: Exiliat</h2>
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
                    <label for="data_exili" class="form-label negreta">Data exili:</label>
                    <input type="text" class="form-control" id="data_exili" name="data_exili" value="<?php echo $data_exili; ?>">
                </div>

                <div class="col-md-4">
                    <label for="lloc_partida" class="form-label negreta">Lloc partida exili:</label>
                    <select class="form-select" name="lloc_partida" id="lloc_partida" value="">
                    </select>

                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/municipi/nou" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi1">Afegir municipi</a>
                        <button id="refreshButton1" class="btn btn-primary btn-sm">Actualitzar llistat Municipis</button>
                    </div>
                </div>

                <div class="col-md-4">
                    <label for="lloc_pas_frontera" class="form-label negreta">Lloc pas de la frontera:</label>
                    <select class="form-select" name="lloc_pas_frontera" id="lloc_pas_frontera" value="">
                    </select>

                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/municipi/nou" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi1">Afegir municipi</a>
                        <button id="refreshButton2" class="btn btn-primary btn-sm">Actualitzar llistat Municipis</button>
                    </div>
                </div>

                <div class="col-md-4">
                    <label for="amb_qui_passa_frontera" class="form-label negreta">Amb qui pasa a l'exili:</label>
                    <input type="text" class="form-control" id="amb_qui_passa_frontera" name="amb_qui_passa_frontera" value="<?php echo $amb_qui_passa_frontera; ?>">
                </div>

                <div class="col-md-4">
                    <label for="primer_desti_exili" class="form-label negreta">Primer municipi de destí a l'exili:</label>
                    <select class="form-select" name="primer_desti_exili" id="primer_desti_exili" value="">
                    </select>

                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/municipi/nou" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi3">Afegir municipi</a>
                        <button id="refreshButton3" class="btn btn-primary btn-sm">Actualitzar llistat Municipis</button>
                    </div>
                </div>

                <div class="col-md-4">
                    <label for="primer_desti_data" class="form-label negreta">Data del primer destí de l'exili:</label>
                    <input type="text" class="form-control" id="primer_desti_data" name="primer_desti_data" value="<?php echo $primer_desti_data; ?>">
                </div>

                <div class="col-md-4">
                    <label for="tipologia_primer_desti" class="form-label negreta">Tipologia del primer destí a l'exili:</label>
                    <select class="form-select" name="tipologia_primer_desti" id="tipologia_primer_desti" value="">
                    </select>

                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/tipologia-espai/nou" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi77">Afegir tipologia espai</a>
                        <button id="refreshButtonTipologia22" class="btn btn-primary btn-sm">Actualitzar llistat espais</button>
                    </div>
                </div>

                <div class="col-md-12">
                    <label for="dades_lloc_primer_desti" class="form-label negreta">Dades del primer destí de l'exili:</label>
                    <textarea class="form-control" id="dades_lloc_primer_desti" name="dades_lloc_primer_desti" rows="3"><?php echo $dades_lloc_primer_desti; ?></textarea>
                </div>

                <div class="col-md-12">
                    <label for="periple_recorregut" class="form-label negreta">Periple del recorregut a l'exili:</label>
                    <textarea class="form-control" id="periple_recorregut" name="periple_recorregut" rows="3"><?php echo $periple_recorregut; ?></textarea>
                </div>

                <div class="col-md-4">
                    <label for="deportat" class="form-label negreta">Deportat:</label>
                    <select class="form-select" id="deportat" name="deportat">
                        <option selected disabled>Selecciona una opció:</option>
                        <option value="1">Si</option>
                        <option value="2">No</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="ultim_desti_exili" class="form-label negreta">Darrer municipi de destí a l'exili:</label>
                    <select class="form-select" name="ultim_desti_exili" id="ultim_desti_exili" value="">
                    </select>
                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/municipi/nou" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi3">Afegir municipi</a>
                        <button id="refreshButton4" class="btn btn-primary btn-sm">Actualitzar llistat Municipis</button>
                    </div>
                </div>

                <div class="col-md-4">
                    <label for="tipologia_ultim_desti" class="form-label negreta">Tipologia del darrer destí a l'exili:</label>
                    <select class="form-select" name="tipologia_ultim_desti" id="tipologia_ultim_desti" value="">
                    </select>

                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/tipologia-espai/nou" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi778">Afegir tipologia espai</a>
                        <button id="refreshButtonTipologia2" class="btn btn-primary btn-sm">Actualitzar llistat espais</button>
                    </div>

                </div>

                <div class="col-md-4">
                    <label for="participacio_resistencia" class="form-label negreta">Participació a la Resistència:</label>
                    <select class="form-select" id="participacio_resistencia" name="participacio_resistencia">
                        <option selected disabled>Selecciona una opció:</option>
                        <option value="1">Si</option>
                        <option value="2">No</option>
                    </select>
                </div>

                <div class="col-md-12">
                    <label for="dades_resistencia" class="form-label negreta">Dades de la Resistència:</label>
                    <textarea class="form-control" id="dades_resistencia" name="dades_resistencia" rows="3"><?php echo $dades_resistencia; ?></textarea>
                </div>

                <div class="col-md-12">
                    <label for="activitat_politica_exili" class="form-label negreta">Activitat política a l'exili:</label>
                    <textarea class="form-control" id="activitat_politica_exili" name="activitat_politica_exili" rows="3"><?php echo $activitat_politica_exili; ?></textarea>
                </div>

                <div class="col-md-12">
                    <label for="activitat_sindical_exili" class="form-label negreta">Activitat sindical a l'exili:</label>
                    <textarea class="form-control" id="activitat_sindical_exili" name="activitat_sindical_exili" rows="3"><?php echo $activitat_sindical_exili; ?></textarea>
                </div>

                <div class="col-md-4">
                    <label for="situacio_legal_espanya" class="form-label negreta">Situació legal a Espanya:</label>
                    <input type="text" class="form-control" id="situacio_legal_espanya" name="situacio_legal_espanya" value="<?php echo $situacio_legal_espanya; ?>">
                </div>

                <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
                    <div class="col"></div>
                    <div class="col d-flex justify-content-end align-items-center">

                        <?php
                        if ($btnModificar === 1) {
                            echo '<a class="btn btn-primary" role="button" aria-disabled="true" id="btnModificarDadesExili" onclick="enviarFormulario(event)">Modificar dades</a>';
                        } else {
                            echo '<a class="btn btn-primary" role="button" aria-disabled="true" id="btnInserirDadesExili" onclick="enviarFormularioPost(event)">Inserir dades</a>';
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

    auxiliarSelect("<?php echo $lloc_partida_id; ?>", "municipis", "lloc_partida", "ciutat");

    document.getElementById('refreshButton1').addEventListener('click', function(event) {
        event.preventDefault();
        auxiliarSelect("<?php echo $lloc_partida_id; ?>", "municipis", "lloc_partida", "ciutat");
    });


    auxiliarSelect("<?php echo $lloc_pas_frontera_id; ?>", "municipis", "lloc_pas_frontera", "ciutat");

    document.getElementById('refreshButton2').addEventListener('click', function(event) {
        event.preventDefault();
        auxiliarSelect("<?php echo $lloc_pas_frontera_id; ?>", "municipis", "lloc_pas_frontera", "ciutat");
    });

    auxiliarSelect("<?php echo $primer_desti_exili_id; ?>", "municipis", "primer_desti_exili", "ciutat");

    document.getElementById('refreshButton3').addEventListener('click', function(event) {
        event.preventDefault();
        auxiliarSelect("<?php echo $primer_desti_exili_id; ?>", "municipis", "primer_desti_exili", "ciutat");
    });

    auxiliarSelect("<?php echo $tipologia_primer_desti_id; ?>", "tipologia_espais", "tipologia_primer_desti", "tipologia_espai_ca");

    document.getElementById('refreshButtonTipologia22').addEventListener('click', function(event) {
        event.preventDefault();
        auxiliarSelect("<?php echo $tipologia_primer_desti_id; ?>", "tipologia_espais", "tipologia_primer_desti", "tipologia_espai_ca");
    });

    auxiliarSelect("<?php echo $ultim_desti_exili_id; ?>", "municipis", "ultim_desti_exili", "ciutat");

    document.getElementById('refreshButton4').addEventListener('click', function(event) {
        event.preventDefault();
        auxiliarSelect("<?php echo $ultim_desti_exili_id; ?>", "municipis", "ultim_desti_exili", "ciutat");
    });

    auxiliarSelect("<?php echo $tipologia_ultim_desti_id; ?>", "tipologia_espais", "tipologia_ultim_desti", "tipologia_espai_ca");

    document.getElementById('refreshButtonTipologia2').addEventListener('click', function(event) {
        event.preventDefault();
        auxiliarSelect("<?php echo $tipologia_ultim_desti_id; ?>", "tipologia_espais", "tipologia_ultim_desti", "tipologia_espai_ca");
    });

    // Función para manejar el envío del formulario
    async function enviarFormulario(event) {
        event.preventDefault(); // Prevenir el envío por defecto

        // Obtener el formulario
        const form = document.getElementById("exiliatForm");

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
        let urlAjax = devDirectory + "/api/exiliats/put";

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
        const form = document.getElementById("exiliatForm");

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
        let urlAjax = devDirectory + "/api/exiliats/post";

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