<?php

// Obtener la URL completa
$url = $_SERVER['REQUEST_URI'];

// Dividir la URL en partes usando '/' como delimitador
$urlParts = explode('/', $url);

// Obtener la parte deseada (en este caso, la cuarta parte)
$categoriaId = $urlParts[5] ?? '';

$idPersona = $routeParams[0];

require_once APP_ROOT . '/public/intranet/includes/header.php';

$modificaBtn = "";
$idRepresaliat = "";

if ($categoriaId === "modifica") {
    $modificaBtn = 1;
    $idRepresaliat = $routeParams[1];
} else {
    $modificaBtn = 2;
}

$id_old = "";
$nom_old = "";
$cognom1_old = "";
$cognom2_old = "";
$anyNaixement_old = "";
$relacio_parentiu_old = "";
$idPersona;
$idParent_old = "";

if ($modificaBtn === 1) {
    // Verificar si la ID existe en la base de datos
    $query = "SELECT f.id, f.nom, f.cognom1, f.cognom2, f.anyNaixement, f.relacio_parentiu, f.idParent, d.nom AS nom_represaliat, d.cognom1 AS cognom1_represaliat, d.cognom2 AS cognom2_represaliat
    FROM aux_familiars AS f
    LEFT JOIN db_dades_personals AS d ON f.idParent = d.id
    WHERE f.id = :id";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $idRepresaliat, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Acceder a las variables de la consulta
            $id_old = $row['id'] ?? "";
            $nom_old = $row['nom'] ?? "";
            $cognom1_old = $row['cognom1'] ?? "";
            $cognom2_old = $row['cognom2'] ?? "";
            $anyNaixement_old = $row['anyNaixement'] ?? "";
            $relacio_parentiu_old = $row['relacio_parentiu'] ?? "";
            $nom_represaliat = $row['nom_represaliat'] ?? "";
            $cognom1_represaliat = $row['cognom1_represaliat'] ?? "";
            $cognom2_represaliat = $row['cognom2_represaliat'] ?? "";
            $idParent_old = $row['idParent'] ?? "";
        }
    }
} else {
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
            $idParent_old = $idPersona;
        }
    }
}
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="familiarForm">
        <div class="container">
            <div class="row">
                <?php if ($modificaBtn === 1) { ?>
                    <h2>Modificació dades familiar</h2>
                    <h4 id="fitxaNomCognoms">Fitxa represaliat: <a href="https://memoriaterrassa.cat/fitxa/<?php echo $idParent_old; ?>" target="_blank"><?php echo $nom_represaliat . " " . $cognom1_represaliat . " " . $cognom2_represaliat; ?></a></h4>
                    <h6 id="fitxaNomCognoms2">Modificació dades de: <?php echo $nom_old . " " . $cognom1_old . " " . $cognom2_old; ?></h6>
                <?php } else { ?>
                    <h2>Inserció dades familiar</h2>
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

                <input type="hidden" name="id" id="id" value="<?php echo $id_old; ?>">

                <div class="col-md-4">
                    <label for="nom" class="form-label negreta">Nom:</label>
                    <input type="text" class="form-control" id="nom" name="nom" value="<?php echo $nom_old; ?>">
                </div>

                <div class="col-md-4">
                    <label for="cognom1" class="form-label negreta">Primer cognom:</label>
                    <input type="text" class="form-control" id="cognom1" name="cognom1" value="<?php echo $cognom1_old; ?>">
                </div>

                <div class="col-md-4">
                    <label for="cognom2" class="form-label negreta">Segon cognom:</label>
                    <input type="text" class="form-control" id="cognom2" name="cognom2" value="<?php echo $cognom2_old; ?>">
                </div>

                <div class="col-md-4">
                    <label for="anyNaixement" class="form-label negreta">Any de naixement:</label>
                    <input type="text" class="form-control" id="anyNaixement" name="anyNaixement" value="<?php echo $anyNaixement_old; ?>">
                </div>

                <div class="col-md-4">
                    <label for="relacio_parentiu" class="form-label negreta">Relació de parentiu:</label>
                    <select class="form-select" name="relacio_parentiu" id="relacio_parentiu" value="">
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="idParent" class="form-label negreta">Familiar represaliat:</label>
                    <select class="form-select" name="idParent" id="idParent" value="">
                    </select>
                </div>

                <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
                    <div class="col">
                        <a class="btn btn-secondary" role="button" aria-disabled="true" onclick="goBack()">Tornar enrere</a>
                    </div>
                    <div class="col d-flex justify-content-end align-items-center">

                        <?php
                        if ($modificaBtn === 1) {
                            echo '<a class="btn btn-primary" role="button" aria-disabled="true" id="btnModificarDadesFamiliar" onclick="enviarFormulario(event)">Modificar dades</a>';
                        } else {
                            echo '<a class="btn btn-primary" role="button" aria-disabled="true" id="btnInserirDadesFamiliar" onclick="enviarFormularioPost(event)">Inserir dades</a>';
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

    auxiliarSelect("<?php echo $relacio_parentiu_old; ?>", "relacions_parentiu", "relacio_parentiu", "relacio_parentiu");
    auxiliarSelect("<?php echo $idParent_old; ?>", "llistat_complert_represaliats", "idParent", "nom_complert");

    // Función para manejar el envío del formulario
    async function enviarFormulario(event) {
        event.preventDefault(); // Prevenir el envío por defecto

        // Obtener el formulario
        const form = document.getElementById("familiarForm");

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
        let urlAjax = devDirectory + "/api/familiars/put";

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
        const form = document.getElementById("familiarForm");

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
        let urlAjax = devDirectory + "/api/familiars/post";

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