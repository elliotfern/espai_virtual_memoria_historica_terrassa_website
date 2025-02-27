<?php
require_once APP_ROOT . '/public/intranet/includes/header.php';

// Obtener la URL completa
$url = $_SERVER['REQUEST_URI'];

// Dividir la URL en partes usando '/' como delimitador
$urlParts = explode('/', $url);

// Obtener la parte deseada (en este caso, la cuarta parte)
$categoriaId = $urlParts[3] ?? '';

$modificaBtn = "";
$idEsdev = "";

if ($categoriaId === "modifica-esdeveniment") {
    $modificaBtn = 1;
    $idEsdev = $routeParams[0];
} else {
    $modificaBtn = 2;
}

$id_old = "";
$any_old = "";
$mes_old = "";
$diaInici_old = "";
$diaFi_old = "";
$mesFi_old = "";
$tema_old = "";
$area_old = "";
$textCa_old = "";
$textEs_old = "";
$textEn_old = "";
$textFr_old = "";
$textIt_old = "";
$textPt_old = "";

if ($modificaBtn === 1) {
    // Verificar si la ID existe en la base de datos
    $query = "SELECT c.id, c.any, c.mes, c.diaInici, c.diaFi, c.mesFi, c.tema, c.area, c.textCa, c.textEs, c.textEn, c.textFr, c.textIt, c.textPt
    FROM db_cronologia AS c
    WHERE c.id = :id";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $idEsdev, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Acceder a las variables de la consulta
            $id_old = $row['id'] ?? "";
            $any_old = $row['any'] ?? "";
            $mes_old = $row['mes'] ?? "";
            $diaInici_old = $row['diaInici'] ?? "";
            $diaFi_old = $row['diaFi'] ?? "";
            $mesFi_old = $row['mesFi'] ?? "";
            $tema_old = $row['tema'] ?? "";
            $area_old = $row['area'] ?? "";
            $textCa_old = $row['textCa'] ?? "";
            $textEs_old = $row['textEs'] ?? "";
            $textEn_old = $row['textEn'] ?? "";
            $textFr_old = $row['textFr'] ?? "";
            $textIt_old = $row['textIt'] ?? "";
            $textPt_old = $row['textPt'] ?? "";
        }
    }
} else {
    // no fer res
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
        min-height: 300px;
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
            <div class="row g-4">

                <?php if ($modificaBtn === 1) { ?>
                    <h2>Modificació esdeveniment cronologia</h2>
                <?php } else { ?>
                    <h2>Cronologia: creació de nou esdeveniment històric</h2>
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

                <div class="col-md-3">
                    <label for="any" class="form-label negreta">Selecciona un any:</label>
                    <select class="form-select" id="any" name="any">
                        <!-- Generar opciones para los años de 1910 a 1989 -->
                        <script>
                            const select = document.getElementById('any');
                            const anyOld = <?php echo json_encode($any_old); ?>; // Obtén el valor de la variable PHP en JavaScript

                            for (let year = 1910; year <= 1989; year++) {
                                const option = document.createElement('option');
                                option.value = year;
                                option.textContent = year;

                                // Si el año es igual al valor de $any_old, lo selecciona
                                if (year == anyOld) {
                                    option.selected = true;
                                }

                                select.appendChild(option);
                            }
                        </script>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="monthSelect" class="form-label negreta">Selecciona un mes:</label>
                    <select class="form-select" id="mes" name="mes">
                        <option value="1" <?php echo ($mes_old == 1) ? 'selected' : ''; ?>>Gener</option>
                        <option value="2" <?php echo ($mes_old == 2) ? 'selected' : ''; ?>>Febrer</option>
                        <option value="3" <?php echo ($mes_old == 3) ? 'selected' : ''; ?>>Març</option>
                        <option value="4" <?php echo ($mes_old == 4) ? 'selected' : ''; ?>>Abril</option>
                        <option value="5" <?php echo ($mes_old == 5) ? 'selected' : ''; ?>>Maig</option>
                        <option value="6" <?php echo ($mes_old == 6) ? 'selected' : ''; ?>>Juny</option>
                        <option value="7" <?php echo ($mes_old == 7) ? 'selected' : ''; ?>>Juliol</option>
                        <option value="8" <?php echo ($mes_old == 8) ? 'selected' : ''; ?>>Agost</option>
                        <option value="9" <?php echo ($mes_old == 9) ? 'selected' : ''; ?>>Setembre</option>
                        <option value="10" <?php echo ($mes_old == 10) ? 'selected' : ''; ?>>Octubre</option>
                        <option value="11" <?php echo ($mes_old == 11) ? 'selected' : ''; ?>>Novembre</option>
                        <option value="12" <?php echo ($mes_old == 12) ? 'selected' : ''; ?>>Desembre</option>
                    </select>
                </div>


                <div class="col-md-3">
                    <label for="diaInici" class="form-label negreta">Dia inici:</label>
                    <input type="text" class="form-control" name="diaInici" id="diaInici" value="<?php echo $diaInici_old; ?>">
                </div>

                <div class="col-md-3">
                    <label for="monthSelect" class="form-label negreta">Selecciona un mes final (opcional):</label>
                    <select class="form-select" id="mesFi" name="mesFi">
                        <option value="1" <?php echo ($mesFi_old == 1) ? 'selected' : ''; ?>>Gener</option>
                        <option value="2" <?php echo ($mesFi_old == 2) ? 'selected' : ''; ?>>Febrer</option>
                        <option value="3" <?php echo ($mesFi_old == 3) ? 'selected' : ''; ?>>Març</option>
                        <option value="4" <?php echo ($mesFi_old == 4) ? 'selected' : ''; ?>>Abril</option>
                        <option value="5" <?php echo ($mesFi_old == 5) ? 'selected' : ''; ?>>Maig</option>
                        <option value="6" <?php echo ($mesFi_old == 6) ? 'selected' : ''; ?>>Juny</option>
                        <option value="7" <?php echo ($mesFi_old == 7) ? 'selected' : ''; ?>>Juliol</option>
                        <option value="8" <?php echo ($mesFi_old == 8) ? 'selected' : ''; ?>>Agost</option>
                        <option value="9" <?php echo ($mesFi_old == 9) ? 'selected' : ''; ?>>Setembre</option>
                        <option value="10" <?php echo ($mesFi_old == 10) ? 'selected' : ''; ?>>Octubre</option>
                        <option value="11" <?php echo ($mesFi_old == 11) ? 'selected' : ''; ?>>Novembre</option>
                        <option value="12" <?php echo ($mesFi_old == 12) ? 'selected' : ''; ?>>Desembre</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="diaFi" class="form-label negreta">Dia fi (opcional):</label>
                    <input type="text" class="form-control" name="diaFi" id="diaFi" value="<?php echo $diaFi_old; ?>">
                </div>

                <div class="col-md-3">
                    <label for="area" class="form-label negreta">Selecciona una àrea geogràfica:</label>
                    <select class="form-select" id="area" name="area">
                        <option value="1" <?php echo ($area_old == 1) ? 'selected' : ''; ?>>Terrassa</option>
                        <option value="2" <?php echo ($area_old == 2) ? 'selected' : ''; ?>>Catalunya</option>
                        <option value="3" <?php echo ($area_old == 3) ? 'selected' : ''; ?>>Espanya</option>
                        <option value="4" <?php echo ($area_old == 4) ? 'selected' : ''; ?>>Europa</option>
                        <option value="5" <?php echo ($area_old == 5) ? 'selected' : ''; ?>>Món</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="tema" class="form-label negreta">Selecciona un tema:</label>
                    <select class="form-select" id="tema" name="tema">
                        <option value="1" <?php echo ($tema_old == 1) ? 'selected' : ''; ?>>Econòmic i laboral</option>
                        <option value="2" <?php echo ($tema_old == 2) ? 'selected' : ''; ?>>Polític i social</option>
                        <option value="3" <?php echo ($tema_old == 3) ? 'selected' : ''; ?>>Moviment obrer</option>
                    </select>
                </div>

                <!-- Crear el editor de texto -->
                <div class="col-md-12">
                    <label for="tema" class="form-label negreta">Esdeveniment (català):</label>
                    <!-- Campo oculto que almacena el valor de Trix -->
                    <input id="textCa" type="hidden" name="textCa" value="<?php echo htmlspecialchars($textCa_old); ?>">

                    <!-- Editor Trix -->
                    <trix-editor input="textCa"></trix-editor>
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
        const trixEditor = document.querySelector("trix-editor");
        if (trixEditor) {
            formData['textCa'] = trixEditor.innerHTML;

        }

        // Obtener el user_id de localStorage
        const userId = localStorage.getItem('user_id');
        if (userId) {
            formData['userId'] = userId;
        }

        // Convertir los datos del formulario a JSON
        const jsonData = JSON.stringify(formData);
        const devDirectory = `https://${window.location.hostname}`;
        let urlAjax = devDirectory + "/api/cronologia/post";
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
        const trixEditor = document.querySelector("trix-editor");
        if (trixEditor) {
            formData['textCa'] = trixEditor.innerHTML;

        }

        // Obtener el user_id de localStorage
        const userId = localStorage.getItem('user_id');
        if (userId) {
            formData['userId'] = userId;
        }

        // Convertir los datos del formulario a JSON
        const jsonData = JSON.stringify(formData);
        const devDirectory = `https://${window.location.hostname}`;
        let urlAjax = devDirectory + "/api/cronologia/put";
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