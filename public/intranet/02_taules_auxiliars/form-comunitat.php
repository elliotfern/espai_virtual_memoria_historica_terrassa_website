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
$categoriaId = $urlParts[3] ?? '';

$id_old = "";
$comunitat_es_old = "";
$comunitat_ca_old = "";
$comunitat_en_old = "";
$comunitat_fr_old = "";
$comunitat_it_old = "";
$comunitat_pt_old = "";
$modificaBtn = "";

if ($categoriaId === "modifica-comunitat") {
    $modificaBtn = 1;
    $id_old = $routeParams[0];

    $query = "SELECT c.id, c.comunitat_es, c.comunitat_ca, c.comunitat_en, c.comunitat_fr, c.comunitat_it, c.comunitat_pt
    FROM aux_dades_municipis_comunitat AS c
    WHERE c.id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id_old, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id_old = $row['id'] ?? "";
            $comunitat_es_old = $row['comunitat_es'] ?? "";
            $comunitat_ca_old = $row['comunitat_ca'] ?? "";
            $comunitat_en_old = $row['comunitat_en'] ?? "";
            $comunitat_fr_old = $row['comunitat_fr'] ?? "";
            $comunitat_it_old = $row['comunitat_it'] ?? "";
            $comunitat_pt_old = $row['comunitat_pt'] ?? "";
        }
    }
} else {
    $modificaBtn = 2;
}
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="formComunitat">
        <div class="container">
            <?php if ($modificaBtn === 1) { ?>
                <h2>Modificació dades Comunitat autònoma / regió / Estat federal</h2>
                <h4 id="fitxa">Comunitat: <?php echo $comunitat_ca_old; ?></h4>
            <?php } else { ?>
                <h2>Inserció dades nova Comunitat Autònoma/Regió/Estat federal</h2>
            <?php } ?>
            <div class="row g-3">

                <div class="alert alert-success" role="alert" id="okMessage" style="display:none">
                    <div id="okText"></div>
                </div>

                <div class="alert alert-danger" role="alert" id="errMessage" style="display:none">
                    <div id="errText"></div>
                </div>

                <div class="alert alert-info">
                    <h5>Sobre l'ús general de topònims en català</h5>
                    <ul>

                        <li>1. Els topònims de Catalunya s'utilitzen en la seva forma oficial i, sempre que sigui possible, en la seva forma íntegra.</li>
                        <li>2. Els topònims d'altres territoris de l'àrea lingüística catalana s'utilitzen en la forma en català.</li>
                        <li>3. Els exotopònims, és a dir, els topònims de fora de l'àrea lingüística catalana s'utilitzen en català quan hi ha una forma establerta amb ús tradicional, sens perjudici que hi pugui figurar també la denominació en altres llengües del territori corresponent.</li>
                        <li>4. Els topònims de l'àrea lingüística occitana de fora de Catalunya s'utilitzen en la forma tradicional en català o en occità, tret de l'Aran, on s'utilitza la forma tradicional en occità, i sens perjudici que hi pugui figurar també la denominació en altres llengües del territori corresponent.</li>
                    </ul>
                </div>

                <input type="hidden" id="id" name="id" value="<?php echo $id_old; ?>">

                <div class="col-md-4 mb-4">
                    <label for="comunitat_ca" class="form-label negreta">Nom comunitat autònoma/regió (nom en català):</label>
                    <input type="text" class="form-control" id="comunitat_ca" name="comunitat_ca" value="<?php echo $comunitat_ca_old; ?>">
                    <div class="avis-form">
                        * Obligatori. Omplir amb la forma catalana del nom de la comunitat / regió.
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="comunitat_es" class="form-label negreta">Nom comunitat autònoma/regió (nom en castellà):</label>
                    <input type="text" class="form-control" id="comunitat_es" name="comunitat_es" value="<?php echo $comunitat_es_old; ?>">
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <?php if (isUserAdmin()) : ?>

                    <div class="col-md-4 mb-4">
                        <label for="comunitat_en" class="form-label negreta">Nom comunitat autònoma/regió (nom en anglès):</label>
                        <input type="text" class="form-control" id="comunitat_en" name="comunitat_en" value="<?php echo $comunitat_en_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="comunitat_fr" class="form-label negreta">Nom comunitat autònoma/regió (nom en francès):</label>
                        <input type="text" class="form-control" id="comunitat_fr" name="comunitat_fr" value="<?php echo $comunitat_fr_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="comunitat_it" class="form-label negreta">Nom comunitat autònoma/regió (nom en italià):</label>
                        <input type="text" class="form-control" id="comunitat_it" name="comunitat_it" value="<?php echo $comunitat_it_old; ?>">
                    </div>


                    <div class="col-md-4 mb-4">
                        <label for="comunitat_pt" class="form-label negreta">Nom comunitat autònoma/regió (nom en portuguès):</label>
                        <input type="text" class="form-control" id="comunitat_pt" name="comunitat_pt" value="<?php echo $comunitat_pt_old; ?>">
                    </div>

                <?php endif; ?>

                <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
                    <div class="col">
                    </div>

                    <div class="col d-flex justify-content-end align-items-center">
                        <?php
                        if ($modificaBtn === 1) {
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