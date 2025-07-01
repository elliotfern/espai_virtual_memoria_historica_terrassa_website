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
$pena_ca_old = "";
$pena_es_old = "";
$pena_en_old = "";
$pena_it_old = "";
$pena_fr_old = "";
$pena_pt_old = "";

$btnModificar = 1;

if ($pag === "modifica-pena") {
    $btnModificar = 2;
    $id = $routeParams[0];

    // Verificar si la ID existe en la base de datos
    $query = "SELECT id, pena_ca, pena_es, pena_en, pena_it, pena_fr, pena_pt
    FROM aux_penes 
    WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Acceder a las variables de la consulta
            $id_old = $row['id'] ?? "";
            $pena_ca_old = $row['pena_ca'] ?? "";
            $pena_es_old = $row['pena_es'] ?? "";
            $pena_en_old = $row['pena_en'] ?? "";
            $pena_it_old = $row['pena_it'] ?? "";
            $pena_fr_old = $row['pena_fr'] ?? "";
            $pena_pt_old = $row['pena_pt'] ?? "";
        }
    }
}
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="penaForm" action="" type="post">
        <div class="container">
            <div class="row g-3">
                <?php if ($btnModificar === 1) {
                    echo '<h2>Crear nova pena</h2>';
                } else {
                    echo '<h2>Modifica pena: ' . $pena_ca_old . '</h2>';
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
                    <label for="pena_ca" class="form-label negreta">Pena (català):</label>
                    <input type="text" class="form-control" id="pena_ca" name="pena_ca" value="<?php echo $pena_ca_old; ?>">
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <?php if (isUserAdmin()) : ?>
                    <hr>

                    <div class="col-md-4 mb-4">
                        <label for="pena_es" class="form-label negreta">Pena (castellano):</label>
                        <input type="text" class="form-control" id="pena_es" name="pena_es" value="<?php echo $pena_es_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="pena_en" class="form-label negreta">Pena (English):</label>
                        <input type="text" class="form-control" id="pena_en" name="pena_en" value="<?php echo $pena_en_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="pena_it" class="form-label negreta">Pena (italiano):</label>
                        <input type="text" class="form-control" id="pena_it" name="pena_it" value="<?php echo $pena_it_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="pena_fr" class="form-label negreta">Pena (français):</label>
                        <input type="text" class="form-control" id="pena_fr" name="pena_fr" value="<?php echo $pena_fr_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="pena_pt" class="form-label negreta">Pena (português):</label>
                        <input type="text" class="form-control" id="pena_pt" name="pena_pt" value="<?php echo $pena_pt_old; ?>">
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