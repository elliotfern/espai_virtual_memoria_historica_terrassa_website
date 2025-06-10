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
$bandol_ca_old = "";
$bandol_es_old = "";
$bandol_en_old = "";
$bandol_it_old = "";
$bandol_fr_old = "";
$bandol_pt_old = "";

$btnModificar = 1;

if ($pag === "modifica-carrec-empresa") {
    $btnModificar = 2;
    $id = $routeParams[0];

    // Verificar si la ID existe en la base de datos
    $query = "SELECT id, bandol_ca, bandol_es, bandol_en, bandol_it, bandol_fr, bandol_pt
    FROM aux_bandol
    WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Acceder a las variables de la consulta
            $id_old = $row['id'] ?? "";
            $bandol_ca_old = $row['bandol_ca'] ?? "";
            $bandol_es_old = $row['bandol_es'] ?? "";
            $bandol_en_old = $row['bandol_en'] ?? "";
            $bandol_it_old = $row['bandol_it'] ?? "";
            $bandol_fr_old = $row['bandol_fr'] ?? "";
            $bandol_pt_old = $row['bandol_pt'] ?? "";
        }
    }
}
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="bandolForm">
        <div class="container">
            <div class="row g-3">
                <?php if ($btnModificar === 1) {
                    echo '<h2>Crear nou bàndol Guerra Civil</h2>';
                } else {
                    echo '<h2>Modifica bàndol Guerra Civil: ' . $bandol_ca_old . '</h2>';
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
                    <label for="bandol_ca" class="form-label negreta">Bàndol guerra (català):</label>
                    <input type="text" class="form-control" id="bandol_ca" name="bandol_ca" value="<?php echo $bandol_ca_old; ?>">
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <?php if (isUserAdmin()) : ?>
                    <hr>

                    <div class="col-md-4 mb-4">
                        <label for="bandol_es" class="form-label negreta">Bàndol guerra (castellà):</label>
                        <input type="text" class="form-control" id="bandol_es" name="bandol_es" value="<?php echo $bandol_es_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="bandol_en" class="form-label negreta">Bàndol guerra (anglès):</label>
                        <input type="text" class="form-control" id="bandol_en" name="bandol_en" value="<?php echo $bandol_en_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="bandol_fr" class="form-label negreta">Bàndol guerra(francès):</label>
                        <input type="text" class="form-control" id="bandol_fr" name="bandol_fr" value="<?php echo $bandol_fr_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="bandol_pt" class="form-label negreta">Bàndol guerra (portuguès):</label>
                        <input type="text" class="form-control" id="bandol_pt" name="bandol_pt" value="<?php echo $bandol_pt_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="bandol_it" class="form-label negreta">Bàndol guerra (italià):</label>
                        <input type="text" class="form-control" id="bandol_it" name="bandol_it" value="<?php echo $bandol_it_old; ?>">
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