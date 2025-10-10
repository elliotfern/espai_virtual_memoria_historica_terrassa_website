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
$comarca_old = "";
$comarca_ca_old = "";
$modificaBtn = "";

if ($categoriaId === "modifica-comarca") {
    $id_old = $routeParams[0];
    $modificaBtn = 1;

    // Verificar si la ID existe en la base de datos
    $query = "SELECT c.id, c.comarca, comarca_ca
    FROM aux_dades_municipis_comarca AS c
    WHERE c.id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id_old, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $comarca_old = $row['comarca'] ?? "";
            $comarca_ca_old = $row['comarca_ca'] ?? "";
            $id_old = $row['id'] ?? "";
        }
    }
} else {
    $modificaBtn = 2;
}
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="formComarca">
        <div class="container">
            <?php if ($modificaBtn === 1) { ?>
                <h2>Modificació dades comarca</h2>
                <h4 id="fitxa">Comarca: <?php echo $comarca_old; ?></h4>
            <?php } else { ?>
                <h2>Inserció dades nova comarca</h2>
            <?php } ?>

            <div class="row g-3">
                <div class="alert alert-success" role="alert" id="okMessage" style="display:none">
                    <div id="okText"></div>
                </div>

                <div class="alert alert-danger" role="alert" id="errMessage" style="display:none">
                    <div id="errText"></div>
                </div>

                <input type="hidden" id="id" name="id" value="<?php echo $id_old; ?>">

                <div class="alert alert-info">
                    <h5>Sobre l'ús general de topònims en català</h5>
                    <ul>

                        <li>1. Els topònims de Catalunya s'utilitzen en la seva forma oficial i, sempre que sigui possible, en la seva forma íntegra.</li>
                        <li>2. Els topònims d'altres territoris de l'àrea lingüística catalana s'utilitzen en la forma en català.</li>
                        <li>3. Els exotopònims, és a dir, els topònims de fora de l'àrea lingüística catalana s'utilitzen en català quan hi ha una forma establerta amb ús tradicional, sens perjudici que hi pugui figurar també la denominació en altres llengües del territori corresponent.</li>
                        <li>4. Els topònims de l'àrea lingüística occitana de fora de Catalunya s'utilitzen en la forma tradicional en català o en occità, tret de l'Aran, on s'utilitza la forma tradicional en occità, i sens perjudici que hi pugui figurar també la denominació en altres llengües del territori corresponent.</li>
                    </ul>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="comarca" class="form-label negreta">Nom oficial de la comarca (a Catalunya, nom sempre en català):</label>
                    <input type="text" class="form-control" id="comarca" name="comarca" value="<?php echo $comarca_old; ?>">
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <?php if (isUserAdmin()) : ?>

                    <div class="col-md-4 mb-4">
                        <label for="comarca" class="form-label negreta">Nom comarca (nom en català):</label>
                        <input type="text" class="form-control" id="comarca_ca" name="comarca_ca" value="<?php echo $comarca_ca_old; ?>">
                        <div class="avis-form">
                            * Omplir en cas que disposem del nom de la comarca en català
                        </div>
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