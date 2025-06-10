<?php
require_once APP_ROOT . '/public/intranet/includes/header.php';

use App\Config\DatabaseConnection;

$conn = DatabaseConnection::getConnection();

if (!$conn) {
    die("No se pudo establecer conexión a la base de datos.");
}

// Obtener la URL completa
$url2 = $_SERVER['REQUEST_URI'];

// Dividir la URL en partes usando '/' como delimitador
$urlParts = explode('/', $url2);

// Obtener la parte deseada (en este caso, la cuarta parte)
$pag = $urlParts[3] ?? '';

$id_old = "";
$acusacio_cat_old = "";
$acusacio_es_old = "";
$acusacio_en_old = "";
$acusacio_fr_old = "";
$acusacio_pt_old = "";
$acusacio_it_old = "";
$btnModificar = 1;

if ($pag === "modifica-carrec-empresa") {
    $btnModificar = 2;
    $id = $routeParams[0];

    // Verificar si la ID existe en la base de datos
    $query = "SELECT id, acusacio_cat, acusacio_es, acusacio_en, acusacio_fr, acusacio_pt, acusacio_it
    FROM aux_acusacions
    WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Acceder a las variables de la consulta
            $id_old = $row['id'] ?? "";
            $acusacio_cat_old = $row['acusacio_cat'] ?? "";
            $acusacio_es_old = $row['acusacio_es'] ?? "";
            $acusacio_en_old = $row['acusacio_en'] ?? "";
            $acusacio_fr_old = $row['acusacio_fr'] ?? "";
            $acusacio_pt_old = $row['acusacio_pt'] ?? "";
            $acusacio_it_old = $row['acusacio_it'] ?? "";
        }
    }
}
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="acusacioForm">
        <div class="container">
            <div class="row g-3">
                <?php if ($btnModificar === 1) {
                    echo '<h2>Crear nova acusació judicial</h2>';
                } else {
                    echo '<h2>Modifica acusació judicial: ' . $acusacio_cat_old . '</h2>';
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
                    <label for="acusacio_cat" class="form-label negreta">Acusació judicial (català):</label>
                    <input type="text" class="form-control" id="acusacio_cat" name="acusacio_cat" value="<?php echo $acusacio_cat_old; ?>">
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <?php if (isUserAdmin()) : ?>
                    <hr>

                    <div class="col-md-4 mb-4">
                        <label for="acusacio_es" class="form-label negreta">Acusació judicial (castellà):</label>
                        <input type="text" class="form-control" id="acusacio_es" name="acusacio_es" value="<?php echo $acusacio_es_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="acusacio_en" class="form-label negreta">Acusació judicial (anglès):</label>
                        <input type="text" class="form-control" id="acusacio_en" name="acusacio_en" value="<?php echo $acusacio_en_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="acusacio_fr" class="form-label negreta">Acusació judicial (francès):</label>
                        <input type="text" class="form-control" id="acusacio_fr" name="acusacio_fr" value="<?php echo $acusacio_fr_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="acusacio_pt" class="form-label negreta">Acusació judicial (portuguès):</label>
                        <input type="text" class="form-control" id="acusacio_pt" name="acusacio_pt" value="<?php echo $acusacio_pt_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="acusacio_it" class="form-label negreta">Acusació judicial (italià):</label>
                        <input type="text" class="form-control" id="acusacio_it" name="acusacio_it" value="<?php echo $acusacio_it_old; ?>">
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