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
$jutjat_ca_old = "";
$jutjat_es_old = "";
$jutjat_en_old = "";
$jutjat_fr_old = "";
$jutjat_it_old = "";
$jutjat_pt_old = "";

$btnModificar = 1;

if ($pag === "modifica-jutjat") {
    $btnModificar = 2;
    $id = $routeParams[0];

    // Verificar si la ID existe en la base de datos
    $query = "SELECT id, jutjat_ca,
    jutjat_es,
    jutjat_en,
    jutjat_fr,
    jutjat_it,
    jutjat_pt
    FROM aux_jutjats 
    WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Acceder a las variables de la consulta
            $id_old = $row['id'] ?? "";
            $jutjat_ca_old = $row['jutjat_ca'] ?? "";
            $jutjat_es_old = $row['jutjat_es'] ?? "";
            $jutjat_en_old = $row['jutjat_en'] ?? "";
            $jutjat_fr_old = $row['jutjat_fr'] ?? "";
            $jutjat_it_old = $row['jutjat_it'] ?? "";
            $jutjat_pt_old = $row['jutjat_pt'] ?? "";
        }
    }
}
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="jutjatForm" action="" type="post">
        <div class="container">
            <div class="row g-3">
                <?php if ($btnModificar === 1) {
                    echo '<h2>Crear nou jutjat</h2>';
                } else {
                    echo '<h2>Modifica jutjat: ' . $jutjat_ca_old . '</h2>';
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
                    <label for="jutjat_ca" class="form-label negreta">Jutjat (català):</label>
                    <input type="text" class="form-control" id="jutjat_ca" name="jutjat_ca" value="<?php echo $jutjat_ca_old; ?>">

                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <?php if (isUserAdmin()) : ?>
                    <hr>

                    <div class="col-md-4 mb-4">
                        <label for="jutjat_es" class="form-label negreta">Juzgado (castellano):</label>
                        <input type="text" class="form-control" id="jutjat_es" name="jutjat_es" value="<?php echo $jutjat_es_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="jutjat_en" class="form-label negreta">Court (English):</label>
                        <input type="text" class="form-control" id="jutjat_en" name="jutjat_en" value="<?php echo $jutjat_en_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="jutjat_fr" class="form-label negreta">Tribunal (français):</label>
                        <input type="text" class="form-control" id="jutjat_fr" name="jutjat_fr" value="<?php echo $jutjat_fr_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="jutjat_it" class="form-label negreta">Tribunale (italiano):</label>
                        <input type="text" class="form-control" id="jutjat_it" name="jutjat_it" value="<?php echo $jutjat_it_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="jutjat_pt" class="form-label negreta">Tribunal (português):</label>
                        <input type="text" class="form-control" id="jutjat_pt" name="jutjat_pt" value="<?php echo $jutjat_pt_old; ?>">
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