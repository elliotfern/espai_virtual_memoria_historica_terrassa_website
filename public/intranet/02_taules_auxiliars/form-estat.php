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
$estat_old = "";
$estat_ca_old = "";
$modificaBtn = "";

if ($categoriaId === "modifica-estat") {
    $modificaBtn = 1;
    $id_old = $routeParams[0];

    $query = "SELECT c.id, c.estat, c.estat_ca
    FROM aux_dades_municipis_estat AS c
    WHERE c.id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id_old, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $estat_old = $row['estat'] ?? "";
            $id_old = $row['id'] ?? "";
            $estat_ca_old = $row['estat_ca'] ?? "";
        }
    }
} else {
    $modificaBtn = 2;
}
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="formEstat">
        <div class="container">
            <?php if ($modificaBtn === 1) { ?>
                <h2>Modificació dades Estat</h2>
                <h4 id="fitxa">Estat: <?php echo $estat_old; ?></h4>
            <?php } else { ?>
                <h2>Inserció dades nou Estat</h2>
            <?php } ?>

            <div class="row g-3">
                <div class="alert alert-success" role="alert" id="okMessage" style="display:none">
                    <h4 class="alert-heading"><strong>Modificació correcte!</strong></h4>
                    <div id="okText"></div>
                </div>

                <div class="alert alert-danger" role="alert" id="errMessage" style="display:none">
                    <h4 class="alert-heading"><strong>Error en les dades!</strong></h4>
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
                    <label for="estat" class="form-label negreta">Nom Estat (forma oficial):</label>
                    <input type="text" class="form-control" id="estat" name="estat" value="<?php echo $estat_old; ?>">
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="estat_ca" class="form-label negreta">Nom Estat (nom en català):</label>
                    <input type="text" class="form-control" id="estat_ca" name="estat_ca" value="<?php echo $estat_ca_old; ?>">
                    <div class="avis-form">
                        * Omplir en cas que disposem del nom de l'estat en català
                    </div>
                </div>

                <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
                    <div class="col">
                        <a class="btn btn-secondary" role="button" aria-disabled="true" onclick="goBack()">Tornar enrere</a>
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