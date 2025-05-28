<?php
require_once APP_ROOT . '/public/intranet/includes/header.php';

// Obtener la URL completa
$url = $_SERVER['REQUEST_URI'];

// Dividir la URL en partes usando '/' como delimitador
$urlParts = explode('/', $url);

// Obtener la parte deseada (en este caso, la cuarta parte)
$categoriaId = $urlParts[3] ?? '';

if ($categoriaId === "modifica-usuari") {
    $modificaBtn = 1;
    $id = $routeParams[0];
    $titol = "Modificació usuari";
?>
    <script type="module">
        formUpdate("<?php echo $id; ?>");
    </script>
<?php
} else {
    $modificaBtn = 2;
    $titol = "Creació de nou usuari";
?>
    <script type="module">
        // Llenar selects con opciones
        selectOmplirDades("/api/auth/get/tipusUsuaris", "", "user_type", "tipus");
        selectOmplirDades("/api/auxiliars/get/avatarsUsuaris", "", "avatar", "nomImatge");
    </script>
<?php
}
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <div class="container">
        <div class="row">
            <h2>Gestió base de dades auxiliars: usuaris</h2>
            <h4><?php echo $titol; ?></h4>
            <?php if (isUserAdmin()) : ?>

                <form id="usuariForm">
                    <div class="row g-5">
                        <div class="alert alert-success" role="alert" id="okMessage" style="display:none">
                            <h4 class="alert-heading"><strong>Modificació correcte!</strong></h4>
                            <div id="okText"></div>
                        </div>

                        <div class="alert alert-danger" role="alert" id="errMessage" style="display:none">
                            <h4 class="alert-heading"><strong>Error en les dades!</strong></h4>
                            <div id="errText"></div>
                        </div>

                        <input type="hidden" name="id" id="id" value="">

                        <div class="col-md-4">
                            <label for="nom" class="form-label negreta">Nom usuari:</label>
                            <input type="text" class="form-control" id="nom" name="nom" value="">
                        </div>

                        <div class="col-md-4">
                            <label for="email" class="form-label negreta">Email:</label>
                            <input type="text" class="form-control" id="email" name="email" value="">
                        </div>

                        <div class="col-md-4">
                            <label for="email" class="form-label negreta">Password:</label>
                            <input type="password" class="form-control" id="password" name="password" value="">
                        </div>

                        <div class="col-md-4">
                            <label for="user_type" class="form-label negreta">Tipus d'usuari:</label>
                            <select class="form-select" id="user_type" name="user_type" value="">
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="avatar" class="form-label negreta">Avatar:</label>
                            <select class="form-select" id="avatar" name="avatar" value="">
                            </select>
                            <div class="mt-2">
                                <a href="https://memoriaterrassa.cat/gestio/auxiliars/nou-avatar-usuari" target="_blank" class="btn btn-secondary btn-sm" id="afegirAvatar">Afegir avatar</a>
                                <button type="button" id="refreshButtonAvatar" class="btn btn-primary btn-sm">Actualitzar llistat</button>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label for="biografia_cat" class="form-label negreta">Descripcio (català):</label>
                            <textarea id="biografia_cat" name="biografia_cat" class="form-control" rows="4"></textarea>
                        </div>

                        <div class="row espai-superior" style="padding-top:25px">
                            <div class="col">
                                <a class="btn btn-secondary" role="button" aria-disabled="true" onclick="goBack()">Tornar enrere</a>
                            </div>
                            <div class="col d-flex justify-content-end align-items-center">

                                <?php
                                if ($modificaBtn === 1) {
                                    echo '<button class="btn btn-primary" id="btnModificar" type="submit">Modificar dades</button>';
                                } else {
                                    echo '<button class="btn btn-primary" id="btnInserir" type="submit">Inserir dades</button>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </form>

            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    function formUpdate(id) {
        let urlAjax = "/api/auth/get/usuari?id=" + id;

        fetch(urlAjax, {
                method: "GET",
            })
            .then(response => response.json())
            .then(data => {
                // Establecer valores en los campos del formulario
                document.getElementById("id").value = data.id;
                document.getElementById("nom").value = data.nom;
                document.getElementById('email').value = data.email;
                document.getElementById('biografia_cat').value = data.biografia_cat;

                // Llenar selects con opciones
                selectOmplirDades("/api/auth/get/tipusUsuaris", data.user_type, "user_type", "tipus");
                selectOmplirDades("/api/auxiliars/get/avatarsUsuaris", data.avatar, "avatar", "nomImatge");
            })
            .catch(error => console.error("Error al obtener los datos:", error));
    }

    async function selectOmplirDades(url, selectedValue, selectId, textField) {
        try {
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error('Error en la sol·licitud AJAX');
            }

            const data = await response.json();
            const selectElement = document.getElementById(selectId);
            if (!selectElement) {
                console.error(`Select element with id ${selectId} not found`);
                return;
            }

            // Netejar les opcions actuals
            selectElement.innerHTML = '';

            // Afegir opció per defecte
            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.text = 'Selecciona una opció:';
            defaultOption.disabled = false;
            defaultOption.selected = !selectedValue; // si no hi ha valor seleccionat, aquesta es selecciona
            selectElement.appendChild(defaultOption);

            // Afegir les noves opcions
            data.forEach((item) => {
                const option = document.createElement('option');
                option.value = item.id;
                option.text = item[textField];
                if (item.id === selectedValue) {
                    option.selected = true;
                }
                selectElement.appendChild(option);
            });
        } catch (error) {
            console.error('Error:', error);
        }
    }
</script>