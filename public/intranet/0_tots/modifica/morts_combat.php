<?php

$condicio_id = "";
$bandol_id = "";
$any_lleva = "";
$unitat_inicial = "";
$cos_id = "";
$unitat_final = "";
$graduacio_final = "";
$periple_militar = "";
$circumstancia_mort_id = "";
$desaparegut_data = "";
$desaparegut_lloc_id = "";
$desaparegut_data_aparicio = "";
$desaparegut_lloc_aparicio_id = "";
$idPersona;
$nom = "";
$cognom1 = "";
$cognom2 = "";
$id = "";

// Verificar si la ID existe en la base de datos
$query = "SELECT 
f.id, 
f.condicio,
f.bandol,
f.any_lleva,
f.unitat_inicial,
f.cos,
f.unitat_final ,
f.graduacio_final,
f.periple_militar,
f.circumstancia_mort,
f.desaparegut_data,
f.desaparegut_lloc,
f.desaparegut_data_aparicio,
f.desaparegut_lloc_aparicio,
d.nom,
d.cognom1,
d.cognom2
FROM db_cost_huma_morts_front AS f
LEFT JOIN db_dades_personals AS d ON f.idPersona = d.id
WHERE f.idPersona = :idPersona";
$stmt = $conn->prepare($query);
$stmt->bindParam(':idPersona', $idPersona, PDO::PARAM_INT);
$stmt->execute();

$btnModificar = "";
if ($stmt->rowCount() > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Acceder a las variables de la consulta
        $condicio_id = $row['condicio'] ?? "";
        $bandol_id = $row['bandol'] ?? "";
        $any_lleva = $row['any_lleva'] ?? "";
        $unitat_inicial = $row['unitat_inicial'] ?? "";
        $cos_id = $row['cos'] ?? "";
        $unitat_final = $row['unitat_final'] ?? "";
        $graduacio_final = $row['graduacio_final'] ?? "";
        $periple_militar = $row['periple_militar'] ?? "";
        $circumstancia_mort_id = $row['circumstancia_mort'] ?? "";
        $desaparegut_data = $row['desaparegut_data'] ?? "";
        $desaparegut_lloc_id = $row['desaparegut_lloc'] ?? "";
        $desaparegut_data_aparicio = $row['desaparegut_data_aparicio'] ?? "";
        $desaparegut_lloc_aparicio_id = $row['desaparegut_lloc_aparicio'] ?? "";
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
                <h2>Tipus de repressió: Morts en combat</h2>
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
                    <label for="condicio" class="form-label negreta">Condició:</label>
                    <select class="form-select" name="condicio" id="condicio" value="">
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="bandol" class="form-label negreta">Bàndol durant la guerra:</label>
                    <select class="form-select" name="bandol" id="bandol" value="">
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="any_lleva" class="form-label negreta">Any lleva:</label>
                    <input type="text" class="form-control" id="any_lleva" name="any_lleva" value="<?php echo $any_lleva; ?>">
                </div>

                <div class="col-md-4">
                    <label for="unitat_inicial" class="form-label negreta">Unitat inicial:</label>
                    <input type="text" class="form-control" id="unitat_inicial" name="unitat_inicial" value="<?php echo $unitat_inicial; ?>">
                </div>

                <div class="col-md-4">
                    <label for="cos" class="form-label negreta">Cos militar:</label>
                    <select class="form-select" name="cos" id="cos" value="">
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="unitat_final" class="form-label negreta">Unitat final:</label>
                    <input type="text" class="form-control" id="unitat_final" name="unitat_final" value="<?php echo $unitat_final; ?>">
                </div>

                <div class="col-md-4">
                    <label for="graduacio_final" class="form-label negreta">Graduació final:</label>
                    <input type="text" class="form-control" id="graduacio_final" name="graduacio_final" value="<?php echo $graduacio_final; ?>">
                </div>

                <div class="col-md-12">
                    <label for="periple_militar" class="form-label negreta">Periple militar:</label>
                    <textarea class="form-control" id="periple_militar" name="periple_militar" rows="3"><?php echo $periple_militar; ?></textarea>
                </div>

                <div class="col-md-4">
                    <label for="circumstancia_mort" class="form-label negreta">Circumstància mort:</label>
                    <select class="form-select" name="circumstancia_mort" id="circumstancia_mort" value="">
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="desaparegut_data" class="form-label negreta">Data de la desaparació:</label>
                    <input type="text" class="form-control" id="desaparegut_data" name="desaparegut_data" value="<?php echo $desaparegut_data; ?>">
                </div>

                <div class="col-md-4">
                    <label for="desaparegut_lloc" class="form-label negreta">Lloc de desaparació:</label>
                    <select class="form-select" name="desaparegut_lloc" id="desaparegut_lloc" value="">
                    </select>

                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/municipi/nou" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi1">Afegir municipi</a>
                        <button id="refreshButton1" class="btn btn-primary btn-sm">Actualitzar llistat Municipis</button>
                    </div>
                </div>

                <div class="col-md-4">
                    <label for="desaparegut_data_aparicio" class="form-label negreta">Data d'aparació del desaparegut:</label>
                    <input type="text" class="form-control" id="desaparegut_data_aparicio" name="desaparegut_data_aparicio" value="<?php echo $desaparegut_data_aparicio; ?>">
                </div>

                <div class="col-md-4">
                    <label for="desaparegut_lloc_aparicio" class="form-label negreta">Lloc d'aparació del desaparegut:</label>
                    <select class="form-select" name="desaparegut_lloc_aparicio" id="desaparegut_lloc_aparicio" value="">
                    </select>

                    <div class="mt-2">
                        <a href="https://memoriaterrassa.cat/gestio/municipi/nou" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi1">Afegir municipi</a>
                        <button id="refreshButton2" class="btn btn-primary btn-sm">Actualitzar llistat Municipis</button>
                    </div>

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

    auxiliarSelect("<?php echo $condicio_id; ?>", "condicio_civil_militar", "condicio", "condicio_ca");
    auxiliarSelect("<?php echo $bandol_id; ?>", "bandols_guerra", "bandol", "bandol_ca");
    auxiliarSelect("<?php echo $cos_id; ?>", "cossos_militars", "cos", "cos_militar_ca");
    auxiliarSelect("<?php echo $circumstancia_mort_id; ?>", "causa_defuncio", "circumstancia_mort", "causa_defuncio_ca");
    auxiliarSelect("<?php echo $desaparegut_lloc_id; ?>", "municipis", "desaparegut_lloc", "ciutat");
    auxiliarSelect("<?php echo $desaparegut_lloc_aparicio_id; ?>", "municipis", "desaparegut_lloc_aparicio", "ciutat");

    document.getElementById('refreshButton2').addEventListener('click', function(event) {
        event.preventDefault();
        auxiliarSelect("<?php echo $desaparegut_lloc_aparicio_id; ?>", "municipis", "desaparegut_lloc_aparicio", "ciutat");
    });

    document.getElementById('refreshButton1').addEventListener('click', function(event) {
        event.preventDefault();
        auxiliarSelect("<?php echo $desaparegut_lloc_id; ?>", "municipis", "desaparegut_lloc", "ciutat");
    });

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
        let urlAjax = devDirectory + "/api/cost_huma_front/put";

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
        let urlAjax = devDirectory + "/api/cost_huma_front/post";

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

    // Asignar la función al botón del formulario
    // document.getElementById("btnInserirDadesCombat").addEventListener("click", enviarFormularioPost);
</script>