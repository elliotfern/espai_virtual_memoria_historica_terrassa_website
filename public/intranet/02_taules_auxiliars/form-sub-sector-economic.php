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

$id_old = "";
$sub_sector_cat_old = "";
$sector_cast_old = "";
$sub_sector_eng_old = "";
$btnModificar = 1;

if ($pag === "modifica-sub-sector-economic") {
    $btnModificar = 2;
    $id = $routeParams[0];

    // Verificar si la ID existe en la base de datos
    $query = "SELECT id, sub_sector_cat, sub_sector_es, sub_sector_en, sub_sector_it, sub_sector_fr, sub_sector_pt, idSector
    FROM aux_sub_sector_economic
    WHERE id = :id";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Acceder a las variables de la consulta
            $id_old = $row['id'] ?? "";
            $sub_sector_cat_old = $row['sub_sector_cat'] ?? "";
            $sector_cast_old = $row['sub_sector_cast'] ?? "";
            $sub_sector_eng_old = $row['sub_sector_eng'] ?? "";
        }
    }
}
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="subSectorForm">
        <div class="container">
            <div class="row g-3">

                <?php if ($btnModificar === 1) {
                    echo '<h2>Crear nou sub-sector econòmic</h2>';
                } else {
                    echo '<h2>Modifica sub-sector econòmic ' . $sub_sector_cat_old . '</h2>';
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
                    <label for="sub_sector_cat" class="form-label negreta">Sub-sector econòmic (català):</label>
                    <input type="text" class="form-control" id="sub_sector_cat" name="sub_sector_cat" value="<?php echo $sub_sector_cat_old; ?>">
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

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