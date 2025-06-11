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
$condicio_ca_old = "";
$condicio_es_old = "";
$condicio_en_old = "";
$condicio_fr_old = "";
$condicio_it_old = "";
$condicio_pt_old = "";

$btnModificar = 1;

if ($pag === "modifica-condicio-militar") {
    $btnModificar = 2;
    $id = $routeParams[0];

    // Verificar si la ID existe en la base de datos
    $query = "SELECT id, condicio_ca, condicio_es, condicio_en, condicio_fr, condicio_it, condicio_pt
    FROM aux_condicio
    WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Acceder a las variables de la consulta
            $id_old = $row['id'] ?? "";
            $condicio_ca_old = $row['condicio_ca'] ?? "";
            $condicio_es_old = $row['condicio_es'] ?? "";
            $condicio_en_old = $row['condicio_en'] ?? "";
            $condicio_fr_old = $row['condicio_fr'] ?? "";
            $condicio_it_old = $row['condicio_it'] ?? "";
            $condicio_pt_old = $row['condicio_pt'] ?? "";
        }
    }
}
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="condicioForm">
        <div class="container">
            <div class="row g-3">
                <?php if ($btnModificar === 1) {
                    echo '<h2>Crear nova condició civil i militar durant la Guerra Civil</h2>';
                } else {
                    echo '<h2>Modifica condició militar a la Guerra Civil: ' . $condicio_ca_old . '</h2>';
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
                    <label for="bandol_ca" class="form-label negreta">Condició militar (català):</label>
                    <input type="text" class="form-control" id="condicio_ca" name="condicio_ca" value="<?php echo $condicio_ca_old; ?>">
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <?php if (isUserAdmin()) : ?>
                    <hr>

                    <div class="col-md-4 mb-4">
                        <label for="condicio_es" class="form-label negreta">Condició militar (castellà):</label>
                        <input type="text" class="form-control" id="condicio_es" name="condicio_es" value="<?php echo $condicio_es_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="condicio_en" class="form-label negreta">Condició militar (anglès):</label>
                        <input type="text" class="form-control" id="condicio_en" name="condicio_en" value="<?php echo $condicio_en_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="condicio_fr" class="form-label negreta">Condició militar (francès):</label>
                        <input type="text" class="form-control" id="condicio_fr" name="condicio_fr" value="<?php echo $condicio_fr_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="condicio_pt" class="form-label negreta">Condició militar (portuguès):</label>
                        <input type="text" class="form-control" id="condicio_pt" name="condicio_pt" value="<?php echo $condicio_pt_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="condicio_it" class="form-label negreta">Condició militar (italià):</label>
                        <input type="text" class="form-control" id="condicio_it" name="condicio_it" value="<?php echo $condicio_it_old; ?>">
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