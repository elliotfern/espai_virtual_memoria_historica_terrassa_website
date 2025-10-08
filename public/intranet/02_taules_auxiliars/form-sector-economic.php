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
$sector_ca_old = "";
$sector_es_old = "";
$sector_en_old = "";
$sector_fr_old = "";
$sector_it_old = "";
$sector_pt_old = "";

$btnModificar = 1;

if ($pag === "modifica-sector-economic") {
    $id_old = $routeParams[0];
    // Verificar si la ID existe en la base de datos
    $query = "SELECT id, sector_ca, sector_es, sector_en, sector_fr, sector_it, sector_pt
    FROM aux_sector_economic
    WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id_old, PDO::PARAM_INT);
    $stmt->execute();


    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Acceder a las variables de la consulta
            $id_old = $row['id'] ?? "";
            $sector_ca_old = $row['sector_ca'] ?? "";
            $sector_es_old = $row['sector_es'] ?? "";
            $sector_en_old = $row['sector_en'] ?? "";
            $sector_fr_old = $row['sector_fr'] ?? "";
            $sector_it_old = $row['sector_it'] ?? "";
            $sector_pt_old = $row['sector_it'] ?? "";

            // Crear el botón o usar los datos (TIPO PUT)
            $btnModificar = 2;
        }
    }
}
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="sectorEconomicForm">
        <div class="container">
            <div class="row g-3">
                <?php if ($btnModificar === 1) {
                    echo '<h2>Crear nou sector econòmic</h2>';
                } else {
                    echo '<h2>Modifica sector econòmic: ' . $sector_ca_old . '</h2>';
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
                    <label for="sector_ca" class="form-label negreta">Sector econòmic (català):</label>
                    <input type="text" class="form-control" id="sector_ca" name="sector_ca" value="<?php echo $sector_ca_old; ?>">
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <?php if (isUserAdmin()) : ?>
                    <hr>

                    <div class="col-md-4 mb-4">
                        <label for="sector_es" class="form-label negreta">Sector econòmic (castellà):</label>
                        <input type="text" class="form-control" id="sector_es" name="sector_es" value="<?php echo $sector_es_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="sector_en" class="form-label negreta">Sector econòmic (anglès):</label>
                        <input type="text" class="form-control" id="sector_en" name="sector_en" value="<?php echo $sector_en_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="sector_fr" class="form-label negreta">Sector econòmic (francès):</label>
                        <input type="text" class="form-control" id="sector_fr" name="sector_fr" value="<?php echo $sector_fr_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="sector_it" class="form-label negreta">Sector econòmic (italià):</label>
                        <input type="text" class="form-control" id="sector_it" name="sector_it" value="<?php echo $sector_it_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="sector_pt" class="form-label negreta">Sector econòmic (portuguès):</label>
                        <input type="text" class="form-control" id="sector_pt" name="sector_pt" value="<?php echo $sector_pt_old; ?>">
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