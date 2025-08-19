<?php

use App\Config\DatabaseConnection;

$conn = DatabaseConnection::getConnection();

if (!$conn) {
    die("No se pudo establecer conexión a la base de datos.");
}
require_once APP_ROOT . '/public/intranet/includes/header.php';

// Obtener la URL completa
$url2 = $_SERVER['REQUEST_URI'];

// Dividir la URL en partes usando '/' como delimitador
$urlParts = explode('/', $url2);

// Obtener la parte deseada (en este caso, la cuarta parte)
$pag = $urlParts[3] ?? '';

$causa_defuncio_ca_old = "";
$id_old = "";
$cat_old = "";
$causa_defuncio_es_old = "";
$causa_defuncio_en_old = "";
$causa_defuncio_fr_old = "";
$causa_defuncio_pt_old = "";
$causa_defuncio_it_old = "";

$btnModificar = 1;

if ($pag === "modifica-causa-mort") {
    // Verificar si la ID existe en la base de datos
    $query = "SELECT id, causa_defuncio_ca, cat, causa_defuncio_es, causa_defuncio_en, causa_defuncio_fr, causa_defuncio_pt, causa_defuncio_it
    FROM aux_causa_defuncio";
    $stmt = $conn->prepare($query);
    $stmt->execute();


    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Acceder a las variables de la consulta
            $id_old = $row['id'] ?? "";
            $causa_defuncio_ca_old = $row['causa_defuncio_ca'] ?? "";
            $causa_defuncio_en_old = $row['causa_defuncio_en'] ?? "";
            $causa_defuncio_fr_old = $row['causa_defuncio_fr'] ?? "";
            $causa_defuncio_pt_old = $row['causa_defuncio_pt'] ?? "";
            $causa_defuncio_it_old = $row['causa_defuncio_it'] ?? "";
            $cat_old = $row['cat'] ?? "";

            // Crear el botón o usar los datos (TIPO PUT)
            $btnModificar = 2;
        }
    }
}
?>

<?php if ($isAdmin): ?>

    <div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
        <form id="causaMortForm">
            <div class="container">
                <div class="row g-3">
                    <?php if ($btnModificar === 1) {
                        echo '<h2>Crear nova causa de mort</h2>';
                    } else {
                        echo '<h2>Modifica causa de mort: ' . $causa_defuncio_ca_old . '</h2>';
                    }
                    ?>

                    <div class="alert alert-success" role="alert" id="okMessage" style="display:none">
                        <div id="okText"></div>
                    </div>

                    <div class="alert alert-danger" role="alert" id="errMessage" style="display:none">
                        <div id="errText"></div>
                    </div>

                    <input type="hidden" name="id" id="id" value="<?php echo $id_old; ?>">

                    <div class="col-md-4 mb-4">
                        <label for="causa_defuncio_ca" class="form-label negreta">Causa de mort (català):</label>
                        <input type="text" class="form-control" id="causa_defuncio_ca" name="causa_defuncio_ca" value="<?php echo $causa_defuncio_ca_old; ?>">
                        <div class="avis-form">
                            * Camp obligatori
                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="cat" class="form-label">Selecciona una categoria:</label>
                        <select class="form-select" id="cat" name="cat">
                            <option selected disabled>Escull una opció</option>
                            <option value="1">Combatent al front</option>
                            <option value="2">Mort civil</option>
                            <option value="3">General</option>
                        </select>
                    </div>

                    <?php if (isUserAdmin()) : ?>
                        <hr>

                        <div class="col-md-4 mb-4">
                            <label for="causa_defuncio_es" class="form-label negreta">Causa de mort (castellà):</label>
                            <input type="text" class="form-control" id="causa_defuncio_es" name="causa_defuncio_es" value="<?php echo $causa_defuncio_es_old; ?>">
                        </div>

                        <div class="col-md-4 mb-4">
                            <label for="causa_defuncio_en" class="form-label negreta">Causa de mort (anglès):</label>
                            <input type="text" class="form-control" id="causa_defuncio_en" name="causa_defuncio_en" value="<?php echo $causa_defuncio_en_old; ?>">
                        </div>

                        <div class="col-md-4 mb-4">
                            <label for="causa_defuncio_fr" class="form-label negreta">Causa de mort (francès):</label>
                            <input type="text" class="form-control" id="causa_defuncio_fr" name="causa_defuncio_fr" value="<?php echo $causa_defuncio_fr_old; ?>">
                        </div>

                        <div class="col-md-4 mb-4">
                            <label for="causa_defuncio_it" class="form-label negreta">Causa de mort (italià):</label>
                            <input type="text" class="form-control" id="causa_defuncio_it" name="causa_defuncio_it" value="<?php echo $causa_defuncio_it_old; ?>">
                        </div>

                        <div class="col-md-4 mb-4">
                            <label for="causa_defuncio_pt" class="form-label negreta">Causa de mort (portuguès):</label>
                            <input type="text" class="form-control" id="causa_defuncio_pt" name="causa_defuncio_pt" value="<?php echo $causa_defuncio_pt_old; ?>">
                        </div>

                    <?php endif; ?>

                    <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
                        <div class="col"></div>

                        <div class="col d-flex justify-content-end align-items-center">

                            <?php
                            if ($btnModificar === 2) {
                                echo '<button class="btn btn-primary" type="submit">Modificar dades</button>';
                            } else {
                                echo '<button class="btn btn-primary" type="submit">Inserir dades</button>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
<?php else: ?>
    <p>Només els usuaris administratius poden crear nous ítems</p>
<?php endif; ?>
<script>
    // Simulamos que este valor viene de una API
    const bandol_id_from_api = "<?php echo $cat_old; ?>";
    console.log(bandol_id_from_api);

    // Esperamos que el DOM esté listo (opcional si script va al final del body)
    document.addEventListener('DOMContentLoaded', () => {
        const selectElement = document.getElementById('cat');

        if (selectElement) {
            selectElement.value = bandol_id_from_api.toString(); // Aseguramos que sea string
        }
    });
</script>